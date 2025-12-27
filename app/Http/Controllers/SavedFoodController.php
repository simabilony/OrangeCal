<?php

namespace App\Http\Controllers;

use App\Http\Resources\FoodResource;
use App\Http\Resources\MealResource;
use App\Models\Food;
use App\Models\UserMeal;
use App\Models\SavedFood;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SavedFoodController extends Controller
{
    public function getSavedFoods(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $savedFoods = $user->savedFoods()
            ->withPivot(['notes', 'custom_name', 'default_quantity', 'default_unit'])
            ->get();

        return FoodResource::collection($savedFoods);
    }

    public function saveFood(Request $request, int $foodId): JsonResponse
    {
        $user = $request->user();
        $food = Food::findOrFail($foodId);

        if ($user->savedFoods()->where('food_id', $foodId)->exists()) {
            return response()->json(['message' => 'Food already saved'], 400);
        }

        $user->savedFoods()->attach($foodId);

        return response()->json(['message' => 'Food saved successfully'], 201);
    }

    public function saveMeal(Request $request, int $mealId): MealResource
    {
        $user = $request->user();
        $meal = UserMeal::findOrFail($mealId);

        return new MealResource($meal);
    }

    public function removeSavedFood(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        if (!$user->savedFoods()->where('food_id', $id)->exists()) {
            return response()->json(['message' => 'Food not found in saved foods'], 404);
        }

        $user->savedFoods()->detach($id);

        return response()->json(['message' => 'Saved food removed successfully']);
    }
}

