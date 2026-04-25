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

use App\Models\ProvablyFairSeed;
use App\Models\Boxes;
use App\Models\CaseItems;
use App\Models\Lives;
use App\Models\UserFreeCase;
use App\Models\EventScores;
use App\Models\Event;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;



class CasesController extends Controller
{

    protected LiveService $liveService;
    protected RedisService $redisService;
    protected RTPService $rtpService;
    
    public function __construct(
        LiveService $liveService,
        RedisService $redisService,
        RTPService $rtpService
    ) {
        $this->liveService = $liveService;
        $this->redisService = $redisService;
        $this->rtpService = $rtpService;
    }

    public function index(Request $request): array
    {
        $user = null;
    
        if ($request->bearerToken()) {
            $accessToken = PersonalAccessToken::findToken($request->bearerToken());
            if ($accessToken && $accessToken->tokenable_type === User::class) {
                $user = $accessToken->tokenable;
            }
        }
    
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        $onlyAvailable = $request->boolean('available');
    
        $query = Boxes::with(['category:id,name,position']) // добавляем position
            ->select('id', 'image', 'name', 'price', 'type', 'url', 'category_id', 'current_rtp', 'total_opened')
            ->where('is_visible', true)
            ->where('is_active', true);
    
        if ($min_price) {
            $query->where('price', '>=', $min_price);
        }
    
        if ($max_price) {
            $query->where('price', '<=', $max_price);
        }
    
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('game')) {
            $query->where('game', $request->game);
        }
    
        if ($onlyAvailable && $user) {
            $query->where('price', '<=', $user->balance);
        }
    
        $boxes = $query->get();
    
        $freeCaseIds = [];
        if ($user) {
            $freeCaseIds = UserFreeCase::where('user_id', $user->id)
                ->available()
                ->pluck('case_id')
                ->toArray();
        }
    
        $boxes->transform(function ($box) use ($freeCaseIds, $user) {
            $box->is_free = $user ? in_array($box->id, $freeCaseIds) : false;
            return $box;
        });
    
        $grouped = $boxes->groupBy(function ($box) {
            return $box->category->id;
        })->map(function ($boxes) {
            return [
                'category' => $boxes->first()->category,
                'boxes' => $boxes->sortBy('id')->values(), 
            ];
        });
    
        $sorted = $grouped->sortBy(function ($group) {
            return $group['category']->position ?? 9999;
        })->values();
    
        return [
            'categories' => $sorted,
            'success' => true,
        ];
    }
    

    public function one(Request $request): array
    {
        $url = $request->url;
        $user = null;

        if ($request->bearerToken()) {
            $accessToken = PersonalAccessToken::findToken($request->bearerToken());
            if ($accessToken && $accessToken->tokenable_type === User::class) {
                $user = $accessToken->tokenable;
            }
        }


        $box = Boxes::query()
            ->where('url', $url)
            ->select('id', 'category_id', 'name', 'url', 'image', 'price', 'is_active', 'is_visible', 'type', 'sound_pack', 'current_rtp')
            ->first();

        if (!$box) return ['case' => null];

        // Для неавторизованных - всегда false
        $isFree = false;
        $freeCaseId = null;

        // Для авторизованных - проверяем наличие бесплатного кейса
        if ($user) {
            $freeCase = UserFreeCase::where('user_id', $user->id)
                ->where('case_id', $box->id)
                ->available()
                ->first();
            $isFree = (bool)$freeCase;
            $freeCaseId = $freeCase->id ?? null;
        }

        $box->is_free = $isFree;
        if ($isFree) {
            $box->free_case_id = $freeCaseId;
        }

        $caseItems = CaseItems::query()
            ->where('box_id', $box->id)
            ->join('items', 'case_items.skin_id', '=', 'items.id')
            ->orderBy('items.steam_price', 'desc')
            ->select('items.weapon', 'items.skin_name', 'items.image', 'items.steam_price', 'items.quality', 'items.rarity', 'case_items.chance')
            ->get();

        // ВАЖНО: Пересчитываем RTP на лету из актуальных данных
        // RTP = (Потрачено / Выиграно) * 100
        $currentRTP = ($box->total_won > 0 && $box->total_spent > 0)
            ? round(($box->total_spent / $box->total_won) * 100, 2)
            : ($box->target_rtp ?? 95);
        
        // Ограничиваем максимальным порогом
        if ($currentRTP > $box->max_rtp) {
            $currentRTP = $box->max_rtp;
        }
        
        // Ограничиваем минимальным порогом
        if ($currentRTP < $box->min_rtp) {
            $currentRTP = $box->min_rtp;
        }

        return [
            'case' => $box,
            'items' => $caseItems,
            'rtp' => $currentRTP,
            'success' => true
        ];
    }
    public function open(Request $request): array
    {
        $id = intval($request->id);
        $count = intval($request->count);

        $user = $request->user();
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Авторизируйтесь'
            ];
        }
        $box = Boxes::findOrFail($id);

        if (!$box) return ['success' => false, 'message' => 'Кейс не найден, обновите страницу'];
        if ($count < 1 || $count > 5) return ['success' => false, 'message' => 'Выберите корректное количество кейсов для открытия'];

        $totalPrice = $box->price * $count;
        $points = $totalPrice * 0.1;

        $freeCase = UserFreeCase::where('user_id', $user->id)
            ->where('case_id', $box->id)
            ->available()
            ->first();

        $hasFreeCase = (bool)$freeCase;

        if (!$hasFreeCase && $user->balance < $totalPrice) {
            return [
                'success' => false,
                'message' => 'Недостаточно средств на балансе'
            ];
        }

        $caseItems = CaseItems::with('item')
            ->where('box_id', $box->id)
            ->where('chance', '>', 0)
            ->get();

        if ($caseItems->isEmpty()) {
            return ['success' => false, 'message' => 'Нет предметов для выпадения'];
        }

        $provablyData = ProvablyFairSeed::where('user_id', $user->id)
            ->where('active', true)
            ->first();

        if (!$provablyData) {
            return ['success' => false, 'message' => 'Не найдены provably fair ключи пользователя'];
        }

        $resultItems = [];
        $liveIds = [];

        for ($i = 0; $i < $count; $i++) {
            // Логика с RTP и Provably Fair
            $provablyData = ProvablyFairSeed::where('user_id', $user->id)
                ->where('active', true)
                ->first();

            if (!$provablyData) {
                return ['success' => false, 'message' => 'Не найдены provably fair ключи пользователя'];
            }

            $provablyData->nonce += 1;
            $provablyData->save();

            // Подготовка предметов с учетом RTP
            $adjustedItems = $this->rtpService->getAdjustedDropChances($box, 
                $caseItems->map(function($item) {
                    return [
                        'item_id' => $item->item->id,
                        'item' => $item->item,
                        'chance' => $item->chance,
                        'price' => $item->item->steam_price
                    ];
                })->toArray()
            );

            // Используем Provably Fair с модифицированными шансами
            $randFloat = $this->provablyFairRandom($provablyData->server_seed, $provablyData->client_seed, $provablyData->nonce);
            $caseItem = $this->selectItemWithRTP($adjustedItems, $randFloat);

            // Если пользователь заблокирован для получения скинов — выдаём случайный предмет из самых дешёвых (30%)
            if ($user->is_skin_blocked) {
                $sorted = collect($adjustedItems)->sortBy('price')->values();
                $cheapCount = max(1, (int) ceil($sorted->count() * 0.3));
                $cheapPool = $sorted->take($cheapCount);
                $caseItem = $cheapPool->random();
            }

            // Получаем объект предмета
            $itemData = is_array($caseItem) ? $caseItem['item'] : $caseItem->item;
            
            $live = Lives::create([
                'user_id' => $user->id,
                'skin_id' => $itemData->id,
                'box_id' => $box->id,
                'from_where' => "BOX",
                'price' => $itemData->steam_price,
                'status' => "STOCK",
            ]);
            $liveIds[] = $live->id;
            $resultItems[] = [
                'id' => $live->id,
                'weapon' => $itemData->weapon,
                'skin_name' => $itemData->skin_name,
                'image' => $itemData->image,
                'steam_price' => $itemData->steam_price,
                'quality' => $itemData->quality,
                'rarity' => $itemData->rarity,
            ];
            
            // Обновляем статистику RTP кейса
            $this->rtpService->updateBoxStats($box, $box->price, $itemData->steam_price);
        }

        if (!$hasFreeCase) {
            $user->update([
                'balance' => $user->balance - $totalPrice,
                'total_bet' => $user->total_bet + $totalPrice,
                'event_points' => $user->event_points + $points
            ]);

            $this->updateEventScores($user->id, $points);
        }
        $this->liveService->addToLive($liveIds, 'BOX');
        $this->redisService->updateUserBalance($user->id, $user->balance, $user->event_points);

        if ($hasFreeCase) {
            $freeCase->markAsUsed();
        }

        return ['success' => true, 'winItems' => $resultItems, 'used_free_case' => $hasFreeCase];
    }

    public function sellItem(Request $request): array
    {
        $liveId = $request->liveId;

        if (!$liveId) {
            return ['success' => false, 'message' => 'Не указан предмет для продажи'];
        }

        $user = $request->user();

        try {
            $result = DB::transaction(function () use ($liveId, $user) {
                $live = Lives::query()
                    ->where('id', $liveId)
                    ->where('user_id', $user->id)
                    ->where('status', 'STOCK')
                    ->lockForUpdate()
                    ->first();

                if (!$live) {
                    return ['success' => false, 'message' => 'Предмет не найден, не принадлежит вам или уже продан'];
                }

                $live->status = Lives::SELL;
                $live->save();

                $user->increment('balance', $live->price);

                return ['success' => true, 'message' => 'Скин успешно продан!', 'price' => $live->price];
            });

            if ($result['success']) {
                $user->refresh();
                $this->redisService->updateUserBalance($user->id, $user->balance, $user->event_points);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Sell item error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Ошибка при продаже предмета. Попробуйте снова.'];
        }
    }

    public function sellAllItems(Request $request): array
    {
        $user = $request->user();

        // Получаем все предметы пользователя со статусами STOCK или OPENED
        $lives = Lives::query()
            ->where('user_id', $user->id)
            ->where('status', 'STOCK')
            ->get();

        if ($lives->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Нет предметов для продажи'
            ];
        }

        // Считаем общую сумму продажи
        $totalPrice = $lives->sum('price');

        // Получаем только ID предметов для обновления
        $liveIds = $lives->pluck('id')->toArray();

        if (!empty($liveIds)) {
            // Обновляем статус всех предметов
            Lives::query()
                ->whereIn('id', $liveIds)
                ->update(['status' => Lives::SELL]);
        }

        // Обновляем баланс пользователя
        $user->update([
            'balance' => $user->balance + $totalPrice,
        ]);

        // Обновляем баланс в Redis
        $this->redisService->updateUserBalance($user->id, $user->balance, $user->event_points);

        return [
            'success' => true,
            'message' => $lives->count() > 1
                ? 'Все предметы успешно проданы!'
                : 'Предмет успешно продан!',
            'added_balance' => $totalPrice,
            'new_balance' => $user->balance,
        ];
    }

    private function updateEventScores($userId, $points)
    {
        $currentEvent = Event::getCurrentEvent();
        
        if (!$currentEvent) {
            return null;
        }

        $eventScore = EventScores::where('user_id', $userId)
            ->where('event_id', $currentEvent->id)
            ->first();

        if ($eventScore) {
            $eventScore->addPoints($points);
        } else {
            $eventScore = EventScores::create([
                'user_id' => $userId,
                'event_id' => $currentEvent->id,
                'points' => $points,
                'position' => 0,
                'reward_received' => false
            ]);

            $eventScore->updatePosition();
        }

        return $eventScore;
    }

    private function getWeightedRandomItem($caseItems)
    {
        $totalWeight = $caseItems->sum('chance');
        $randomWeight = mt_rand(1, $totalWeight * 10000) / 10000; // Более точный рандом

        $currentWeight = 0;
        foreach ($caseItems as $item) {
            $currentWeight += $item->chance;
            if ($randomWeight <= $currentWeight) {
                return $item;
            }
        }

        return $caseItems->last(); // fallback
    }

    protected function getWeightedRandomItemWithPF($items, float $rand)
    {
        $totalWeight = $items->sum('chance');
        $randNormalized = ($rand / 1_000_000) * $totalWeight; // нормализуем к сумме шансов

        $acc = 0;
        foreach ($items as $item) {
            $acc += $item->chance;
            if ($randNormalized <= $acc) {
                return $item;
            }
        }

        return $items->last();
    }

    /**
     * Выбрать предмет с учетом RTP и Provably Fair
     */
    private function selectItemWithRTP(array $adjustedItems, float $randFloat): array
    {
        // Нормализуем randFloat (0-1000000) к проценту (0-100)
        $randPercent = ($randFloat / 1_000_000) * 100;
        
        $cumulativeChance = 0;
        foreach ($adjustedItems as $item) {
            $cumulativeChance += $item['normalized_chance'];
            
            if ($randPercent <= $cumulativeChance) {
                return $item;
            }
        }

        // Fallback - возвращаем последний предмет
        return end($adjustedItems);
    }

    protected function provablyFairRandom(string $serverSeed, string $clientSeed, int $nonce): float
    {
        $hash = hash('sha256', $serverSeed . ':' . $clientSeed . ':' . $nonce);
        $dec = hexdec(substr($hash, 0, 8));
        return (int) (($dec / 0xFFFFFFFF) * 1_000_000);
    }
}
