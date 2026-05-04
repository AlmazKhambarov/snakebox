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
        Schema::table('case_items', function (Blueprint $table) {
            $table->boolean('droppable')->default(true)->after('chance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_items', function (Blueprint $table) {
            $table->dropColumn('droppable');
        });
    }
};
