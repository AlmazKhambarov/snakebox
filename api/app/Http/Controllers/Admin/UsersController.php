<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lives;
use App\Models\Payment;
use App\Models\User;
use App\Models\ReferralEarning;
use App\Models\BonusClaim;
use App\Models\PromocodeUse;
use App\Models\Boxes;
use App\Models\UserIpHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class UsersController extends Controller
{
    public function get()
    {
        return datatables(User::query())->toJson();
    }

    public function user(Request $request)
    {
        $user = User::query()->find($request->id);
        if (!$user) return ['success' => false, 'message' => 'Пользователь не найден'];

        // ========== ДЕПОЗИТЫ ==========
        $totalDeposited = $user->total_deposited ?? 0;
        $depositsCount = Payment::where('user_id', $user->id)
            ->where('status', Payment::PAID)
            ->count();
        $depositsSum = Payment::where('user_id', $user->id)
            ->where('status', Payment::PAID)
            ->sum('amount');
        $avgDeposit = $depositsCount > 0 ? round($depositsSum / $depositsCount, 2) : 0;
        $maxDeposit = Payment::where('user_id', $user->id)
            ->where('status', Payment::PAID)
            ->max('amount') ?? 0;
        $minDeposit = Payment::where('user_id', $user->id)
            ->where('status', Payment::PAID)
            ->min('amount') ?? 0;
        
        // Депозиты по периодам
        $now = Carbon::now();
        $todayDeposits = Payment::where('user_id', $user->id)
            ->where('status', Payment::PAID)
            ->whereDate('created_at', $now->toDateString())
            ->sum('amount') ?? 0;
        $weekDeposits = Payment::where('user_id', $user->id)
            ->where('status', Payment::PAID)
            ->where('created_at', '>=', $now->copy()->subDays(7))
            ->sum('amount') ?? 0;
        $monthDeposits = Payment::where('user_id', $user->id)
            ->where('status', Payment::PAID)
            ->where('created_at', '>=', $now->copy()->subDays(30))
            ->sum('amount') ?? 0;
        
        $pendingDeposits = Payment::where('user_id', $user->id)
            ->where('status', Payment::PENDING)
            ->sum('amount') ?? 0;
        $failedDeposits = Payment::where('user_id', $user->id)
            ->where('status', Payment::FAILED)
            ->sum('amount') ?? 0;

        // ========== ВЫВОДЫ ==========
        $withdrawnItems = Lives::where('user_id', $user->id)
            ->whereIn('status', [Lives::WITHDRAWN, Lives::SENDING, Lives::WAIT, Lives::ORDER_READY])
            ->get();
        $totalWithdrawn = $withdrawnItems->sum('price');
        $withdrawsCount = $withdrawnItems->count();
        $avgWithdraw = $withdrawsCount > 0 ? round($totalWithdrawn / $withdrawsCount, 0) : 0;
        $maxWithdraw = $withdrawnItems->max('price') ?? 0;
        
        // Выводы по статусам
        $withdrawnAmount = Lives::where('user_id', $user->id)
            ->where('status', Lives::WITHDRAWN)
            ->sum('price') ?? 0;
        $sendingAmount = Lives::where('user_id', $user->id)
            ->where('status', Lives::SENDING)
            ->sum('price') ?? 0;
        $waitAmount = Lives::where('user_id', $user->id)
            ->where('status', Lives::WAIT)
            ->sum('price') ?? 0;
        $orderReadyAmount = Lives::where('user_id', $user->id)
            ->where('status', Lives::ORDER_READY)
            ->sum('price') ?? 0;

        // ========== ИНВЕНТАРЬ ==========
        $inventoryItems = Lives::where('user_id', $user->id)
            ->where('status', Lives::OPENED)
            ->with('item')
            ->get();
        $inventoryCount = $inventoryItems->count();
        $inventoryValue = $inventoryItems->sum('price');
        $avgItemPrice = $inventoryCount > 0 ? round($inventoryValue / $inventoryCount, 0) : 0;
        $maxItemPrice = $inventoryItems->max('price') ?? 0;
        $minItemPrice = $inventoryItems->min('price') ?? 0;
        
        // Топ предметы в инвентаре
        $topInventoryItems = $inventoryItems->sortByDesc('price')->take(10)->values()->map(function($item) {
            return [
                'id' => $item->id,
                'item_name' => $item->item->title ?? 'Неизвестно',
                'price' => $item->price,
                'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
            ];
        });

        // ========== ПРОДАЖИ ==========
        $soldItems = Lives::where('user_id', $user->id)
            ->where('status', Lives::SELL)
            ->get();
        $soldCount = $soldItems->count();
        $soldAmount = $soldItems->sum('price');

        // ========== ОТКРЫТИЯ КЕЙСОВ ==========
        $openedCases = Lives::where('user_id', $user->id)
            ->where('from_where', Lives::CASE_TYPE)
            ->whereNotNull('box_id')
            ->get();
        $casesOpenedCount = $openedCases->count();
        $casesSpent = 0;
        foreach ($openedCases as $live) {
            $box = Boxes::find($live->box_id);
            if ($box) {
                $casesSpent += $box->price;
            }
        }
        $casesWon = $openedCases->sum('price');
        $casesProfit = $casesWon - $casesSpent;
        $casesRTP = $casesSpent > 0 ? round(($casesWon / $casesSpent) * 100, 2) : 0;
        
        // Статистика по кейсам
        $casesByBox = $openedCases->groupBy('box_id')->map(function($group, $boxId) {
            $box = Boxes::find($boxId);
            $spent = $box ? $box->price * $group->count() : 0;
            $won = $group->sum('price');
            return [
                'box_id' => $boxId,
                'box_name' => $box->name ?? 'Неизвестно',
                'opened' => $group->count(),
                'spent' => $spent,
                'won' => $won,
                'profit' => $won - $spent,
            ];
        })->sortByDesc('opened')->take(10)->values();

        // ========== РАЗНИЦА ДЕПОЗИТ/ВЫВОД ==========
        $depositWithdrawDiff = $totalDeposited - $totalWithdrawn;
        $depositWithdrawPercent = $totalDeposited > 0 
            ? round(($totalWithdrawn / $totalDeposited) * 100, 2) 
            : 0;

        // ========== РЕФЕРАЛЬНАЯ СИСТЕМА ==========
        $referralsCount = $user->referrals()->count();
        $referralsTotalDeposited = $user->referrals()->sum('total_deposited') ?? 0;
        $referralEarnings = ReferralEarning::where('user_id', $user->id)->get();
        $referralEarningsTotal = $referralEarnings->sum('amount') ?? 0;
        $referralEarningsCount = $referralEarnings->count();
        $referralBalance = $user->referral_balance ?? 0;
        $totalEarned = $user->total_earned ?? 0;

        // ========== БОНУСЫ ==========
        $bonusClaims = BonusClaim::where('user_id', $user->id)->get();
        $bonusTotal = $bonusClaims->sum('amount') ?? 0;
        $bonusCount = $bonusClaims->count();
        $bonusByType = $bonusClaims->groupBy('bonus_type')->map(function($group) {
            return [
                'type' => $group->first()->bonus_type,
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ];
        })->values();

        // ========== ПРОМОКОДЫ ==========
        $promocodeUses = PromocodeUse::where('user_id', $user->id)->get();
        $promocodeBonusTotal = $promocodeUses->sum('bonus_amount') ?? 0;
        $promocodeUsesCount = $promocodeUses->count();

        // ========== ОБЩАЯ СТАТИСТИКА ==========
        $totalBet = $user->total_bet ?? 0;
        $balance = $user->balance ?? 0;
        $eventPoints = $user->event_points ?? 0;
        
        // Общий профит/убыток
        $totalProfit = $casesWon + $soldAmount + $totalWithdrawn - $casesSpent - $totalDeposited;
        
        // Средний чек
        $avgCheck = $casesOpenedCount > 0 ? round($casesSpent / $casesOpenedCount, 0) : 0;
        
        // Активность
        $firstActivity = Lives::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->first();
        $lastActivity = Lives::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();
        $firstDeposit = Payment::where('user_id', $user->id)
            ->where('status', Payment::PAID)
            ->orderBy('created_at', 'asc')
            ->first();
        $lastDeposit = Payment::where('user_id', $user->id)
            ->where('status', Payment::PAID)
            ->orderBy('created_at', 'desc')
            ->first();

        // Статистика по дням
        $activityByDays = Lives::where('user_id', $user->id)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(price) as total_price')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        // Статистика по месяцам
        $activityByMonths = Lives::where('user_id', $user->id)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count, SUM(price) as total_price')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return [
            'success' => true,
            'user' => $user,
            'statistics' => [
                // Финансы
                'finances' => [
                    'balance' => $balance,
                    'event_points' => $eventPoints,
                    'total_deposited' => $totalDeposited,
                    'total_withdrawn' => $totalWithdrawn,
                    'deposit_withdraw_diff' => $depositWithdrawDiff,
                    'deposit_withdraw_percent' => $depositWithdrawPercent,
                    'total_profit' => $totalProfit,
                    'total_bet' => $totalBet,
                ],
                
                // Депозиты
                'deposits' => [
                    'total' => $totalDeposited,
                    'count' => $depositsCount,
                    'sum' => $depositsSum,
                    'avg' => $avgDeposit,
                    'max' => $maxDeposit,
                    'min' => $minDeposit,
                    'today' => $todayDeposits,
                    'week' => $weekDeposits,
                    'month' => $monthDeposits,
                    'pending' => $pendingDeposits,
                    'failed' => $failedDeposits,
                    'first_deposit' => $firstDeposit ? [
                        'date' => $firstDeposit->created_at->format('Y-m-d H:i:s'),
                        'amount' => $firstDeposit->amount,
                    ] : null,
                    'last_deposit' => $lastDeposit ? [
                        'date' => $lastDeposit->created_at->format('Y-m-d H:i:s'),
                        'amount' => $lastDeposit->amount,
                    ] : null,
                ],
                
                // Выводы
                'withdraws' => [
                    'total' => $totalWithdrawn,
                    'count' => $withdrawsCount,
                    'avg' => $avgWithdraw,
                    'max' => $maxWithdraw,
                    'by_status' => [
                        'withdrawn' => $withdrawnAmount,
                        'sending' => $sendingAmount,
                        'wait' => $waitAmount,
                        'order_ready' => $orderReadyAmount,
                    ],
                ],
                
                // Инвентарь
                'inventory' => [
                    'count' => $inventoryCount,
                    'value' => $inventoryValue,
                    'avg_price' => $avgItemPrice,
                    'max_price' => $maxItemPrice,
                    'min_price' => $minItemPrice,
                    'top_items' => $topInventoryItems,
                ],
                
                // Продажи
                'sales' => [
                    'count' => $soldCount,
                    'amount' => $soldAmount,
                ],
                
                // Кейсы
                'cases' => [
                    'opened_count' => $casesOpenedCount,
                    'spent' => $casesSpent,
                    'won' => $casesWon,
                    'profit' => $casesProfit,
                    'rtp' => $casesRTP,
                    'avg_check' => $avgCheck,
                    'by_box' => $casesByBox,
                ],
                
                // Реферальная система
                'referral' => [
                    'referrals_count' => $referralsCount,
                    'referrals_total_deposited' => $referralsTotalDeposited,
                    'earnings_total' => $referralEarningsTotal,
                    'earnings_count' => $referralEarningsCount,
                    'referral_balance' => $referralBalance,
                    'total_earned' => $totalEarned,
                ],
                
                // Бонусы
                'bonuses' => [
                    'total' => $bonusTotal,
                    'count' => $bonusCount,
                    'by_type' => $bonusByType,
                ],
                
                // Промокоды
                'promocodes' => [
                    'bonus_total' => $promocodeBonusTotal,
                    'uses_count' => $promocodeUsesCount,
                ],
                
                // Активность
                'activity' => [
                    'first_activity' => $firstActivity ? $firstActivity->created_at->format('Y-m-d H:i:s') : null,
                    'last_activity' => $lastActivity ? $lastActivity->created_at->format('Y-m-d H:i:s') : null,
                    'by_days' => $activityByDays,
                    'by_months' => $activityByMonths,
                ],
            ],
            'is_banned' => $user->is_banned ?? false,
        ];
    }

    public function save(Request $request)
    {
        if ($request->balance < 0) return ['success' => false, 'message' => 'Укажите баланс пользователя'];

        $user = User::query()->find($request->id);
        if (!$user) return ['success' => false, 'message' => 'Пользователь не найден'];

        $user->update([
            'trade_url' => $request->trade_url,
            'role' => $request->role,
            'event_points' => $request->event_points,
            'reg_ip' => $request->reg_ip,
            'last_ip' => $request->last_ip,
            'referral_code' => $request->referral_code,
            'referral_balance' => $request->referral_balance,
            'total_earned' => $request->total_earned,
            'balance' => $request->balance,
        ]);

        return ['success' => true, 'message' => 'Пользователь обновлён'];
    }

    /**
     * Получить инвентарь пользователя
     */
    public function getInventory(Request $request)
    {
        $userId = $request->user_id;
        $user = User::query()->find($userId);
        if (!$user) return ['success' => false, 'message' => 'Пользователь не найден'];

        $status = $request->status; // STOCK, SELL, WITHDRAWN и т.д.
        $query = Lives::where('user_id', $userId)
            ->with('item', 'box');

        if ($status) {
            $query->where('status', $status);
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(50);

        return [
            'success' => true,
            'items' => $items,
        ];
    }

    /**
     * Продать предмет (без начисления баланса пользователю)
     */
    public function sellItem(Request $request)
    {
        $itemId = $request->item_id;
        $live = Lives::find($itemId);

        if (!$live) {
            return ['success' => false, 'message' => 'Предмет не найден'];
        }

        if ($live->status === Lives::SELL) {
            return ['success' => false, 'message' => 'Предмет уже продан'];
        }

        $live->status = Lives::SELL;
        $live->save();

        Log::channel('admin_users')->info('Admin sold user item', [
            'admin_id' => $request->user()->id,
            'user_id' => $live->user_id,
            'item_id' => $itemId,
            'price' => $live->price,
        ]);

        return [
            'success' => true,
            'message' => 'Предмет успешно продан (без начисления баланса)',
        ];
    }

    /**
     * Удалить предмет из инвентаря
     */
    public function deleteItem(Request $request)
    {
        $itemId = $request->item_id;
        $live = Lives::find($itemId);

        if (!$live) {
            return ['success' => false, 'message' => 'Предмет не найден'];
        }

        $userId = $live->user_id;
        $itemPrice = $live->price;

        $live->delete();

        Log::channel('admin_users')->info('Admin deleted user item', [
            'admin_id' => $request->user()->id,
            'user_id' => $userId,
            'item_id' => $itemId,
            'price' => $itemPrice,
        ]);

        return [
            'success' => true,
            'message' => 'Предмет успешно удален',
        ];
    }

    /**
     * Продать все предметы пользователя (без начисления баланса)
     */
    public function sellAllItems(Request $request)
    {
        $userId = $request->user_id;
        $user = User::query()->find($userId);
        if (!$user) return ['success' => false, 'message' => 'Пользователь не найден'];

        $status = $request->status ?? Lives::OPENED; // По умолчанию продаем только STOCK предметы

        $items = Lives::where('user_id', $userId)
            ->where('status', $status)
            ->get();

        if ($items->isEmpty()) {
            return ['success' => false, 'message' => 'Нет предметов для продажи'];
        }

        $count = $items->count();
        $totalPrice = $items->sum('price');

        Lives::where('user_id', $userId)
            ->where('status', $status)
            ->update(['status' => Lives::SELL]);

        Log::channel('admin_users')->info('Admin sold all user items', [
            'admin_id' => $request->user()->id,
            'user_id' => $userId,
            'count' => $count,
            'total_price' => $totalPrice,
        ]);

        return [
            'success' => true,
            'message' => "Продано {$count} предметов (без начисления баланса)",
            'count' => $count,
            'total_price' => $totalPrice,
        ];
    }

    /**
     * Удалить все предметы пользователя
     */
    public function deleteAllItems(Request $request)
    {
        $userId = $request->user_id;
        $user = User::query()->find($userId);
        if (!$user) return ['success' => false, 'message' => 'Пользователь не найден'];

        $status = $request->status; // Если указан, удаляем только предметы с этим статусом

        $query = Lives::where('user_id', $userId);
        if ($status) {
            $query->where('status', $status);
        }

        $items = $query->get();
        $count = $items->count();
        $totalPrice = $items->sum('price');

        $query->delete();

        Log::channel('admin_users')->info('Admin deleted all user items', [
            'admin_id' => $request->user()->id,
            'user_id' => $userId,
            'count' => $count,
            'total_price' => $totalPrice,
            'status' => $status,
        ]);

        return [
            'success' => true,
            'message' => "Удалено {$count} предметов",
            'count' => $count,
            'total_price' => $totalPrice,
        ];
    }

    /**
     * Изменить статус предмета
     */
    public function changeItemStatus(Request $request)
    {
        $itemId = $request->item_id;
        $newStatus = $request->status;

        $live = Lives::find($itemId);
        if (!$live) {
            return ['success' => false, 'message' => 'Предмет не найден'];
        }

        $validStatuses = [
            Lives::OPENED,
            Lives::SELL,
            Lives::SENDING,
            Lives::WAIT,
            Lives::ORDER_READY,
            Lives::TRADE_LOCK,
            Lives::WITHDRAWN,
        ];

        if (!in_array($newStatus, $validStatuses)) {
            return ['success' => false, 'message' => 'Некорректный статус'];
        }

        $oldStatus = $live->status;
        $live->status = $newStatus;
        $live->save();

        Log::channel('admin_users')->info('Admin changed item status', [
            'admin_id' => $request->user()->id,
            'user_id' => $live->user_id,
            'item_id' => $itemId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);

        return [
            'success' => true,
            'message' => 'Статус предмета изменен',
        ];
    }

    /**
     * Получить активные сессии пользователя
     */
    public function getSessions(Request $request)
    {
        $userId = $request->user_id;
        $user = User::query()->find($userId);
        if (!$user) return ['success' => false, 'message' => 'Пользователь не найден'];

        $tokens = PersonalAccessToken::where('tokenable_id', $userId)
            ->where('tokenable_type', User::class)
            ->orderBy('last_used_at', 'desc')
            ->get()
            ->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'last_used_at' => $token->last_used_at?->format('Y-m-d H:i:s'),
                    'created_at' => $token->created_at->format('Y-m-d H:i:s'),
                    'expires_at' => $token->expires_at?->format('Y-m-d H:i:s'),
                    'is_current' => false, // Будет установлено на фронтенде
                ];
            });

        return [
            'success' => true,
            'sessions' => $tokens,
        ];
    }

    /**
     * Завершить сессию (удалить токен)
     */
    public function revokeSession(Request $request)
    {
        $tokenId = $request->token_id;
        $token = PersonalAccessToken::find($tokenId);

        if (!$token) {
            return ['success' => false, 'message' => 'Токен не найден'];
        }

        $userId = $token->tokenable_id;
        $token->delete();

        Log::channel('admin_users')->info('Admin revoked user session', [
            'admin_id' => $request->user()->id,
            'user_id' => $userId,
            'token_id' => $tokenId,
        ]);

        return [
            'success' => true,
            'message' => 'Сессия успешно завершена',
        ];
    }

    /**
     * Завершить все сессии пользователя
     */
    public function revokeAllSessions(Request $request)
    {
        $userId = $request->user_id;
        $user = User::query()->find($userId);
        if (!$user) return ['success' => false, 'message' => 'Пользователь не найден'];

        $count = PersonalAccessToken::where('tokenable_id', $userId)
            ->where('tokenable_type', User::class)
            ->count();

        PersonalAccessToken::where('tokenable_id', $userId)
            ->where('tokenable_type', User::class)
            ->delete();

        Log::channel('admin_users')->info('Admin revoked all user sessions', [
            'admin_id' => $request->user()->id,
            'user_id' => $userId,
            'count' => $count,
        ]);

        return [
            'success' => true,
            'message' => "Завершено {$count} сессий",
            'count' => $count,
        ];
    }

    /**
     * Получить историю IP адресов пользователя
     */
    public function getIpHistory(Request $request)
    {
        $userId = $request->user_id;
        $user = User::query()->find($userId);
        if (!$user) return ['success' => false, 'message' => 'Пользователь не найден'];

        $history = [];

        // IP из таблицы истории IP (включая все сессии - активные и завершенные)
        $ipHistoryRecords = UserIpHistory::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($ipHistoryRecords as $record) {
            $history[] = [
                'ip' => $record->ip_address,
                'type' => $record->type,
                'description' => $record->description ?? $this->getIpTypeDescription($record->type),
                'date' => $record->created_at->format('Y-m-d H:i:s'),
                'token_id' => $record->token_id,
            ];
        }

        // IP из всех токенов (активных и завершенных) - если IP не записан в истории
        $tokens = PersonalAccessToken::where('tokenable_id', $userId)
            ->where('tokenable_type', User::class)
            ->orderBy('created_at', 'desc')
            ->get();

        $tokenIdsInHistory = $ipHistoryRecords->whereNotNull('token_id')->pluck('token_id')->toArray();
        
        foreach ($tokens as $token) {
            // Если токен уже есть в истории, пропускаем
            if (in_array((string)$token->id, $tokenIdsInHistory)) {
                continue;
            }
            
            // Пытаемся найти IP из истории по token_id
            $ipFromHistory = $ipHistoryRecords
                ->where('token_id', (string)$token->id)
                ->first();
            
            if ($ipFromHistory) {
                continue; // Уже добавлено выше
            }
            
            // Если IP не найден, пытаемся найти по дате создания токена (ближайшая запись в истории)
            $ipFromHistoryByDate = $ipHistoryRecords
                ->where('created_at', '<=', $token->created_at)
                ->sortByDesc('created_at')
                ->first();
            
            // Используем IP из истории по дате, или last_ip пользователя, или последний IP из истории
            $ip = $ipFromHistoryByDate?->ip_address 
                ?? ($ipHistoryRecords->first()?->ip_address) 
                ?? $user->last_ip 
                ?? 'N/A';
            
            if ($ip !== 'N/A') {
                $history[] = [
                    'ip' => $ip,
                    'type' => 'session',
                    'description' => 'Сессия: ' . $token->name . ' (создана ' . $token->created_at->format('Y-m-d H:i:s') . ')',
                    'date' => $token->created_at->format('Y-m-d H:i:s'),
                    'token_id' => (string)$token->id,
                ];
            }
        }

        // IP из платежей
        $payments = Payment::where('user_id', $userId)
            ->whereNotNull('metadata')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($payments as $payment) {
            $metadata = $payment->metadata ?? [];
            if (isset($metadata['ip'])) {
                $history[] = [
                    'ip' => $metadata['ip'],
                    'type' => 'payment',
                    'description' => 'Платеж #' . $payment->id . ' (' . ($payment->amount) . ' ₽)',
                    'date' => $payment->created_at->format('Y-m-d H:i:s'),
                ];
            }
        }

        // Сортируем по дате (новые первыми)
        usort($history, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Убираем дубликаты IP, но сохраняем все уникальные записи
        $seen = [];
        $result = [];
        foreach ($history as $item) {
            $key = $item['ip'] . '_' . $item['date'];
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $result[] = $item;
            }
        }

        return [
            'success' => true,
            'ip_history' => $result,
            'current_ip' => $user->last_ip,
            'registration_ip' => $user->reg_ip,
            'total_records' => count($result),
        ];
    }

    /**
     * Получить описание типа IP
     */
    private function getIpTypeDescription($type): string
    {
        $descriptions = [
            'registration' => 'Регистрация',
            'login' => 'Вход в систему',
            'token_created' => 'Создание токена',
            'session' => 'Сессия',
            'payment' => 'Платеж',
        ];
        
        return $descriptions[$type] ?? $type;
    }

    /**
     * Получить историю активности пользователя
     */
    public function getActivityHistory(Request $request)
    {
        $userId = $request->user_id;
        $user = User::query()->find($userId);
        if (!$user) return ['success' => false, 'message' => 'Пользователь не найден'];

        $limit = $request->limit ?? 100;
        $activity = [];

        // Открытия кейсов
        $cases = Lives::where('user_id', $userId)
            ->where('from_where', Lives::CASE_TYPE)
            ->with('item', 'box')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        foreach ($cases as $live) {
            $activity[] = [
                'type' => 'case_opened',
                'description' => 'Открыт кейс: ' . ($live->box->name ?? 'Неизвестно'),
                'item' => $live->item->title ?? 'Неизвестно',
                'price' => $live->price,
                'date' => $live->created_at->format('Y-m-d H:i:s'),
                'timestamp' => $live->created_at->timestamp,
            ];
        }

        // Депозиты
        $deposits = Payment::where('user_id', $userId)
            ->where('status', Payment::PAID)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        foreach ($deposits as $payment) {
            $activity[] = [
                'type' => 'deposit',
                'description' => 'Депозит: ' . ($payment->amount) . ' ₽',
                'item' => null,
                'price' => $payment->amount * 100,
                'date' => $payment->created_at->format('Y-m-d H:i:s'),
                'timestamp' => $payment->created_at->timestamp,
            ];
        }

        // Выводы
        $withdraws = Lives::where('user_id', $userId)
            ->whereIn('status', [Lives::WITHDRAWN, Lives::SENDING, Lives::WAIT, Lives::ORDER_READY])
            ->with('item')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        foreach ($withdraws as $live) {
            $statusText = match($live->status) {
                Lives::WITHDRAWN => 'Выведено',
                Lives::SENDING => 'Отправляется',
                Lives::WAIT => 'Ожидает',
                Lives::ORDER_READY => 'Готово к выдаче',
                default => 'Вывод',
            };
            $activity[] = [
                'type' => 'withdraw',
                'description' => $statusText . ': ' . ($live->item->title ?? 'Неизвестно'),
                'item' => $live->item->title ?? 'Неизвестно',
                'price' => $live->price,
                'date' => $live->created_at->format('Y-m-d H:i:s'),
                'timestamp' => $live->created_at->timestamp,
            ];
        }

        // Продажи
        $sales = Lives::where('user_id', $userId)
            ->where('status', Lives::SELL)
            ->with('item')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        foreach ($sales as $live) {
            $activity[] = [
                'type' => 'sale',
                'description' => 'Продано: ' . ($live->item->title ?? 'Неизвестно'),
                'item' => $live->item->title ?? 'Неизвестно',
                'price' => $live->price,
                'date' => $live->created_at->format('Y-m-d H:i:s'),
                'timestamp' => $live->created_at->timestamp,
            ];
        }

        // Реферальные начисления
        $referralEarnings = ReferralEarning::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        foreach ($referralEarnings as $earning) {
            $activity[] = [
                'type' => 'referral_earning',
                'description' => 'Реферальное начисление: ' . ($earning->amount / 100) . ' ₽',
                'item' => null,
                'price' => $earning->amount,
                'date' => $earning->created_at->format('Y-m-d H:i:s'),
                'timestamp' => $earning->created_at->timestamp,
            ];
        }

        // Бонусы
        $bonuses = BonusClaim::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        foreach ($bonuses as $bonus) {
            $activity[] = [
                'type' => 'bonus',
                'description' => 'Бонус (' . $bonus->bonus_type . '): ' . ($bonus->amount / 100) . ' ₽',
                'item' => null,
                'price' => $bonus->amount,
                'date' => $bonus->created_at->format('Y-m-d H:i:s'),
                'timestamp' => $bonus->created_at->timestamp,
            ];
        }

        // Сортируем по дате (новые первыми)
        usort($activity, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        // Ограничиваем результат
        $activity = array_slice($activity, 0, $limit);

        return [
            'success' => true,
            'activity' => $activity,
            'total' => count($activity),
        ];
    }

    public function banUser(Request $request)
    {
        $userId = $request->user_id;
        $user = User::query()->find($userId);
        if (!$user) return ['success' => false, 'message' => 'Пользователь не найден'];

        $user->update([
            'is_banned' => true,
        ]);

        return [
            'success' => true,
            'message' => 'Пользователь заблокирован',
        ];
    }

    public function unbanUser(Request $request)
    {
        $userId = $request->user_id;
        $user = User::query()->find($userId);
        if (!$user) return ['success' => false, 'message' => 'Пользователь не найден'];

        $user->update([
            'is_banned' => false,
        ]);

        return [
            'success' => true,
            'message' => 'Пользователь разблокирован',
        ];
    }
}
