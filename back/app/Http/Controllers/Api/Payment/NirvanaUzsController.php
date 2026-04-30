<?php

namespace App\Http\Controllers\Api\Payment;

use App\Models\PaymentMethods;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Promocode;
use App\Models\Settings;
use App\Models\User;
use App\Services\RedisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\PromocodeUse;
use App\Models\ReferralEarning;

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
            return ['success' => false, 'message' => "Минимальная сумма депозита: {$limits['min_amount']} сум."];
        }
        if ($amount > $limits['max_amount']) {
            return ['success' => false, 'message' => "Максимальная сумма депозита: {$limits['max_amount']} сум."];
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
            'status'         => Payment::STATUS_PENDING,
            'transaction_id' => $transaction_id,
            'metadata'       => [
                'user_id'  => $user->id,
                'username' => $user->username,
                'time'     => Carbon::now()->format('Y-m-d H:i:s'),
                'ip'       => $request->getClientIp(true),
            ],
        ]);

        $options = [
            'clientID'    => $transaction_id,
            'amount'      => $amount,
            'token'       => $method,
            'currency'    => 'UZS',
            'callbackUrl' => config('app.url') . '/api/payment/nirvana-uzs/callback?clientID=' . $transaction_id,
            'userInfo'    => [
                'ip'    => $request->getClientIp(),
                'ua'    => $request->userAgent(),
                'email' => 'no-email@gmail.com',
                'id'    => (string) $user->id,
            ],
        ];

        try {
            $client   = new Client();
            $response = $client->post('https://api.nirvanapay.pro/create/in', [
                'headers' => [
                    'ApiPublic'  => env('NIRVANA_API_PUBLIC'),
                    'ApiPrivate' => env('NIRVANA_API_PRIVATE'),
                ],
                'json' => $options,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            Log::channel('payment_nirvana')->info('NirvanaUZS response:', $responseData);

            if (($responseData['status'] ?? '') === 'ERROR') {
                Log::channel('payment_nirvana')->error('NirvanaUZS error:', $responseData);
                return ['success' => false, 'message' => $responseData['reason'] ?? 'Ошибка при создании платежа.'];
            }

            // Save trackerID in metadata
            $metadata = $payment->metadata ?? [];
            $metadata['trackerID'] = $responseData['trackerID'] ?? null;
            $payment->metadata = $metadata;
            $payment->save();

            return [
                'success'       => true,
                'message'       => 'Переведите средства по реквизитам ниже',
                'type'          => 'card',
                'receiver'      => $responseData['receiver'] ?? '',
                'bankName'      => $responseData['extra']['bankName'] ?? $method,
                'recipientName' => $responseData['extra']['recipientName'] ?? '',
                'amount'        => $amount,
                'currency'      => 'UZS',
            ];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'no response';
            Log::channel('payment_nirvana')->error('NirvanaUZS client error: ' . $e->getMessage() . ' | Body: ' . $responseBody);
            return ['success' => false, 'message' => 'Ошибка Nirvana: ' . $responseBody];
        } catch (\Exception $e) {
            Log::channel('payment_nirvana')->error('NirvanaUZS exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()];
        }
    }

    public function callback(Request $request)
    {
        $clientID = $request->query('clientID');

        try {
            $client   = new Client();
            $response = $client->post('https://api.nirvanapay.pro/transaction/status', [
                'headers' => [
                    'ApiPublic'  => env('NIRVANA_API_PUBLIC'),
                    'ApiPrivate' => env('NIRVANA_API_PRIVATE'),
                ],
                'json' => ['clientID' => $clientID],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            Log::channel('payment_nirvana')->debug('NirvanaUZS callback:', $body);

            if (($body['status'] ?? '') !== 'SUCCESS') {
                return response('Payment not SUCCESS', 200);
            }

            $payment = Payment::query()->where('transaction_id', $clientID)->first();
            if (!$payment) return response('Payment not found', 200);
            if ($payment->status == Payment::STATUS_APPROVED) return response('Already paid', 200);

            $user = User::query()->find($payment->user_id);
            if (!$user) return response('User not found', 200);

            $receivedAmount = $body['amountFiatReceived'] ?? $payment->amount;
            
            // Conversion rate: 5000 UZS = 32 RUB (1 RUB = 156.25 UZS)
            $conversionRate = 156.25;
            $amountInRubCents = ($receivedAmount * 100) / $conversionRate;
            
            $amount = $amountInRubCents;

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

            $event_points = ($receivedAmount / $conversionRate) * 0.1;
            $user->increment('balance', $amount);
            $user->increment('event_points', $event_points);
            $user->increment('total_deposited', $amountInRubCents);

            $payment->status = Payment::STATUS_APPROVED;
            $payment->save();

            $hasOtherDeposits = Payment::query()
                ->where('user_id', $user->id)
                ->where('status', Payment::STATUS_APPROVED)
                ->where('id', '!=', $payment->id)
                ->exists();

            // === РЕФЕРАЛКА ===
            if ($user->referrer_id !== null) {
                $referrer = User::query()->find($user->referrer_id);
                if ($referrer) {
                    $value = $referrer->custom_referral_percentage ?? match ($referrer->referral_level) {
                        1 => 0.5, 2 => 1, 3 => 1.5, 4 => 2, 5 => 2.5, default => 0,
                    };

                    if (!$hasOtherDeposits && ($receivedAmount / $conversionRate) >= 1000) {
                        $fixedBonus   = 2500;
                        $percentBonus = $amountInRubCents * ($value / 100);
                        $totalBonus   = $fixedBonus + $percentBonus;
                        $referrer->update([
                            'balance'          => $referrer->balance + $fixedBonus,
                            'referral_balance' => $referrer->referral_balance + $percentBonus,
                            'total_earned'     => $referrer->total_earned + $totalBonus,
                        ]);
                        ReferralEarning::query()->create([
                            'user_id'        => $referrer->id,
                            'referral_id'    => $user->id,
                            'amount'         => $totalBonus,
                            'deposit_amount' => $amountInRubCents,
                            'percentage'     => $value,
                            'type'           => 'nirvana_uzs',
                            'description'    => 'Бонус 25₽ и ' . $value . '% за первый депозит',
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ]);
                    } else {
                        $percentBonus = $amountInRubCents * ($value / 100);
                        $referrer->update([
                            'balance'          => $referrer->balance + $percentBonus,
                            'referral_balance' => $referrer->referral_balance + $percentBonus,
                            'total_earned'     => $referrer->total_earned + $percentBonus,
                        ]);
                        ReferralEarning::query()->create([
                            'user_id'        => $referrer->id,
                            'referral_id'    => $user->id,
                            'amount'         => $percentBonus,
                            'deposit_amount' => $amountInRubCents,
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
            'min_amount' => 5000,
            'max_amount' => 50000000,
        ];
    }
}
