<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * Create subscription from payment.
     */
    public function createSubscription(User $user, array $data): Subscription
    {
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_name' => $data['plan_name'] ?? [],
            'plan_type' => $data['plan_type'],
            'price' => $data['price'],
            'currency' => $data['currency'] ?? 'SAR',
            'starts_at' => now(),
            'ends_at' => now()->addDays($data['duration_days'] ?? 30),
            'status' => 'active',
            'auto_renew' => $data['auto_renew'] ?? true,
            'payment_method' => $data['payment_method'],
            'transaction_id' => $data['transaction_id'],
            'receipt_url' => $data['receipt_url'] ?? null,
            'store_product_id' => $data['store_product_id'] ?? null,
            'store_transaction_id' => $data['store_transaction_id'] ?? null,
            'store_receipt' => $data['store_receipt'] ?? null,
        ]);

        $user->update(['is_premium' => true]);

        return $subscription;
    }

    /**
     * Cancel subscription.
     */
    public function cancelSubscription(Subscription $subscription): Subscription
    {
        $subscription->cancel();

        return $subscription->fresh();
    }

    /**
     * Verify receipt (Apple/Google).
     */
    public function verifyReceipt(array $receiptData, string $platform): bool
    {
        try {
            if ($platform === 'apple') {
                return $this->verifyAppleReceipt($receiptData);
            } elseif ($platform === 'google') {
                return $this->verifyGoogleReceipt($receiptData);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Receipt verification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify Apple receipt.
     */
    protected function verifyAppleReceipt(array $receiptData): bool
    {
        // Implement Apple receipt verification
        // This is a placeholder
        return true;
    }

    /**
     * Verify Google receipt.
     */
    protected function verifyGoogleReceipt(array $receiptData): bool
    {
        // Implement Google receipt verification
        // This is a placeholder
        return true;
    }
}






