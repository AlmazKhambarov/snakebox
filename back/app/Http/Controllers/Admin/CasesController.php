<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Boxes;
use App\Models\Items;
use App\Models\Categories;
use App\Models\CaseItems;
use App\Models\Lives;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CasesController extends Controller
{
  public function get()
  {
    return datatables(Boxes::query()->with(['category']))->toJson();
  }

  public function create(Request $request): array
  {

    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'url' => 'nullable|string',
      'category_id' => 'required',
      'price' => 'required',
      'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
      'is_active' => 'required',
      'is_visible' => 'required',
      'type' => 'required',
      'sound_pack' => 'required',
    ], [
      'name.required' => 'Вы не указали название',
      'url.required' => 'Вы не указали ссылку на кейс',
      'category_id.required' => 'Вы не указали категорию',
      'price.required' => 'Вы не указали стоимость кейса',
      'image.required' => 'Вы не указали картинку кейса',
      'image.image' => 'Файл должен быть изображением',
      'image.mimes' => 'Изображение должно быть в формате jpeg, png, jpg или gif,webp',
      'image.max' => 'Размер изображения не должен превышать 2MB',
      'is_active.required' => 'Вы не указали скрытость кейса',
      'is_visible.required' => 'Вы не указали визибл кейса',
      'type.required' => 'Вы не указали тип кейса',
      'sound_pack.required' => 'Вы не указали саунд пак кейса',
    ]);

    if ($validator->fails()) {
      return ['success' => false, 'message' => $validator->errors()->first()];
    }

    $categoryExist = Categories::query()->find($request->category_id);
    if (!$categoryExist) return ['success' => false, 'message' => 'Категория не найдена'];

    $randomId = Str::random(10);
    $image = $request->file('image');
    $imageName = $randomId . '_' . time() . '.' . $image->getClientOriginalExtension();
    Storage::disk('public')->putFileAs('boxes', $image, $imageName);

    Boxes::query()->create([
      'category_id' => $request->category_id,
      'name' => $request->name,
      'url' => $request->url,
      'image' => config('app.url') . '/storage/boxes/' . $imageName,
      'price' => $request->price * 100,
      'is_active' => $request->is_active,
      'is_visible' => $request->is_visible,
      'type' => $request->type,
      'game' => $request->game ?? 'cs',
      'sound_pack' => $request->sound_pack
    ]);

    return ['success' => true, 'message' => 'Кейс успешно добавлен!'];
  }

  public function case(Request $request): array
  {
    $id = $request->id;

    $case = Boxes::query()->with(['category'])->find($id);
    if (!$case) return ['success' => false, 'message' => 'Кейс не найден'];

    return [
      'success' => true,
      'case' => $case
    ];
  }

  public function save(Request $request): array
{
    $id = $request->id;

    $box = Boxes::query()->find($id);
    if (!$box) {
        return ['success' => false, 'message' => 'Кейс не найден'];
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'url' => 'nullable|string',
        'category_id' => 'required',
        'price' => 'required|numeric',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'is_active' => 'required|boolean',
        'is_visible' => 'required|boolean',
        'type' => 'required',
        'sound_pack' => 'required',
    ], [
        'name.required' => 'Вы не указали название',
        'category_id.required' => 'Вы не указали категорию',
        'price.required' => 'Вы не указали стоимость кейса',
        'image.image' => 'Файл должен быть изображением',
        'image.mimes' => 'Изображение должно быть в формате jpeg, png, jpg, gif или webp',
        'image.max' => 'Размер изображения не должен превышать 2MB',
        'is_active.required' => 'Вы не указали скрытость кейса',
        'is_visible.required' => 'Вы не указали визибл кейса',
        'type.required' => 'Вы не указали тип кейса',
        'sound_pack.required' => 'Вы не указали саунд пак кейса',
    ]);

    if ($validator->fails()) {
        return ['success' => false, 'message' => $validator->errors()->first()];
    }

    $categoryExist = Categories::query()->find($request->category_id);
    if (!$categoryExist) {
        return ['success' => false, 'message' => 'Категория не найдена'];
    }

    $imagePath = $box->image; // по умолчанию оставляем старое изображение

    if ($request->hasFile('image')) {
        // удаляем старое
        if ($box->image) {
            // Extract path after '/storage/' from full URL or relative path
            $parts = explode('/storage/', $box->image);
            $oldPath = end($parts);
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // сохраняем новое
        $randomId = Str::random(10);
        $image = $request->file('image');
        $imageName = $randomId . '_' . time() . '.' . $image->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('boxes', $image, $imageName);

        $imagePath = config('app.url') . '/storage/boxes/' . $imageName;
    }

    $box->update([
        'category_id' => $request->category_id,
        'name' => $request->name,
        'url' => $request->url,
        'image' => $imagePath,
        'price' => $request->price * 100,
        'is_active' => $request->is_active,
        'is_visible' => $request->is_visible,
        'type' => $request->type,
        'game' => $request->game ?? $box->game,
        'sound_pack' => $request->sound_pack,
    ]);

    return ['success' => true, 'message' => 'Кейс успешно обновлен!'];
}


  public function delete(Request $request): array
  {
    $id = intval($request->id);

    $box = Boxes::query()->find($id);
    if (!$box) return ['success' => false, 'message' => 'Кейс не найден'];

    $box->delete();

    return ['success' => true, 'message' => 'Кейс удалён'];
  }

  public function items(Request $request)
  {
    return datatables(CaseItems::query()->with(['item'])->where('box_id', $request->boxId))->toJson();
  }

  public function createItem(Request $request): array
  {

    if (!$request->skin_id) return ['success' => false, 'message' => 'Вы не указали предмет'];
    if ($request->chance < 0) return ['success' => false, 'message' => 'Вы не указали шанс выпадения'];

    $itemExist = CaseItems::query()->where('box_id', $request->box_id)->where('skin_id', $request->skin_id)->first();
    if ($itemExist) return ['success' => false, 'message' => 'Предмет уже добавлен'];

    CaseItems::query()->create([
      'box_id' => $request->box_id,
      'skin_id' => $request->skin_id,
      'chance' => $request->chance,
      'droppable' => $request->droppable
    ]);

    return ['success' => true, 'message' => 'Предмет успешно добавлен!'];
  }

  public function getItem(Request $request): array
  {
    $boxItem = CaseItems::query()->find($request->id);
    if (!$boxItem) return ['success' => false, 'message' => 'Предмет не найден'];

    $boxItem->load('item');

    return [
      'success' => true,
      'item' => $boxItem
    ];
  }

  public function saveItem(Request $request)
  {
    if ($request->chance < 0) return ['success' => false, 'message' => 'Вы не указали шанс выпадения'];

    $boxItem = CaseItems::query()->find($request->id);
    $boxItem->update([
      'chance' => $request->chance,
      'droppable' => $request->droppable
    ]);

    return ['success' => true, 'message' => 'Предмет успешно обновлен!'];
  }

  public function deleteItem(Request $request): array
  {
    $boxItem = CaseItems::query()->find($request->id);
    if (!$boxItem) return ['success' => false, 'message' => 'Предмет не найден'];

    $boxItem->delete();

    return ['success' => true, 'message' => 'Предмет удалён'];
  }

  public function itemsAll(Request $request): array
  {
    $defaultSearchFields = ['title'];

    return $this->getPaginatedResults($request, Items::query(), $defaultSearchFields, function ($item) {
      return [
        'id' => $item->id,
        'text' => $item->title . ' (' . ($item->steam_price / 100) . '₽)'
      ];
    });
  }

  public function itemsAllForCase(Request $request): array
  {
    $defaultSearchFields = ['title'];

    return $this->getPaginatedResults($request, Items::query(), $defaultSearchFields, function ($item) {
      return [
        'id' => $item->id,
        'text' => $item->title
      ];
    });
  }

  public function categories(Request $request): array
  {
    $defaultSearchFields = ['name'];

    return $this->getPaginatedResults($request, Categories::query(), $defaultSearchFields, function ($category) {
      return [
        'id' => $category->id,
        'text' => $category->name
      ];
    });
  }

  protected function getPaginatedResults(Request $request, $query, array $defaultSearchFields, callable $transformCallback): array
  {
    if ($request->has('search')) {
      $search = $request->input('search');
      $searchFields = $defaultSearchFields;

      $query->where(function ($query) use ($search, $searchFields) {
        foreach ($searchFields as $field) {
          $query->orWhere($field, 'LIKE', '%' . $search . '%');
        }
      });
    }

    $pagination = $query->paginate(15);

    $items = $pagination->getCollection()->map($transformCallback);

    return [
      'results' => $items,
      'more' => $pagination->hasMorePages()
    ];
  }

  public function calcChance(Request $request): array
  {
    $box = Boxes::query()->find($request->id);
    if (!$box) {
      return ['success' => false, 'message' => 'Кейс не найден'];
    }

    $caseItems = CaseItems::query()->with(['item'])->where('box_id', $box->id)->get();
    if ($caseItems->isEmpty()) {
      return ['success' => false, 'message' => 'В кейсе нет предметов'];
    }

    $rawChances = [];
    $chances = [];

    foreach ($caseItems as $caseItem) {
      $itemPrice = $caseItem->item->steam_price ?? 0;

      if ($itemPrice <= 0) {
        $chance = 1;
      } else {
        $chance = 1 / ($itemPrice / $box->price);
      }

      $chance = min(100, $chance);
      $chance = max(0.001, $chance);

      $rawChances[$caseItem->id] = $chance;
    }

    $sum = array_sum($rawChances);
    if ($sum <= 0) {
      return ['success' => false, 'message' => 'Ошибка при расчёте шансов'];
    }

    foreach ($caseItems as $caseItem) {
      $normalizedChance = ($rawChances[$caseItem->id] / $sum) * 100;
      $normalizedChance = round($normalizedChance, 4);

      $caseItem->update(['chance' => $normalizedChance]);
      $chances[] = [
        'id' => $caseItem->id,
        'chance' => $normalizedChance,
        'price' => $caseItem->item->steam_price // для отладки
      ];
    }

    return ['success' => true, 'message' => 'Шансы успешно расчитаны', 'chances' => $chances];
  }


  public function generateCaseItems(Request $request): array
  {
    $box = Boxes::query()->find($request->id);
    if (!$box) {
      return ['success' => false, 'message' => 'Кейс не найден'];
    }

    $maxPrice = $box->price * 25;
    $minPrice = $box->price / 5;

    $items = Items::query()
      ->where('steam_price', '>', 0)
      ->where('steam_price', '<=', $maxPrice)
      ->where('steam_price', '>=', $minPrice)
      ->get();

    if ($items->isEmpty()) {
      return ['success' => false, 'message' => 'Нет доступных предметов'];
    }

    // Настройки генерации
    $notProfitPercentage = 30;
    $profitPercentage = 70;
    $totalItems = mt_rand(18, 25);

    $notProfitItems = $items->filter(fn($item) => $item->steam_price < $box->price);
    $profitItems = $items->filter(fn($item) => $item->steam_price >= $box->price);

    if ($notProfitItems->isEmpty() || $profitItems->isEmpty()) {
      return ['success' => false, 'message' => 'Некорректный баланс предметов'];
    }

    // Определяем количество окупных и неокупных предметов
    $notProfitCount = intval(($notProfitPercentage / 100) * $totalItems);
    $profitCount = $totalItems - $notProfitCount;

    // Выбираем случайные предметы
    $selectedNotProfit = $notProfitItems->random($notProfitCount);
    $selectedProfit = $profitItems->random($profitCount);

    // Итоговый список предметов
    $finalItems = $selectedNotProfit->merge($selectedProfit)->shuffle();

    CaseItems::where('box_id', $box->id)->delete();

    // Записываем предметы в таблицу CaseItem
    foreach ($finalItems as $item) {
      CaseItems::create([
        'box_id' => $box->id,
        'skin_id' => $item->id,
        'chance' => 1,
      ]);
    }


    return $this->calcChance($request);
  }

  /**
   * Получить RTP данные кейса
   */
  public function getRTP(Request $request): array
  {
    $id = $request->id;
    $box = Boxes::find($id);
    
    if (!$box) {
      return ['success' => false, 'message' => 'Кейс не найден'];
    }

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
      'success' => true,
      'rtp' => [
        'current_rtp' => $currentRTP,
        'target_rtp' => $box->target_rtp,
        'min_rtp' => $box->min_rtp,
        'max_rtp' => $box->max_rtp,
        'total_opened' => $box->total_opened,
        'total_spent' => $box->total_spent,
        'total_won' => $box->total_won,
        'last_rtp_update' => $box->last_rtp_update?->format('Y-m-d H:i:s'),
      ]
    ];
  }

  /**
   * Обновить настройки RTP кейса
   */
  public function updateRTP(Request $request): array
  {
    $id = $request->id;

    $request->validate([
      'target_rtp' => 'required|numeric|min:50|max:100',
      'min_rtp' => 'required|numeric|min:50|max:100',
      'max_rtp' => 'required|numeric|min:50|max:100',
    ]);

    $box = Boxes::find($id);
    if (!$box) {
      return ['success' => false, 'message' => 'Кейс не найден'];
    }

    $box->update([
      'target_rtp' => $request->target_rtp,
      'min_rtp' => $request->min_rtp,
      'max_rtp' => $request->max_rtp,
    ]);

    Log::channel('admin_cases')->info('Box RTP settings updated', [
      'box_id' => $box->id,
      'target_rtp' => $request->target_rtp,
    ]);

    return ['success' => true, 'message' => 'Настройки RTP обновлены'];
  }

  /**
   * Сбросить статистику RTP кейса
   */
  public function resetRTP(Request $request): array
  {
    $id = $request->id;

    $box = Boxes::find($id);
    if (!$box) {
      return ['success' => false, 'message' => 'Кейс не найден'];
    }

    $box->update([
      'current_rtp' => $box->target_rtp,
      'total_opened' => 0,
      'total_spent' => 0,
      'total_won' => 0,
      'last_rtp_update' => now(),
    ]);

    Log::channel('admin_cases')->info('Box RTP statistics reset', ['box_id' => $box->id]);

    return ['success' => true, 'message' => 'Статистика RTP сброшена'];
  }

  /**
   * Включить автоотключенный кейс вручную
   */
  public function enableCase(Request $request): array
  {
    $id = $request->id;

    $box = Boxes::find($id);
    if (!$box) {
      return ['success' => false, 'message' => 'Кейс не найден'];
    }

    $box->update([
      'is_active' => true,
      'auto_disabled' => false,
      'auto_disabled_reason' => null,
      'auto_disabled_at' => null,
    ]);

    Log::channel('admin_cases')->info('Case manually enabled', [
      'box_id' => $box->id,
      'admin_action' => true,
    ]);

    return ['success' => true, 'message' => 'Кейс успешно включен'];
  }

  /**
   * Получить расширенную статистику по всем кейсам
   */
  public function statistics(): array
  {
    $totalCases = Boxes::count();
    $activeCases = Boxes::where('is_active', true)->count();
    $inactiveCases = Boxes::where('is_active', false)->count();
    $autoDisabledCases = Boxes::where('auto_disabled', true)->count();
    
    // Общая статистика по всем кейсам
    $totalOpened = Boxes::sum('total_opened');
    $totalSpent = Boxes::sum('total_spent');
    $totalWon = Boxes::sum('total_won');
    $totalProfit = $totalSpent - $totalWon;
    // RTP = (Потрачено / Выиграно) * 100
    $overallRTP = ($totalWon > 0 && $totalSpent > 0) ? round(($totalSpent / $totalWon) * 100, 2) : 0;
    
    // Статистика по RTP (пересчитываем на лету)
    $casesWithStats = Boxes::where('total_opened', '>', 0)->get();
    $rtpValues = [];
    $casesAboveTarget = 0;
    $casesBelowTarget = 0;
    $casesInTarget = 0;
    
    foreach ($casesWithStats as $box) {
      $calculatedRTP = $this->calculateRTP($box);
      $rtpValues[] = $calculatedRTP;
      
      if ($calculatedRTP > $box->max_rtp) {
        $casesAboveTarget++;
      } elseif ($calculatedRTP < $box->min_rtp) {
        $casesBelowTarget++;
      } else {
        $casesInTarget++;
      }
    }
    
    $avgRTP = count($rtpValues) > 0 ? array_sum($rtpValues) / count($rtpValues) : 0;
    
    // Статистика по периодам (из lives таблицы)
    $now = Carbon::now();
    $today = $now->copy()->startOfDay();
    $weekAgo = $now->copy()->subDays(7)->startOfDay();
    $monthAgo = $now->copy()->subDays(30)->startOfDay();
    
    // Сегодня
    $todayStats = $this->getPeriodStats($today, $now);
    
    // Последние 7 дней
    $weekStats = $this->getPeriodStats($weekAgo, $now);
    
    // Последние 30 дней
    $monthStats = $this->getPeriodStats($monthAgo, $now);
    
    // Топ кейсов по открытиям
    $topByOpens = Boxes::orderBy('total_opened', 'desc')
      ->limit(10)
      ->get()
      ->map(function ($box) {
        $profit = $box->total_spent - $box->total_won;
        $calculatedRTP = $this->calculateRTP($box);
        
        return [
          'id' => $box->id,
          'name' => $box->name,
          'image' => $box->image,
          'price' => $box->price,
          'total_opened' => $box->total_opened,
          'current_rtp' => round($calculatedRTP, 2), // Используем пересчитанный RTP
          'total_spent' => $box->total_spent,
          'total_won' => $box->total_won,
          'profit' => $profit,
          'rtp' => round($calculatedRTP, 2),
          'is_active' => $box->is_active,
        ];
      });

    // Топ кейсов по доходу (прибыльности)
    $topByProfit = Boxes::where('total_opened', '>', 0)
      ->get()
      ->map(function ($box) {
        $calculatedRTP = $this->calculateRTP($box);
        
        return [
          'id' => $box->id,
          'name' => $box->name,
          'image' => $box->image,
          'price' => $box->price,
          'current_rtp' => round($calculatedRTP, 2), // Используем пересчитанный RTP
          'total_opened' => $box->total_opened,
          'total_spent' => $box->total_spent,
          'total_won' => $box->total_won,
          'profit' => $box->total_spent - $box->total_won,
          'is_active' => $box->is_active,
        ];
      })
      ->sortByDesc('profit')
      ->take(10)
      ->values();

    // Топ кейсов по убыточности (низкий RTP)
    $topByLoss = Boxes::where('total_opened', '>', 0)
      ->get()
      ->map(function ($box) {
        $calculatedRTP = $this->calculateRTP($box);
        
        return [
          'id' => $box->id,
          'name' => $box->name,
          'image' => $box->image,
          'price' => $box->price,
          'current_rtp' => round($calculatedRTP, 2), // Используем пересчитанный RTP
          'total_opened' => $box->total_opened,
          'total_spent' => $box->total_spent,
          'total_won' => $box->total_won,
          'loss' => $box->total_won - $box->total_spent,
          'is_active' => $box->is_active,
        ];
      })
      ->sortBy('loss')
      ->take(10)
      ->values();

    // Проблемные кейсы (НИЗКИЙ RTP - кейс сильно выдает пользователям)
    // Проблемные = те, у которых RTP < 90% (игроки выиграли больше, чем потратили)
    $problematicCases = Boxes::where('total_opened', '>=', 20)
      ->get()
      ->map(function ($box) {
        $calculatedRTP = $this->calculateRTP($box);
        return [
          'box' => $box,
          'rtp' => $calculatedRTP,
        ];
      })
      ->filter(function ($item) {
        // Проблемные = RTP < 90% (низкий RTP = казино в минусе)
        return $item['rtp'] < 90;
      })
      ->map(function ($item) {
        $box = $item['box'];
        $calculatedRTP = $item['rtp'];
        $profit = $box->total_spent - $box->total_won;
        $avgWin = $box->total_opened > 0 ? $box->total_won / $box->total_opened : 0;
        $avgSpent = $box->total_opened > 0 ? $box->total_spent / $box->total_opened : 0;
        
        return [
          'id' => $box->id,
          'name' => $box->name,
          'image' => $box->image,
          'current_rtp' => round($calculatedRTP, 2),
          'min_rtp' => $box->min_rtp,
          'target_rtp' => $box->target_rtp,
          'max_rtp' => $box->max_rtp,
          'total_opened' => $box->total_opened,
          'total_spent' => $box->total_spent,
          'total_won' => $box->total_won,
          'profit' => $profit,
          'avg_win' => round($avgWin, 0),
          'avg_spent' => round($avgSpent, 0),
          'is_active' => $box->is_active,
          'auto_disabled' => $box->auto_disabled,
          'auto_disabled_reason' => $box->auto_disabled_reason,
          'auto_disabled_at' => $box->auto_disabled_at?->format('Y-m-d H:i:s'),
        ];
      })
      ->sortBy('current_rtp') // Сортируем по возрастанию RTP (самые проблемные первыми)
      ->values();
    
    // Топ кейсов с НИЗКИМ RTP (самые проблемные)
    $worstByRTP = Boxes::where('total_opened', '>=', 10)
      ->get()
      ->map(function ($box) {
        $calculatedRTP = $this->calculateRTP($box);
        $profit = $box->total_spent - $box->total_won;
        return [
          'id' => $box->id,
          'name' => $box->name,
          'image' => $box->image,
          'current_rtp' => round($calculatedRTP, 2),
          'total_opened' => $box->total_opened,
          'total_spent' => $box->total_spent,
          'total_won' => $box->total_won,
          'profit' => $profit,
          'loss' => $box->total_won - $box->total_spent,
          'is_active' => $box->is_active,
        ];
      })
      ->sortBy('current_rtp') // Сортируем по возрастанию RTP
      ->take(10)
      ->values();
    
    // Топ кейсов с ВЫСОКИМ RTP (самые прибыльные)
    // Берем только кейсы с RTP >= 90% (выше проблемного порога)
    $bestByRTP = Boxes::where('total_opened', '>=', 10)
      ->get()
      ->map(function ($box) {
        $calculatedRTP = $this->calculateRTP($box);
        return [
          'box' => $box,
          'rtp' => $calculatedRTP,
        ];
      })
      ->filter(function ($item) {
        // Фильтруем только кейсы с RTP >= 90% (не проблемные)
        return $item['rtp'] >= 90;
      })
      ->map(function ($item) {
        $box = $item['box'];
        $calculatedRTP = $item['rtp'];
        $profit = $box->total_spent - $box->total_won;
        return [
          'id' => $box->id,
          'name' => $box->name,
          'image' => $box->image,
          'current_rtp' => round($calculatedRTP, 2),
          'total_opened' => $box->total_opened,
          'total_spent' => $box->total_spent,
          'total_won' => $box->total_won,
          'profit' => $profit,
          'is_active' => $box->is_active,
        ];
      })
      ->sortByDesc('current_rtp') // Сортируем по убыванию RTP
      ->take(10)
      ->values();

    // Статистика по категориям
    $categoryStats = Categories::with('cases')
      ->get()
      ->map(function ($category) {
        $boxes = $category->cases;
        $totalOpened = $boxes->sum('total_opened');
        $totalSpent = $boxes->sum('total_spent');
        $totalWon = $boxes->sum('total_won');
        
        // Пересчитываем средний RTP на лету
        $rtpValues = [];
        foreach ($boxes->where('total_opened', '>', 0) as $box) {
          $rtpValues[] = $this->calculateRTP($box);
        }
        $avgRTP = count($rtpValues) > 0 ? array_sum($rtpValues) / count($rtpValues) : 0;
        
        return [
          'id' => $category->id,
          'name' => $category->name,
          'cases_count' => $boxes->count(),
          'active_cases' => $boxes->where('is_active', true)->count(),
          'total_opened' => $totalOpened,
          'total_spent' => $totalSpent,
          'total_won' => $totalWon,
          'profit' => $totalSpent - $totalWon,
          'avg_rtp' => round($avgRTP, 2),
        ];
      })
      ->sortByDesc('total_opened')
      ->values();

    // Детальная статистика по каждому кейсу
    $allCasesStats = Boxes::with('category')
      ->orderBy('total_opened', 'desc')
      ->get()
      ->map(function ($box) {
        $profit = $box->total_spent - $box->total_won;
        $rtp = $this->calculateRTP($box);
        $avgWin = $box->total_opened > 0 ? $box->total_won / $box->total_opened : 0;
        
        return [
          'id' => $box->id,
          'name' => $box->name,
          'image' => $box->image,
          'category' => $box->category?->name,
          'price' => $box->price,
          'is_active' => $box->is_active,
          'is_visible' => $box->is_visible,
          'total_opened' => $box->total_opened,
          'total_spent' => $box->total_spent,
          'total_won' => $box->total_won,
          'profit' => $profit,
          'current_rtp' => round($rtp, 2), // Используем пересчитанный RTP
          'target_rtp' => $box->target_rtp,
          'min_rtp' => $box->min_rtp,
          'max_rtp' => $box->max_rtp,
          'rtp' => round($rtp, 2),
          'avg_win' => round($avgWin, 0),
          'auto_disabled' => $box->auto_disabled,
          'last_rtp_update' => $box->last_rtp_update?->format('Y-m-d H:i:s'),
        ];
      });

    // Дополнительная статистика
    $casesWithData = Boxes::where('total_opened', '>', 0)->get();
    $profitableCases = 0;
    $unprofitableCases = 0;
    $totalAvgWin = 0;
    $totalAvgSpent = 0;
    $casesWithHighRTP = 0; // RTP >= 150%
    $casesWithLowRTP = 0;  // RTP < 100%
    
    foreach ($casesWithData as $box) {
      $calculatedRTP = $this->calculateRTP($box);
      $profit = $box->total_spent - $box->total_won;
      
      if ($profit > 0) {
        $profitableCases++;
      } else {
        $unprofitableCases++;
      }
      
      // Высокий RTP = выше целевого (хорошо для казино)
      if ($calculatedRTP > $box->target_rtp) {
        $casesWithHighRTP++;
      }
      // Низкий RTP = ниже 90% (плохо для казино)
      if ($calculatedRTP < 90) {
        $casesWithLowRTP++;
      }
      
      if ($box->total_opened > 0) {
        $totalAvgWin += $box->total_won / $box->total_opened;
        $totalAvgSpent += $box->total_spent / $box->total_opened;
      }
    }
    
    $avgWinPerOpen = count($casesWithData) > 0 ? $totalAvgWin / count($casesWithData) : 0;
    $avgSpentPerOpen = count($casesWithData) > 0 ? $totalAvgSpent / count($casesWithData) : 0;
    $profitabilityRate = count($casesWithData) > 0 ? ($profitableCases / count($casesWithData)) * 100 : 0;
    
    // Средние показатели
    $avgStats = [
      'avg_case_price' => round(Boxes::avg('price') ?? 0, 0),
      'avg_opened_per_case' => $totalCases > 0 ? round($totalOpened / $totalCases, 2) : 0,
      'avg_rtp' => round($avgRTP, 2), // Используем пересчитанный avgRTP
      'avg_profit_per_case' => $totalCases > 0 ? round($totalProfit / $totalCases, 0) : 0,
      'avg_win_per_open' => round($avgWinPerOpen, 0),
      'avg_spent_per_open' => round($avgSpentPerOpen, 0),
      'profitable_cases' => $profitableCases,
      'unprofitable_cases' => $unprofitableCases,
      'profitability_rate' => round($profitabilityRate, 2),
      'cases_with_high_rtp' => $casesWithHighRTP, // RTP > target_rtp (выше целевого)
      'cases_with_low_rtp' => $casesWithLowRTP,  // RTP < 90% (проблемные)
    ];

    return [
      'success' => true,
      'statistics' => [
        // Общая статистика
        'total_cases' => $totalCases,
        'active_cases' => $activeCases,
        'inactive_cases' => $inactiveCases,
        'auto_disabled_cases' => $autoDisabledCases,
        'total_opened' => $totalOpened,
        'total_spent' => $totalSpent,
        'total_won' => $totalWon,
        'total_profit' => $totalProfit,
        'overall_rtp' => round($overallRTP, 2),
        
        // Статистика по RTP
        'avg_rtp' => round($avgRTP ?? 0, 2),
        'cases_above_target' => $casesAboveTarget,
        'cases_below_target' => $casesBelowTarget,
        'cases_in_target' => $casesInTarget,
        
        // Статистика по периодам
        'periods' => [
          'today' => $todayStats,
          'last_7_days' => $weekStats,
          'last_30_days' => $monthStats,
        ],
        
        // Топы
        'top_by_opens' => $topByOpens,
        'top_by_profit' => $topByProfit,
        'top_by_loss' => $topByLoss,
        'problematic_cases' => $problematicCases,
        'worst_by_rtp' => $worstByRTP, // Топ кейсов с НИЗКИМ RTP (проблемные)
        'best_by_rtp' => $bestByRTP,   // Топ кейсов с ВЫСОКИМ RTP (прибыльные)
        
        // Статистика по категориям
        'category_stats' => $categoryStats,
        
        // Детальная статистика по всем кейсам
        'all_cases' => $allCasesStats,
        
        // Средние показатели
        'averages' => $avgStats,
      ]
    ];
  }

  /**
   * Получить статистику за период
   */
  private function getPeriodStats(Carbon $from, Carbon $to): array
  {
    $lives = Lives::where('from_where', 'BOX')
      ->whereNotNull('box_id')
      ->whereBetween('created_at', [$from, $to])
      ->get();

    $totalOpened = $lives->count();
    
    // Считаем потраченное (цена кейса * количество открытий)
    $boxIds = $lives->pluck('box_id')->unique();
    $totalSpent = 0;
    foreach ($boxIds as $boxId) {
      $box = Boxes::find($boxId);
      if ($box) {
        $openedCount = $lives->where('box_id', $boxId)->count();
        $totalSpent += $box->price * $openedCount;
      }
    }
    
    $totalWon = $lives->sum('price');
    $profit = $totalSpent - $totalWon;
    // RTP = (Потрачено / Выиграно) * 100
    $rtp = ($totalWon > 0 && $totalSpent > 0) ? round(($totalSpent / $totalWon) * 100, 2) : 0;
    
    // Статистика по кейсам за период
    $casesStats = $lives->groupBy('box_id')
      ->map(function ($group, $boxId) {
        $box = Boxes::find($boxId);
        if (!$box) return null;
        
        $opened = $group->count();
        $spent = $box->price * $opened;
        $won = $group->sum('price');
        
        return [
          'box_id' => $boxId,
          'box_name' => $box->name,
          'opened' => $opened,
          'spent' => $spent,
          'won' => $won,
          'profit' => $spent - $won,
        ];
      })
      ->filter()
      ->sortByDesc('opened')
      ->take(10)
      ->values();

    return [
      'total_opened' => $totalOpened,
      'total_spent' => $totalSpent,
      'total_won' => $totalWon,
      'profit' => $profit,
      'rtp' => round($rtp, 2),
      'top_cases' => $casesStats,
    ];
  }

  /**
   * Вспомогательный метод для расчета RTP с ограничениями
   */
  private function calculateRTP(Boxes $box): float
  {
    // RTP = (Потрачено / Выиграно) * 100
    $rtp = ($box->total_won > 0 && $box->total_spent > 0)
      ? round(($box->total_spent / $box->total_won) * 100, 2)
      : ($box->target_rtp ?? 95);
    
    // Ограничиваем максимальным порогом
    if ($rtp > $box->max_rtp) {
      $rtp = $box->max_rtp;
    }
    
    // Ограничиваем минимальным порогом
    if ($rtp < $box->min_rtp) {
      $rtp = $box->min_rtp;
    }
    
    return $rtp;
  }
}
