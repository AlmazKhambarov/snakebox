<?php

namespace App\Http\Controllers\Api\Payment;

use App\Models\PaymentMethods;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Promocode;
use App\Models\User;
use App\Services\RedisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\PromocodeUse;
use App\Models\ReferralEarning;
use App\Models\Settings;

class NirvanaUzsController extends Controller
{
    protected RedisService $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
    }

    public function create(Request $request)
    {
        $user           = $request->user();
        $settings       = Settings::query()->first();
        $method         = $request->payment_method; // e.g. "Humo UZS"
        $system         = $request->system;          // e.g. "nirvana_uzs"
        $transaction_id = time() . uniqid();

        if (!$method) {
            return ['success' => false, 'message' => 'Выберите способ оплаты'];
        }

        $amount      = $request->amount;
        $promo_input = $request->promocode;

        $limits = $this->getDepositLimits($method, $system);

        if ($amount < $limits['min_amount']) {
            return ['success' => false, 'message' => "Минимальная сумма депозита: {$limits['min_amount']} ₽."];
        }

        if ($amount > $limits['max_amount']) {
            return ['success' => false, 'message' => "Максимальная сумма депозита: {$limits['max_amount']} ₽."];
        }

        $promocode = null;
        if (!is_null($promo_input)) {
            $promocode = Promocode::query()
                ->where('code', $promo_input)
                ->where('type', 'deposit_percent')
                ->where('is_active', true)
                ->where('uses_left', '>', 0)
                ->where('valid_from', '<', Carbon::now())
                ->where('valid_until', '>', Carbon::now())
                ->first();

            if (!$promocode) {
                return ['success' => false, 'message' => 'Промокод не найден. Истек срок действия, или неактивен!'];
            }
        }

        $payment = Payment::query()->create([
            'user_id'        => $user->id,
            'promocode_id'   => $promocode ? $promocode->id : null,
            'system'         => 'nirvana_uzs',
            'method'         => $method,
            'amount'         => $amount,
            'status'         => Payment::PENDING,
            'transaction_id' => $transaction_id,
            'metadata'       => [
                'user_id'  => $user->id,
                'username' => $user->username,
                'time'     => Carbon::now()->format('Y-m-d H:i:s'),
                'ip'       => $request->getClientIp(true),
            ],
        ]);

        $options = [
            'amount'      => $amount,
            'redirectURL' => config('app.frontend_url'),
            'siteName'    => $settings->title,
            'callbackURL' => config('app.url') . '/api/payment/nirvana-uzs/callback?externalId=' . $transaction_id,
            'externalID'  => $transaction_id,
            'currency'    => 'UZS',
            'userInfo'    => [
                'id'        => (string) $user->id,
                'ip'        => $request->getClientIp(),
                'userAgent' => $request->userAgent(),
                'email'     => 'no-email@gmail.com',
            ],
        ];

        try {
            $client   = new Client();
            $response = $client->post('https://f.nirvanapay.pro/api/v2/order', [
                'headers' => [
                    'ApiPublic'  => env('NIRVANA_API_PUBLIC'),
                    'ApiPrivate' => env('NIRVANA_API_PRIVATE'),
                ],
                'json' => $options,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            Log::channel('payment_nirvana')->info('NirvanaUZS response:', $responseData);

            if (empty($responseData['data']['redirectURL'])) {
                Log::channel('payment_nirvana')->error('NirvanaUZS: no redirectURL', $responseData);
                return ['success' => false, 'message' => 'Ошибка при создании платежа. Попробуйте позже.'];
            }

            return [
                'success' => true,
                'message' => 'Переносим на страницу оплаты...',
                'link'    => $responseData['data']['redirectURL'],
            ];
        } catch (\Exception $e) {
            Log::channel('payment_nirvana')->error('NirvanaUZS exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Ошибка соединения с платёжной системой.'];
        }
    }

    public function callback(Request $request)
    {
        $externalId = $request->query('externalId');

        try {
            $client   = new Client();
            $response = $client->get('https://f.nirvanapay.pro/api/v2/order', [
                'headers' => [
                    'ApiPublic'  => env('NIRVANA_API_PUBLIC'),
                    'ApiPrivate' => env('NIRVANA_API_PRIVATE'),
                ],
                'query' => ['externalId' => $externalId],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            Log::channel('payment_nirvana')->debug('NirvanaUZS callback:', $body);

            if (($body['data']['status'] ?? '') !== 'SUCCESS') {
                return response('Payment not SUCCESS', 200);
            }

            $payment = Payment::query()->where('transaction_id', $body['data']['externalID'])->first();
            if (!$payment) return response('Payment not found', 200);
            if ($payment->status == Payment::PAID) return response('Already paid', 200);

            $user = User::query()->find($payment->user_id);
            if (!$user) return response('User not found', 200);

            $amount = $payment->amount * 100;

            // === Промокод ===
            if ($payment->promocode_id) {
                $promocode = Promocode::query()->find($payment->promocode_id);
                if ($promocode) {
                    $bonus  = ($amount * $promocode->value) / 100;
                    $amount += $bonus;
                    $promocode->decrement('uses_left');
                    PromocodeUse::query()->create([
                        'user_id'      => $payment->user_id,
                        'promocode_id' => $payment->promocode_id,
                        'bonus_amount' => $bonus,
                    ]);
                }
            }

            $event_points = $payment->amount * 0.1;
            $user->increment('balance', $amount);
            $user->increment('event_points', $event_points);
            $user->increment('total_deposited', $payment->amount * 100);

            $payment->status = Payment::PAID;
            $payment->save();

            $hasOtherDeposits = Payment::query()
                ->where('user_id', $user->id)
                ->where('status', Payment::PAID)
                ->where('id', '!=', $payment->id)
                ->exists();

            // === РЕФЕРАЛКА ===
            if ($user->referrer_id !== null) {
                $referrer = User::query()->find($user->referrer_id);
                if ($referrer) {
                    $value = $referrer->custom_referral_percentage ?? match ($referrer->referral_level) {
                        1 => 0.5, 2 => 1, 3 => 1.5, 4 => 2, 5 => 2.5, default => 0,
                    };

                    if (!$hasOtherDeposits && $payment->amount >= 1000) {
                        $fixedBonus   = 2500;
                        $percentBonus = ($payment->amount * 100) * ($value / 100);
                        $totalBonus   = $fixedBonus + $percentBonus;
                        $referrer->update([
                            'balance'           => $referrer->balance + $fixedBonus,
                            'referral_balance'  => $referrer->referral_balance + $percentBonus,
                            'total_earned'      => $referrer->total_earned + $totalBonus,
                        ]);
                        ReferralEarning::query()->create([
                            'user_id'        => $referrer->id,
                            'referral_id'    => $user->id,
                            'amount'         => $totalBonus,
                            'deposit_amount' => $payment->amount * 100,
                            'percentage'     => $value,
                            'type'           => 'nirvana_uzs',
                            'description'    => 'Бонус 25₽ и ' . $value . '% за первый депозит',
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ]);
                    } else {
                        $percentBonus = ($payment->amount * 100) * ($value / 100);
                        $referrer->update([
                            'balance'          => $referrer->balance + $percentBonus,
                            'referral_balance' => $referrer->referral_balance + $percentBonus,
                            'total_earned'     => $referrer->total_earned + $percentBonus,
                        ]);
                        ReferralEarning::query()->create([
                            'user_id'        => $referrer->id,
                            'referral_id'    => $user->id,
                            'amount'         => $percentBonus,
                            'deposit_amount' => $payment->amount * 100,
                            'percentage'     => $value,
                            'type'           => 'nirvana_uzs',
                            'description'    => $value . '% от депозита',
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ]);
                    }

                    $this->redisService->updateUserBalance($referrer->id, $referrer->balance, $referrer->event_points);
                }
            }

            $this->redisService->updateUserBalance($user->id, $user->balance, $user->event_points);
            return response('OK', 200);
        } catch (\Exception $e) {
            Log::channel('payment_nirvana')->error('NirvanaUZS callback exception: ' . $e->getMessage());
            return response('Error', 500);
        }
    }

    private function getDepositLimits($method, $system): array
    {
        $paymentMethod = PaymentMethods::where('method', $method)
            ->where('system', $system)
            ->first();

        if ($paymentMethod) {
            return [
                'min_amount' => $paymentMethod->min_amount,
                'max_amount' => $paymentMethod->max_amount,
            ];
        }

        return [
            'min_amount' => 1000,     // 10 сум
            'max_amount' => 10000000, // 10 000 000 сум
        ];
    }
}
