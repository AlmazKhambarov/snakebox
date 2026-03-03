<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Promocode;
use App\Models\PromocodeUse;
use App\Models\ReferralEarning;
use App\Models\User;
use App\Models\PaymentMethods;
use App\Models\Settings;
use App\Services\RedisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymeController extends Controller
{
    // Payme transaction states
    const STATE_CREATED            = 1;
    const STATE_COMPLETED          = 2;
    const STATE_CANCELLED          = -1;
    const STATE_CANCELLED_AFTER    = -2;

    // Payme error codes
    const ERROR_INTERNAL           = -32400;
    const ERROR_INVALID_AMOUNT     = -31001;
    const ERROR_ORDER_NOT_FOUND    = -31050;
    const ERROR_CANT_PERFORM       = -31008;
    const ERROR_CANT_CANCEL        = -31007;
    const ERROR_TRANSACTION_NOT_FOUND = -31003;
    const ERROR_AUTH               = -32504;

    protected RedisService $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
    }

    /**
     * Create payment and redirect user to Payme checkout
     */
    public function create(Request $request)
    {
        $user     = $request->user();
        $settings = Settings::query()->first();
        $method   = $request->payment_method;
        $system   = $request->system;

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

        $transaction_id = 'PM_' . time() . '_' . $user->id;

        $payment = Payment::query()->create([
            'user_id'        => $user->id,
            'promocode_id'   => $promocode ? $promocode->id : null,
            'system'         => 'payme',
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

        // Payme amount is in tiyin (1 sum = 100 tiyin)
        $amountInTiyin = $amount * 100;

        // Generate Payme checkout URL
        $merchantId = env('PAYME_MERCHANT_ID');
        $params = "m={$merchantId};ac.order_id={$payment->id};a={$amountInTiyin};c=" . urlencode(config('app.frontend_url'));

        $encodedParams = base64_encode($params);

        $baseUrl = env('PAYME_TEST_MODE', true) ? 'https://test.paycom.uz' : 'https://checkout.paycom.uz';
        $paymeUrl = $baseUrl . '/' . $encodedParams;

        return [
            'success' => true,
            'message' => 'Переносим на страницу оплаты...',
            'link'    => $paymeUrl,
        ];
    }

    /**
     * Handle JSON-RPC callbacks from Payme
     */
    public function callback(Request $request)
    {
        // Authenticate request
        $auth = $request->header('Authorization');
        if (!$this->authenticate($auth)) {
            return $this->errorResponse(self::ERROR_AUTH, 'Unauthorized', null);
        }

        $method = $request->input('method');
        $params = $request->input('params');
        $id     = $request->input('id');

        Log::channel('payment_payme')->info("Payme callback: {$method}", $params ?? []);

        switch ($method) {
            case 'CheckPerformTransaction':
                return $this->checkPerformTransaction($params, $id);
            case 'CreateTransaction':
                return $this->createTransaction($params, $id);
            case 'PerformTransaction':
                return $this->performTransaction($params, $id);
            case 'CancelTransaction':
                return $this->cancelTransaction($params, $id);
            case 'CheckTransaction':
                return $this->checkTransaction($params, $id);
            case 'GetStatement':
                return $this->getStatement($params, $id);
            default:
                return $this->errorResponse(-32601, 'Method not found', $id);
        }
    }

    private function authenticate($auth): bool
    {
        if (!$auth || !str_starts_with($auth, 'Basic ')) {
            return false;
        }

        $decoded = base64_decode(substr($auth, 6));
        $parts = explode(':', $decoded, 2);

        if (count($parts) !== 2) return false;

        $login = $parts[0];
        $key   = $parts[1];

        return $login === 'Paycom' && $key === env('PAYME_MERCHANT_KEY');
    }

    private function checkPerformTransaction($params, $id)
    {
        $orderId = $params['account']['order_id'] ?? null;
        $amount  = $params['amount'] ?? 0;

        $payment = Payment::query()->find($orderId);

        if (!$payment) {
            return $this->errorResponse(self::ERROR_ORDER_NOT_FOUND, 'Order not found', $id);
        }

        if ($payment->status === Payment::PAID) {
            return $this->errorResponse(self::ERROR_CANT_PERFORM, 'Order already paid', $id);
        }

        // Amount is in tiyin, our amount is in sum
        if ($payment->amount * 100 != $amount) {
            return $this->errorResponse(self::ERROR_INVALID_AMOUNT, 'Invalid amount', $id);
        }

        return response()->json([
            'id'     => $id,
            'result' => ['allow' => true],
            'error'  => null,
        ]);
    }

    private function createTransaction($params, $id)
    {
        $paymeId = $params['id'];
        $time    = $params['time'];
        $amount  = $params['amount'];
        $orderId = $params['account']['order_id'] ?? null;

        $payment = Payment::query()->find($orderId);

        if (!$payment) {
            return $this->errorResponse(self::ERROR_ORDER_NOT_FOUND, 'Order not found', $id);
        }

        if ($payment->amount * 100 != $amount) {
            return $this->errorResponse(self::ERROR_INVALID_AMOUNT, 'Invalid amount', $id);
        }

        // Check if transaction already exists
        $existingPayme = $payment->metadata['payme_id'] ?? null;

        if ($existingPayme) {
            if ($existingPayme !== $paymeId) {
                return $this->errorResponse(self::ERROR_CANT_PERFORM, 'Transaction already exists with different id', $id);
            }

            // Return existing transaction
            return response()->json([
                'id'     => $id,
                'result' => [
                    'create_time'  => $payment->metadata['payme_create_time'] ?? $time,
                    'transaction'  => (string) $payment->id,
                    'state'        => $this->getPaymeState($payment),
                ],
                'error' => null,
            ]);
        }

        if ($payment->status === Payment::PAID) {
            return $this->errorResponse(self::ERROR_CANT_PERFORM, 'Order already paid', $id);
        }

        // Save payme transaction data
        $metadata = $payment->metadata ?? [];
        $metadata['payme_id'] = $paymeId;
        $metadata['payme_create_time'] = $time;
        $payment->metadata = $metadata;
        $payment->save();

        return response()->json([
            'id'     => $id,
            'result' => [
                'create_time' => $time,
                'transaction' => (string) $payment->id,
                'state'       => self::STATE_CREATED,
            ],
            'error' => null,
        ]);
    }

    private function performTransaction($params, $id)
    {
        $paymeId = $params['id'];

        $payment = Payment::query()
            ->whereJsonContains('metadata->payme_id', $paymeId)
            ->first();

        if (!$payment) {
            return $this->errorResponse(self::ERROR_TRANSACTION_NOT_FOUND, 'Transaction not found', $id);
        }

        if ($payment->status === Payment::PAID) {
            return response()->json([
                'id'     => $id,
                'result' => [
                    'transaction'  => (string) $payment->id,
                    'perform_time' => $payment->metadata['payme_perform_time'] ?? now()->timestamp * 1000,
                    'state'        => self::STATE_COMPLETED,
                ],
                'error' => null,
            ]);
        }

        if ($payment->status !== Payment::PENDING) {
            return $this->errorResponse(self::ERROR_CANT_PERFORM, 'Cannot perform transaction', $id);
        }

        // Credit user balance
        $user = User::query()->find($payment->user_id);
        if (!$user) {
            return $this->errorResponse(self::ERROR_INTERNAL, 'User not found', $id);
        }

        $amount = $payment->amount * 100; // Convert to internal currency (kopecks/tiyin)

        // Promo code handling
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

        $performTime = now()->timestamp * 1000;
        $metadata = $payment->metadata ?? [];
        $metadata['payme_perform_time'] = $performTime;
        $payment->metadata = $metadata;
        $payment->status = Payment::PAID;
        $payment->save();

        // Referral logic
        $hasOtherDeposits = Payment::query()
            ->where('user_id', $user->id)
            ->where('status', Payment::PAID)
            ->where('id', '!=', $payment->id)
            ->exists();

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
                        'balance'          => $referrer->balance + $fixedBonus,
                        'referral_balance' => $referrer->referral_balance + $percentBonus,
                        'total_earned'     => $referrer->total_earned + $totalBonus,
                    ]);
                    ReferralEarning::query()->create([
                        'user_id'        => $referrer->id,
                        'referral_id'    => $user->id,
                        'amount'         => $totalBonus,
                        'deposit_amount' => $payment->amount * 100,
                        'percentage'     => $value,
                        'type'           => 'payme',
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
                        'type'           => 'payme',
                        'description'    => $value . '% от депозита',
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }

                $this->redisService->updateUserBalance($referrer->id, $referrer->balance, $referrer->event_points);
            }
        }

        $this->redisService->updateUserBalance($user->id, $user->balance, $user->event_points);

        return response()->json([
            'id'     => $id,
            'result' => [
                'transaction'  => (string) $payment->id,
                'perform_time' => $performTime,
                'state'        => self::STATE_COMPLETED,
            ],
            'error' => null,
        ]);
    }

    private function cancelTransaction($params, $id)
    {
        $paymeId = $params['id'];
        $reason  = $params['reason'] ?? null;

        $payment = Payment::query()
            ->whereJsonContains('metadata->payme_id', $paymeId)
            ->first();

        if (!$payment) {
            return $this->errorResponse(self::ERROR_TRANSACTION_NOT_FOUND, 'Transaction not found', $id);
        }

        if ($payment->status === Payment::PAID) {
            // Already completed - cancel after perform
            $cancelTime = now()->timestamp * 1000;
            $metadata = $payment->metadata ?? [];
            $metadata['payme_cancel_time'] = $cancelTime;
            $metadata['payme_cancel_reason'] = $reason;
            $payment->metadata = $metadata;
            $payment->status = 'cancelled';
            $payment->save();

            return response()->json([
                'id'     => $id,
                'result' => [
                    'transaction'  => (string) $payment->id,
                    'cancel_time'  => $cancelTime,
                    'state'        => self::STATE_CANCELLED_AFTER,
                ],
                'error' => null,
            ]);
        }

        $cancelTime = now()->timestamp * 1000;
        $metadata = $payment->metadata ?? [];
        $metadata['payme_cancel_time'] = $cancelTime;
        $metadata['payme_cancel_reason'] = $reason;
        $payment->metadata = $metadata;
        $payment->status = 'cancelled';
        $payment->save();

        return response()->json([
            'id'     => $id,
            'result' => [
                'transaction' => (string) $payment->id,
                'cancel_time' => $cancelTime,
                'state'       => self::STATE_CANCELLED,
            ],
            'error' => null,
        ]);
    }

    private function checkTransaction($params, $id)
    {
        $paymeId = $params['id'];

        $payment = Payment::query()
            ->whereJsonContains('metadata->payme_id', $paymeId)
            ->first();

        if (!$payment) {
            return $this->errorResponse(self::ERROR_TRANSACTION_NOT_FOUND, 'Transaction not found', $id);
        }

        return response()->json([
            'id'     => $id,
            'result' => [
                'create_time'  => $payment->metadata['payme_create_time'] ?? 0,
                'perform_time' => $payment->metadata['payme_perform_time'] ?? 0,
                'cancel_time'  => $payment->metadata['payme_cancel_time'] ?? 0,
                'transaction'  => (string) $payment->id,
                'state'        => $this->getPaymeState($payment),
                'reason'       => $payment->metadata['payme_cancel_reason'] ?? null,
            ],
            'error' => null,
        ]);
    }

    private function getStatement($params, $id)
    {
        $from = $params['from'] ?? 0;
        $to   = $params['to'] ?? now()->timestamp * 1000;

        $payments = Payment::query()
            ->where('system', 'payme')
            ->where('created_at', '>=', Carbon::createFromTimestampMs($from))
            ->where('created_at', '<=', Carbon::createFromTimestampMs($to))
            ->get();

        $transactions = [];
        foreach ($payments as $payment) {
            if (!isset($payment->metadata['payme_id'])) continue;

            $transactions[] = [
                'id'           => $payment->metadata['payme_id'],
                'time'         => $payment->metadata['payme_create_time'] ?? 0,
                'amount'       => $payment->amount * 100,
                'account'      => ['order_id' => $payment->id],
                'create_time'  => $payment->metadata['payme_create_time'] ?? 0,
                'perform_time' => $payment->metadata['payme_perform_time'] ?? 0,
                'cancel_time'  => $payment->metadata['payme_cancel_time'] ?? 0,
                'transaction'  => (string) $payment->id,
                'state'        => $this->getPaymeState($payment),
                'reason'       => $payment->metadata['payme_cancel_reason'] ?? null,
            ];
        }

        return response()->json([
            'id'     => $id,
            'result' => ['transactions' => $transactions],
            'error'  => null,
        ]);
    }

    private function getPaymeState(Payment $payment): int
    {
        return match ($payment->status) {
            Payment::PENDING   => self::STATE_CREATED,
            Payment::PAID      => self::STATE_COMPLETED,
            'cancelled'        => isset($payment->metadata['payme_perform_time'])
                                    ? self::STATE_CANCELLED_AFTER
                                    : self::STATE_CANCELLED,
            default            => self::STATE_CREATED,
        };
    }

    private function errorResponse($code, $message, $id)
    {
        return response()->json([
            'id'     => $id,
            'result' => null,
            'error'  => [
                'code'    => $code,
                'message' => ['ru' => $message, 'uz' => $message, 'en' => $message],
            ],
        ]);
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
            'min_amount' => 1000,
            'max_amount' => 5000000,
        ];
    }
}
