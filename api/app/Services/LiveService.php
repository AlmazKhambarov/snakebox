<?php

namespace App\Services;

use App\Models\Lives;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class LiveService
{
    protected RedisService $redisService;
    public function __construct(
        RedisService $redisService
    ) {
        $this->redisService = $redisService;
    }

    public function getLive(): array
    {
        // Все последние 20
        $all = Lives::query()
            ->select(['id', 'box_id', 'user_id', 'skin_id', 'from_where'])
            ->with([
                'item:id,skin_name,weapon,image,rarity,steam_price,quality',
                'box' => function ($query) {
                    $query->select(['id', 'name', 'image', 'url'])->where('is_active', 1);
                },
                'user:id,steam_id,username,avatar'
            ])
            ->latest()
            ->take(20)
            ->get();

        // Топ дропы от 100000
        $best = Lives::query()
            ->select(['id', 'box_id', 'user_id', 'skin_id', 'from_where'])
            ->with([
                'item:id,skin_name,weapon,image,rarity,steam_price,quality',
                'box' => function ($query) {
                    $query->select(['id', 'name', 'image', 'url'])->where('is_active', 1);
                },
                'user:id,steam_id,username,avatar'
            ])
            ->whereHas('item', function ($query) {
                $query->where('steam_price', '>=', 100000);
            })
            ->latest()
            ->take(20)
            ->get();

        return [
            'all' => $all,
            'best' => $best,
        ];
    }



    public function addToLive(array $liveIds, string $type): void
    {
        $lives = Lives::query()
            ->select(['id', 'box_id', 'user_id', 'skin_id', 'from_where'])
            ->whereIn('id', $liveIds)
            ->with([
                'item:id,skin_name,weapon,image,rarity,steam_price,quality',
                'box:id,name,image,url',
                'user:id,steam_id,username,avatar'
            ])
            ->get();

        $data = [
            'items' => $lives->toArray(), // Преобразуем в массив
            'type' => $type
        ];

        Log::channel('redis')->info('Publishing to liveFeed channel', [
            'channel' => 'liveFeed',
            'data' => $data,
            'live_ids' => $liveIds,
            'type' => $type,
            'items_count' => $lives->count(),
            'timestamp' => now()->toISOString()
        ]);

        $this->redisService->publish('liveFeed', $data);
    }
}
