<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'foodId' => $this->food_id,
            'userMealId' => $this->user_meal_id,
            'name' => $this->name,
            'logDate' => $this->log_date,
            'logTime' => $this->log_time,
            'mealType' => $this->meal_type,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'grams' => $this->grams,
            'calories' => $this->calories,
            'protein' => $this->protein,
            'carbs' => $this->carbs,
            'fats' => $this->fats,
            'fiber' => $this->fiber,
            'sugar' => $this->sugar,
            'source' => $this->source,
            'barcodeScanned' => $this->barcode_scanned,
            'imageUrl' => $this->image_url,
            'isHalal' => $this->is_halal,
            'notes' => $this->notes,
            'food' => $this->whenLoaded('food', fn() => new FoodResource($this->food)),
            'userMeal' => $this->whenLoaded('userMeal', fn() => new MealResource($this->userMeal)),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
