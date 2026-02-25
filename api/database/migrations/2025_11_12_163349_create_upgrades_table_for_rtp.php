<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Проверяем, существует ли таблица upgrades
        if (!Schema::hasTable('upgrades')) {
            Schema::create('upgrades', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('item_id')->nullable(); // Предмет который использовали
                $table->unsignedBigInteger('win_id')->nullable(); // Предмет который выиграли
                $table->integer('price'); // Цена использованного предмета
                $table->integer('price_win')->nullable(); // Цена выигранного предмета
                $table->integer('profit')->nullable(); // Прибыль/убыток
                $table->decimal('percent', 5, 2)->nullable(); // Процент шанса
                $table->string('status')->default('LOSE'); // WIN или LOSE
                $table->decimal('base_chance', 5, 2)->nullable(); // Базовый шанс
                $table->decimal('game_chance', 5, 2)->nullable(); // Финальный шанс с учетом RTP
                $table->decimal('random_float', 10, 8)->nullable(); // Случайное число от provably fair
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('item_id')->references('id')->on('items')->onDelete('set null');
                $table->foreign('win_id')->references('id')->on('items')->onDelete('set null');
                $table->index('user_id');
                $table->index('status');
                $table->index('created_at');
            });
        } else {
            // Если таблица существует, добавляем недостающие поля
            Schema::table('upgrades', function (Blueprint $table) {
                if (!Schema::hasColumn('upgrades', 'base_chance')) {
                    $table->decimal('base_chance', 5, 2)->nullable()->after('percent');
                }
                if (!Schema::hasColumn('upgrades', 'game_chance')) {
                    $table->decimal('game_chance', 5, 2)->nullable()->after('base_chance');
                }
                if (!Schema::hasColumn('upgrades', 'random_float')) {
                    $table->decimal('random_float', 10, 8)->nullable()->after('game_chance');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Не удаляем таблицу, только убираем добавленные поля
        Schema::table('upgrades', function (Blueprint $table) {
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
