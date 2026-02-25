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
        Schema::table('upgrades', function (Blueprint $table) {
            // Проверяем и добавляем item_id, если его нет
            if (!Schema::hasColumn('upgrades', 'item_id')) {
                // Проверяем, есть ли колонка skin_id (старое название)
                if (Schema::hasColumn('upgrades', 'skin_id')) {
                    // Переименовываем skin_id в item_id через SQL
                    DB::statement('ALTER TABLE `upgrades` CHANGE `skin_id` `item_id` BIGINT UNSIGNED NULL');
                } else {
                    // Добавляем новую колонку item_id
                    $table->unsignedBigInteger('item_id')->nullable()->after('user_id');
                }
            }
            
            // Добавляем внешний ключ для item_id, если его еще нет
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'upgrades' 
                AND COLUMN_NAME = 'item_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            if (empty($foreignKeys)) {
                try {
                    DB::statement('ALTER TABLE `upgrades` ADD CONSTRAINT `upgrades_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE SET NULL');
                } catch (\Exception $e) {
                    // Игнорируем ошибку, если внешний ключ уже существует
                }
            }

            // Проверяем и добавляем win_id, если его нет
            if (!Schema::hasColumn('upgrades', 'win_id')) {
                $table->unsignedBigInteger('win_id')->nullable()->after('item_id');
                $table->foreign('win_id')->references('id')->on('items')->onDelete('set null');
            }

            // Проверяем и добавляем price, если его нет
            if (!Schema::hasColumn('upgrades', 'price')) {
                $table->integer('price')->after('win_id');
            }

            // Проверяем и добавляем price_win, если его нет
            if (!Schema::hasColumn('upgrades', 'price_win')) {
                $table->integer('price_win')->nullable()->after('price');
            }

            // Проверяем и добавляем profit, если его нет
            if (!Schema::hasColumn('upgrades', 'profit')) {
                $table->integer('profit')->nullable()->after('price_win');
            }

            // Проверяем и добавляем percent, если его нет
            if (!Schema::hasColumn('upgrades', 'percent')) {
                $table->decimal('percent', 5, 2)->nullable()->after('profit');
            }

            // Проверяем и добавляем status, если его нет
            if (!Schema::hasColumn('upgrades', 'status')) {
                $table->string('status')->default('LOSE')->after('percent');
            }

            // Проверяем и добавляем base_chance, если его нет
            if (!Schema::hasColumn('upgrades', 'base_chance')) {
                $table->decimal('base_chance', 5, 2)->nullable()->after('status');
            }

            // Проверяем и добавляем game_chance, если его нет
            if (!Schema::hasColumn('upgrades', 'game_chance')) {
                $table->decimal('game_chance', 5, 2)->nullable()->after('base_chance');
            }

            // Проверяем и добавляем random_float, если его нет
            if (!Schema::hasColumn('upgrades', 'random_float')) {
                $table->decimal('random_float', 10, 8)->nullable()->after('game_chance');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('upgrades', function (Blueprint $table) {
            // Удаляем только те колонки, которые мы добавили
            if (Schema::hasColumn('upgrades', 'base_chance')) {
                $table->dropColumn('base_chance');
            }
            if (Schema::hasColumn('upgrades', 'game_chance')) {
                $table->dropColumn('game_chance');
            }
            if (Schema::hasColumn('upgrades', 'random_float')) {
                $table->dropColumn('random_float');
            }
        });
    }
};
