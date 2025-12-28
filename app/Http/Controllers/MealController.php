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
use Illuminate\Validation\ValidationException;

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

        // Prepare meal data
        $mealData = array_merge($data, [
            'user_id' => $user->id,
            'image_url' => $this->handleImageUpload($request),
            'servings' => $data['servings'] ?? 1,
            'is_favorite' => $data['is_favorite'] ?? false,
            'is_public' => $data['is_public'] ?? false,
        ]);

        // Remove ingredients from meal data (will be handled separately)
        $ingredients = $mealData['ingredients'] ?? [];
        unset($mealData['ingredients']);

        // Create meal
        $meal = UserMeal::create($mealData);

        // Create ingredients (totals will be calculated automatically via model events)
        $this->createIngredients($meal, $ingredients);

        return new MealResource($meal->fresh()->load('ingredients.food'));
    }

    public function updateMeal(UpdateMealRequest $request, int $id): MealResource
    {
        $data = $request->validated();
        $user = $request->user();
        $meal = $user->meals()->findOrFail($id);

        // Handle image upload
        if ($request->hasFile('image')) {
            $this->deleteImage($meal->image_url);
            $data['image_url'] = $this->handleImageUpload($request);
        }

        // Extract ingredients if provided
        $ingredients = $data['ingredients'] ?? null;
        unset($data['ingredients']);

        // Update meal
        $meal->update($data);

        // Update ingredients if provided (totals will be recalculated automatically)
        if ($ingredients !== null) {
            $meal->ingredients()->delete();
            $this->createIngredients($meal, $ingredients);
        }

        return new MealResource($meal->fresh()->load('ingredients.food'));
    }

    public function deleteMeal(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $meal = $user->meals()->findOrFail($id);

        $this->deleteImage($meal->image_url);
        $meal->delete();

        return response()->json(['message' => 'Meal deleted successfully']);
    }

    /**
     * Handle image upload and return the image URL.
     */
    protected function handleImageUpload(Request $request): ?string
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        $path = $request->file('image')->store('meal-images', 'public');
        return Storage::url($path);
    }

    /**
     * Delete image from storage.
     */
    protected function deleteImage(?string $imageUrl): void
    {
        if ($imageUrl) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $imageUrl));
        }
    }

    /**
     * Create meal ingredients.
     * Nutrition values and totals are calculated automatically via model events.
     */
    protected function createIngredients(UserMeal $meal, array $ingredients): void
    {
        foreach ($ingredients as $ingredientData) {
            if (!isset($ingredientData['food_id'])) {
                throw ValidationException::withMessages([
                    'ingredients' => ['Each ingredient must have a food_id.'],
                ]);
            }

            $grams = $ingredientData['grams'] ?? ($ingredientData['quantity'] * 100);

            MealIngredient::create([
                'user_meal_id' => $meal->id,
                'food_id' => $ingredientData['food_id'],
                'quantity' => $ingredientData['quantity'],
                'unit' => $ingredientData['unit'],
                'grams' => $grams,
                'notes' => $ingredientData['notes'] ?? null,
            ]);
        }
    }
}

