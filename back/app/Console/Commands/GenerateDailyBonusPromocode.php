<?php

namespace App\Console\Commands;

use App\Models\Promocode;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateDailyBonusPromocode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bonus:generate-daily-promocode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерирует ежедневный бонусный промокод на депозит (15-30%)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Деактивируем старые ежедневные промокоды
        Promocode::where('type', 'deposit_percent')
            ->where('code', 'like', 'DAILY-%')
            ->update(['is_active' => false]);

        // Генерируем случайный процент от 15 до 30
        $percent = rand(15, 30);

        // Генерируем уникальный код
        $code = 'DAILY-' . strtoupper(Str::random(5));

        // Проверяем, что код уникален
        while (Promocode::where('code', $code)->exists()) {
            $code = 'DAILY-' . strtoupper(Str::random(5));
        }

        // Создаем промокод на сегодня
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $promocode = Promocode::create([
            'code' => $code,
            'type' => 'deposit_percent',
            'value' => $percent,
            'uses_left' => 999999, // Неограниченное количество использований на день
            'max_uses' => 999999,
            'valid_from' => $today,
            'valid_until' => $tomorrow,
            'is_active' => true,
        ]);

        Log::channel('api_promocode')->info('Daily bonus promocode generated', [
            'code' => $code,
            'percent' => $percent,
            'valid_from' => $today,
            'valid_until' => $tomorrow,
        ]);

        $this->info("Ежедневный бонусный промокод создан: {$code} ({$percent}%)");

        return 0;
    }
}
