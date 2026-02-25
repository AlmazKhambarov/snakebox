<?php

namespace App\Console\Commands;

use App\Models\Giveaway;
use App\Services\RedisService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Lives;

class GiveawayCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'giveaway:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Управление розыгрышами: проверка активных, создание новых, определение победителей';

    protected RedisService $redisService;

    public function __construct(RedisService $redisService)
    {
        parent::__construct();
        $this->redisService = $redisService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Запуск проверки розыгрышей...');

        // 1. Определяем победителей завершенных розыгрышей
        $this->processFinishedGiveaways();

        // 2. Создаем новые розыгрыши, если необходимо
        $this->createNewGiveaways();

        $this->info('Проверка розыгрышей завершена.');
        return Command::SUCCESS;
    }

    /**
     * Обработка завершенных розыгрышей
     */
    protected function processFinishedGiveaways(): void
    {
        $finishedGiveaways = Giveaway::where('status', 'IN PROCESS')
            ->where('finished_at', '<=', now())
            ->get();

        if ($finishedGiveaways->isEmpty()) {
            $this->info('Нет завершенных розыгрышей для обработки.');
            return;
        }

        foreach ($finishedGiveaways as $giveaway) {
            // Загружаем участников перед выбором победителя для анимации
            $participants = $giveaway->participants()->with('user')->get()->map(function ($participant) {
                return [
                    'id' => $participant->user->id,
                    'username' => $participant->user->username,
                    'avatar' => $participant->user->avatar,
                ];
            })->toArray();

            $winner = $giveaway->selectWinner();

            if ($winner) {
                $this->info("Розыгрыш #{$giveaway->id} ({$giveaway->type}): выбран победитель - {$winner->username} (ID: {$winner->id})");
                Log::info("Giveaway #{$giveaway->id} winner selected", [
                    'giveaway_id' => $giveaway->id,
                    'type' => $giveaway->type,
                    'winner_id' => $winner->id,
                    'winner_username' => $winner->username,
                    'item_id' => $giveaway->drop_id,
                ]);

                Lives::create([
                    'user_id' => $winner->id,
                    'skin_id' => $giveaway->item->id,
                    'from_where' => 'RAFFLE',
                    'price' => $giveaway->item->steam_price,
                    'status' => Lives::OPENED,
                ]);

                // Отправляем событие о завершении розыгрыша через Redis
                try {
                    $this->redisService->publish('giveawayFinished', [
                        'type' => $giveaway->type,
                        'winner' => [
                            'id' => $winner->id,
                            'username' => $winner->username,
                            'avatar' => $winner->avatar,
                        ],
                        'participants' => $participants,
                        'prize' => [
                            'id' => $giveaway->item->id,
                            'name' => $giveaway->item->title,
                            'image' => $giveaway->item->image,
                            'price' => $giveaway->item->steam_price,
                            'rarity' => $giveaway->item->rarity,
                        ],
                    ]);
                    $this->info("Событие giveawayFinished отправлено в Redis для розыгрыша #{$giveaway->id}");
                } catch (\Exception $e) {
                    Log::error("Failed to publish giveawayFinished to Redis", [
                        'giveaway_id' => $giveaway->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Здесь можно добавить логику начисления выигрыша пользователю
                // Например, добавить предмет в инвентарь пользователя
            } else {
                $this->warn("Розыгрыш #{$giveaway->id} ({$giveaway->type}): нет участников, статус изменен на FAILED");
                Log::warning("Giveaway #{$giveaway->id} failed - no participants", [
                    'giveaway_id' => $giveaway->id,
                    'type' => $giveaway->type,
                ]);

                // Создаем новый розыгрыш взамен проваленного
                $newGiveaway = Giveaway::createGiveaway($giveaway->type);
                if ($newGiveaway) {
                    $this->info("Создан новый розыгрыш #{$newGiveaway->id} ({$newGiveaway->type}) взамен проваленного");
                }
            }
        }
    }

    /**
     * Создание новых розыгрышей
     */
    protected function createNewGiveaways(): void
    {
        $types = ['hourly', 'daily', 'weekly'];

        foreach ($types as $type) {
            // Проверяем, есть ли активный розыгрыш данного типа
            $activeGiveaway = Giveaway::where('type', $type)
                ->where('status', 'IN PROCESS')
                ->where('finished_at', '>', now())
                ->first();

            if (!$activeGiveaway) {
                // Создаем новый розыгрыш
                $giveaway = Giveaway::createGiveaway($type);

                if ($giveaway) {
                    $this->info("Создан новый розыгрыш: {$type} (ID: {$giveaway->id})");
                    Log::info("New giveaway created", [
                        'giveaway_id' => $giveaway->id,
                        'type' => $type,
                        'started_at' => $giveaway->started_at,
                        'finished_at' => $giveaway->finished_at,
                        'item_id' => $giveaway->drop_id,
                    ]);
                } else {
                    $this->error("Не удалось создать розыгрыш типа: {$type}");
                    Log::error("Failed to create giveaway", ['type' => $type]);
                }
            } else {
                $this->info("Активный розыгрыш {$type} уже существует (ID: {$activeGiveaway->id})");
            }
        }
    }
}

