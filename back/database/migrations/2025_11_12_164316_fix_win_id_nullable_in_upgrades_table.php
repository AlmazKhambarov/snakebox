<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Делаем win_id nullable, если он еще не nullable
        if (Schema::hasColumn('upgrades', 'win_id')) {
            DB::statement('ALTER TABLE `upgrades` MODIFY `win_id` BIGINT UNSIGNED NULL');
        }
        
        // Делаем price_win nullable, если он еще не nullable
        if (Schema::hasColumn('upgrades', 'price_win')) {
            DB::statement('ALTER TABLE `upgrades` MODIFY `price_win` INT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // В обратной миграции можно сделать NOT NULL, но это может вызвать проблемы с существующими данными
        // Оставляем как есть
    }
};
