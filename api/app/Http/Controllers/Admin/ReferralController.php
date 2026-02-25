<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ReferralEarning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ReferralController extends Controller
{
    /**
     * Получить список всех пользователей с реферальной информацией
     */
    public function index()
    {
        return datatables(
            User::query()
                ->whereHas('referrals') // Только пользователи с рефералами
                ->orderBy('id', 'desc')
        )
            ->addColumn('referrals_count', function ($user) {
                return User::where('referrer_id', $user->id)->count();
            })
            ->toJson();
    }

    /**
     * Получить данные конкретного пользователя
     */
    public function get(Request $request): array
    {
        $id = $request->id;

        $user = User::with(['referrals', 'referrer'])->find($id);
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден'];
        }

        // Получаем статистику по рефералам
        $referralsStats = User::where('referrer_id', $user->id)
            ->selectRaw('COUNT(*) as count, SUM(total_deposited) as total_deposited')
            ->first();

        // Получаем историю начислений (исключая админские)
        $earnings = ReferralEarning::where('user_id', $user->id)
            ->where('type', '!=', 'admin')
            ->with('referral')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return [
            'success' => true,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'referral_code' => $user->referral_code,
                'referral_level' => $user->referral_level,
                'custom_referral_percentage' => $user->custom_referral_percentage,
                'referrer' => $user->referrer,
                'referral_balance' => $user->referral_balance,
                'total_earned' => $user->total_earned,
                'total_deposited' => $user->total_deposited,
                'referrals_count' => $user->referrals->count(),
                'referrals' => $user->referrals->map(function ($ref) {
                    return [
                        'id' => $ref->id,
                        'username' => $ref->username,
                        'avatar' => $ref->avatar,
                        'total_deposited' => $ref->total_deposited,
                        'created_at' => $ref->created_at?->format('Y-m-d H:i:s'),
                    ];
                }),
                'referrals_total_deposited' => $referralsStats->total_deposited ?? 0,
                'earnings' => $earnings->map(function ($earning) {
                    return [
                        'id' => $earning->id,
                        'referral' => $earning->referral ? [
                            'id' => $earning->referral->id,
                            'username' => $earning->referral->username,
                        ] : null,
                        'amount' => $earning->amount,
                        'deposit_amount' => $earning->deposit_amount,
                        'percentage' => $earning->percentage,
                        'type' => $earning->type,
                        'description' => $earning->description,
                        'created_at' => $earning->created_at?->format('Y-m-d H:i:s'),
                    ];
                }),
            ]
        ];
    }

    /**
     * Обновить реферальные данные пользователя
     */
    public function update(Request $request): array
    {
        $id = $request->id;

        $request->validate([
            'referral_code' => 'nullable|string|max:20|unique:users,referral_code,' . $id,
            'referral_level' => 'nullable|integer|min:1|max:5',
            'custom_referral_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $user = User::find($id);
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден'];
        }

        $updateData = [];

        if ($request->has('referral_code') && $request->referral_code !== null) {
            $updateData['referral_code'] = strtoupper($request->referral_code);
        }

        if ($request->has('referral_level') && $request->referral_level !== null) {
            $updateData['referral_level'] = $request->referral_level;
        }

        if ($request->has('custom_referral_percentage')) {
            $updateData['custom_referral_percentage'] = $request->custom_referral_percentage;
        }

        $user->update($updateData);

        Log::channel('admin_referral')->info('Referral data updated by admin', [
            'user_id' => $user->id,
            'changes' => $updateData,
        ]);

        return ['success' => true, 'message' => 'Реферальные данные обновлены'];
    }

    /**
     * Получить статистику реферальной системы
     */
    public function statistics(): array
    {
        // Базовая статистика (исключая админские начисления)
        $totalUsersWithReferrer = User::whereNotNull('referrer_id')->count();
        $totalUsersWithReferrals = User::has('referrals')->count();
        $totalReferralEarnings = ReferralEarning::where('type', '!=', 'admin')->sum('amount');
        
        // Статистика по уровням
        $usersByLevel = User::select('referral_level', DB::raw('COUNT(*) as count'))
            ->whereNotNull('referral_level')
            ->groupBy('referral_level')
            ->orderBy('referral_level')
            ->get()
            ->mapWithKeys(function ($item) {
                return ['level_' . $item->referral_level => $item->count];
            });

        // Пользователи с индивидуальным процентом
        $usersWithCustomPercentage = User::whereNotNull('custom_referral_percentage')->count();
        
        // Средний процент по индивидуальным настройкам
        $avgCustomPercentage = User::whereNotNull('custom_referral_percentage')
            ->avg('custom_referral_percentage');

        // Топ рефереров по количеству рефералов
        $topReferrersByCount = User::select('users.*')
            ->selectRaw('(SELECT COUNT(*) FROM users as refs WHERE refs.referrer_id = users.id) as referrals_count')
            ->havingRaw('referrals_count > 0')
            ->orderByDesc('referrals_count')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                    'referrals_count' => $user->referrals_count,
                    'total_earned' => $user->total_earned,
                    'referral_balance' => $user->referral_balance,
                ];
            });

        // Топ рефереров по заработку
        $topReferrersByEarnings = User::where('total_earned', '>', 0)
            ->orderBy('total_earned', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                    'total_earned' => $user->total_earned,
                    'referral_balance' => $user->referral_balance,
                    'referrals_count' => User::where('referrer_id', $user->id)->count(),
                ];
            });

        // Статистика по типам начислений (исключая админские)
        $earningsByType = ReferralEarning::select('type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->where('type', '!=', 'admin')
            ->groupBy('type')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->type,
                    'total' => $item->total,
                    'count' => $item->count,
                    'average' => $item->count > 0 ? round($item->total / $item->count, 2) : 0,
                ];
            });

        // Статистика за последние 30 дней (исключая админские начисления)
        $earningsLast30Days = ReferralEarning::where('created_at', '>=', now()->subDays(30))
            ->where('type', '!=', 'admin')
            ->sum('amount');
        
        $newReferralsLast30Days = User::whereNotNull('referrer_id')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // Статистика за последние 7 дней (исключая админские начисления)
        $earningsLast7Days = ReferralEarning::where('created_at', '>=', now()->subDays(7))
            ->where('type', '!=', 'admin')
            ->sum('amount');
        
        $newReferralsLast7Days = User::whereNotNull('referrer_id')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // Средний заработок на реферала
        $avgEarningsPerReferral = $totalUsersWithReferrer > 0 
            ? round($totalReferralEarnings / $totalUsersWithReferrer, 2)
            : 0;

        // Средний заработок реферера
        $avgEarningsPerReferrer = $totalUsersWithReferrals > 0
            ? round($totalReferralEarnings / $totalUsersWithReferrals, 2)
            : 0;

        // Средний депозит приведенных пользователей
        $avgDepositReferredUsers = User::whereNotNull('referrer_id')
            ->where('total_deposited', '>', 0)
            ->avg('total_deposited');

        // Конверсия (% рефералов с депозитом)
        $referralsWithDeposit = User::whereNotNull('referrer_id')
            ->where('total_deposited', '>', 0)
            ->count();
        
        $conversionRate = $totalUsersWithReferrer > 0
            ? round(($referralsWithDeposit / $totalUsersWithReferrer) * 100, 2)
            : 0;

        // Общая сумма депозитов приведенных пользователей
        $totalDepositsFromReferrals = User::whereNotNull('referrer_id')
            ->sum('total_deposited');

        // Статистика начислений по датам (последние 30 дней, исключая админские)
        $earningsByDate = ReferralEarning::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->where('type', '!=', 'admin')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $stats = [
            // Основная статистика
            'total_users_with_referrer' => $totalUsersWithReferrer,
            'total_users_with_referrals' => $totalUsersWithReferrals,
            'total_referral_earnings' => $totalReferralEarnings,
            'users_with_custom_percentage' => $usersWithCustomPercentage,
            'avg_custom_percentage' => round($avgCustomPercentage ?? 0, 2),
            
            // Средние показатели
            'average_referrals_per_user' => round(
                $totalUsersWithReferrals > 0 ? $totalUsersWithReferrer / $totalUsersWithReferrals : 0,
                2
            ),
            'avg_earnings_per_referral' => $avgEarningsPerReferral,
            'avg_earnings_per_referrer' => $avgEarningsPerReferrer,
            'avg_deposit_referred_users' => round($avgDepositReferredUsers ?? 0, 2),
            
            // Конверсия и депозиты
            'referrals_with_deposit' => $referralsWithDeposit,
            'conversion_rate' => $conversionRate,
            'total_deposits_from_referrals' => $totalDepositsFromReferrals,
            
            // Статистика за периоды
            'earnings_last_7_days' => $earningsLast7Days,
            'new_referrals_last_7_days' => $newReferralsLast7Days,
            'earnings_last_30_days' => $earningsLast30Days,
            'new_referrals_last_30_days' => $newReferralsLast30Days,
            
            // Распределение по уровням
            'users_by_level' => $usersByLevel,
            
            // Топы
            'top_referrers_by_count' => $topReferrersByCount,
            'top_referrers_by_earnings' => $topReferrersByEarnings,
            
            // Статистика по типам начислений
            'earnings_by_type' => $earningsByType,
            
            // График начислений за 30 дней
            'earnings_by_date' => $earningsByDate,
        ];

        return [
            'success' => true,
            'statistics' => $stats,
        ];
    }

    /**
     * Сгенерировать новый реферальный код для пользователя
     */
    public function generateReferralCode(Request $request): array
    {
        $id = $request->id;

        $user = User::find($id);
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден'];
        }

        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (User::where('referral_code', $code)->exists());

        $user->update(['referral_code' => $code]);

        Log::channel('admin_referral')->info('New referral code generated by admin', [
            'user_id' => $user->id,
            'new_code' => $code,
        ]);

        return [
            'success' => true,
            'message' => 'Новый реферальный код сгенерирован',
            'referral_code' => $code,
        ];
    }

    /**
     * Добавить баланс на реферальный счет пользователя
     */
    public function addBalance(Request $request): array
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $user = User::find($request->user_id);
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден'];
        }

        $user->increment('referral_balance', $request->amount);
        $user->increment('total_earned', $request->amount);

        // Создаем запись о начислении
        ReferralEarning::create([
            'user_id' => $user->id,
            'referral_id' => null, // NULL для админских начислений (не от реферала)
            'amount' => $request->amount,
            'deposit_amount' => 0,
            'percentage' => 0,
            'type' => 'admin',
            'description' => $request->description ?? 'Ручное начисление администратором',
        ]);

        Log::channel('admin_referral')->info('Referral balance added by admin', [
            'user_id' => $user->id,
            'amount' => $request->amount,
        ]);

        return [
            'success' => true,
            'message' => 'Баланс успешно начислен',
            'new_balance' => $user->referral_balance,
        ];
    }
}

