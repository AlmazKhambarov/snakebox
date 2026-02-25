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
        Schema::create('upgrade_rtp_stats', function (Blueprint $table) {
            $table->id();
            $table->integer('total_upgrades')->default(0); // Всего апгрейдов
            $table->bigInteger('total_spent')->default(0); // Всего потрачено (копейки)
            $table->bigInteger('total_won')->default(0); // Всего выиграно (копейки)
            $table->decimal('target_rtp', 5, 2)->default(92.0); // Целевой RTP
            $table->decimal('min_rtp', 5, 2)->default(88.0); // Минимальный RTP
            $table->decimal('max_rtp', 5, 2)->default(96.0); // Максимальный RTP
            $table->decimal('current_rtp', 5, 2)->default(92.0); // Текущий RTP
            $table->timestamp('last_rtp_update')->nullable(); // Последнее обновление RTP
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upgrade_rtp_stats');
    }
};
