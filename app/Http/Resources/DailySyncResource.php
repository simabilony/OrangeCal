<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailySyncResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'syncDate' => $this->sync_date,
            'totalCaloriesConsumed' => $this->total_calories_consumed,
            'totalCaloriesBurned' => $this->total_calories_burned,
            'netCalories' => $this->net_calories,
            'totalProtein' => $this->total_protein,
            'totalCarbs' => $this->total_carbs,
            'totalFats' => $this->total_fats,
            'totalFiber' => $this->total_fiber,
            'totalSugar' => $this->total_sugar,
            'waterIntake' => $this->water_intake,
            'totalExerciseMinutes' => $this->total_exercise_minutes,
            'totalSteps' => $this->total_steps,
            'calorieGoal' => $this->calorie_goal,
            'goalProgress' => $this->goal_progress,
            'weightLogged' => $this->weight_logged,
            'isComplete' => $this->is_complete,
            'remainingCalories' => $this->getRemainingCalories(),
            'macroPercentages' => $this->getMacroPercentages(),
            'lastSyncedAt' => $this->last_synced_at,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
