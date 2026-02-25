<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Services\LiveService;
use App\Services\RedisService;
use App\Services\RTPService;
use App\Models\Lives;
use App\Models\Items;


class ContractsController extends Controller
{

  protected LiveService $liveService;
  protected RedisService $redisService;
  protected RTPService $rtpService;
  
  // Целевой RTP для контрактов
  private float $targetContractRTP = 90.0;
  
  public function __construct(
    LiveService $liveService,
    RedisService $redisService,
    RTPService $rtpService
  ) {
    $this->liveService = $liveService;
    $this->redisService = $redisService;
    $this->rtpService = $rtpService;
  }
  public function getItems(Request $request): array
  {
    $user = $request->user();

    if (!$user) {
      return ['success' => false, 'message' => 'Авторизируйтесь'];
    }

    $priceFrom = $request->input('price_from'); // минимальная цена
    $priceTo = $request->input('price_to');     // максимальная цена
    $search = $request->input('search');       // поиск по названию

    $liveQuery = Lives::query()
      ->select(['id', 'user_id', 'skin_id', 'status', 'price'])
      ->with(['item:id,weapon,skin_name,quality,image,rarity'])
      ->where('user_id', $user->id)
      ->where('status', Lives::OPENED);

    if ($priceFrom !== null) {
      $liveQuery->where('price', '>=', floatval($priceFrom));
    }
    if ($priceTo !== null) {
      $liveQuery->where('price', '<=', floatval($priceTo));
    }

    if ($search) {
      $liveQuery->whereHas('item', function ($query) use ($search) {
        $query->where('skin_name', 'like', '%' . $search . '%');
      });
    }

    $live = $liveQuery->orderByDesc('id')->paginate(12);

    return [
      'success' => true,
      'items' => $live,
      'hasMorePages' => $live->hasMorePages(),
    ];
  }

  public function create(Request $request)
  {
    $liveSkins = (array) $request->liveIds;
    $type = $request->type;
    Log::channel('api_contracts')->info($type);

    if (count($liveSkins) < 3) {
      return ['success' => false, 'message' => 'Необходимо минимум 3 предмета для контракта'];
    }

    $user = $request->user();

    if (!$user) {
      return ['success' => false, 'message' => 'Авторизируйтесь'];
    }

    DB::beginTransaction();


    $lives = Lives::query()->where('user_id', $user->id)
      ->whereIn('id', $liveSkins)
      ->where('status', Lives::OPENED)
      ->get();

    if ($lives->count() !== count($liveSkins)) {
      return ['success' => false, 'message' => 'Предметы устарели, обновите страницу'];
    }

    $totalPrice = $lives->sum('price');

    // Базовые множители для типов контрактов
    if ($type === 'low') {
      $minMultiplier = 1/3;
      $maxMultiplier = 3;
    } else if ($type === 'medium') {
      $minMultiplier = 1/5;
      $maxMultiplier = 5;
    } else if ($type === 'high') {
      $minMultiplier = 1/10;
      $maxMultiplier = 10;
    } else {
      $minMultiplier = 1/3;
      $maxMultiplier = 3;
    }

    // Получаем статистику контрактов для RTP коррекции
    $contractStats = DB::table('lives')
      ->where('from_where', 'CONTRACTS')
      ->selectRaw('COUNT(*) as total_contracts, AVG(price) as avg_won')
      ->first();

    // Рассчитываем средний потраченный (примерно)
    $contractsUsedItems = DB::table('lives')
      ->where('from_where', 'CONTRACTS')
      ->where('status', Lives::SELL)
      ->selectRaw('AVG(price) as avg_spent')
      ->first();

    $currentContractRTP = 0;
    if ($contractsUsedItems && $contractsUsedItems->avg_spent > 0 && $contractStats->avg_won > 0) {
      // Примерная оценка RTP (средняя стоимость выигрыша / средняя стоимость вложенных предметов)
      $currentContractRTP = ($contractStats->avg_won / ($contractsUsedItems->avg_spent * 3)) * 100;
    }

    // Применяем RTP коррекцию к диапазону
    $rtpDifference = $this->targetContractRTP - $currentContractRTP;
    $rtpModifier = 1 + ($rtpDifference / 100);
    $rtpModifier = max(0.85, min(1.15, $rtpModifier)); // Ограничиваем ±15%

    // Корректируем диапазон: если RTP низкий - сдвигаем диапазон вверх
    $min = ($totalPrice * $minMultiplier) * $rtpModifier;
    $max = ($totalPrice * $maxMultiplier) * $rtpModifier;

    $winItem = Items::query()->whereBetween('steam_price', [$min, $max])
      ->inRandomOrder()
      ->firstOr(function () {
        return Items::query()->orderBy('steam_price', 'asc')->first();
      });

    $lives->each(function ($live) {
      $live->update(['status' => Lives::SELL]);
    });

    $newLive = Lives::create([
      'user_id' => $user->id,
      'skin_id' => $winItem->id,
      'from_where' => 'CONTRACTS',
      'price' => $winItem->steam_price,
      'status' => Lives::OPENED,
    ]);

    $liveIds[] = $newLive->id;

    $this->liveService->addToLive($liveIds, 'CONTRACTS');

    DB::commit();

    return [
      'success' => true,
      'winItem' => [
        'id' => $newLive->id,
        'weapon' => $winItem->weapon,
        'skin_name' => $winItem->skin_name,
        'quality' => $winItem->quality,
        'rarity' => $winItem->rarity,
        'steam_price' => $winItem->steam_price,
        'image' => $winItem->image
      ]
    ];
  }
}
