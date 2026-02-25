<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promocode;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PromocodesController extends Controller
{
    public function get(Request $request)
    {
        return datatables(Promocode::query())->toJson();
    }

    public function create(Request $request)
    {
        $code = $request->code;
        $type = $request->type;
        $value = $request->value;
        $skin_id = $request->skin_id;
        $case_id = $request->case_id;
        $uses_left = $request->uses_left;
        $max_uses = $request->max_uses;
        $valid_from = $request->valid_from;
        $valid_until = $request->valid_until;
        $is_active = $request->is_active;

        if ($type == 'balance_bonus') {
            $value = $value * 100;
        }

        Promocode::query()->create([
            'code' => $code,
            'type' => $type,
            'value' => $value,
            'skin_id' => $skin_id,
            'case_id' => $case_id,
            'uses_left' => $uses_left,
            'max_uses' => $max_uses,
            'valid_from' => $valid_from,
            'valid_until' => $valid_until,
            'is_active' => $is_active
        ]);

        return ['success' => true, 'message' => 'Промокод создан'];
    }

    public function delete(Request $request)
    {
        $promo = Promocode::query()->find($request->id);
        if (!$promo) return ['success' => false, 'message' => 'Промокод не найден'];

        $promo->delete();

        return ['success' => true, 'message' => 'Промокод удален'];
    }
}
