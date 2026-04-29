<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UCController extends Controller
{
    /**
     * User purchases UC using balance.
     */
    public function buy(Request $request): array
    {
        $user = $request->user();
        $amount = (int) $request->input('amount'); // amount of UC to buy
        $pubgUid = $request->input('pubg_uid');

        if (!$pubgUid || strlen($pubgUid) < 6) {
            return ['success' => false, 'message' => 'Введите корректный PUBG UID'];
        }
        if ($amount < 60) {
            return ['success' => false, 'message' => 'Минимальная сумма: 60 UC'];
        }
        $pricePerUC = (int) config('app.uc_price', 160); // 60 UC = 9600 (96 coins) = 15000 сум
        $totalPrice = $amount * $pricePerUC;
        if ($user->balance < $totalPrice) {
            return ['success' => false, 'message' => 'Недостаточно средств'];
        }
        DB::transaction(function () use ($user, $amount, $totalPrice, $pubgUid) {
            // deduct balance
            $user->decrement('balance', $totalPrice);
            // create pending payment
            Payment::create([
                'user_id'  => $user->id,
                'system'   => 'uc',
                'method'   => 'balance',
                'type'     => Payment::TYPE_UC,
                'amount'   => $amount,
                'price'    => $totalPrice,
                'status'   => Payment::STATUS_PENDING,
                'pubg_uid' => $pubgUid,
            ]);
        });
        return ['success' => true, 'message' => 'Запрос отправлен, ожидайте подтверждения'];
    }

    /** List pending UC payments for admin */
    public function listPending(Request $request): array
    {
        $payments = Payment::where('type', Payment::TYPE_UC)
            ->where('status', Payment::STATUS_PENDING)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(20);
        return ['success' => true, 'payments' => $payments];
    }

    /** Confirm a payment */
    public function confirm(Request $request): array
    {
        $payment = Payment::find($request->id);
        if (!$payment || $payment->status !== Payment::STATUS_PENDING) {
            return ['success' => false, 'message' => 'Платёж не найден'];
        }
        DB::transaction(function () use ($payment) {
            $payment->update(['status' => Payment::STATUS_APPROVED]);
            $payment->user->increment('uc_balance', $payment->amount);
        });
        return ['success' => true, 'message' => 'UC начислены пользователю'];
    }

    /** Decline a payment and refund balance */
    public function decline(Request $request): array
    {
        $payment = Payment::find($request->id);
        if (!$payment) {
            return ['success' => false, 'message' => 'Платёж не найден'];
        }
        DB::transaction(function () use ($payment) {
            $payment->update(['status' => Payment::STATUS_DECLINED]);
            $payment->user->increment('balance', $payment->price);
        });
        return ['success' => true, 'message' => 'Платёж отклонён, средства возвращены'];
    }
}
?>
