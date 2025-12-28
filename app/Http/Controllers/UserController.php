<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\OnboardingRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\MealSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function completeOnboarding(OnboardingRequest $request): UserResource
    {
        $data = $request->validated();
        $user = $request->user();

        // Update user fields
        $userUpdates = [];
        if (isset($data['mobile_id'])) {
            // Check if mobile_id is already taken by another user
            $existingUser = User::where('mobile_id', $data['mobile_id'])
                ->where('id', '!=', $user->id)
                ->first();

            if ($existingUser) {
                throw ValidationException::withMessages([
                    'mobile_id' => ['This mobile ID is already in use by another user.'],
                ]);
            }

            $userUpdates['mobile_id'] = $data['mobile_id'];
        }
        if (isset($data['firebase_token'])) {
            $userUpdates['fcm_token'] = $data['firebase_token'];
        }
        if (!empty($userUpdates)) {
            $user->update($userUpdates);
        }

        // Calculate BMI and age
        $heightInMeters = $data['height'] / 100;
        $bmi = round($data['weight'] / ($heightInMeters * $heightInMeters), 2);
        $age = \Carbon\Carbon::parse($data['birthdate'])->age;

        // Prepare profile data
        $profileData = [
            'gender' => $data['gender'],
            'birth_date' => $data['birthdate'],
            'height' => $data['height'],
            'weight' => $data['weight'],
            'bmi' => $bmi,
            'age' => $age,
            'goal' => $data['goal'],
            'activity_level' => $data['activity'],
        ];

        // Add optional fields
        if (isset($data['target_weight'])) {
            $profileData['target_weight'] = $data['target_weight'];
        }
        if (isset($data['weekly_target'])) {
            $profileData['weekly_target'] = $data['weekly_target'];
        }
        if (isset($data['diet'])) {
            // Convert diet to array if it's a string, then encode to JSON
            $dietArray = is_array($data['diet']) ? $data['diet'] : [$data['diet']];
            $profileData['dietary_preferences'] = json_encode($dietArray);
        }
        if (isset($data['tdee_goal'])) {
            $profileData['tdee_goal'] = $data['tdee_goal'];
            // Calculate daily calories based on TDEE goal if provided
            $profileData['daily_calories'] = $this->calculateDailyCalories(
                $data['weight'],
                $data['height'],
                $age,
                $data['gender'],
                $data['activity'],
                $data['goal'] ?? 'maintain_weight',
                $data['target_weight'] ?? null
            );
        } else {
            // Calculate daily calories even if tdee_goal is not provided
            $profileData['daily_calories'] = $this->calculateDailyCalories(
                $data['weight'],
                $data['height'],
                $age,
                $data['gender'],
                $data['activity'],
                $data['goal'] ?? 'maintain_weight',
                $data['target_weight'] ?? null
            );
        }
        if (isset($data['tried_another_app'])) {
            $profileData['tried_another_app'] = $data['tried_another_app'];
        }
        if (isset($data['hearing_about_us'])) {
            $profileData['hearing_about_us'] = $data['hearing_about_us'];
        }

        // Create or update profile
        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        // Handle meals schedule
        if (isset($data['meals']) && is_array($data['meals'])) {
            // Delete existing meal schedules
            $user->mealSchedules()->delete();

            // Create new meal schedules
            foreach ($data['meals'] as $meal) {
                $scheduledTime = null;
                if (isset($meal['time']) && !empty($meal['time'])) {
                    // Parse time string (e.g., "08:00" or "14:30") to datetime
                    try {
                        $scheduledTime = \Carbon\Carbon::createFromFormat('H:i', $meal['time']);
                    } catch (\Exception $e) {
                        // Try other formats if needed
                        $scheduledTime = \Carbon\Carbon::parse($meal['time']);
                    }
                }

                MealSchedule::create([
                    'user_id' => $user->id,
                    'meal_type' => $meal['type'],
                    'scheduled_time' => $scheduledTime,
                    'is_active' => true,
                ]);
            }
        }

        // Mark user as onboarded
        $user->update(['is_onboarded' => true]);

        return new UserResource($user->fresh()->load(['profile', 'mealSchedules']));
    }

    /**
     * Calculate daily calories based on user data.
     */
    protected function calculateDailyCalories(
        int $weight,
        int $height,
        int $age,
        string $gender,
        string $activity,
        string $goal,
        ?float $targetWeight = null
    ): int {
        // BMR calculation using Mifflin-St Jeor Equation
        $bmr = $gender === 'male'
            ? (10 * $weight) + (6.25 * $height) - (5 * $age) + 5
            : (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;

        // Activity multipliers
        $activityMultipliers = [
            'sedentary' => 1.2,
            'light' => 1.375,
            'moderate' => 1.55,
            'active' => 1.725,
            'very_active' => 1.9,
        ];

        $tdee = $bmr * ($activityMultipliers[$activity] ?? 1.2);

        // Adjust based on goal
        switch ($goal) {
            case 'lose_weight':
                return (int) ($tdee - 500); // 500 calorie deficit
            case 'gain_weight':
                return (int) ($tdee + 500); // 500 calorie surplus
            case 'build_muscle':
                return (int) ($tdee + 300); // 300 calorie surplus for muscle gain
            case 'maintain_weight':
            default:
                return (int) $tdee;
        }
    }

    public function getProfile(Request $request): UserResource
    {
        return new UserResource($request->user()->load('profile'));
    }

    public function updateProfile(UpdateProfileRequest $request): UserResource
    {
        $data = $request->validated();
        $user = $request->user();

        // Update user basic info
        if (isset($data['name'])) {
            $user->update(['name' => $data['name']]);
        }

        if (isset($data['email'])) {
            $user->update(['email' => $data['email']]);
        }

        if (isset($data['fcm_token'])) {
            $user->update(['fcm_token' => $data['fcm_token']]);
        }

        // Update profile
        if ($user->profile) {
            // Recalculate BMI if height or weight changed
            if (isset($data['height']) || isset($data['weight'])) {
                $height = $data['height'] ?? $user->profile->height;
                $weight = $data['weight'] ?? $user->profile->weight;
                if ($height && $weight) {
                    $heightInMeters = $height / 100;
                    $data['bmi'] = round($weight / ($heightInMeters * $heightInMeters), 2);
                }
            }

            // Recalculate age if birth date changed
            if (isset($data['birth_date'])) {
                $data['age'] = \Carbon\Carbon::parse($data['birth_date'])->age;
            }

            $user->profile->update($data);
        } else {
            $user->profile()->create($data);
        }

        return new UserResource($user->fresh()->load('profile'));
    }

    public function getStats(Request $request): JsonResponse
    {
        $user = $request->user();
        $today = today();

        $todayFoodLogs = $user->foodLogs()->today()->get();
        $todayCalories = $todayFoodLogs->sum('calories');
        $todayProtein = $todayFoodLogs->sum('protein');
        $todayCarbs = $todayFoodLogs->sum('carbs');
        $todayFats = $todayFoodLogs->sum('fats');

        $todayExercises = $user->exerciseLogs()->today()->get();
        $todayCaloriesBurned = $todayExercises->sum('calories_burned');
        $todayExerciseMinutes = $todayExercises->sum('duration');

        $dailyCaloriesGoal = $user->profile->daily_calories ?? 2000;
        $remainingCalories = max(0, $dailyCaloriesGoal - $todayCalories);

        return response()->json([
            'date' => $today->toDateString(),
            'consumed' => [
                'calories' => $todayCalories,
                'protein' => $todayProtein,
                'carbs' => $todayCarbs,
                'fats' => $todayFats,
            ],
            'burned' => [
                'calories' => $todayCaloriesBurned,
                'minutes' => $todayExerciseMinutes,
            ],
            'goal' => [
                'calories' => $dailyCaloriesGoal,
                'remaining' => $remainingCalories,
                'progress' => $dailyCaloriesGoal > 0
                    ? min(100, round(($todayCalories / $dailyCaloriesGoal) * 100, 2))
                    : 0,
            ],
            'net' => [
                'calories' => $todayCalories - $todayCaloriesBurned,
            ],
        ]);
    }
}

