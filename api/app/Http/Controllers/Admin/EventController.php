<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventPrize;
use App\Models\Items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Получить список всех ивентов
     */
    public function index()
    {
        $events = Event::with('prizes')->orderBy('id', 'desc')->get();
        
        return datatables($events->map(function($event) {
            return [
                'id' => $event->id,
                'name' => $event->name,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,
                'is_active' => $event->is_active,
                'prizes_count' => $event->prizes->count(),
            ];
        }))->toJson();
    }

    /**
     * Получить конкретный ивент с призами
     */
    public function get(Request $request): array
    {
        $id = $request->id;
        $event = Event::with(['prizes.item'])->find($id);
        
        if (!$event) {
            return ['success' => false, 'message' => 'Ивент не найден'];
        }

        // Формируем призы для удобства на фронте
        $prizes = [];
        foreach ($event->prizes as $prize) {
            $prizes[$prize->position] = [
                'id' => $prize->id,
                'position' => $prize->position,
                'item_id' => $prize->item_id,
                'item' => $prize->item ? [
                    'id' => $prize->item->id,
                    'title' => $prize->item->title,
                    'image' => $prize->item->image,
                    'steam_price' => $prize->item->steam_price,
                ] : null,
                'min_price' => $prize->min_price,
                'max_price' => $prize->max_price,
            ];
        }

        return [
            'success' => true,
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'start_date' => $event->start_date?->format('Y-m-d H:i:s'),
                'end_date' => $event->end_date?->format('Y-m-d H:i:s'),
                'is_active' => $event->is_active,
                'prizes' => $prizes,
            ],
        ];
    }

    /**
     * Создать новый ивент
     */
    public function create(Request $request): array
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'prizes' => 'nullable|array',
        ]);

        $event = Event::create([
            'name' => $request->name,
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'is_active' => $request->is_active ?? true,
            'rewards' => [],
        ]);

        // Если переданы призы, создаем их
        if ($request->prizes && is_array($request->prizes)) {
            foreach ($request->prizes as $position => $prizeData) {
                $position = (int) $position;
                if ($position >= 1 && $position <= 50 && isset($prizeData['item_id'])) {
                    EventPrize::create([
                        'event_id' => $event->id,
                        'position' => $position,
                        'item_id' => $prizeData['item_id'],
                        'min_price' => null,
                        'max_price' => null,
                    ]);
                }
            }
        } else {
            // Если призы не переданы, создаем призы по умолчанию (1-50 места)
            $this->createDefaultPrizes($event->id);
        }

        Log::channel('api_event')->info('Event created by admin', [
            'event_id' => $event->id,
            'name' => $event->name,
            'prizes_count' => $event->prizes()->count(),
        ]);

        return [
            'success' => true,
            'message' => 'Ивент создан',
            'event_id' => $event->id,
        ];
    }

    /**
     * Обновить ивент
     */
    public function update(Request $request): array
    {
        $id = $request->id;
        $event = Event::find($id);

        if (!$event) {
            return ['success' => false, 'message' => 'Ивент не найден'];
        }

        $event->update([
            'name' => $request->name ?? $event->name,
            'start_date' => $request->start_date ? Carbon::parse($request->start_date) : $event->start_date,
            'end_date' => $request->end_date ? Carbon::parse($request->end_date) : $event->end_date,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : $event->is_active,
        ]);

        Log::channel('api_event')->info('Event updated by admin', [
            'event_id' => $event->id,
        ]);

        return [
            'success' => true,
            'message' => 'Ивент обновлен',
        ];
    }

    /**
     * Удалить ивент
     */
    public function delete(Request $request): array
    {
        $id = $request->id;
        $event = Event::find($id);

        if (!$event) {
            return ['success' => false, 'message' => 'Ивент не найден'];
        }

        $event->delete();

        Log::channel('api_event')->info('Event deleted by admin', [
            'event_id' => $id,
        ]);

        return [
            'success' => true,
            'message' => 'Ивент удален',
        ];
    }

    /**
     * Обновить приз за место (только конкретный предмет)
     */
    public function updatePrize(Request $request): array
    {
        $request->validate([
            'event_id' => 'required|exists:event,id',
            'position' => 'required|integer|min:1|max:50',
            'item_id' => 'required|exists:items,id',
        ]);

        $prize = EventPrize::where('event_id', $request->event_id)
            ->where('position', $request->position)
            ->first();

        if (!$prize) {
            $prize = EventPrize::create([
                'event_id' => $request->event_id,
                'position' => $request->position,
                'item_id' => $request->item_id,
                'min_price' => null,
                'max_price' => null,
            ]);
        } else {
            $prize->update([
                'item_id' => $request->item_id,
                'min_price' => null,
                'max_price' => null,
            ]);
        }

        Log::channel('api_event')->info('Event prize updated by admin', [
            'event_id' => $request->event_id,
            'position' => $request->position,
            'item_id' => $request->item_id,
        ]);

        return [
            'success' => true,
            'message' => 'Приз обновлен',
        ];
    }

    /**
     * Получить список предметов для выбора приза
     * Поддерживает фильтрацию по диапазону цен для конкретного места
     */
    public function getItems(Request $request): array
    {
        $min = $request->min ?? 0;
        $max = $request->max ?? null;
        $search = $request->search ?? '';
        $position = $request->position ?? null;

        // Если указано место, используем рекомендуемые диапазоны цен
        if ($position) {
            $recommendedRanges = [
                1 => ['min' => 4500000, 'max' => 6000000],  // 45000-60000 руб
                2 => ['min' => 3500000, 'max' => 4000000],  // 35000-40000 руб
                3 => ['min' => 2500000, 'max' => 3000000],  // 25000-30000 руб
            ];

            if (isset($recommendedRanges[$position])) {
                $min = $recommendedRanges[$position]['min'];
                $max = $recommendedRanges[$position]['max'];
            } else {
                // Для остальных мест вычисляем диапазон
                $basePrice = 2000000 - (($position - 4) * 30000);
                $min = max($basePrice - 100000, 100000);
                $max = max($basePrice + 100000, 200000);
            }
        }

        $query = Items::query()
            ->whereNotNull('image')
            ->where('steam_price', '>=', $min);

        if ($max) {
            $query->where('steam_price', '<=', $max);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('weapon', 'like', "%{$search}%")
                  ->orWhere('skin_name', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('steam_price', 'desc')
            ->limit(100)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'image' => $item->image,
                    'weapon' => $item->weapon,
                    'skin_name' => $item->skin_name,
                    'rarity' => $item->rarity,
                    'steam_price' => $item->steam_price,
                ];
            });

        return [
            'success' => true,
            'items' => $items,
            'recommended_range' => $position ? ['min' => $min, 'max' => $max] : null,
        ];
    }

    /**
     * Создать призы по умолчанию для ивента (конкретные предметы)
     */
    public function createDefaultPrizes(int $eventId): void
    {
        $priceRanges = [
            1 => ['min' => 4500000, 'max' => 6000000],  // 45000-60000 руб
            2 => ['min' => 3500000, 'max' => 4000000],  // 35000-40000 руб
            3 => ['min' => 2500000, 'max' => 3000000],  // 25000-30000 руб
        ];

        // Для остальных мест создаем убывающую прогрессию
        for ($i = 4; $i <= 50; $i++) {
            $basePrice = 2000000 - (($i - 4) * 30000); // Начинаем с 20000 и уменьшаем на 300
            $priceRanges[$i] = [
                'min' => max($basePrice - 100000, 100000), // Минимум 1000 руб
                'max' => max($basePrice + 100000, 200000), // Максимум на 1000 больше минимума
            ];
        }

        // Для каждого места выбираем конкретный предмет из диапазона
        foreach ($priceRanges as $position => $range) {
            $item = Items::where('steam_price', '>=', $range['min'])
                ->where('steam_price', '<=', $range['max'])
                ->whereNotNull('image')
                ->inRandomOrder()
                ->first();

            if ($item) {
                EventPrize::create([
                    'event_id' => $eventId,
                    'position' => $position,
                    'item_id' => $item->id,
                    'min_price' => null,
                    'max_price' => null,
                ]);
            }
        }
    }
}
