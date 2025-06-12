<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Models\AppStoreWebhookLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppStoreNotificationService
{
    private AppleJWTService $jwtService;

    public function __construct(AppleJWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Process Apple App Store Server notification
     */
    public function processNotification(array $payload): bool
    {
        $webhookLog = null;

        try {
            // Create webhook log entry
            $webhookLog = $this->createWebhookLog($payload);
            
            // Extract and decode the signed payload
            $signedPayload = $payload['signedPayload'] ?? null;
            if (!$signedPayload) {
                throw new \Exception('Missing signedPayload in notification');
            }

            // Verify and decode the notification
            $decodedPayload = $this->jwtService->verifyAndDecodePayload($signedPayload);
            
            // Update webhook log with decoded data
            $webhookLog->update([
                'decoded_payload' => $decodedPayload,
                'notification_type' => $decodedPayload['notificationType'] ?? 'unknown',
                'subtype' => $decodedPayload['subtype'] ?? null,
                'notification_uuid' => $decodedPayload['notificationUUID'] ?? null,
                'notification_timestamp' => isset($decodedPayload['signedDate']) 
                    ? Carbon::createFromTimestamp($decodedPayload['signedDate'] / 1000)
                    : now(),
            ]);

            // Process the notification based on type
            $result = $this->handleNotificationType($decodedPayload, $webhookLog);
            
            if ($result) {
                $webhookLog->markAsProcessed();
                Log::info('App Store notification processed successfully', [
                    'type' => $decodedPayload['notificationType'],
                    'subtype' => $decodedPayload['subtype'] ?? null,
                    'log_id' => $webhookLog->id
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            if ($webhookLog) {
                $webhookLog->markAsFailed($e->getMessage());
            }

            Log::error('Failed to process App Store notification', [
                'error' => $e->getMessage(),
                'payload' => $payload,
                'log_id' => $webhookLog?->id
            ]);

            return false;
        }
    }

    /**
     * Handle different notification types
     */
    private function handleNotificationType(array $decodedPayload, AppStoreWebhookLog $webhookLog): bool
    {
        $notificationType = $decodedPayload['notificationType'];
        $subtype = $decodedPayload['subtype'] ?? null;
        $data = $decodedPayload['data'] ?? [];

        Log::info("Processing notification type: {$notificationType}", [
            'subtype' => $subtype,
            'log_id' => $webhookLog->id
        ]);

        return match ($notificationType) {
            'SUBSCRIBED' => $this->handleSubscribed($data, $webhookLog),
            'DID_RENEW' => $this->handleDidRenew($data, $webhookLog),
            'EXPIRED' => $this->handleExpired($data, $webhookLog),
            'DID_CHANGE_RENEWAL_STATUS' => $this->handleDidChangeRenewalStatus($data, $webhookLog),
            'DID_CHANGE_RENEWAL_PREF' => $this->handleDidChangeRenewalPref($data, $webhookLog),
            'OFFER_REDEEMED' => $this->handleOfferRedeemed($data, $webhookLog),
            'REFUND' => $this->handleRefund($data, $webhookLog),
            'REVOKE' => $this->handleRevoke($data, $webhookLog),
            'PRICE_INCREASE' => $this->handlePriceIncrease($data, $webhookLog),
            'GRACE_PERIOD_EXPIRED' => $this->handleGracePeriodExpired($data, $webhookLog),
            default => $this->handleUnknownNotification($notificationType, $data, $webhookLog),
        };
    }

    /**
     * Handle SUBSCRIBED notification
     */
    private function handleSubscribed(array $data, AppStoreWebhookLog $webhookLog): bool
    {
        return DB::transaction(function () use ($data, $webhookLog) {
            $transactionInfo = $this->decodeTransactionInfo($data['signedTransactionInfo']);
            $renewalInfo = $this->decodeRenewalInfo($data['signedRenewalInfo']);

            $originalTransactionId = $transactionInfo['originalTransactionId'];
            $user = $this->findUserByTransactionData($transactionInfo);

            if (!$user) {
                Log::warning('User not found for subscription', [
                    'original_transaction_id' => $originalTransactionId,
                    'app_account_token' => $transactionInfo['appAccountToken'] ?? null,
                    'log_id' => $webhookLog->id
                ]);
                return true; // Acknowledge receipt but don't process
            }

            // Update webhook log with transaction info
            $webhookLog->update([
                'original_transaction_id' => $originalTransactionId,
                'bundle_id' => $transactionInfo['bundleId'] ?? null,
            ]);

            // Create or update subscription
            $subscription = Subscription::updateOrCreate(
                ['original_transaction_id' => $originalTransactionId],
                [
                    'user_id' => $user->id,
                    'web_order_line_item_id' => $transactionInfo['webOrderLineItemId'] ?? null,
                    'product_id' => $transactionInfo['productId'],
                    'subscription_group_identifier' => $transactionInfo['subscriptionGroupIdentifier'] ?? null,
                    'status' => 'active',
                    'purchased_at' => Carbon::createFromTimestamp($transactionInfo['purchaseDate'] / 1000),
                    'expires_at' => Carbon::createFromTimestamp($transactionInfo['expiresDate'] / 1000),
                    'auto_renew_status' => $renewalInfo['autoRenewStatus'] ?? true,
                    'auto_renew_product_id' => $renewalInfo['autoRenewProductId'] ?? null,
                    'latest_transaction_info' => $transactionInfo,
                    'latest_renewal_info' => $renewalInfo,
                ]
            );

            // Enable premium features for user
            $this->enablePremiumFeatures($user, $subscription);

            Log::info('Subscription created/updated', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'product_id' => $subscription->product_id,
                'log_id' => $webhookLog->id
            ]);

            return true;
        });
    }

    /**
     * Handle DID_RENEW notification
     */
    private function handleDidRenew(array $data, AppStoreWebhookLog $webhookLog): bool
    {
        return DB::transaction(function () use ($data, $webhookLog) {
            $transactionInfo = $this->decodeTransactionInfo($data['signedTransactionInfo']);
            $renewalInfo = $this->decodeRenewalInfo($data['signedRenewalInfo']);

            $originalTransactionId = $transactionInfo['originalTransactionId'];
            $subscription = Subscription::where('original_transaction_id', $originalTransactionId)->first();

            if (!$subscription) {
                Log::warning('Subscription not found for renewal', [
                    'original_transaction_id' => $originalTransactionId,
                    'log_id' => $webhookLog->id
                ]);
                return true;
            }

            // Update subscription with renewal data
            $subscription->update([
                'status' => 'active',
                'expires_at' => Carbon::createFromTimestamp($transactionInfo['expiresDate'] / 1000),
                'auto_renew_status' => $renewalInfo['autoRenewStatus'] ?? true,
                'auto_renew_product_id' => $renewalInfo['autoRenewProductId'] ?? null,
                'latest_transaction_info' => $transactionInfo,
                'latest_renewal_info' => $renewalInfo,
            ]);

            // Update webhook log
            $webhookLog->update(['original_transaction_id' => $originalTransactionId]);

            // Ensure premium features are still active
            $this->enablePremiumFeatures($subscription->user, $subscription);

            Log::info('Subscription renewed', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'new_expires_at' => $subscription->expires_at,
                'log_id' => $webhookLog->id
            ]);

            return true;
        });
    }

    /**
     * Handle EXPIRED notification
     */
    private function handleExpired(array $data, AppStoreWebhookLog $webhookLog): bool
    {
        return DB::transaction(function () use ($data, $webhookLog) {
            $transactionInfo = $this->decodeTransactionInfo($data['signedTransactionInfo']);
            $originalTransactionId = $transactionInfo['originalTransactionId'];

            $subscription = Subscription::where('original_transaction_id', $originalTransactionId)->first();

            if (!$subscription) {
                Log::warning('Subscription not found for expiration', [
                    'original_transaction_id' => $originalTransactionId,
                    'log_id' => $webhookLog->id
                ]);
                return true;
            }

            // Update subscription status
            $subscription->update([
                'status' => 'expired',
                'latest_transaction_info' => $transactionInfo,
            ]);

            // Update webhook log
            $webhookLog->update(['original_transaction_id' => $originalTransactionId]);

            // Disable premium features
            $this->disablePremiumFeatures($subscription->user, $subscription);

            Log::info('Subscription expired', [
                'subscription_id' => $subscription->id,
                'user_id' => $subscription->user_id,
                'log_id' => $webhookLog->id
            ]);

            return true;
        });
    }

    /**
     * Handle DID_CHANGE_RENEWAL_STATUS notification
     */
    private function handleDidChangeRenewalStatus(array $data, AppStoreWebhookLog $webhookLog): bool
    {
        $transactionInfo = $this->decodeTransactionInfo($data['signedTransactionInfo']);
        $renewalInfo = $this->decodeRenewalInfo($data['signedRenewalInfo']);

        $originalTransactionId = $transactionInfo['originalTransactionId'];
        $subscription = Subscription::where('original_transaction_id', $originalTransactionId)->first();

        if ($subscription) {
            $subscription->update([
                'auto_renew_status' => $renewalInfo['autoRenewStatus'] ?? false,
                'latest_renewal_info' => $renewalInfo,
            ]);

            $webhookLog->update(['original_transaction_id' => $originalTransactionId]);

            Log::info('Subscription renewal status changed', [
                'subscription_id' => $subscription->id,
                'auto_renew_status' => $subscription->auto_renew_status,
                'log_id' => $webhookLog->id
            ]);
        }

        return true;
    }

    /**
     * Handle REFUND notification
     */
    private function handleRefund(array $data, AppStoreWebhookLog $webhookLog): bool
    {
        return DB::transaction(function () use ($data, $webhookLog) {
            $transactionInfo = $this->decodeTransactionInfo($data['signedTransactionInfo']);
            $originalTransactionId = $transactionInfo['originalTransactionId'];

            $subscription = Subscription::where('original_transaction_id', $originalTransactionId)->first();

            if ($subscription) {
                $subscription->update([
                    'status' => 'refunded',
                    'latest_transaction_info' => $transactionInfo,
                ]);

                $webhookLog->update(['original_transaction_id' => $originalTransactionId]);

                // Disable premium features
                $this->disablePremiumFeatures($subscription->user, $subscription);

                Log::info('Subscription refunded', [
                    'subscription_id' => $subscription->id,
                    'user_id' => $subscription->user_id,
                    'log_id' => $webhookLog->id
                ]);
            }

            return true;
        });
    }

    /**
     * Handle unknown notification types
     */
    private function handleUnknownNotification(string $notificationType, array $data, AppStoreWebhookLog $webhookLog): bool
    {
        Log::warning('Received unknown notification type', [
            'notification_type' => $notificationType,
            'data' => $data,
            'log_id' => $webhookLog->id
        ]);

        return true; // Acknowledge receipt
    }

    /**
     * Create webhook log entry
     */
    private function createWebhookLog(array $payload): AppStoreWebhookLog
    {
        return AppStoreWebhookLog::create([
            'notification_type' => 'unknown',
            'status' => 'pending',
            'payload' => $payload,
        ]);
    }

    /**
     * Decode transaction info from signed JWT
     */
    private function decodeTransactionInfo(string $signedTransactionInfo): array
    {
        return $this->jwtService->verifyAndDecodePayload($signedTransactionInfo);
    }

    /**
     * Decode renewal info from signed JWT
     */
    private function decodeRenewalInfo(string $signedRenewalInfo): array
    {
        return $this->jwtService->verifyAndDecodePayload($signedRenewalInfo);
    }

    /**
     * Find user by transaction data
     */
    private function findUserByTransactionData(array $transactionInfo): ?User
    {
        $appAccountToken = $transactionInfo['appAccountToken'] ?? null;

        if ($appAccountToken) {
            // Try to find by app account token (which should be your user ID)
            if (is_numeric($appAccountToken)) {
                $user = User::find($appAccountToken);
                if ($user) return $user;
            }

            // Try to find by email or username if appAccountToken contains that
            $user = User::where('email', $appAccountToken)
                       ->orWhere('username', $appAccountToken)
                       ->first();
            if ($user) return $user;
        }

        // If no appAccountToken or user not found, you might need to implement
        // additional logic based on your app's user identification strategy
        
        Log::warning('Could not identify user from transaction data', [
            'app_account_token' => $appAccountToken,
            'original_transaction_id' => $transactionInfo['originalTransactionId'] ?? null,
        ]);

        return null;
    }

    /**
     * Enable premium features for user
     */
    private function enablePremiumFeatures(User $user, Subscription $subscription): void
    {
        // Update user model with subscription info
        $user->update([
            'is_premium' => true,
            'subscription_status' => 'active',
            'subscribed_product' => $subscription->product_id,
            'subscription_expires_at' => $subscription->expires_at,
        ]);

        // You can add more logic here:
        // - Send welcome email
        // - Enable specific features
        // - Update user permissions
        // - Send push notification
        
        Log::info('Premium features enabled for user', [
            'user_id' => $user->id,
            'product_id' => $subscription->product_id,
            'expires_at' => $subscription->expires_at,
        ]);
    }

    /**
     * Disable premium features for user
     */
    private function disablePremiumFeatures(User $user, Subscription $subscription): void
    {
        // Update user model
        $user->update([
            'is_premium' => false,
            'subscription_status' => $subscription->status,
        ]);

        // You can add more logic here:
        // - Send cancellation email
        // - Disable specific features
        // - Update user permissions
        // - Send push notification
        
        Log::info('Premium features disabled for user', [
            'user_id' => $user->id,
            'reason' => $subscription->status,
        ]);
    }

    // Add placeholder methods for other notification types
    private function handleDidChangeRenewalPref(array $data, AppStoreWebhookLog $webhookLog): bool
    {
        // Implement based on your needs
        return true;
    }

    private function handleOfferRedeemed(array $data, AppStoreWebhookLog $webhookLog): bool
    {
        // Implement based on your needs
        return true;
    }

    private function handleRevoke(array $data, AppStoreWebhookLog $webhookLog): bool
    {
        // Implement similar to refund
        return $this->handleRefund($data, $webhookLog);
    }

    private function handlePriceIncrease(array $data, AppStoreWebhookLog $webhookLog): bool
    {
        // Implement based on your needs
        return true;
    }

    private function handleGracePeriodExpired(array $data, AppStoreWebhookLog $webhookLog): bool
    {
        // Implement similar to expired
        return $this->handleExpired($data, $webhookLog);
    }
}