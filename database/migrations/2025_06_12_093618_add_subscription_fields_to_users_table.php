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
          Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false);
            $table->string('subscription_status')->nullable();
            $table->string('subscribed_product')->nullable();
            $table->timestamp('subscription_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_premium',
                'subscription_status',
                'subscribed_product',
                'subscription_expires_at'
            ]);
            });
    }
};
