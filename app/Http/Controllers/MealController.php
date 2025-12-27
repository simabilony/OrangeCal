<?php

namespace App\Http\Controllers;

use App\Http\Requests\Meal\CreateMealRequest;
use App\Http\Requests\Meal\UpdateMealRequest;
use App\Http\Resources\MealResource;
use App\Models\UserMeal;
use App\Models\MealIngredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class MealController extends Controller
{
    public function getUserMeals(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $meals = $user->meals()
            ->with('ingredients.food')
            ->orderBy('created_at', 'desc')
            ->get();

        return MealResource::collection($meals);
    }

    public function createMeal(CreateMealRequest $request): MealResource
    {
        $data = $request->validated();
        $user = $request->user();

        // Handle image upload
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('meal-images', 'public');
            $imageUrl = Storage::url($path);
        }

        // Create meal
        $meal = $user->meals()->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'meal_type' => $data['meal_type'] ?? null,
            'servings' => $data['servings'] ?? 1,
            'prep_time' => $data['prep_time'] ?? null,
            'instructions' => $data['instructions'] ?? null,
            'image_url' => $imageUrl,
            'is_favorite' => $data['is_favorite'] ?? false,
            'is_public' => $data['is_public'] ?? false,
        ]);

        // Create ingredients and calculate totals
        $totalCalories = 0;
        $totalProtein = 0;
        $totalCarbs = 0;
        $totalFats = 0;

        foreach ($data['ingredients'] as $ingredientData) {
            $food = \App\Models\Food::findOrFail($ingredientData['food_id']);
            $grams = $ingredientData['grams'] ?? ($ingredientData['quantity'] * 100);
            $nutrition = $food->calculateNutritionForGrams($grams, 100);

            MealIngredient::create([
                'user_meal_id' => $meal->id,
                'food_id' => $food->id,
                'quantity' => $ingredientData['quantity'],
                'unit' => $ingredientData['unit'],
                'grams' => $grams,
                'calories' => $nutrition['calories'],
                'protein' => $nutrition['protein'],
                'carbs' => $nutrition['carbs'],
                'fats' => $nutrition['fats'],
                'notes' => $ingredientData['notes'] ?? null,
            ]);

            $totalCalories += $nutrition['calories'];
            $totalProtein += $nutrition['protein'];
            $totalCarbs += $nutrition['carbs'];
            $totalFats += $nutrition['fats'];
        }

        $meal->update([
            'total_calories' => $totalCalories,
            'total_protein' => $totalProtein,
            'total_carbs' => $totalCarbs,
            'total_fats' => $totalFats,
        ]);

        return new MealResource($meal->load('ingredients.food'));
    }

    public function updateMeal(UpdateMealRequest $request, int $id): MealResource
    {
        $data = $request->validated();
        $user = $request->user();
        $meal = $user->meals()->findOrFail($id);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($meal->image_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $meal->image_url));
            }

            $image = $request->file('image');
            $path = $image->store('meal-images', 'public');
            $data['image_url'] = Storage::url($path);
        }

        // Update meal
        $meal->update($data);

        // Update ingredients if provided
        if (isset($data['ingredients'])) {
            $meal->ingredients()->delete();

            $totalCalories = 0;
            $totalProtein = 0;
            $totalCarbs = 0;
            $totalFats = 0;

            foreach ($data['ingredients'] as $ingredientData) {
                $food = \App\Models\Food::findOrFail($ingredientData['food_id']);
                $grams = $ingredientData['grams'] ?? ($ingredientData['quantity'] * 100);
                $nutrition = $food->calculateNutritionForGrams($grams, 100);

                MealIngredient::create([
                    'user_meal_id' => $meal->id,
                    'food_id' => $food->id,
                    'quantity' => $ingredientData['quantity'],
                    'unit' => $ingredientData['unit'],
                    'grams' => $grams,
                    'calories' => $nutrition['calories'],
                    'protein' => $nutrition['protein'],
                    'carbs' => $nutrition['carbs'],
                    'fats' => $nutrition['fats'],
                    'notes' => $ingredientData['notes'] ?? null,
                ]);

                $totalCalories += $nutrition['calories'];
                $totalProtein += $nutrition['protein'];
                $totalCarbs += $nutrition['carbs'];
                $totalFats += $nutrition['fats'];
            }

            $meal->update([
                'total_calories' => $totalCalories,
                'total_protein' => $totalProtein,
                'total_carbs' => $totalCarbs,
                'total_fats' => $totalFats,
            ]);
        }

        return new MealResource($meal->fresh()->load('ingredients.food'));
    }

    public function deleteMeal(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $meal = $user->meals()->findOrFail($id);

        if ($meal->image_url) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $meal->image_url));
        }

        $meal->delete();

        return response()->json(['message' => 'Meal deleted successfully']);
    }
}

