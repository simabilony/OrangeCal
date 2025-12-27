<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', $locale),
            'description' => $this->getTranslation('description', $locale),
            'category' => $this->getTranslation('category', $locale),
            'barcode' => $this->barcode,
            'source' => $this->source,
            'calories' => $this->calories,
            'protein' => $this->protein,
            'carbs' => $this->carbs,
            'fats' => $this->fats,
            'fiber' => $this->fiber,
            'sugar' => $this->sugar,
            'sodium' => $this->sodium,
            'saturatedFat' => $this->saturated_fat,
            'cholesterol' => $this->cholesterol,
            'servingSize' => $this->serving_size,
            'servingUnit' => $this->serving_unit,
            'isHalal' => $this->is_halal,
            'isVegetarian' => $this->is_vegetarian,
            'isVegan' => $this->is_vegan,
            'isGlutenFree' => $this->is_gluten_free,
            'isVerified' => $this->is_verified,
            'imageUrl' => $this->image_url,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
