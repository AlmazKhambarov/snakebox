<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'type')) {
                $table->string('type')->nullable()->after('status');
            }
            if (!Schema::hasColumn('payments', 'price')) {
                $table->bigInteger('price')->unsigned()->nullable()->after('amount');
            }
            if (!Schema::hasColumn('payments', 'pubg_uid')) {
                $table->string('pubg_uid')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['type', 'pubg_uid', 'price']);
        });
    }
};
