<?php

namespace App\Http\Controllers;

use App\Http\Requests\Food\SearchFoodRequest;
use App\Http\Requests\Food\BarcodeScanRequest;
use App\Http\Requests\Food\AnalyzeFoodRequest;
use App\Http\Requests\Food\LabelScanRequest;
use App\Http\Resources\FoodResource;
use App\Services\FoodService;
use App\Services\BarcodeService;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FoodController extends Controller
{
    public function __construct(
        protected FoodService $foodService,
        protected BarcodeService $barcodeService,
        protected AIService $aiService
    ) {}

    public function searchFoods(SearchFoodRequest $request): AnonymousResourceCollection
    {
        $data = $request->validated();
        $locale = app()->getLocale();
        
        $foods = $this->foodService->searchFoods(
            $data['query'],
            $locale,
            $data['per_page'] ?? 20
        );

        return FoodResource::collection($foods);
    }

    public function getFoodById(int $id): FoodResource
    {
        $food = $this->foodService->getFoodById($id);

        return new FoodResource($food);
    }

    public function scanBarcode(BarcodeScanRequest $request): FoodResource|JsonResponse
    {
        $data = $request->validated();
        $food = $this->barcodeService->lookupBarcode($data['barcode']);

        if (!$food) {
            return response()->json(['message' => 'Food not found for this barcode'], 404);
        }

        return new FoodResource($food);
    }

    public function scanLabel(LabelScanRequest $request): JsonResponse
    {
        $image = $request->file('image');
        $result = $this->aiService->analyzeFoodImage($image);

        return response()->json($result);
    }

    public function analyzeFood(AnalyzeFoodRequest $request): JsonResponse
    {
        $image = $request->file('image');
        $result = $this->aiService->analyzeFoodImage($image);

        return response()->json($result);
    }
}

