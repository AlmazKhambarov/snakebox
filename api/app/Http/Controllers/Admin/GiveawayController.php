<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Giveaway;
use App\Models\Items;
use App\Models\GiveawayParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class GiveawayController extends Controller
{
    /**
     * Получить список всех розыгрышей для админки
     */
    public function index()
    {
        return datatables(Giveaway::with(['item', 'winner', 'participants'])->orderBy('id', 'desc'))->toJson();
    }

    /**
     * Получить конкретный розыгрыш
     */
    public function get(Request $request): array
    {
        $id = $request->id;

        $giveaway = Giveaway::with(['item', 'winner', 'participants.user'])->find($id);
        if (!$giveaway) {
            return ['success' => false, 'message' => 'Розыгрыш не найден'];
        }

        return [
            'success' => true,
            'giveaway' => [
                'id' => $giveaway->id,
                'drop_id' => $giveaway->drop_id,
                'item' => $giveaway->item ? [
                    'id' => $giveaway->item->id,
                    'title' => $giveaway->item->title,
                    'image' => $giveaway->item->image,
                    'steam_price' => $giveaway->item->steam_price,
                ] : null,
                'started_at' => $giveaway->started_at?->format('Y-m-d H:i:s'),
                'finished_at' => $giveaway->finished_at?->format('Y-m-d H:i:s'),
                'min_deposit' => $giveaway->min_deposit,
                'type' => $giveaway->type,
                'status' => $giveaway->status,
                'winner_id' => $giveaway->winner_id,
                'winner' => $giveaway->winner,
                'participants' => $giveaway->participants->map(function ($participant) {
                    return [
                        'id' => $participant->id,
                        'user_id' => $participant->user_id,
                        'user' => $participant->user ? [
                            'id' => $participant->user->id,
                            'username' => $participant->user->username,
                            'avatar' => $participant->user->avatar,
                        ] : null,
                        'created_at' => $participant->created_at?->format('Y-m-d H:i:s'),
                    ];
                }),
            ]
        ];
    }

    /**
     * Создать новый розыгрыш
     */
    public function create(Request $request): array
    {
        $request->validate([
            'drop_id' => 'required|exists:items,id',
            'type' => 'required|in:hourly,daily,weekly',
            'min_deposit' => 'required|numeric|min:0',
            'started_at' => 'nullable|date',
            'finished_at' => 'required|date|after:started_at',
        ]);

        $item = Items::find($request->drop_id);
        if (!$item) {
            return ['success' => false, 'message' => 'Предмет не найден'];
        }

        $giveaway = Giveaway::create([
            'drop_id' => $request->drop_id,
            'started_at' => $request->started_at ?? now(),
            'finished_at' => $request->finished_at,
            'min_deposit' => $request->min_deposit,
            'type' => $request->type,
            'status' => 'IN PROCESS',
        ]);

        Log::channel('admin_giveaway')->info('Giveaway created by admin', [
            'giveaway_id' => $giveaway->id,
            'type' => $giveaway->type,
        ]);

        return ['success' => true, 'message' => 'Розыгрыш создан', 'giveaway' => $giveaway];
    }

    /**
     * Обновить розыгрыш
     */
    public function update(Request $request): array
    {
        $id = $request->id;

        $request->validate([
            'drop_id' => 'required|exists:items,id',
            'type' => 'required|in:hourly,daily,weekly',
            'min_deposit' => 'required|numeric|min:0',
            'started_at' => 'nullable|date',
            'finished_at' => 'required|date',
            'status' => 'required|in:IN PROCESS,FINISHED,FAILED',
        ]);

        $giveaway = Giveaway::find($id);
        if (!$giveaway) {
            return ['success' => false, 'message' => 'Розыгрыш не найден'];
        }

        $giveaway->update([
            'drop_id' => $request->drop_id,
            'started_at' => $request->started_at ?? $giveaway->started_at,
            'finished_at' => $request->finished_at,
            'min_deposit' => $request->min_deposit,
            'type' => $request->type,
            'status' => $request->status,
        ]);

        Log::channel('admin_giveaway')->info('Giveaway updated by admin', [
            'giveaway_id' => $giveaway->id,
        ]);

        return ['success' => true, 'message' => 'Розыгрыш обновлен'];
    }

    /**
     * Удалить розыгрыш
     */
    public function delete(Request $request): array
    {
        $id = $request->id;

        $giveaway = Giveaway::find($id);
        if (!$giveaway) {
            return ['success' => false, 'message' => 'Розыгрыш не найден'];
        }

        // Удаляем всех участников
        $giveaway->participants()->delete();
        
        // Удаляем розыгрыш
        $giveaway->delete();

        Log::channel('admin_giveaway')->info('Giveaway deleted by admin', [
            'giveaway_id' => $id,
        ]);

        return ['success' => true, 'message' => 'Розыгрыш удален'];
    }

    /**
     * Выбрать победителя вручную
     */
    public function selectWinner(Request $request): array
    {
        $id = $request->id;
        $userId = $request->user_id;

        $giveaway = Giveaway::find($id);
        if (!$giveaway) {
            return ['success' => false, 'message' => 'Розыгрыш не найден'];
        }

        // Если указан user_id, выбираем конкретного победителя
        if ($userId) {
            $participant = $giveaway->participants()->where('user_id', $userId)->first();
            
            if (!$participant) {
                return ['success' => false, 'message' => 'Пользователь не участвует в розыгрыше'];
            }

            $giveaway->update([
                'winner_id' => $userId,
                'status' => 'FINISHED'
            ]);

            Log::channel('admin_giveaway')->info('Winner selected manually by admin', [
                'giveaway_id' => $giveaway->id,
                'winner_id' => $userId,
            ]);

            return ['success' => true, 'message' => 'Победитель выбран'];
        }

        // Если не указан user_id, выбираем случайного
        $winner = $giveaway->selectWinner();
        
        if (!$winner) {
            return ['success' => false, 'message' => 'Не удалось выбрать победителя (нет участников)'];
        }

        Log::channel('admin_giveaway')->info('Winner selected randomly by admin', [
            'giveaway_id' => $giveaway->id,
            'winner_id' => $winner->id,
        ]);

        return ['success' => true, 'message' => 'Победитель выбран случайным образом'];
    }

    /**
     * Получить список участников розыгрыша
     */
    public function participants(Request $request): array
    {
        $id = $request->id;

        $giveaway = Giveaway::with('participants.user')->find($id);
        if (!$giveaway) {
            return ['success' => false, 'message' => 'Розыгрыш не найден'];
        }

        $participants = $giveaway->participants->map(function ($participant) {
            return [
                'id' => $participant->id,
                'user_id' => $participant->user_id,
                'user' => $participant->user ? [
                    'id' => $participant->user->id,
                    'username' => $participant->user->username,
                    'avatar' => $participant->user->avatar,
                ] : null,
                'created_at' => $participant->created_at?->format('Y-m-d H:i:s'),
            ];
        });

        return [
            'success' => true,
            'participants' => $participants
        ];
    }

    /**
     * Получить список предметов для выбора
     */
    public function items(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        
        $items = Items::when($search, function ($query, $search) {
            return $query->where('title', 'like', "%{$search}%");
        })
        ->orderBy('steam_price', 'desc')
        ->limit(50)
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'image' => $item->image,
                'steam_price' => $item->steam_price,
                'rarity' => $item->rarity,
            ];
        });

        return response()->json([
            'success' => true,
            'items' => $items
        ]);
    }
}

