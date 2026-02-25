<?php

namespace App\Http\Controllers\Api\MarketOrSteam;

use App\Http\Controllers\Controller;
use App\Models\Items;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Settings;
use App\Models\Lives;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Services\LiveService;
use App\Services\RedisService;

class MarketController extends Controller
{

  const MARKET_URL = 'https://market.csgo.com';

  protected LiveService $liveService;
  protected RedisService $redisService;
  public function __construct(
    LiveService $liveService,
    RedisService $redisService
  ) {
    $this->liveService = $liveService;
    $this->redisService = $redisService;
  }

  public function withdraw(Request $request): array
  {
    $user = $request->user();

    $settings = Settings::query()->select(['market_key'])->first();

    if (!$user) return ['success' => false, 'message' => 'Авторизуйтесь'];
    if (is_null($user->trade_url)) return ['success' => false, 'message' => 'Укажите ссылку на обмен'];
    if (!$settings->market_key) return ['success' => false, 'message' => 'Серверная ошибка. Обратитесь в техническую поддержку'];

    if (Cache::has('send.yes.' . $user->id)) return ['success' => false, 'message' => 'Не повторяйте запросы. Подождите минуту после прошлого вывода'];
    Cache::put('send.' . $user->id, '', 10);

    $liveId = $request->liveId;

    Log::channel('market')->info('liveId: ' . $liveId);

    $live = Lives::query()
      ->with('item')
      ->where('user_id', $user->id)
      ->where('status', Lives::OPENED)
      ->where('id', $liveId)
      ->first();

    if (!$live) return ['success' => false, 'message' => 'Данный предмет уже был продан или выведен'];


    $item = $live->item;
    $market_hash_name = $item['title'];

    // Получаем список активных оферов на market csgo по market_hash_name
    $url = Http::get(self::MARKET_URL . '/api/v2/search-item-by-hash-name', [
      'key' => $settings->market_key,
      'hash_name' => $market_hash_name
    ])->json();

    if (!$url['success'] || !isset($url['data'][0])) {
      $live->update([
        'status' => Lives::OPENED
      ]);
      return ['success' => false, 'message' => 'Не нашли подходящий предложений на маркете', 'status' => $live->status];
    }

    // Получаем первый самый дешевый офер
    $item = $url['data'][0];
    $custom_id = Str::random(50);

    $token = $this->_parseToken($user->trade_url);
    $partner = $this->_parsePartner($user->trade_url);

    $maxPrice = ($live->price) * 1.05;


    $minPrice = $item['price'] / 100;

    // Проверяем, чтобы цена предмета не превышала допустимый лимит
    if ($minPrice > $maxPrice) {
      $live->update([
        'status' => Lives::OPENED
      ]);
      return ['success' => false, 'message' => 'Ошибка вывода. Попробуйте позже!', 'status' => $live->status];
    }

    // Создаём запрос на покупку предмета
    $params = [
      'key' => $settings->market_key,
      'hash_name' => $item['market_hash_name'],
      'price' => $item['price'],
      'chance' => 1,
      'partner' => $partner,
      'token' => $token,
      'custom_id' => $custom_id,
    ];

    $url = self::MARKET_URL . '/api/v2/buy-for?' . http_build_query($params);

    $response = file_get_contents($url);

    if ($response === false) {
      Log::channel('market')->error('Ошибка при обращении к Market API (buy-for): ' . $url);
      $live->update(['status' => Lives::OPENED]);
      return [
        'success' => false,
        'message' => 'Ошибка соединения с Market API. Попробуйте позже.',
        'status' => $live->status,
      ];
    }

    $url = json_decode($response, true);

    Log::channel('market')->info('$resp', ['resp' => $url]);

    if (!$url['success']) {
      $live->update([
        'status' => Lives::OPENED
      ]);
      return ['success' => false, 'message' => 'Не нашли подходящий предложений на маркете', 'status' => $live->status];
    }

    Cache::put('send.yes.' . $user->id, '', 60);

    $live->update([
      'status' => Lives::SENDING,
      'market_id' => $url['id'],
      'custom_id' => $custom_id
    ]);

    return ['success' => true, 'message' => 'Запрос на покупку отправлен', 'status' => $live->status];
  }

  public function checkItems(): void
  {
    $settings = Settings::query()->first();
    if (is_null($settings->market_key)) {
      return;
    }

    // Получаем все нужные items одним запросом
    $items = Lives::query()
      ->whereIn('status', [
        Lives::SENDING,
        Lives::WAIT,
        Lives::ORDER_READY
      ])
      ->get();


    if ($items->isEmpty()) {
      return;
    }

    $queryParams = [
      'key' => $settings->market_key,
      'custom_id' => $items->pluck('custom_id')->toArray(),
    ];

    Log::channel('market')->debug($queryParams);

    $response = Http::get('https://market.csgo.com/api/v2' . '/get-list-buy-info-by-custom-id', $queryParams);
    Log::channel('market')->debug('Market raw response:', [
      'status' => $response->status(),
      'body' => $response->body(),
    ]);

    $marketResponse = $response->json();

    Log::channel('market')->debug('Market parsed response:', $marketResponse);

    if (!is_array($marketResponse) || !isset($marketResponse['success'])) {
      Log::channel('market')->error('Invalid or empty response from Market API', [
        'response' => $marketResponse,
        'queryParams' => $queryParams
      ]);
      return;
    }

    $updates = [];
    $publishData = [];

    foreach ($items as $item) {
      // Получаем данные по конкретному custom_id
      $marketData = $marketResponse['data'][$item->custom_id] ?? null;

      if (!$marketData) {
        continue;
      }

      $updateData = $this->determineUpdateData($marketData);

      if (!empty($updateData)) {
        $updates[$item->id] = $updateData;

        $publishData[] = [
          'id' => $item->id,
          'user_id' => $item->user_id,
          'status' => $updateData['status'] ?? $item->status,
          'trade_id' => $updateData['trade_id'] ?? $item->trade_id,
          'settlement' => $updateData['settlement'] ?? $item->settlement
        ];
      }
    }

    if (!empty($updates)) {
      $this->batchUpdateItems($updates);
      $this->redisService->publish('setItemsStatus', $publishData);
    }
  }

  public function marketItems()
  {
    $url = json_decode(file_get_contents('https://market.csgo.com/api/v2/prices/RUB.json'), true);

    if (!$url['success']) {
      return ['success' => false, 'message' => 'Ошибка запроса API'];
    }

    $items = $url['items'];

    // Ключи для перевода качества
    $qualityMap = [
      'Factory New' => 'FN',
      'Minimal Wear' => 'MW',
      'Field-Tested' => 'FT',
      'Well-Worn' => 'WW',
      'Battle-Scarred' => 'BS',
    ];

    // Исключаем типы предметов, которые не парсим
    $excludedItems = [
      'case',
      'capsule',
      'music kit',
      'sticker capsule',
      'RMR',
      'Sticker',
      'Package',
      'Autograph',
      'Legends',
      'Patch',
      'Katowice',
      'Pass',
      'PGL',
      'Graffiti',
    ];

    $added = 0;
    $updated = 0;

    foreach (array_chunk($items, 500) as $chunk) {
      foreach ($chunk as $item) {
        $title = $item['market_hash_name'] ?? null;
        $price = isset($item['price']) ? (float)$item['price'] : 0;
        $volume = isset($item['volume']) ? (int)$item['volume'] : 0;

        if (!$title || $price <= 0) {
          continue;
        }

        // Пропускаем предметы, у которых volume меньше 4
        if ($volume < 4) {
          continue;
        }

        // Пропускаем предметы, содержащие исключённые слова
        $lowerTitle = mb_strtolower($title);
        $skip = false;
        foreach ($excludedItems as $excluded) {
          if (mb_strpos($lowerTitle, mb_strtolower($excluded)) !== false) {
            $skip = true;
            break;
          }
        }
        if ($skip) {
          continue;
        }

        // Парсим название: "AK-47 | Aquamarine Revenge (Well-Worn)"
        $weapon = '';
        $skinName = '';
        $quality = '';

        if (preg_match('/^([^|]+)\s\|\s(.+)\s\(([^)]+)\)$/u', $title, $matches)) {
          // С шаблоном с качеством
          $weapon = trim($matches[1]);
          $skinName = trim($matches[2]);
          $qualityFull = trim($matches[3]);
          $quality = $qualityMap[$qualityFull] ?? null;
        } elseif (preg_match('/^([^|]+)\s\|\s(.+)$/u', $title, $matches)) {
          // Без качества
          $weapon = trim($matches[1]);
          $skinName = trim($matches[2]);
          $quality = null;
        }


        // Проверяем, есть ли предмет в базе
        $existingItem = Items::query()->where('title', $title)->first();

        if ($existingItem) {
          // Обновляем старую цену перед изменением
          $existingItem->update([
            'steam_price_before' => $existingItem->steam_price ?? 0,
            'steam_price' => $price * 100,
          ]);
          $updated++;
        } else {
          // Добавляем новый предмет
          Items::query()->create([
            'title' => $title,
            'weapon' => $weapon,
            'skin_name' => $skinName,
            'quality' => $quality,
            'steam_price' => $price * 100,
            'steam_price_before' => 0,
            'image' => null,
            'rarity' => null,
          ]);
          $added++;
        }
      }
    }

    Log::channel('market')->info("Обновление предметов завершено. Добавлено: {$added}, обновлено: {$updated}");

    return [
      'success' => true,
      'message' => "База предметов обновлена. Добавлено: {$added}, обновлено: {$updated}",
    ];
  }
  public function updateItemImagesAndRarity()
  {
    $url = 'https://steamp.ru/api/v2?appid=730&classid=true&img=true&rarity=true';
    $response = Http::get($url);

    if ($response->failed()) {
      return response()->json([
        'success' => false,
        'message' => 'Не удалось получить данные с steamp.ru'
      ]);
    }

    $data = $response->json();

    if (empty($data['success']) || !$data['success'] || empty($data['items'])) {
      return response()->json([
        'success' => false,
        'message' => 'Неверный ответ API'
      ]);
    }

    $itemsData = $data['items'];
    $updatedCount = 0;

    foreach ($itemsData as $title => $itemData) {
      $existingItem = Items::where('title', $title)->first();
      if (!$existingItem) continue;

      $updateData = [];

      // Обновляем image только если NULL
      if (is_null($existingItem->image) && !empty($itemData['img'])) {
        $updateData['image'] = 'https://steamcommunity-a.akamaihd.net/economy/image/' . $itemData['img'];
      }

      // Обновляем rarity только если NULL
      if (is_null($existingItem->rarity) && !empty($itemData['rarity'])) {
        $updateData['rarity'] = $itemData['rarity'];
      }

      if (!empty($updateData)) {
        $updateData['updated_at'] = now();
        $existingItem->update($updateData);
        $updatedCount++;
      }
    }

    Log::channel('market')->info("Обновлены image и rarity для предметов: {$updatedCount}");

    return response()->json([
      'success' => true,
      'message' => "Обновлены image и rarity для предметов: {$updatedCount}"
    ]);
  }

  protected function batchUpdateItems(array $updates): void
  {
    DB::transaction(function () use ($updates) {
      foreach ($updates as $id => $data) {
        Lives::query()->where('id', $id)->update($data);
      }
    });
  }

  protected function determineUpdateData(array $marketData): array
  {
    $updateData = [];

    switch ($marketData['stage']) {
      case '1':
        $tradeId = (int) $marketData['trade_id'];
        $settlement = (float) $marketData['settlement'];

        if ($tradeId > 0 && $settlement > 0) {
          // предмет уже выведен
          $updateData = [
            'status' => Lives::WITHDRAWN,
            'trade_id' => $tradeId,
            'settlement' => $settlement,
          ];
        } elseif ($tradeId > 0 && $settlement == 0) {
          // предмет готов к трейду
          $updateData = [
            'status' => Lives::ORDER_READY,
            'trade_id' => $tradeId,
            'settlement' => $settlement,
          ];
        } else {
          // trade_id = 0 или null → предмет в ожидании
          $updateData = [
            'status' => Lives::WAIT,
            'trade_id' => null,
            'settlement' => 0,
          ];
        }
        break;

      case '2':
        $updateData = ['status' => Lives::WITHDRAWN];
        break;

      case '5':
        $updateData = [
          'status' => Lives::OPENED,
          'market_id' => null,
          'custom_id' => null,
          'trade_id' => null,
          'settlement' => null,
        ];
        break;
    }

    return $updateData;
  }




  private function _parsePartner($tradeLink)
  {
    $query_str = parse_url($tradeLink, PHP_URL_QUERY);
    parse_str($query_str, $query_params);
    return isset($query_params['partner']) ? $query_params['partner'] : false;
  }

  private function _parseToken($tradeLink)
  {
    $query_str = parse_url($tradeLink, PHP_URL_QUERY);
    parse_str($query_str, $query_params);
    return isset($query_params['token']) ? $query_params['token'] : false;
  }
}
