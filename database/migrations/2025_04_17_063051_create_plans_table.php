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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->integer('duration');
            $table->enum('duration_unit', ['day', 'week', 'month', 'year'])->default('day');
            $table->string('stripe_price_id')->nullable();
            $table->integer('trial_days')->default(0);
            $table->boolean('is_best_deal')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
