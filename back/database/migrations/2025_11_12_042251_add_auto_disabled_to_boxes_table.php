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
            $table->boolean('auto_disabled')->default(false)->after('is_active')->comment('Автоматически отключен из-за высокого RTP');
            $table->string('auto_disabled_reason')->nullable()->after('auto_disabled')->comment('Причина автоотключения');
            $table->timestamp('auto_disabled_at')->nullable()->after('auto_disabled_reason')->comment('Время автоотключения');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            $table->dropColumn(['auto_disabled', 'auto_disabled_reason', 'auto_disabled_at']);
        });
    }
};
