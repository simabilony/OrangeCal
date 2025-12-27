<?php

namespace App\Services;

use App\Models\Food;
use Illuminate\Support\Facades\Log;

class BarcodeService
{
    public function __construct(
        protected FatSecretService $fatSecretService
    ) {}

    /**
     * Lookup food by barcode.
     */
    public function lookupBarcode(string $barcode): ?Food
    {
        // First check local database
        $food = Food::where('barcode', $barcode)->first();

        if ($food) {
            return $food;
        }

        // Try FatSecret API if configured
        if ($this->fatSecretService->isConfigured()) {
            try {
                $foodData = $this->fatSecretService->scanBarcode($barcode);
                
                if ($foodData) {
                    return $this->createFoodFromFatSecretData($barcode, $foodData);
                }
            } catch (\Exception $e) {
                Log::error('Barcode lookup error: ' . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Create food from FatSecret API data.
     */
    protected function createFoodFromFatSecretData(string $barcode, array $foodData): Food
    {
        $servings = $foodData['servings']['serving'] ?? [];
        $serving = is_array($servings) && isset($servings[0]) ? $servings[0] : (is_array($servings) ? $servings : []);

        $food = new Food();
        $food->barcode = $barcode;
        $food->calories = (float) ($serving['calories'] ?? 0);
        $food->protein = (float) ($serving['protein'] ?? 0);
        $food->carbs = (float) ($serving['carbohydrate'] ?? 0);
        $food->fats = (float) ($serving['fat'] ?? 0);
        $food->fiber = (float) ($serving['fiber'] ?? 0);
        $food->sugar = (float) ($serving['sugar'] ?? 0);
        $food->sodium = (float) ($serving['sodium'] ?? 0);
        $food->serving_size = (float) ($serving['serving_size'] ?? 100);
        $food->serving_unit = $serving['metric_serving_unit'] ?? 'g';
        $food->source = 'fatsecret_api';
        $food->is_verified = false;

        // Set translatable fields
        $food->setTranslation('name', 'en', $foodData['food_name'] ?? 'Unknown');
        $food->setTranslation('name', 'ar', $foodData['food_name'] ?? 'Unknown');

        if (isset($foodData['food_description'])) {
            $food->setTranslation('description', 'en', $foodData['food_description']);
            $food->setTranslation('description', 'ar', $foodData['food_description']);
        }

        $food->save();

        return $food;
    }
}

