<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentMethods;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentsController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query()->with(['user', 'promo']);
        
        if ($request->has('system')) {
            $query->where('system', $request->system);
        } else {
            // Если система не указана, исключаем UC из общего списка (по желанию пользователя, т.к. он хочет отдельную страницу)
            $query->where('system', '!=', 'uc');
        }

        return datatables($query)->toJson();
    }
    public function delete(Request $request): array
    {
        $payment = Payment::query()->find(intval($request->id));

        if (!$payment) return ['success' => false, 'message' => 'Платеж не найден'];

        $payment->delete();

        return ['success' => true, 'message' => 'Платеж удален'];
    }

    public function getMethods(Request $request)
    {
        return datatables(PaymentMethods::query())->toJson();
    }

    public function deleteMethods(Request $request): array
    {
        $method = PaymentMethods::query()->find(intval($request->id));

        if (!$method) return ['success' => false, 'message' => 'Метод не найден'];

        $method->delete();

        return ['success' => true, 'message' => 'Метод удален'];
    }

    public function methods(Request $request): array
    {
        $id = $request->id;

        $method = PaymentMethods::query()->find($id);
        if (!$method) return ['success' => false, 'message' => 'Кейс не найден'];

        return [
            'success' => true,
            'method' => $method
        ];
    }

    public function save(Request $request): array
    {
        $id = $request->id;

        $method = PaymentMethods::query()->find($id);
        if (!$method) return ['success' => false, 'message' => 'Метод не найден'];

        Log::channel('admin_payments')->debug($request);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'system' => 'required',
            'method' => 'required',
            'is_active' => 'required',
            'min_amount' => 'required',
            'max_amount' => 'required',
            'api_url' => 'required',
            'icon' => 'required',
            'sort_order' => 'required',
        ], [
            'name.required' => 'Вы не указали название',
            'system.required' => 'Вы не указали систему',
            'method.required' => 'Вы не указали метод',
            'is_active.required' => 'Вы не указали активность',
            'min_amount.required' => 'Вы не указали минимальную сумму',
            'max_amount.required' => 'Вы не указали максимальную сумму',
            'api_url.required' => 'Вы не указали роут',
            'icon.required' => 'Вы не указали иконку',
            'sort_order.required' => 'Вы не указали позицию',

        ]);


        if ($validator->fails()) {
            Log::channel('admin_payments')->debug('Ошибка валидатора');
            return ['success' => false, 'message' => $validator->errors()->first()];
        }

        $method->update([
            'name' => $request->name,
            'system' => $request->system,
            'method' => $request->method,
            'is_active' => $request->is_active,
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
            'api_url' => $request->api_url,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order
        ]);

        return ['success' => true, 'message' => 'Метод успешно обновлен!'];
    }

    public function create(Request $request): array
    {
        Log::channel('admin_payments')->debug($request);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'system' => 'required',
            'method' => 'required',
            'is_active' => 'required',
            'min_amount' => 'required',
            'max_amount' => 'required',
            'api_url' => 'required',
            'icon' => 'required',
            'sort_order' => 'required',
        ], [
            'name.required' => 'Вы не указали название',
            'system.required' => 'Вы не указали систему',
            'method.required' => 'Вы не указали метод',
            'is_active.required' => 'Вы не указали активность',
            'min_amount.required' => 'Вы не указали минимальную сумму',
            'max_amount.required' => 'Вы не указали максимальную сумму',
            'api_url.required' => 'Вы не указали роут',
            'icon.required' => 'Вы не указали иконку',
            'sort_order.required' => 'Вы не указали позицию',

        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => $validator->errors()->first()];
        }

        PaymentMethods::query()->create([
            'name' => $request->name,
            'system' => $request->system,
            'method' => $request->method,
            'is_active' => $request->is_active,
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
            'api_url' => $request->api_url,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order
        ]);

        return ['success' => true, 'message' => 'Метод успешно добавлен!'];
    }
}
