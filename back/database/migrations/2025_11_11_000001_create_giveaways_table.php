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
        Schema::create('giveaways', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('drop_id');
            $table->dateTime('started_at');
            $table->dateTime('finished_at');
            $table->integer('min_deposit');
            $table->enum('type', ['hourly', 'daily', 'weekly']);
            $table->enum('status', ['IN PROCESS', 'FINISHED', 'FAILED'])->default('IN PROCESS');
            $table->unsignedBigInteger('winner_id')->nullable();
            $table->timestamps();

            $table->foreign('drop_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('winner_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['type', 'status']);
            $table->index('finished_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giveaways');
    }
};

