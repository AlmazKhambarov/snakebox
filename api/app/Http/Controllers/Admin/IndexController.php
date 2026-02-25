<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LiveStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Lives;
use App\Models\Payment;
use App\Models\User;
use App\Models\Boxes;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function get()
    {
        // 1. Запрос для платежей (Payment)
        $payments = Payment::query()
            ->where('status', Payment::PAID)
            ->selectRaw('
            SUM(CASE WHEN created_at >= ? THEN amount ELSE 0 END) as payments_today,
            SUM(CASE WHEN created_at >= ? THEN amount ELSE 0 END) as payment_week,
            SUM(CASE WHEN created_at >= ? THEN amount ELSE 0 END) as payments_month,
            SUM(amount) as payments_total
        ', [
                Carbon::today(),
                Carbon::now()->subWeek(),
                Carbon::now()->subMonth(),
            ])
            ->first();

        // 2. Запрос для выводов (Live)
        $withdraws = Lives::query()
            ->whereStatus(Lives::WITHDRAWN)
            ->selectRaw('
            SUM(CASE WHEN created_at >= ? THEN price ELSE 0 END) as withdraws_today,
            SUM(CASE WHEN created_at >= ? THEN price ELSE 0 END) as withdraw_week,
            SUM(CASE WHEN created_at >= ? THEN price ELSE 0 END) as withdraw_month,
            SUM(price) as withdraw_total
        ', [
                Carbon::today(),
                Carbon::now()->subWeek(),
                Carbon::now()->subMonth()
            ])
            ->first();

        $startOfWeek = Carbon::now()->startOfWeek(); // Понедельник 00:00 текущей недели
        $endOfWeek = Carbon::now()->endOfWeek();     // Воскресенье 23:59 текущей недели

        // Получаем суммы платежей по дням недели одним запросом
        $paymentsByDay = Payment::query()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->where('status', Payment::PAID) // Фильтр по статусу, если нужно
            ->selectRaw('
            DAYOFWEEK(created_at) as day_of_week,
            SUM(amount) as total_amount
        ')
            ->groupBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        // Получаем суммы выводов по дням недели
        $withdrawsByDay = Lives::query()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->whereStatus(Lives::WITHDRAWN)
            ->selectRaw('
            DAYOFWEEK(created_at) as day_of_week,
            SUM(price) as total_amount
        ')
            ->groupBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        // Получаем количество регистраций по дням недели
        $usersByDay = User::query()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw('
            DAYOFWEEK(created_at) as day_of_week,
            COUNT(*) as total_count
        ')
            ->groupBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        // Функция для заполнения недельных данных
        $fillWeeklyData = function ($dataByDay) {
            $weeklyData = [0, 0, 0, 0, 0, 0, 0];
            foreach ($dataByDay as $day) {
                $index = $day->day_of_week - 2;
                if ($index === -1) $index = 6;
                $weeklyData[$index] = (float)$day->total_amount ?? (float)$day->total_count;
            }
            return $weeklyData;
        };

        $weeklyPayments = $fillWeeklyData($paymentsByDay);
        $weeklyWithdraws = $fillWeeklyData($withdrawsByDay);
        $weeklyUsers = $fillWeeklyData($usersByDay);

        // 3. Запрос для пользователей
        $users = \App\Models\User::query()
            ->selectRaw('
            COUNT(CASE WHEN created_at >= ? THEN 1 ELSE NULL END) as users_today,
            COUNT(CASE WHEN created_at >= ? THEN 1 ELSE NULL END) as users_week,
            COUNT(CASE WHEN created_at >= ? THEN 1 ELSE NULL END) as users_month,
            COUNT(*) as users_total
        ', [
                Carbon::today(),
                Carbon::now()->subWeek(),
                Carbon::now()->subMonth(),
            ])
            ->first();

        return [
            'payments' => [
                'paymentsToday' => $payments->payments_today ?? 0,
                'paymentsWeek' => $payments->payment_week ?? 0,
                'paymentsMonth' => $payments->payments_month ?? 0,
                'paymentsTotal' => $payments->payments_total ?? 0,
            ],
            'withdraws' => [
                'withdrawsToday' => $withdraws->withdraws_today ?? 0,
                'withdrawsWeek' => $withdraws->withdraw_week ?? 0,
                'withdrawsMonth' => $withdraws->withdraw_month ?? 0,
                'withdrawsTotal' => $withdraws->withdraw_total ?? 0
            ],
            'users' => [
                'usersToday' => $users->users_today ?? 0,
                'usersWeek' => $users->users_week ?? 0,
                'usersMonth' => $users->users_month ?? 0,
                'usersTotal' => $users->users_total ?? 0,
            ],
            'week_payments' => $weeklyPayments,
            'week_withdraws' => $weeklyWithdraws,
            'week_users' => $weeklyUsers,
            'auto_disabled_cases' => $this->getAutoDisabledCases(),
        ];
    }

    /**
     * Получить список автоматически отключенных кейсов
     */
    private function getAutoDisabledCases(): array
    {
        $cases = Boxes::where('auto_disabled', true)
            ->where('is_active', false)
            ->orderBy('auto_disabled_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($box) {
                return [
                    'id' => $box->id,
                    'name' => $box->name,
                    'image' => $box->image,
                    'current_rtp' => $box->current_rtp,
                    'max_rtp' => $box->max_rtp,
                    'total_opened' => $box->total_opened,
                    'auto_disabled_reason' => $box->auto_disabled_reason,
                    'auto_disabled_at' => $box->auto_disabled_at?->format('Y-m-d H:i:s'),
                ];
            });

        return $cases->toArray();
    }
}
