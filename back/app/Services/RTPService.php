<?php

namespace App\Services;

use App\Models\Boxes;
use App\Models\Items;
use App\Models\UpgradeRtpStats;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RTPService
{
    /**
     * Рассчитать текущий RTP кейса
     */
    public function calculateCurrentRTP(Boxes $box): float
    {
        if ($box->total_spent == 0) {
            return $box->target_rtp;
        }

        // RTP = (Выиграно / Потрачено) * 100
        $rtp = ($box->total_won / $box->total_spent) * 100;

        // Если RTP превышает максимальный порог, ограничиваем максимальным порогом
        if ($rtp > $box->max_rtp) {
            $rtp = $box->max_rtp;
        }

        // Если RTP меньше минимального порога, ограничиваем минимальным порогом
        if ($rtp < $box->min_rtp) {
            $rtp = $box->min_rtp;
        }

        return round($rtp, 2);
    }

    /**
     * Обновить статистику кейса после открытия
     */
    public function updateBoxStats(Boxes $box, int $spent, int $won): void
    {
        // Обновляем значения через increment
        $box->increment('total_opened', 1);
        $box->increment('total_spent', $spent);
        $box->increment('total_won', $won);

        // ВАЖНО: Перезагружаем модель из БД, чтобы получить актуальные значения
        $box->refresh();

        // Пересчитываем текущий RTP на основе актуальных данных
        $currentRTP = $this->calculateCurrentRTP($box);
        
        $box->update([
            'current_rtp' => $currentRTP,
            'last_rtp_update' => now()
        ]);

        Log::info('Box RTP updated', [
            'box_id' => $box->id,
            'box_name' => $box->name,
            'spent' => $spent,
            'won' => $won,
            'total_opened' => $box->total_opened,
            'total_spent' => $box->total_spent,
            'total_won' => $box->total_won,
            'current_rtp' => $currentRTP,
            'calculated_rtp' => $this->calculateCurrentRTP($box),
        ]);
    }

    /**
     * Получить модифицированные шансы с учетом RTP
     * 
     * @param Boxes $box
     * @param array $items Массив предметов с их базовыми шансами
     * @return array Модифицированные шансы
     */
    public function getAdjustedDropChances(Boxes $box, array $items): array
    {
        // ВАЖНО: Пересчитываем RTP на лету из актуальных данных
        $currentRTP = $this->calculateCurrentRTP($box);
        $targetRTP = $box->target_rtp;
        $minRTP = $box->min_rtp;
        $maxRTP = $box->max_rtp;

        // ЛОГИКА:
        // Высокий RTP = казино в минусе (игроки выигрывают больше) → УМЕНЬШАЕМ шансы на дорогие
        // Низкий RTP = казино в плюсе (игроки тратят больше) → УВЕЛИЧИВАЕМ шансы на дорогие
        
        // Вычисляем отклонение от целевого RTP
        $rtpDeviation = $currentRTP - $targetRTP;
        
        // Определяем режим работы
        $isOverMax = $currentRTP > $maxRTP; // Игроки выигрывают слишком много (казино в минусе)
        $isUnderMin = $currentRTP < $minRTP; // Игроки выигрывают слишком мало (казино в плюсе)
        $isInRange = !$isOverMax && !$isUnderMin; // В пределах нормы

        // Коэффициент коррекции
        // Если RTP высокий (deviation > 0) -> correctionFactor отрицательный -> уменьшаем шансы
        // Если RTP низкий (deviation < 0) -> correctionFactor положительный -> увеличиваем шансы
        if ($isOverMax) {
            // Слишком окупается (RTP > max_rtp) → ограничиваем уменьшение шансов
            $currentDeviation = $currentRTP - $maxRTP;
            $correctionFactor = max(-0.7, min(-0.3, -$currentDeviation / 15)); 
        } elseif ($isUnderMin) {
            // Слишком в минусе (RTP < min_rtp) → ограничиваем увеличение шансов
            $currentDeviation = $minRTP - $currentRTP;
            $correctionFactor = max(0.3, min(0.7, $currentDeviation / 15));
        } else {
            // В пределах нормы
            $correctionFactor = -$rtpDeviation / 25; 
            $correctionFactor = max(-0.5, min(0.5, $correctionFactor));
        }
        
        Log::info('RTP correction calculation', [
            'box_id' => $box->id,
            'current_rtp' => $currentRTP,
            'target_rtp' => $targetRTP,
            'correction_factor' => $correctionFactor,
            'mode' => $isOverMax ? 'OVER_MAX' : ($isUnderMin ? 'UNDER_MIN' : 'DYNAMIC'),
        ]);

        $adjustedItems = [];
        $totalChance = 0;

        foreach ($items as $item) {
            $baseChance = $item['chance'];
            $itemPrice = $item['price'];
            $boxPrice = $box->price;

            // Определяем, насколько предмет дорогой относительно цены кейса
            $priceRatio = $itemPrice / $boxPrice;

            // Для дорогих предметов (>1.5x от цены кейса) применяем коррекцию
            if ($priceRatio > 1.5) {
                if ($isOverMax) {
                    // Сильно окупается (казино в минусе) → ограничиваем минимальным порогом снизу
                    // Уменьшаем шансы на дорогие (correctionFactor отрицательный), но не меньше минимального порога
                    $adjustment = 1 + ($correctionFactor * 0.5); // correctionFactor от -0.6 до -0.2
                    $adjustment = max(0.6, $adjustment); // Минимальный порог: минимум 60% от базового шанса
                    $adjustment = min(1.0, $adjustment); // Максимум 100% от базового
                } elseif ($isUnderMin) {
                    // Сильно в плюсе (казино в плюсе) → ограничиваем максимальным порогом сверху
                    // Увеличиваем шансы на дорогие (correctionFactor положительный), но не больше максимального порога
                    $adjustment = 1 + ($correctionFactor * 0.5); // correctionFactor от +0.2 до +0.6
                    $adjustment = max(1.0, $adjustment); // Минимум 100% от базового
                    $adjustment = min(1.3, $adjustment); // Максимальный порог: максимум 130% от базового шанса
                } else {
                    // В пределах нормы → плавная динамическая коррекция
                    // Чем выше RTP → меньше дорогих, чем ниже → больше дорогих (ИНВЕРТИРОВАНО)
                    $adjustment = 1 + ($correctionFactor * 0.4); // Максимум ±20%
                }
                $adjustedChance = $baseChance * $adjustment;
            } 
            // Для дешевых предметов (<0.8x от цены кейса) делаем обратное
            else if ($priceRatio < 0.8) {
                if ($isOverMax) {
                    // Сильно окупается (казино в минусе) → увеличиваем шансы на дешевые (компенсируем уменьшением дорогих)
                    $adjustment = 1 - ($correctionFactor * 0.3); // correctionFactor отрицательный, получаем увеличение
                    $adjustment = max(1.0, $adjustment); // Минимум 100%
                    $adjustment = min(1.2, $adjustment); // Максимум 120%
                } elseif ($isUnderMin) {
                    // Сильно в плюсе (казино в плюсе) → уменьшаем шансы на дешевые (компенсируем увеличением дорогих)
                    $adjustment = 1 - ($correctionFactor * 0.3); // correctionFactor положительный, получаем уменьшение
                    $adjustment = max(0.8, $adjustment); // Минимум 80%
                    $adjustment = min(1.0, $adjustment); // Максимум 100%
                } else {
                    // В пределах нормы → плавная динамическая коррекция
                    $adjustment = 1 - ($correctionFactor * 0.3); // Максимум ±15%
                }
                $adjustedChance = $baseChance * $adjustment;
            } 
            // Для средних предметов оставляем как есть
            else {
                $adjustedChance = $baseChance;
            }

            $adjustedChance = max(0.01, $adjustedChance); // Минимум 0.01%
            
            $adjustedItems[] = [
                'item_id' => $item['item_id'],
                'item' => $item['item'],
                'price' => $itemPrice,
                'chance' => $baseChance,
                'adjusted_chance' => round($adjustedChance, 4),
                'price_ratio' => round($priceRatio, 2),
            ];

            $totalChance += $adjustedChance;
        }

        // Нормализуем шансы, чтобы их сумма была 100%
        foreach ($adjustedItems as &$item) {
            $item['normalized_chance'] = ($item['adjusted_chance'] / $totalChance) * 100;
        }

        return $adjustedItems;
    }

    /**
     * Выбрать предмет с учетом RTP
     */
    public function selectItemWithRTP(Boxes $box, array $caseItems): ?array
    {
        // Получаем модифицированные шансы
        $adjustedItems = $this->getAdjustedDropChances($box, $caseItems);

        // Генерируем случайное число от 0 до 100
        $random = mt_rand(0, 10000) / 100; // Точность до 0.01%
        
        $cumulativeChance = 0;
        foreach ($adjustedItems as $item) {
            $cumulativeChance += $item['normalized_chance'];
            
            if ($random <= $cumulativeChance) {
                return $item;
            }
        }

        // Fallback - возвращаем последний предмет
        return end($adjustedItems) ?: null;
    }

    /**
     * Проверить, нужна ли коррекция RTP
     */
    public function needsRTPCorrection(Boxes $box): bool
    {
        // Если открыто меньше 10 кейсов - не корректируем (недостаточно данных)
        if ($box->total_opened < 10) {
            return false;
        }

        // ВАЖНО: Пересчитываем RTP на лету из актуальных данных
        $currentRTP = $this->calculateCurrentRTP($box);
        $targetRTP = $box->target_rtp;
        $minRTP = $box->min_rtp;
        $maxRTP = $box->max_rtp;

        // Коррекция нужна, если RTP вышел за границы
        if ($currentRTP < $minRTP || $currentRTP > $maxRTP) {
            return true;
        }

        // Коррекция нужна, если отклонение от целевого больше 5%
        $deviation = abs($targetRTP - $currentRTP);
        return $deviation > 5;
    }

    /**
     * Получить информацию о RTP для отображения
     */
    public function getRTPInfo(Boxes $box): array
    {
        // ВАЖНО: Пересчитываем RTP на лету из актуальных данных
        $currentRTP = $this->calculateCurrentRTP($box);
        
        return [
            'current_rtp' => $currentRTP,
            'target_rtp' => $box->target_rtp,
            'min_rtp' => $box->min_rtp,
            'max_rtp' => $box->max_rtp,
            'total_opened' => $box->total_opened,
            'total_spent' => $box->total_spent,
            'total_won' => $box->total_won,
            'needs_correction' => $this->needsRTPCorrection($box),
            'status' => $this->getRTPStatus($box),
        ];
    }

    /**
     * Получить статус RTP (good/warning/critical)
     */
    private function getRTPStatus(Boxes $box): string
    {
        // ВАЖНО: Пересчитываем RTP на лету из актуальных данных
        $currentRTP = $this->calculateCurrentRTP($box);
        $targetRTP = $box->target_rtp;
        $deviation = abs($targetRTP - $currentRTP);

        if ($deviation <= 2) {
            return 'good'; // Зеленый
        } else if ($deviation <= 5) {
            return 'warning'; // Желтый
        } else {
            return 'critical'; // Красный
        }
    }

    /**
     * Сбросить статистику RTP кейса
     */
    public function resetBoxRTP(Boxes $box): void
    {
        $box->update([
            'current_rtp' => $box->target_rtp,
            'total_opened' => 0,
            'total_spent' => 0,
            'total_won' => 0,
            'last_rtp_update' => now(),
        ]);

        Log::info('Box RTP reset', ['box_id' => $box->id]);
    }

    /**
     * Установить целевой RTP для кейса
     */
    public function setTargetRTP(Boxes $box, float $targetRTP, float $minRTP = null, float $maxRTP = null): void
    {
        $updateData = [
            'target_rtp' => $targetRTP,
        ];

        if ($minRTP !== null) {
            $updateData['min_rtp'] = $minRTP;
        }

        if ($maxRTP !== null) {
            $updateData['max_rtp'] = $maxRTP;
        }

        $box->update($updateData);

        Log::info('Box target RTP updated', [
            'box_id' => $box->id,
            'target_rtp' => $targetRTP,
        ]);
    }

    /**
     * Рассчитать текущий RTP апгрейдов
     */
    public function calculateUpgradeRTP(UpgradeRtpStats $stats): float
    {
        if ($stats->total_spent == 0) {
            return $stats->target_rtp;
        }

        // RTP = (Выиграно / Потрачено) * 100
        $rtp = ($stats->total_won / $stats->total_spent) * 100;

        // Если RTP превышает максимальный порог, ограничиваем максимальным порогом
        if ($rtp > $stats->max_rtp) {
            $rtp = $stats->max_rtp;
        }

        // Если RTP меньше минимального порога, ограничиваем минимальным порогом
        if ($rtp < $stats->min_rtp) {
            $rtp = $stats->min_rtp;
        }

        return round($rtp, 2);
    }

    /**
     * Обновить статистику RTP апгрейдов
     */
    public function updateUpgradeStats(int $spent, int $won): void
    {
        $stats = UpgradeRtpStats::getStats();
        
        // Обновляем значения через increment
        $stats->increment('total_upgrades', 1);
        $stats->increment('total_spent', $spent);
        $stats->increment('total_won', $won);

        // ВАЖНО: Перезагружаем модель из БД, чтобы получить актуальные значения
        $stats->refresh();

        // Пересчитываем текущий RTP на основе актуальных данных
        $currentRTP = $this->calculateUpgradeRTP($stats);
        
        $stats->update([
            'current_rtp' => $currentRTP,
            'last_rtp_update' => now()
        ]);

        Log::channel('api_upgrade')->info('Upgrade RTP updated', [
            'spent' => $spent,
            'won' => $won,
            'total_upgrades' => $stats->total_upgrades,
            'total_spent' => $stats->total_spent,
            'total_won' => $stats->total_won,
            'current_rtp' => $currentRTP,
        ]);
    }

    /**
     * Рассчитать шанс апгрейда с учетом RTP и приоритетом низких процентов
     * Логика: низкие проценты (3, 10, 30) должны выпадать чаще чем высокие (50-75%)
     */
    public function calculateUpgradeChance(float $baseChance, UpgradeRtpStats $stats): float
    {
        // Получаем текущий RTP
        $currentRTP = $this->calculateUpgradeRTP($stats);
        $targetRTP = $stats->target_rtp;
        
        // Применяем RTP коррекцию
        // Если RTP > target (игроки выигрывают много) -> уменьшаем шансы
        $rtpDifference = $targetRTP - $currentRTP;
        $rtpModifier = 1 + ($rtpDifference / 100);
        $rtpModifier = max(0.8, min(1.2, $rtpModifier)); // Ограничиваем ±20%

        // Базовый шанс с учетом RTP
        $adjustedChance = $baseChance * $rtpModifier;

        // ЛОГИКА ПРИОРИТЕТА НИЗКИХ ПРОЦЕНТОВ (как на Case-Battle)
        // Низкие проценты (3, 10, 30) должны выпадать чаще
        // Высокие проценты (50-75%) должны выпадать реже
        
        if ($adjustedChance <= 3) {
            // Очень низкий шанс (до 3%) - увеличиваем на 15-25%
            $priorityModifier = 1.20; // +20%
        } elseif ($adjustedChance <= 10) {
            // Низкий шанс (3-10%) - увеличиваем на 10-15%
            $priorityModifier = 1.12; // +12%
        } elseif ($adjustedChance <= 30) {
            // Средний шанс (10-30%) - увеличиваем на 5-10%
            $priorityModifier = 1.07; // +7%
        } elseif ($adjustedChance <= 50) {
            // Высокий шанс (30-50%) - уменьшаем на 5-10%
            $priorityModifier = 0.93; // -7%
        } else {
            // Очень высокий шанс (50-75%) - уменьшаем на 10-20%
            $priorityModifier = 0.85; // -15%
        }

        $finalChance = $adjustedChance * $priorityModifier;
        
        // Ограничиваем финальный шанс
        $finalChance = max(0.01, min($finalChance, 90)); // Максимум 90%

        return round($finalChance, 2);
    }
}

