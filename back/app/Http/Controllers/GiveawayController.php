<?php

namespace App\Http\Controllers;

use App\Models\Giveaway;
use App\Models\GiveawayParticipant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use Laravel\Sanctum\PersonalAccessToken;

class GiveawayController extends Controller
{
    /**
     * Получить список активных розыгрышей
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Опциональная аутентификация - проверяем токен, если он есть
            $user = null;
            if ($request->bearerToken()) {
                try {
                    $user = PersonalAccessToken::findToken($request->bearerToken())?->tokenable;
                } catch (\Exception $e) {
                    // Игнорируем ошибки токена для неавторизованных пользователей
                }
            }
            
            $giveaways = Giveaway::with(['item', 'participants.user'])
                ->where('status', 'IN PROCESS')
                ->where('finished_at', '>', now())
                ->orderBy('finished_at', 'asc')
                ->get()
                ->map(function ($giveaway) use ($user) {
                    if (!$giveaway->item) {
                        return null;
                    }
                    
                    return [
                        'id' => $giveaway->id,
                        'item' => [
                            'id' => $giveaway->item->id,
                            'name' => $giveaway->item->title,
                            'skin_name' => $giveaway->item->skin_name,
                            'weapon' => $giveaway->item->weapon,
                            'quality' => $giveaway->item->quality,
                            'image' => $giveaway->item->image,
                            'price' => $giveaway->item->steam_price,
                            'rarity' => $giveaway->item->rarity,
                        ],
                        'type' => $giveaway->type,
                        'started_at' => $giveaway->started_at->toIso8601String(),
                        'finished_at' => $giveaway->finished_at->toIso8601String(),
                        'min_deposit' => $giveaway->min_deposit,
                        'participants_count' => $giveaway->participants->count(),
                        'is_participating' => $user ? $giveaway->hasParticipant($user->id) : false,
                        'time_left' => $giveaway->finished_at->diffInSeconds(now()),
                    ];
                })
                ->filter();

            return response()->json([
                'success' => true,
                'giveaways' => $giveaways,
            ]);
        } catch (\Exception $e) {
            Log::channel('giveaway')->error('Error fetching giveaways', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении розыгрышей',
            ], 500);
        }
    }


    public function join(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходима авторизация',
                ], 401);
            }

            $giveaway = Giveaway::with('item')->find($id);

            if (!$giveaway) {
                return response()->json([
                    'success' => false,
                    'message' => 'Розыгрыш не найден',
                ], 404);
            }

            // Проверяем, активен ли розыгрыш
            if (!$giveaway->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Розыгрыш уже завершен или неактивен',
                ], 400);
            }

            // Определяем период проверки депозита в зависимости от типа розыгрыша
            $periodMap = [
                'hourly' => ['hours' => 1, 'text' => 'последний час'],
                'daily' => ['hours' => 24, 'text' => 'последние 24 часа'],
                'weekly' => ['hours' => 168, 'text' => 'последние 7 дней'], // 7 * 24
            ];
            
            $period = $periodMap[$giveaway->type] ?? ['hours' => 24, 'text' => 'последние 24 часа'];
            $periodStart = now()->subHours($period['hours']);
            
            // Проверяем, достаточно ли депозита у пользователя за указанный период
            $userTotalDeposit = Payment::where('user_id', $user->id)
                ->where('status', Payment::PAID)
                ->where('created_at', '>=', $periodStart)
                ->sum('amount');
            
            if ($userTotalDeposit < $giveaway->min_deposit) {
                return response()->json([
                    'success' => false,
                    'message' => "Для участия необходимо пополнить баланс на {$giveaway->min_deposit}₽ за {$period['text']}",
                ], 400);
            }

            // Проверяем, не участвует ли уже пользователь
            if ($giveaway->hasParticipant($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы уже участвуете в этом розыгрыше',
                ], 400);
            }

            // Добавляем участника
            GiveawayParticipant::create([
                'giveaway_id' => $giveaway->id,
                'user_id' => $user->id,
            ]);

            Log::channel('giveaway')->info('User joined giveaway', [
                'user_id' => $user->id,
                'giveaway_id' => $giveaway->id,
                'type' => $giveaway->type,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Вы успешно участвуете в розыгрыше!',
                'participants_count' => $giveaway->participants()->count(),
            ]);
        } catch (\Exception $e) {
            Log::channel('giveaway')->error('Error joining giveaway', [
                'user_id' => $request->user() ? $request->user()->id : null,
                'giveaway_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при участии в розыгрыше',
            ], 500);
        }
    }

    /**
     * История завершенных розыгрышей
     */
    public function winners(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $type = $request->get('type'); // фильтр по типу (необязательно)

            $query = Giveaway::with(['item', 'winner'])
                ->where('status', 'FINISHED')
                ->whereNotNull('winner_id')
                ->orderBy('finished_at', 'desc');

            if ($type && in_array($type, ['hourly', 'daily', 'weekly'])) {
                $query->where('type', $type);
            }

            $giveaways = $query->paginate($perPage);

            $data = $giveaways->map(function ($giveaway) {
                if (!$giveaway->item || !$giveaway->winner) {
                    return null;
                }
                
                return [
                    'id' => $giveaway->id,
                    'item' => [
                        'id' => $giveaway->item->id,
                        'name' => $giveaway->item->title,
                        'image' => $giveaway->item->image,
                        'price' => $giveaway->item->steam_price,
                        'rarity' => $giveaway->item->rarity,
                    ],
                    'type' => $giveaway->type,
                    'finished_at' => $giveaway->finished_at->toIso8601String(),
                    'winner' => [
                        'id' => $giveaway->winner->id,
                        'username' => $giveaway->winner->username,
                        'avatar' => $giveaway->winner->avatar ?? null,
                    ],
                    'participants_count' => $giveaway->participants()->count(),
                ];
            });

            return response()->json([
                'success' => true,
                'winners' => $data->filter()->values(),
                'pagination' => [
                    'current_page' => $giveaways->currentPage(),
                    'last_page' => $giveaways->lastPage(),
                    'per_page' => $giveaways->perPage(),
                    'total' => $giveaways->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::channel('giveaway')->error('Error fetching winners', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении истории розыгрышей',
            ], 500);
        }
    }

    /**
     * Получить конкретный розыгрыш по ID
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $giveaway = Giveaway::with(['item', 'participants.user', 'winner'])->find($id);

            if (!$giveaway) {
                return response()->json([
                    'success' => false,
                    'message' => 'Розыгрыш не найден',
                ], 404);
            }

            if (!$giveaway->item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Предмет розыгрыша не найден',
                ], 404);
            }

            $userId = $request->user() ? $request->user()->id : null;

            $data = [
                'id' => $giveaway->id,
                'item' => [
                    'id' => $giveaway->item->id,
                    'name' => $giveaway->item->title,
                    'image' => $giveaway->item->image,
                    'price' => $giveaway->item->steam_price,
                    'rarity' => $giveaway->item->rarity,
                ],
                'type' => $giveaway->type,
                'started_at' => $giveaway->started_at->toIso8601String(),
                'finished_at' => $giveaway->finished_at->toIso8601String(),
                'min_deposit' => $giveaway->min_deposit,
                'status' => $giveaway->status,
                'participants_count' => $giveaway->participants->count(),
                'is_participating' => $userId ? $giveaway->hasParticipant($userId) : false,
            ];

            if ($giveaway->status === 'IN PROCESS' && $giveaway->finished_at > now()) {
                $data['time_left'] = $giveaway->finished_at->diffInSeconds(now());
            }

            if ($giveaway->winner) {
                $data['winner'] = [
                    'id' => $giveaway->winner->id,
                    'username' => $giveaway->winner->username,
                    'avatar' => $giveaway->winner->avatar ?? null,
                ];
            }

            return response()->json([
                'success' => true,
                'giveaway' => $data,
            ]);
        } catch (\Exception $e) {
            Log::channel('giveaway')->error('Error fetching giveaway details', [
                'giveaway_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении данных розыгрыша',
            ], 500);
        }
    }
}

