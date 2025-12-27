<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'name' => $this->getTranslation('name', $locale),
            'description' => $this->getTranslation('description', $locale),
            'totalCalories' => $this->total_calories,
            'totalProtein' => $this->total_protein,
            'totalCarbs' => $this->total_carbs,
            'totalFats' => $this->total_fats,
            'mealType' => $this->meal_type,
            'servings' => $this->servings,
            'prepTime' => $this->prep_time,
            'instructions' => $this->instructions,
            'imageUrl' => $this->image_url,
            'isFavorite' => $this->is_favorite,
            'isPublic' => $this->is_public,
            'ingredients' => $this->whenLoaded('ingredients', fn() => $this->ingredients->map(function ($ingredient) {
                return [
                    'id' => $ingredient->id,
                    'foodId' => $ingredient->food_id,
                    'quantity' => $ingredient->quantity,
                    'unit' => $ingredient->unit,
                    'grams' => $ingredient->grams,
                    'calories' => $ingredient->calories,
                    'protein' => $ingredient->protein,
                    'carbs' => $ingredient->carbs,
                    'fats' => $ingredient->fats,
                    'notes' => $ingredient->notes,
                    'food' => $ingredient->relationLoaded('food') ? new FoodResource($ingredient->food) : null,
                ];
            })),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
