<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    public function __construct(
        protected FatSecretService $fatSecretService
    ) {}

    /**
     * Analyze food image using FatSecret Image Recognition.
     */
    public function analyzeFoodImage(UploadedFile $image): array
    {
        // Check if FatSecret is configured
        if (!$this->fatSecretService->isConfigured()) {
            Log::warning('FatSecret API is not configured. Returning fallback data.');
            return $this->getFallbackFoodData();
        }

        try {
            $base64Image = base64_encode(file_get_contents($image->getRealPath()));
            
            return $this->fatSecretService->analyzeFoodImage($base64Image);
        } catch (\Exception $e) {
            Log::error('AI Service Error: ' . $e->getMessage());
            
            return $this->getFallbackFoodData();
        }
    }

    /**
     * Get fallback food data when API is unavailable.
     */
    protected function getFallbackFoodData(): array
    {
        return [
            'name' => 'Food Item',
            'calories' => 0,
            'protein' => 0,
            'carbs' => 0,
            'fats' => 0,
            'fiber' => 0,
            'sugar' => 0,
            'sodium' => 0,
            'confidence' => 0,
            'is_halal' => true,
            'is_vegetarian' => false,
            'is_vegan' => false,
            'is_gluten_free' => false,
        ];
    }

    /**
     * Analyze exercise from text/image.
     */
    public function analyzeExercise(string $text, ?UploadedFile $image = null): array
    {
        try {
            // Placeholder for exercise analysis
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(30)->post(config('services.ai.endpoint'), [
                'type' => 'exercise',
                'text' => $text,
                'image' => $image ? base64_encode(file_get_contents($image->getRealPath())) : null,
            ]);

            if ($response->successful()) {
                return $this->parseExerciseResponse($response->json());
            }

            throw new \Exception('AI service unavailable');
        } catch (\Exception $e) {
            Log::error('AI Exercise Service Error: ' . $e->getMessage());

            return [
                'type' => 'General Exercise',
                'calories_burned' => 0,
                'duration' => 0,
                'intensity' => 'medium',
                'confidence' => 0,
            ];
        }
    }

    /**
     * Parse AI food response.
     */
    protected function parseAIResponse(array $response): array
    {
        // Parse and normalize AI response
        // This should be customized based on your AI provider
        return [
            'name' => $response['name'] ?? 'Unknown Food',
            'calories' => $response['calories'] ?? 0,
            'protein' => $response['protein'] ?? 0,
            'carbs' => $response['carbs'] ?? 0,
            'fats' => $response['fats'] ?? 0,
            'fiber' => $response['fiber'] ?? 0,
            'sugar' => $response['sugar'] ?? 0,
            'sodium' => $response['sodium'] ?? 0,
            'confidence' => $response['confidence'] ?? 0.5,
            'is_halal' => $response['is_halal'] ?? true,
            'is_vegetarian' => $response['is_vegetarian'] ?? false,
            'is_vegan' => $response['is_vegan'] ?? false,
            'is_gluten_free' => $response['is_gluten_free'] ?? false,
        ];
    }

    /**
     * Parse AI exercise response.
     */
    protected function parseExerciseResponse(array $response): array
    {
        return [
            'type' => $response['type'] ?? 'General Exercise',
            'calories_burned' => $response['calories_burned'] ?? 0,
            'duration' => $response['duration'] ?? 0,
            'intensity' => $response['intensity'] ?? 'medium',
            'confidence' => $response['confidence'] ?? 0.5,
        ];
    }
}




