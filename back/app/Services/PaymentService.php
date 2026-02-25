<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Promocode;
use App\Models\PromocodeLog;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentService
{
    protected RedisService $redisService;
    public function __construct(
        RedisService $redisService
    ) {
        $this->redisService = $redisService;
    }

    public function createHandler(Request $request, $paymentMethod, $paymentSystem)
    {
        $settings = Setting::query()->first();

        if (!$paymentMethod) return ['success' => false, 'message' => 'Выберите способ оплаты'];

        $amount = intval($request->amount);
        $promo = $request->promocode;

        if ($amount > $settings->max_payment_amount) return ['success' => false, 'message' => 'Максимальная сумма пополнения - ' . $settings->max_payment_amount];
        if ($amount < $settings->min_payment_amount) return ['success' => false, 'message' => 'Минимальная сумма пополнения - ' . $settings->min_payment_amount];

        $promocode = null;
        if (!is_null($promo)) {
            $promocode = Promocode::query()
                ->where('name', $promo)
                ->where('status', true)
                ->where('count', '>', 0)
                ->first();
            if (is_null($promocode)) return ['success' => false, 'message' => 'Промокод не найден'];
            $log = PromocodeLog::query()->where('promocode_id', $promocode->id)->where('user_id', $request->user()->id)->first();
            if ($log) return ['success' => false, 'message' => 'Вы уже использовали промокод'];
        }

        $payment = Payment::query()->create([
            'user_id' => $request->user()->id,
            'promocode_id' => $promocode ? $promocode->id : null,
            'system' => $paymentSystem,
            'method' => $paymentMethod,
            'amount' => $amount,
            'metadata' => [
                'ip' => $request->getClientIp(true),
            ],
        ]);

        return ['success' => true, 'settings' => $settings, 'payment' => $payment];
    }

    public function handlePayment(Payment $payment, $amount, User $user): void
    {
        if ($payment->promocode_id) {
            $promocode = Promocode::query()->find($payment->promocode_id);

            if ($promocode) {
                $bonus = ($amount * $promocode->percent) / 100;
                $amount += $bonus;
                $promocode->decrement('count');

                PromocodeLog::query()->create([
                    'user_id' => $payment->user_id,
                    'promocode_id' => $payment->promocode_id,
                ]);
            } else {
                $payment->promocode_id = null;
                $payment->save();
            }
        }

        $user->increment('balance', $amount);

        $payment->status = PaymentStatus::PAID;
        $payment->amount = $amount;
        $payment->save();

        $this->redisService->updateUserBalance($user->id, $user->balance, $user->event_points);

        die('OK');
    }
}
