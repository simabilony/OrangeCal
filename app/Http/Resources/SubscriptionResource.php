<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'planName' => $this->getTranslation('plan_name', $locale),
            'planType' => $this->plan_type,
            'price' => $this->price,
            'currency' => $this->currency,
            'startsAt' => $this->starts_at,
            'endsAt' => $this->ends_at,
            'trialEndsAt' => $this->trial_ends_at,
            'status' => $this->status,
            'autoRenew' => $this->auto_renew,
            'paymentMethod' => $this->payment_method,
            'daysRemaining' => $this->daysRemaining(),
            'isActive' => $this->isActive(),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
