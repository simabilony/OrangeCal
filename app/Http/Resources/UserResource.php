<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'isActive' => $this->is_active,
            'isOnboarded' => $this->is_onboarded,
            'isPremium' => $this->is_premium,
            'profile' => $this->whenLoaded('profile', fn() => new UserProfileResource($this->profile)),
            'mealSchedules' => $this->whenLoaded('mealSchedules', fn() => MealScheduleResource::collection($this->mealSchedules)),
            'createdAt' => $this->created_at->toDateTimeString(),
            'updatedAt' => $this->updated_at->toDateTimeString(),
        ];
    }
}
