<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'gender' => $this->gender,
            'birthDate' => $this->birth_date?->format('Y-m-d'),
            'age' => $this->age,
            'height' => $this->height,
            'weight' => $this->weight,
            'targetWeight' => $this->target_weight,
            'weeklyTarget' => $this->weekly_target,
            'bmi' => $this->bmi,
            'goal' => $this->goal,
            'activityLevel' => $this->activity_level,
            'tdeeGoal' => $this->tdee_goal,
            'dailyCalories' => $this->daily_calories,
            'dailyProtein' => $this->daily_protein,
            'dailyCarbs' => $this->daily_carbs,
            'dailyFats' => $this->daily_fats,
            'dailyWater' => $this->daily_water,
            'dietaryPreferences' => $this->dietary_preferences,
            'allergies' => $this->allergies,
            'healthConditions' => $this->health_conditions,
            'preferredLanguage' => $this->preferred_language,
            'timezone' => $this->timezone,
            'notificationsEnabled' => $this->notifications_enabled,
            'triedAnotherApp' => $this->tried_another_app,
            'hearingAboutUs' => $this->hearing_about_us,
        ];
    }
}
