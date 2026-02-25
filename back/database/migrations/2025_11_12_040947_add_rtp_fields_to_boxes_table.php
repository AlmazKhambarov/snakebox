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
        Schema::table('boxes', function (Blueprint $table) {
            $table->decimal('current_rtp', 5, 2)->default(95.00)->after('price')->comment('Текущий RTP кейса в процентах');
            $table->decimal('target_rtp', 5, 2)->default(95.00)->after('current_rtp')->comment('Целевой RTP кейса');
            $table->decimal('min_rtp', 5, 2)->default(85.00)->after('target_rtp')->comment('Минимальный RTP');
            $table->decimal('max_rtp', 5, 2)->default(98.00)->after('min_rtp')->comment('Максимальный RTP');
            $table->bigInteger('total_opened')->default(0)->after('max_rtp')->comment('Всего открыто кейсов');
            $table->bigInteger('total_spent')->default(0)->after('total_opened')->comment('Всего потрачено (в копейках)');
            $table->bigInteger('total_won')->default(0)->after('total_spent')->comment('Всего выиграно (в копейках)');
            $table->timestamp('last_rtp_update')->nullable()->after('total_won')->comment('Последнее обновление RTP');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn([
                'current_rtp',
                'target_rtp',
                'min_rtp',
                'max_rtp',
                'total_opened',
                'total_spent',
                'total_won',
                'last_rtp_update'
            ]);
        });
    }
};
