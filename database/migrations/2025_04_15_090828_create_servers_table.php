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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('android')->default(false);
            $table->boolean('ios')->default(false);
            $table->boolean('macos')->default(false);
            $table->boolean('windows')->default(false);
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->enum('type', ['free', 'premium'])->default('free'); // Free or Premium Server
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
