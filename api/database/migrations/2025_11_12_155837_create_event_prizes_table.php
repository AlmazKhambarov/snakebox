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
        Schema::create('event_prizes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->integer('position'); // Место (1-50)
            $table->unsignedBigInteger('item_id')->nullable(); // Конкретный скин (если задан)
            $table->integer('min_price')->nullable(); // Минимальная цена для случайного выбора
            $table->integer('max_price')->nullable(); // Максимальная цена для случайного выбора
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('event')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('set null');
            $table->unique(['event_id', 'position']);
            $table->index('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_prizes');
    }
};
