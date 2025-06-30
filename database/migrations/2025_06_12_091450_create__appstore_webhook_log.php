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
        Schema::create('appstore_webhook_log', function (Blueprint $table) {
           $table->id();
            $table->string('notification_type');
            $table->string('subtype')->nullable();
            $table->string('notification_uuid')->nullable();
            $table->string('original_transaction_id')->nullable();
            $table->string('bundle_id')->nullable();
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->json('payload');
            $table->json('decoded_payload')->nullable();
            $table->timestamp('notification_timestamp')->nullable();
            $table->timestamps();

            $table->index(['notification_type', 'status']);
            $table->index('original_transaction_id');
            $table->index('notification_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appstore_webhook_log');
    }
};
