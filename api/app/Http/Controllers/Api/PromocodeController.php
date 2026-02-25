<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Promocode;
use App\Models\PromocodeUse;
use App\Models\Items;
use App\Models\Lives;
use App\Models\Boxes;
use App\Models\UserFreeCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromocodeController extends Controller
{
  // Активация промокодов balance_bonus, free_skin, free_case
  public function activate(Request $request)
  {
    $request->validate([
      'code' => 'required|string|max:50'
    ]);

    $user = $request->user();
    $code = strtoupper($request->code);

    DB::beginTransaction();
    try {
      $promocode = Promocode::where('code', $code)->first();

      if (!$promocode) {
        return response()->json([
          'success' => false,
          'message' => 'Промокод не найден'
        ], 404);
      }

      // Проверяем что промокод НЕ deposit_percent типа
      if ($promocode->type === 'deposit_percent') {
        return response()->json([
          'success' => false,
          'message' => 'Этот промокод применяется при пополнении баланса'
        ], 400);
      }

      if (!$promocode->canBeUsedByUser($user->id)) {
        return response()->json([
          'success' => false,
          'message' => 'Промокод уже использован или истек'
        ], 400);
      }

      // Применяем бонус в зависимости от типа промокода
      $result = $this->applyBonus($promocode, $user);

      if (!$result['success']) {
        DB::rollBack();
        return response()->json([
          'success' => false,
          'message' => $result['message']
        ], 400);
      }

      // Записываем использование промокода
      PromocodeUse::create([
        'promocode_id' => $promocode->id,
        'user_id' => $user->id,
        'bonus_amount' => $result['bonus_amount'] ?? null,
        'metadata' => $result['metadata'] ?? null
      ]);

      // Уменьшаем количество использований
      $promocode->decrement('uses_left');

      DB::commit();

      return response()->json([
        'success' => true,
        'message' => $result['message'],
        'bonus' => $result['bonus'] ?? null,
        'type' => $promocode->type
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      \Log::channel('api_promocode')->error('Promocode activation error: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Произошла ошибка при активации промокода'
      ], 500);
    }
  }

  // Применение промокода deposit_percent при пополнении баланса
  public function applyDepositPromocode(Request $request)
  {
    $request->validate([
      'code' => 'required|string|max:50',
      'deposit_amount' => 'required|numeric|min:0'
    ]);

    $user = $request->user();
    $code = strtoupper($request->code);
    $depositAmount = $request->deposit_amount;

    DB::beginTransaction();
    try {
      $promocode = Promocode::where('code', $code)->first();

      if (!$promocode) {
        return response()->json([
          'success' => false,
          'message' => 'Промокод не найден'
        ], 404);
      }

      // Проверяем что промокод именно deposit_percent типа
      if ($promocode->type !== 'deposit_percent') {
        return response()->json([
          'success' => false,
          'message' => 'Этот промокод не применяется при пополнении баланса'
        ], 400);
      }

      if (!$promocode->canBeUsedByUser($user->id)) {
        return response()->json([
          'success' => false,
          'message' => 'Промокод уже использован или истек'
        ], 400);
      }

      // Рассчитываем бонус
      $bonusAmount = ($depositAmount * $promocode->value) / 100;
      $totalAmount = $depositAmount + $bonusAmount;

      // Записываем использование промокода
      PromocodeUse::create([
        'promocode_id' => $promocode->id,
        'user_id' => $user->id,
        'bonus_amount' => $bonusAmount,
        'metadata' => [
          'deposit_amount' => $depositAmount,
          'bonus_percent' => $promocode->value,
          'total_amount' => $totalAmount
        ]
      ]);

      // Уменьшаем количество использований
      $promocode->decrement('uses_left');

      DB::commit();

      return response()->json([
        'success' => true,
        'message' => "Промокод активирован! Бонус: {$promocode->value}%",
        'bonus' => [
          'percent' => $promocode->value,
          'bonus_amount' => $bonusAmount,
          'deposit_amount' => $depositAmount,
          'total_amount' => $totalAmount
        ],
        'type' => $promocode->type
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      \Log::channel('api_promocode')->error('Deposit promocode application error: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Произошла ошибка при применении промокода'
      ], 500);
    }
  }

  private function applyBonus(Promocode $promocode, $user)
  {
    switch ($promocode->type) {
      case 'balance_bonus':
        return $this->applyBalanceBonus($promocode, $user);

      case 'free_skin':
        return $this->applyFreeSkin($promocode, $user);

      case 'free_case':
        return $this->applyFreeCase($promocode, $user);

      default:
        return ['success' => false, 'message' => 'Неизвестный тип промокода'];
    }
  }

  private function applyBalanceBonus($promocode, $user)
  {
    $bonusAmount = $promocode->value;

    $user->update([
      'balance' => $user->balance + $bonusAmount
    ]);

    $bonusAmount = $bonusAmount / 100;

    return [
      'success' => true,
      'message' => "Бонус {$bonusAmount} руб. начислен на баланс!",
    ];
  }

  private function applyFreeSkin($promocode, $user)
  {
    $skin = Items::find($promocode->skin_id);

    if (!$skin) {
      return ['success' => false, 'message' => 'Скин не найден'];
    }

    Lives::create([
      'user_id' => $user->id,
      'skin_id' => $skin->id,
      'box_id' => null,
      'from_where' => "BONUS",
      'price' => $skin->steam_price,
      'status' => "STOCK",
    ]);

    return [
      'success' => true,
      'message' => "Скин '{$skin->title}' добавлен в ваш инвентарь!",
      'bonus' => ['skin' => $skin],
      'metadata' => ['skin_id' => $skin->id, 'type' => 'free_skin']
    ];
  }

  private function applyFreeCase($promocode, $user)
  {
    $case = Boxes::find($promocode->case_id);

    if (!$case) {
      return ['success' => false, 'message' => 'Кейс не найден'];
    }

    $freeCase = UserFreeCase::create([
      'user_id' => $user->id,
      'case_id' => $case->id,
      'promocode_id' => $promocode->id,
      'is_used' => false
    ]);

    return [
      'success' => true,
      'message' => "Бесплатное открытие кейса '{$case->name}' активировано!",
      'bonus' => ['case' => $case],
      'metadata' => ['case_id' => $case->id, 'type' => 'free_case']
    ];
  }

  public function check(Request $request)
  {
    $request->validate([
      'code' => 'required|string|max:50'
    ]);

    $user = $request->user();
    $code = strtoupper($request->code);

    $promocode = Promocode::where('code', $code)->first();

    if (!$promocode) {
      return response()->json([
        'exists' => false,
        'message' => 'Промокод не найден'
      ]);
    }

    $canUse = $promocode->canBeUsedByUser($user->id);

    return response()->json([
      'exists' => true,
      'can_use' => $canUse,
      'promocode' => [
        'code' => $promocode->code,
        'type' => $promocode->type,
        'value' => $promocode->value,
        'valid_until' => $promocode->valid_until
      ],
      'message' => $canUse ? 'Промокод доступен для использования' : 'Промокод недоступен'
    ]);
  }

  // Получение текущего ежедневного бонусного промокода
  public function getDailyBonus()
  {
    $promocode = Promocode::where('type', 'deposit_percent')
      ->where('code', 'like', 'DAILY-%')
      ->where('is_active', true)
      ->where('valid_from', '<=', now())
      ->where('valid_until', '>=', now())
      ->orderBy('created_at', 'DESC')
      ->first();

    if (!$promocode) {
      return response()->json([
        'success' => false,
        'message' => 'Ежедневный бонусный промокод не найден'
      ]);
    }

    return response()->json([
      'success' => true,
      'promocode' => [
        'code' => $promocode->code,
        'percent' => $promocode->value,
        'valid_until' => $promocode->valid_until,
      ]
    ]);
  }
}
