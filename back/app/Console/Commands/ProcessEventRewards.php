<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\EventScores;
use App\Models\EventPrize;
use App\Models\Lives;
use App\Models\Items;
use App\Http\Controllers\Admin\EventController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessEventRewards extends Command
{
    protected $signature = 'event:process-rewards';
    protected $description = 'Выдает призы за завершившиеся ивенты и создает новый ивент';

    public function handle()
    {
        $this->info('Обработка призов ивентов...');

        // Находим завершившиеся ивенты, которые еще не обработаны
        $finishedEvents = Event::where('is_active', true)
            ->where('end_date', '<=', now())
            ->get();

        foreach ($finishedEvents as $event) {
            $this->info("Обработка ивента: {$event->name} (ID: {$event->id})");
            $this->distributeRewards($event);
            $this->createNewEvent($event);
        }

        $this->info('Обработка завершена!');
    }

    private function distributeRewards(Event $event)
    {
        DB::beginTransaction();
        try {
            // Получаем топ-50 игроков
            $topScores = EventScores::where('event_id', $event->id)
                ->where('reward_received', false)
                ->orderBy('points', 'DESC')
                ->orderBy('updated_at', 'ASC')
                ->limit(50)
                ->get();

            $this->info("Найдено игроков для выдачи призов: {$topScores->count()}");

            // Пересчитываем позиции на основе очков
            $sortedScores = $topScores->sortByDesc('points')->values();
            
            foreach ($sortedScores as $index => $score) {
                $position = $index + 1;
                
                // Получаем приз за это место
                $prize = $event->getPrizeForPosition($position);
                
                if (!$prize) {
                    $this->warn("Приз не найден для места {$position}");
                    continue;
                }

                // Получаем конкретный скин для приза
                if (!$prize->item_id) {
                    $this->warn("Приз для места {$position} не имеет назначенного предмета");
                    continue;
                }

                $item = $prize->item;
                
                if (!$item) {
                    $this->warn("Предмет не найден для места {$position} (item_id: {$prize->item_id})");
                    continue;
                }

                // Создаем запись в инвентаре
                Lives::create([
                    'user_id' => $score->user_id,
                    'skin_id' => $item->id,
                    'price' => $item->steam_price,
                    'status' => Lives::OPENED,
                    'from_where' => 'EVENT',
                ]);

                // Отмечаем, что приз выдан
                $score->update(['reward_received' => true]);

                $this->info("Приз выдан игроку {$score->user->username} за место {$position}: {$item->title}");
            }

            // Деактивируем ивент
            $event->update(['is_active' => false]);

            DB::commit();
            $this->info("Призы за ивент '{$event->name}' успешно выданы!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('api_event')->error('Ошибка при выдаче призов ивента', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);
            $this->error("Ошибка при выдаче призов: {$e->getMessage()}");
        }
    }

    private function createNewEvent(Event $oldEvent)
    {
        try {
            // Создаем новый ивент на основе старого
            $newEvent = Event::create([
                'name' => $oldEvent->name . ' #' . (Event::count() + 1),
                'start_date' => now(),
                'end_date' => now()->addDays(30), // Ивент на 30 дней
                'is_active' => true,
                'rewards' => [],
            ]);

            // Копируем призы из старого ивента
            $oldPrizes = $oldEvent->prizes;
            foreach ($oldPrizes as $oldPrize) {
                EventPrize::create([
                    'event_id' => $newEvent->id,
                    'position' => $oldPrize->position,
                    'item_id' => $oldPrize->item_id,
                    'min_price' => $oldPrize->min_price,
                    'max_price' => $oldPrize->max_price,
                ]);
            }

            // Если призов не было, создаем по умолчанию
            if ($oldPrizes->isEmpty()) {
                $eventController = new EventController();
                $eventController->createDefaultPrizes($newEvent->id);
            }

            $this->info("Создан новый ивент: {$newEvent->name} (ID: {$newEvent->id})");
        } catch (\Exception $e) {
            Log::channel('api_event')->error('Ошибка при создании нового ивента', [
                'error' => $e->getMessage(),
            ]);
            $this->error("Ошибка при создании нового ивента: {$e->getMessage()}");
        }
    }
}
