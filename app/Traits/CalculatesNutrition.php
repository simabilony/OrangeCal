<?php

namespace App\Traits;

trait CalculatesNutrition
{
    /**
     * Calculate nutrition values for a given quantity in grams.
     */
    public function calculateNutritionForGrams(float $grams, float $baseGrams = 100): array
    {
        $multiplier = $grams / $baseGrams;

        return [
            'calories' => round(($this->calories ?? 0) * $multiplier, 2),
            'protein' => round(($this->protein ?? 0) * $multiplier, 2),
            'carbs' => round(($this->carbs ?? 0) * $multiplier, 2),
            'fats' => round(($this->fats ?? 0) * $multiplier, 2),
            'fiber' => round(($this->fiber ?? 0) * $multiplier, 2),
            'sugar' => round(($this->sugar ?? 0) * $multiplier, 2),
            'sodium' => round(($this->sodium ?? 0) * $multiplier, 2),
            'saturated_fat' => round(($this->saturated_fat ?? 0) * $multiplier, 2),
            'cholesterol' => round(($this->cholesterol ?? 0) * $multiplier, 2),
        ];
    }

    /**
     * Calculate macro percentages.
     */
    public function getMacroPercentages(): array
    {
        $total = ($this->protein ?? 0) + ($this->carbs ?? 0) + ($this->fats ?? 0);

        if ($total == 0) {
            return ['protein' => 0, 'carbs' => 0, 'fats' => 0];
        }

        return [
            'protein' => round((($this->protein ?? 0) / $total) * 100, 1),
            'carbs' => round((($this->carbs ?? 0) / $total) * 100, 1),
            'fats' => round((($this->fats ?? 0) / $total) * 100, 1),
        ];
    }
}






