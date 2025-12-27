<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\FoodLogController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SavedFoodController;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\CheckSubscription;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Apply locale middleware to all routes
Route::middleware([LocaleMiddleware::class])->group(function () {

    // Public routes (no authentication required)
    Route::prefix('auth')->group(function () {
        Route::post('/google', [AuthController::class, 'googleLogin']);
        Route::post('/apple', [AuthController::class, 'appleLogin']);
        Route::post('/mobile', [AuthController::class, 'mobileLogin']);
    });

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {

        // Authentication
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refreshToken']);

        // User endpoints
        Route::post('/user-onboarding', [UserController::class, 'completeOnboarding']);
        Route::get('/user/profile', [UserController::class, 'getProfile']);
        Route::put('/user/profile', [UserController::class, 'updateProfile']);
        Route::get('/user/stats', [UserController::class, 'getStats']);

        // Food & Meals endpoints
        Route::get('/food-db', [FoodController::class, 'searchFoods']);// required
        Route::get('/food-db-id/{id}', [FoodController::class, 'getFoodById']);
        Route::post('/barcode', [FoodController::class, 'scanBarcode']);
        Route::post('/label', [FoodController::class, 'scanLabel']);
        Route::post('/analyze-food', [FoodController::class, 'analyzeFood']);

        // User meals
        Route::get('/user-meals', [MealController::class, 'getUserMeals']);
        Route::post('/new-meal', [MealController::class, 'createMeal']);// required
        Route::put('/user-meals/{id}', [MealController::class, 'updateMeal']);
        Route::delete('/user-meals/{id}', [MealController::class, 'deleteMeal']);

        // Food logs endpoints
        Route::get('/food-logs', [FoodLogController::class, 'getFoodLogs']);
        Route::post('/food-db-id', [FoodLogController::class, 'saveFoodFromDb']);
        Route::post('/meal-id', [FoodLogController::class, 'saveUserMeal']);
        Route::put('/food-logs/{id}', [FoodLogController::class, 'updateFoodLog']);
        Route::delete('/food-logs/{id}', [FoodLogController::class, 'deleteFoodLog']);

        // Exercise endpoints
        Route::get('/exercise', [ExerciseController::class, 'getExerciseLogs']);
        Route::post('/exercise/save', [ExerciseController::class, 'saveExercise']);
        Route::post('/exercise/saveAi', [ExerciseController::class, 'saveAIExercise']);
        Route::put('/exercise/{id}', [ExerciseController::class, 'updateExercise']);
        Route::delete('/exercise/{id}', [ExerciseController::class, 'deleteExercise']);

        // Sync & Saved Foods endpoints
        Route::post('/sync', [SyncController::class, 'dailySync']);
        Route::get('/saved-food', [SavedFoodController::class, 'getSavedFoods']);
        Route::post('/save/{foodId}', [SavedFoodController::class, 'saveFood']);
        Route::post('/save-meal/{mealId}', [SavedFoodController::class, 'saveMeal']);
        Route::delete('/saved-food/{id}', [SavedFoodController::class, 'removeSavedFood']);

        // Payment & Subscription endpoints (premium features)
        Route::middleware([CheckSubscription::class])->group(function () {
            Route::post('/payment/create', [SubscriptionController::class, 'createPayment']);
        });

        Route::get('/subscription/status', [SubscriptionController::class, 'getSubscriptionStatus']);
        Route::post('/subscription/cancel', [SubscriptionController::class, 'cancelSubscription']);

        // Notification endpoints
        Route::get('/notifications', [NotificationController::class, 'getNotifications']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/test-notification', [NotificationController::class, 'testNotification']);
    });
});
