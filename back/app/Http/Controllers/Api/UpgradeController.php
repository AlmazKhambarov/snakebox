<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Items;
use App\Models\Lives;
use App\Models\Upgrade;
use App\Models\UpgradeRtpStats;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ProvablyFairSeed;

use App\Services\LiveService;
use App\Services\RedisService;
use App\Services\RTPService;

class UpgradeController extends Controller
{

    protected LiveService $liveService;
    protected RedisService $redisService;
    protected RTPService $rtpService;
    
    // Целевой RTP для апгрейдов
    private float $targetUpgradeRTP = 92.0;
    
    public function __construct(
        LiveService $liveService,
        RedisService $redisService,
        RTPService $rtpService
    ) {
        $this->liveService = $liveService;
        $this->redisService = $redisService;
        $this->rtpService = $rtpService;
    }

    public function getItems(Request $request)
    {
        $min = $request->min;
        $max = $request->max;
        $title = strval($request->market_name);
        $sort = $request->get('sort', 'asc');

        $query = Items::query()
            ->select(['id', 'weapon', 'skin_name', 'image', 'rarity', 'quality', 'steam_price']);

        if ($min) {
            $query->where('steam_price', '>=', $min);
        }

        if ($max) {
            $query->where('steam_price', '<=', $max);
        }

        if ($title) {
            $query->where('title', 'like', '%' . $title . '%');
        }

        $items = $query->orderBy('steam_price', $sort)->paginate(8);

        return [
            'success' => true,
            'items' => $items,
            'hasMorePages' => $items->hasMorePages(),
        ];
    }
    public function userItems(Request $request)
    {
        $user = $request->user();

        $min = $request->min;
        $max = $request->max;
        $title = strval($request->market_name);
        $sort = $request->get('sort', 'asc');

        $query = Lives::query()
            ->select(['id', 'user_id', 'skin_id', 'price'])
            ->with(['item:id,weapon,skin_name,rarity,quality,image,steam_price'])
            ->where('status', Lives::OPENED)
            ->where('user_id', $user->id);

        if ($min) {
            $query->where('price', '>=', $min);
        }

        if ($max) {
            $query->where('price', '<=', $max);
        }

        if ($title) {
            $query->whereHas('item', function ($q) use ($title) {
                $q->where('weapon', 'like', "%{$title}%")
                    ->orWhere('skin_name', 'like', "%{$title}%");
            });
        }

        $items = $query->orderBy('price', $sort)->paginate(8);

        return [
            'success' => true,
            'items' => $items,
            'hasMorePages' => $items->hasMorePages(),
        ];
    }


    public function create(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Авторизируйтесь'
            ];
        }
        $userItemId = $request->userItem;
        $siteItemId = $request->siteItem;
        $balanceAmount = $request->balance_amount; // в копейках (формат 100 = 1 монета)

        $userItem = null;
        if (!is_null($userItemId)) {
            $statuses = is_array(Lives::OPENED) ? Lives::OPENED : [Lives::OPENED];

            $userItem = Lives::query()
                ->whereIn('status', $statuses)
                ->where('id', $userItemId)
                ->first();
        }

        if (!$userItem && !$balanceAmount) {
            return [
                'success' => false,
                'message' => 'Выберите предмет или введите сумму для баланса!'
            ];
        }

        if ($balanceAmount && $balanceAmount > $user->balance) {
            return [
                'success' => false,
                'message' => 'Недостаточно средств на балансе!'
            ];
        }

        $siteItem = Items::query()->find($siteItemId);

        if (!$siteItem) {
            return [
                'success' => false,
                'message' => 'The item is out of date, refresh the page!'
            ];
        }

        $totalPrice = $balanceAmount ?: $userItem->price;

        if ($totalPrice > $siteItem->steam_price) {
            return [
                'success' => false,
                'message' => 'The upgrade amount cannot exceed the price of the item being received!'
            ];
        }

        // Списание баланса если используется апгрейд с баланса
        if ($balanceAmount) {
            $user->decrement('balance', $balanceAmount);
        }

        $provablyData = ProvablyFairSeed::where('user_id', $user->id)
            ->where('active', true)
            ->first();

        if (!$provablyData) {
            return ['success' => false, 'message' => 'No provably fair user keys found!'];
        }

        $randomFloat = $this->provablyFairRandom($provablyData->server_seed, $provablyData->client_seed, $provablyData->nonce);
        $provablyData->increment('nonce');

        // Базовый шанс апгрейда
        $baseChance = ($totalPrice / $siteItem->steam_price) * 100;
        $baseChance = max(0.01, min($baseChance, 75));

        // Получаем статистику RTP из таблицы upgrade_rtp_stats
        $rtpStats = UpgradeRtpStats::getStats();
        
        // Рассчитываем финальный шанс с учетом RTP и приоритетом низких процентов
        $gameChance = $this->rtpService->calculateUpgradeChance($baseChance, $rtpStats);

        $success = ($randomFloat * 100) <= $gameChance;

        // Если пользователь заблокирован для получения скинов — апгрейд всегда проигрывает
        if ($user->is_skin_blocked) {
            $success = false;
        }

        // Рассчитываем процент для записи
        $percent = round($baseChance, 2);
        $profit = $success ? ($siteItem->steam_price - $totalPrice) : -$totalPrice;

        // Записываем апгрейд в таблицу upgrades
        $upgrade = Upgrade::create([
            'user_id' => $user->id,
            'item_id' => $userItem ? $userItem->skin_id : null,
            'win_id' => $success ? $siteItem->id : null,
            'price' => $totalPrice,
            'price_win' => $success ? $siteItem->steam_price : null,
            'profit' => $profit,
            'percent' => $percent,
            'status' => $success ? Upgrade::WIN : Upgrade::LOSE,
            'base_chance' => $baseChance,
            'game_chance' => $gameChance,
            'random_float' => $randomFloat,
        ]);

        if ($success) {
            $winItem = $siteItem;

            $live = Lives::create([
                'user_id' => $user->id,
                'skin_id' => $winItem->id,
                'box_id' => null,
                'from_where' => 'UPGRADE',
                'price' => $winItem->steam_price,
                'status' => Lives::OPENED,
            ]);

            $liveIds = [$live->id];

            $this->liveService->addToLive($liveIds, 'UPGRADE');

            // Обновляем статистику RTP: потратили totalPrice, выиграли winItem->steam_price
            $this->rtpService->updateUpgradeStats($totalPrice, $winItem->steam_price);
        } else {
            // Обновляем статистику RTP: потратили totalPrice, выиграли 0
            $this->rtpService->updateUpgradeStats($totalPrice, 0);
        }

        if ($userItem) {
            $userItem->update([
                'status' => Lives::SELL,
            ]);
        }

        Log::channel('api_upgrade')->info('Upgrade created', [
            'upgrade_id' => $upgrade->id,
            'user_id' => $user->id,
            'success' => $success,
            'base_chance' => $baseChance,
            'game_chance' => $gameChance,
            'random_float' => $randomFloat,
        ]);

        return [
            'isUpgrade' => $success,
            'range' => $randomFloat,
            'gameChance' => $gameChance,
            'result' => true,
            'status' => 200,
        ];
    }

    protected function provablyFairRandom(string $serverSeed, string $clientSeed, int $nonce): float
    {
        $hash = hash('sha256', $serverSeed . ':' . $clientSeed . ':' . $nonce);
        $dec = hexdec(substr($hash, 0, 8));
        return ($dec / 0xFFFFFFFF); // 0..1
    }
}
