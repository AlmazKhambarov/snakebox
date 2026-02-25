<?php

namespace App\Console\Commands;

use App\Models\Boxes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckCasesRTP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cases:check-rtp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверяет RTP кейсов и автоматически отключает слишком окупаемые';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начало проверки RTP кейсов...');

        // Получаем все активные кейсы с достаточной статистикой (открыто больше 20 раз)
        $cases = Boxes::where('is_active', true)
            ->where('total_opened', '>=', 20)
            ->get();

        $disabledCount = 0;

        foreach ($cases as $box) {
            // Проверяем условие: если Потрачено < Выиграно * 1.5, то отключаем кейс
            // Это означает, что кейс слишком окупается (игроки выигрывают больше, чем тратят в 1.5 раза)
            if ($box->total_won > 0 && $box->total_spent < ($box->total_won * 1.5)) {
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
                
                // Обновляем current_rtp в БД перед отключением
                $box->update([
                    'current_rtp' => $currentRTP,
                    'is_active' => false,
                    'is_visible' => false,
                    'auto_disabled' => true,
                    'auto_disabled_reason' => "Потрачено ({$box->total_spent}) меньше выиграно в 1.5 раза ({$box->total_won} * 1.5 = " . ($box->total_won * 1.5) . ")",
                    'auto_disabled_at' => now(),
                ]);

                $disabledCount++;

                Log::warning('Case auto-disabled due to high profitability', [
                    'box_id' => $box->id,
                    'box_name' => $box->name,
                    'current_rtp' => $currentRTP,
                    'total_spent' => $box->total_spent,
                    'total_won' => $box->total_won,
                    'threshold' => $box->total_won * 1.5,
                ]);

                $this->warn("Кейс '{$box->name}' (ID: {$box->id}) отключен. Потрачено: {$box->total_spent}, Выиграно: {$box->total_won}, Порог: " . ($box->total_won * 1.5));
            }
        }

        // Проверяем кейсы, которые были автоматически отключены
        $autoDisabledCases = Boxes::where('auto_disabled', true)
            ->where('is_active', false)
            ->where('is_visible', false)
            ->where('total_opened', '>=', 20)
            ->get();

        $reenabledCount = 0;

        foreach ($autoDisabledCases as $box) {
            // Включаем обратно, если Потрачено >= Выиграно * 1.5
            // Это означает, что кейс больше не слишком окупается
            if ($box->total_won > 0 && $box->total_spent >= ($box->total_won * 1.5)) {
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
                
                $box->update([
                    'current_rtp' => $currentRTP,
                    'is_active' => true,
                    'is_visible' => true,
                    'auto_disabled' => false,
                    'auto_disabled_reason' => null,
                    'auto_disabled_at' => null,
                ]);

                $reenabledCount++;

                Log::info('Case auto-reenabled after profitability normalization', [
                    'box_id' => $box->id,
                    'box_name' => $box->name,
                    'current_rtp' => $currentRTP,
                    'total_spent' => $box->total_spent,
                    'total_won' => $box->total_won,
                    'threshold' => $box->total_won * 1.5,
                ]);

                $this->info("Кейс '{$box->name}' (ID: {$box->id}) включен обратно. Потрачено: {$box->total_spent}, Выиграно: {$box->total_won}, Порог: " . ($box->total_won * 1.5));
            }
        }

        $this->info("Проверка завершена. Отключено: {$disabledCount}, Включено обратно: {$reenabledCount}");

        return Command::SUCCESS;
    }
}
