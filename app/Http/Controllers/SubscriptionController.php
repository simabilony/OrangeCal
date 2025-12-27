<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\CreatePaymentRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    public function createPayment(CreatePaymentRequest $request): SubscriptionResource|JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        if (isset($data['receipt'])) {
            $platform = $data['payment_method'] === 'apple' ? 'apple' : 'google';
            $isValid = $this->subscriptionService->verifyReceipt($data['receipt'], $platform);

            if (!$isValid) {
                return response()->json(['message' => 'Invalid receipt'], 400);
            }
        }

        $planDetails = $this->getPlanDetails($data['plan_type']);

        $subscription = $this->subscriptionService->createSubscription($user, [
            'plan_name' => $planDetails['name'],
            'plan_type' => $data['plan_type'],
            'price' => $planDetails['price'],
            'currency' => 'SAR',
            'duration_days' => $planDetails['duration_days'],
            'payment_method' => $data['payment_method'],
            'transaction_id' => $data['store_transaction_id'] ?? uniqid('txn_'),
            'store_product_id' => $data['store_product_id'] ?? null,
            'store_transaction_id' => $data['store_transaction_id'] ?? null,
            'store_receipt' => $data['receipt'] ?? null,
            'auto_renew' => true,
        ]);

        return new SubscriptionResource($subscription);
    }

    public function getSubscriptionStatus(Request $request): JsonResponse
    {
        $user = $request->user();
        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest()
            ->first();

        return response()->json([
            'has_active_subscription' => $subscription !== null,
            'is_premium' => $user->is_premium,
            'subscription' => $subscription ? new SubscriptionResource($subscription) : null,
        ]);
    }

    public function cancelSubscription(Request $request): SubscriptionResource|JsonResponse
    {
        $user = $request->user();
        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'No active subscription found'], 404);
        }

        $this->subscriptionService->cancelSubscription($subscription);

        return new SubscriptionResource($subscription->fresh());
    }

    protected function getPlanDetails(string $planType): array
    {
        $plans = [
            'monthly' => [
                'name' => ['ar' => 'اشتراك شهري', 'en' => 'Monthly Subscription'],
                'price' => 29.99,
                'duration_days' => 30,
            ],
            'quarterly' => [
                'name' => ['ar' => 'اشتراك ربع سنوي', 'en' => 'Quarterly Subscription'],
                'price' => 79.99,
                'duration_days' => 90,
            ],
            'yearly' => [
                'name' => ['ar' => 'اشتراك سنوي', 'en' => 'Yearly Subscription'],
                'price' => 249.99,
                'duration_days' => 365,
            ],
        ];

        return $plans[$planType] ?? $plans['monthly'];
    }
}

