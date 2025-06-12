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
        Schema::create('apple_notifications', function (Blueprint $table) {
              $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('original_transaction_id')->unique();
            $table->string('web_order_line_item_id')->nullable();
            $table->string('product_id');
            $table->string('subscription_group_identifier')->nullable();
            $table->enum('status', [
                'active', 
                'expired', 
                'cancelled', 
                'refunded',
                'revoked',
                'billing_retry',
                'billing_grace_period'
            ])->default('active');
            $table->timestamp('purchased_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('grace_period_expires_at')->nullable();
            $table->boolean('auto_renew_status')->default(true);
            $table->string('auto_renew_product_id')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->string('price_increase_status')->nullable();
            $table->json('latest_transaction_info')->nullable();
            $table->json('latest_renewal_info')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('original_transaction_id');
            $table->index('product_id');
            $table->index('expires_at');
             });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apple_notifications');
    }
};
