<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Promocode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PaymentMethods;

class PaymentController extends Controller
{
    public function getMethods(Request $request)
    {
        $methods = PaymentMethods::query()
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        return ['success' => true, 'methods' => $methods];
    }
    public function checkPromocode(Request $request)
    {
        $code = strval($request->code);

        $promocode = Promocode::query()
            ->where('code', $code)
            ->where('type', 'deposit_percent')
            ->where('is_active', true)
            ->where('uses_left', '>', 0)
            ->where('valid_from', '<', Carbon::now())
            ->where('valid_until', '>', Carbon::now())
            ->first();

        if (!$promocode) return ['success' => false, 'message' => 'Промокод не найден. Истек срок действия, или неактивен!'];

        return ['success' => true, 'message' => 'Промокод применен', 'percent' => $promocode->value];
    }
    public function success(Request $request)
    {
        $transaction_id = $request->transaction_id;

        $transaction = Payment::query()
            ->where('transaction_id', $transaction_id)
            ->select('status')
            ->first();

            return ['success' => true, 'payment' => $transaction];
    }

}
