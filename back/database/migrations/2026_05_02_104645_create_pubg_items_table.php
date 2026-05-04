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
        Schema::create('pubg_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->string('rarity')->nullable();
            $table->string('quality')->nullable();
            $table->string('skin_name')->nullable();
            $table->string('weapon')->nullable();
            $table->integer('steam_price'); // in cents/coins
            $table->integer('steam_price_before')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pubg_items');
    }
};
