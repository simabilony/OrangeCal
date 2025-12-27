<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FatSecretService
{
    protected ?string $consumerKey;
    protected ?string $consumerSecret;
    protected string $baseUrl;
    protected ?string $accessToken = null;

    public function __construct()
    {
        $this->consumerKey = config('services.fatsecret.client_id');
        $this->consumerSecret = config('services.fatsecret.client_secret');
        $this->baseUrl = config('services.fatsecret.api_base', 'https://platform.fatsecret.com/rest');
    }

    /**
     * Check if FatSecret API is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->consumerKey) && !empty($this->consumerSecret);
    }

    /**
     * Get OAuth 2.0 access token.
     */
    protected function getAccessToken(): string
    {
        if (!$this->isConfigured()) {
            throw new \Exception('FatSecret API is not configured. Please set FATSECRET_CLIENT_ID and FATSECRET_CLIENT_SECRET in .env');
        }

        if ($this->accessToken) {
            return $this->accessToken;
        }

        // Check cache first
        $cachedToken = Cache::get('fatsecret_access_token');
        if ($cachedToken) {
            $this->accessToken = $cachedToken;
            return $cachedToken;
        }

        try {
            $tokenUrl = config('services.fatsecret.token_url', 'https://oauth.fatsecret.com/connect/token');
            $scope = config('services.fatsecret.scope', 'basic');
            
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->asForm()
                ->post($tokenUrl, [
                    'grant_type' => 'client_credentials',
                    'scope' => $scope,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'] ?? null;
                $expiresIn = $data['expires_in'] ?? 3600;

                if ($token) {
                    Cache::put('fatsecret_access_token', $token, now()->addSeconds($expiresIn - 60));
                    $this->accessToken = $token;
                    return $token;
                }
            }

            Log::error('FatSecret OAuth failed', ['response' => $response->body()]);
            throw new \Exception('Failed to obtain FatSecret access token');
        } catch (\Exception $e) {
            Log::error('FatSecret OAuth Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Make authenticated request to FatSecret API.
     */
    protected function makeRequest(string $endpoint, array $params = [], string $method = 'GET'): array
    {
        $token = $this->getAccessToken();
        $url = "{$this->baseUrl}/{$endpoint}";

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Content-Type' => 'application/json',
        ]);

        if (strtoupper($method) === 'POST') {
            $response = $response->post($url, $params);
        } else {
            $response = $response->get($url, $params);
        }

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('FatSecret API Error', [
            'endpoint' => $endpoint,
            'status' => $response->status(),
            'response' => $response->body(),
        ]);

        throw new \Exception("FatSecret API Error: " . $response->body());
    }

    /**
     * Search foods by query.
     */
    public function searchFoods(string $query, int $page = 0, int $maxResults = 20, ?string $region = null, ?string $language = null): array
    {
        try {
            // FatSecret API uses method-based integration
            // Use foods.search (basic method available in Free edition)
            // Note: foods.search.v4 requires Premier subscription
            $params = [
                'method' => 'foods.search',
                'search_expression' => $query,
                'page_number' => $page,
                'max_results' => $maxResults,
                'format' => 'json',
            ];

            // Note: region and language parameters are only available in Premier edition (v4)
            // For Free edition, we use the basic foods.search method
            
            Log::info('FatSecret search params', ['params' => $params]);

            return $this->makeRequest('server.api', $params);
        } catch (\Exception $e) {
            Log::error('FatSecret search error: ' . $e->getMessage());
            return ['foods' => ['food' => []]];
        }
    }

    /**
     * Get food by ID.
     */
    public function getFoodById(int $foodId): array
    {
        try {
            return $this->makeRequest('server.api', [
                'method' => 'food.get.v5',
                'food_id' => $foodId,
                'format' => 'json',
            ]);
        } catch (\Exception $e) {
            Log::error('FatSecret get food error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Scan barcode.
     */
    public function scanBarcode(string $barcode): ?array
    {
        try {
            $result = $this->makeRequest('server.api', [
                'method' => 'food.find_id_for_barcode',
                'barcode' => $barcode,
                'format' => 'json',
            ]);

            if (isset($result['food_id'])) {
                // Get food details using the food_id
                $foodDetails = $this->getFoodById($result['food_id']);
                return $foodDetails['food'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('FatSecret barcode scan error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Analyze food image (Image Recognition - Premier feature).
     */
    public function analyzeFoodImage(string $base64Image): array
    {
        try {
            $result = $this->makeRequest('foods/image', [
                'image' => $base64Image,
                'format' => 'json',
            ], 'POST');

            if (isset($result['food'])) {
                return $this->parseFoodData($result['food']);
            }

            throw new \Exception('No food data in response');
        } catch (\Exception $e) {
            Log::error('FatSecret image analysis error: ' . $e->getMessage());

            // Return fallback data
            return [
                'name' => 'Unknown Food',
                'calories' => 0,
                'protein' => 0,
                'carbs' => 0,
                'fats' => 0,
                'fiber' => 0,
                'sugar' => 0,
                'sodium' => 0,
                'is_halal' => true,
                'is_vegetarian' => false,
                'is_vegan' => false,
                'is_gluten_free' => false,
            ];
        }
    }

    /**
     * Parse FatSecret food data to our format.
     */
    public function parseFoodData(array $foodData): array
    {
        $servings = $foodData['servings']['serving'] ?? [];
        $serving = is_array($servings) && isset($servings[0]) ? $servings[0] : (is_array($servings) ? $servings : []);

        return [
            'name' => $foodData['food_name'] ?? 'Unknown Food',
            'description' => $foodData['food_description'] ?? null,
            'calories' => (float) ($serving['calories'] ?? 0),
            'protein' => (float) ($serving['protein'] ?? 0),
            'carbs' => (float) ($serving['carbohydrate'] ?? 0),
            'fats' => (float) ($serving['fat'] ?? 0),
            'fiber' => (float) ($serving['fiber'] ?? 0),
            'sugar' => (float) ($serving['sugar'] ?? 0),
            'sodium' => (float) ($serving['sodium'] ?? 0),
            'serving_size' => (float) ($serving['serving_size'] ?? 100),
            'serving_unit' => $serving['metric_serving_unit'] ?? 'g',
            'is_halal' => true, // FatSecret doesn't provide halal info, default to true
            'is_vegetarian' => false,
            'is_vegan' => false,
            'is_gluten_free' => false,
        ];
    }
}

