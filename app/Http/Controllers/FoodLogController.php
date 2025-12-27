<?php

namespace App\Http\Controllers;

use App\Http\Requests\Food\SaveFoodLogRequest;
use App\Http\Requests\Food\SaveMealLogRequest;
use App\Http\Requests\Food\UpdateFoodLogRequest;
use App\Http\Resources\FoodLogResource;
use App\Models\FoodLog;
use App\Models\Food;
use App\Models\UserMeal;
use App\Models\DailySync;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FoodLogController extends Controller
{
    public function getFoodLogs(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $date = $request->input('date', today()->toDateString());

        $foodLogs = $user->foodLogs()
            ->whereDate('log_date', $date)
            ->with(['food', 'userMeal'])
            ->orderBy('log_time', 'asc')
            ->get();

        return FoodLogResource::collection($foodLogs);
    }

    public function saveFoodFromDb(SaveFoodLogRequest $request): FoodLogResource
    {
        $data = $request->validated();
        $user = $request->user();
        $food = Food::findOrFail($data['food_id']);

        $grams = $data['grams'] ?? ($data['quantity'] * 100);
        $nutrition = $food->calculateNutritionForGrams($grams, 100);

        $foodLog = FoodLog::query()->create(array_merge($data, [
            'user_id' => $user->id,
            'food_id' => $food->id,
            'name' => $food->getTranslation('name', app()->getLocale()),
            'log_time' => $data['log_time'] ?? now()->format('H:i'),
            'grams' => $grams,
            'calories' => $nutrition['calories'],
            'protein' => $nutrition['protein'],
            'carbs' => $nutrition['carbs'],
            'fats' => $nutrition['fats'],
            'fiber' => $nutrition['fiber'],
            'sugar' => $nutrition['sugar'],
            'source' => 'database',
            'is_halal' => $food->is_halal,
        ]));

        DailySync::updateOrCreateForDate($user->id, $data['log_date']);

        return new FoodLogResource($foodLog->load('food'));
    }

    public function saveUserMeal(SaveMealLogRequest $request): AnonymousResourceCollection
    {
        $data = $request->validated();
        $user = $request->user();
        $meal = UserMeal::findOrFail($data['meal_id']);

        $servings = $data['servings'] ?? 1;
        $multiplier = $servings / ($meal->servings ?? 1);

        $foodLogs = [];
        foreach ($meal->ingredients as $ingredient) {
            $foodLog = FoodLog::query()->create([
                'user_id' => $user->id,
                'user_meal_id' => $meal->id,
                'food_id' => $ingredient->food_id,
                'name' => $ingredient->food->getTranslation('name', app()->getLocale()),
                'log_date' => $data['log_date'],
                'log_time' => $data['log_time'] ?? now()->format('H:i'),
                'meal_type' => $data['meal_type'],
                'quantity' => $ingredient->quantity * $multiplier,
                'unit' => $ingredient->unit,
                'grams' => $ingredient->grams * $multiplier,
                'calories' => $ingredient->calories * $multiplier,
                'protein' => $ingredient->protein * $multiplier,
                'carbs' => $ingredient->carbs * $multiplier,
                'fats' => $ingredient->fats * $multiplier,
                'source' => 'meal',
            ]);
            $foodLogs[] = $foodLog;
        }

        DailySync::updateOrCreateForDate($user->id, $data['log_date']);

        return FoodLogResource::collection($foodLogs);
    }

    public function updateFoodLog(UpdateFoodLogRequest $request, int $id): FoodLogResource
    {
        $data = $request->validated();
        $user = $request->user();
        $foodLog = $user->foodLogs()->findOrFail($id);

        if (isset($data['quantity']) || isset($data['grams'])) {
            if ($foodLog->food) {
                $grams = $data['grams'] ?? ($data['quantity'] * 100);
                $nutrition = $foodLog->food->calculateNutritionForGrams($grams, 100);
                $data = array_merge($data, $nutrition);
            }
        }

        $foodLog->update($data);
        DailySync::updateOrCreateForDate($user->id, $foodLog->log_date);

        return new FoodLogResource($foodLog->fresh()->load('food', 'userMeal'));
    }

    public function deleteFoodLog(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $foodLog = $user->foodLogs()->findOrFail($id);
        $logDate = $foodLog->log_date;

        $foodLog->delete();
        DailySync::updateOrCreateForDate($user->id, $logDate);

        return response()->json(['message' => 'Food log deleted successfully']);
    }
}

