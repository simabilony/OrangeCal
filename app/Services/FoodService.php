<?php

namespace App\Services;

use App\Models\Food;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class FoodService
{
    public function __construct(
        protected FatSecretService $fatSecretService
    ) {}

    /**
     * Search foods in database and FatSecret API.
     */
    public function searchFoods(string $query, ?string $locale = 'ar', int $perPage = 20): LengthAwarePaginator
    {
        $queryBuilder = Food::query();

        // Spatie translatable stores as JSON, so we search in JSON
        $queryBuilder->where(function ($q) use ($query, $locale) {
            $q->whereRaw('JSON_EXTRACT(name, "$.ar") LIKE ?', ["%{$query}%"])
              ->orWhereRaw('JSON_EXTRACT(name, "$.en") LIKE ?', ["%{$query}%"])
              ->orWhere('name', 'like', "%{$query}%"); // Fallback for non-translatable
        });

        $localResults = $queryBuilder->paginate($perPage);

        // If local results are less than perPage and FatSecret is configured, search FatSecret API
        if ($localResults->count() < $perPage && $this->fatSecretService->isConfigured()) {
            try {
                // Map locale to FatSecret language codes
                $fatSecretLanguage = $locale === 'ar' ? 'ar' : 'en';
                $fatSecretRegion = $locale === 'ar' ? 'EG' : 'US'; // Egypt for Arabic, US for English
                
                $fatSecretResults = $this->fatSecretService->searchFoods($query, 0, $perPage, $fatSecretRegion, $fatSecretLanguage);
                
                // Log the response structure for debugging
                Log::info('FatSecret API Response', ['response' => $fatSecretResults]);

                // Check for API errors
                if (isset($fatSecretResults['error'])) {
                    $errorCode = $fatSecretResults['error']['code'] ?? null;
                    $errorMessage = $fatSecretResults['error']['message'] ?? 'Unknown error';
                    
                    Log::error('FatSecret API Error', [
                        'code' => $errorCode,
                        'message' => $errorMessage
                    ]);
                    
                    // If IP address error, log it clearly
                    if ($errorCode == 21 || str_contains($errorMessage, 'Invalid IP address')) {
                        Log::warning('FatSecret IP Whitelist Error: Please add your IP address to FatSecret Platform whitelist. Error: ' . $errorMessage);
                    }
                    
                    // Return empty results instead of throwing exception
                    return $localResults;
                }

                // Handle different response structures from FatSecret API
                $foods = [];
                
                // Check if there are actual results
                $totalResults = $fatSecretResults['foods']['total_results'] ?? 0;
                
                if ($totalResults > 0) {
                    // Check for different response structures
                    if (isset($fatSecretResults['foods']['food'])) {
                        $foods = $fatSecretResults['foods']['food'];
                        if (!is_array($foods)) {
                            $foods = [$foods];
                        }
                    } elseif (isset($fatSecretResults['food'])) {
                        // Single food result
                        $foods = [$fatSecretResults['food']];
                    }
                }

                Log::info('FatSecret foods extracted', [
                    'total_results' => $totalResults,
                    'extracted_count' => count($foods),
                    'response_keys' => array_keys($fatSecretResults),
                    'foods_structure' => isset($fatSecretResults['foods']) ? gettype($fatSecretResults['foods']) : 'not_set',
                    'has_food_key' => isset($fatSecretResults['foods']['food'])
                ]);

                $savedCount = 0;
                foreach ($foods as $foodData) {
                    if (isset($foodData['food_id'])) {
                        // Check if already exists
                        $existing = Food::where('source', 'fatsecret_api')
                            ->whereRaw('JSON_EXTRACT(name, "$.en") = ?', [$foodData['food_name'] ?? ''])
                            ->first();

                        if (!$existing) {
                            try {
                                $this->createFoodFromFatSecretSearch($foodData);
                                $savedCount++;
                                Log::info('Food saved from FatSecret', ['food_id' => $foodData['food_id']]);
                            } catch (\Exception $e) {
                                Log::error('Error saving food from FatSecret', [
                                    'food_id' => $foodData['food_id'] ?? null,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }
                }

                // If we saved new foods, search again to include them in results
                if ($savedCount > 0) {
                    $queryBuilder = Food::query();
                    $queryBuilder->where(function ($q) use ($query, $locale) {
                        $q->whereRaw('JSON_EXTRACT(name, "$.ar") LIKE ?', ["%{$query}%"])
                          ->orWhereRaw('JSON_EXTRACT(name, "$.en") LIKE ?', ["%{$query}%"])
                          ->orWhere('name', 'like', "%{$query}%");
                    });
                    
                    $localResults = $queryBuilder->paginate($perPage);
                    Log::info('Re-searched after saving', ['count' => $localResults->count()]);
                }
            } catch (\Exception $e) {
                Log::error('FatSecret search error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return $localResults;
    }

    /**
     * Get food by ID.
     */
    public function getFoodById(int $id): Food
    {
        return Food::findOrFail($id);
    }

    /**
     * Get foods by barcode.
     */
    public function getFoodByBarcode(string $barcode): ?Food
    {
        return Food::where('barcode', $barcode)->first();
    }

    /**
     * Create food from FatSecret search result.
     */
    protected function createFoodFromFatSecretSearch(array $foodData): Food
    {
        $food = new Food();
        $food->source = 'fatsecret_api';
        $food->is_verified = false;

        // Get detailed food info
        try {
            $detailed = $this->fatSecretService->getFoodById((int) $foodData['food_id']);
            if (isset($detailed['food'])) {
                $parsed = $this->fatSecretService->parseFoodData($detailed['food']);

                $food->calories = $parsed['calories'];
                $food->protein = $parsed['protein'];
                $food->carbs = $parsed['carbs'];
                $food->fats = $parsed['fats'];
                $food->fiber = $parsed['fiber'];
                $food->sugar = $parsed['sugar'];
                $food->sodium = $parsed['sodium'];
                $food->serving_size = $parsed['serving_size'];
                $food->serving_unit = $parsed['serving_unit'];

                $food->setTranslation('name', 'en', $parsed['name']);
                $food->setTranslation('name', 'ar', $parsed['name']);

                if ($parsed['description']) {
                    $food->setTranslation('description', 'en', $parsed['description']);
                    $food->setTranslation('description', 'ar', $parsed['description']);
                }
            } else {
                // Fallback to basic data
                $food->setTranslation('name', 'en', $foodData['food_name'] ?? 'Unknown');
                $food->setTranslation('name', 'ar', $foodData['food_name'] ?? 'Unknown');
            }
        } catch (\Exception $e) {
            Log::error('Error fetching detailed food: ' . $e->getMessage());
            $food->setTranslation('name', 'en', $foodData['food_name'] ?? 'Unknown');
            $food->setTranslation('name', 'ar', $foodData['food_name'] ?? 'Unknown');
        }

        $food->save();
        return $food;
    }
}

