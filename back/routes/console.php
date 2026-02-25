<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Планировщик розыгрышей
Schedule::command('giveaway:cron')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Проверка RTP кейсов (каждые 5 минут)
Schedule::command('cases:check-rtp')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Генерация ежедневного бонусного промокода (каждый день в 00:00)
Schedule::command('bonus:generate-daily-promocode')
    ->daily()
    ->at('00:00')
    ->withoutOverlapping()
    ->runInBackground();

// Обработка призов завершившихся ивентов (каждые 30 минут)
Schedule::command('event:process-rewards')
->everyThirtyMinutes()
->withoutOverlapping()
->runInBackground();
