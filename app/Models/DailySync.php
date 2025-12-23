<?php
// app/Models/DailySync.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DailySync extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sync_date',
        'total_calories_consumed',
        'total_calories_burned',
        'net_calories',
        'total_protein',
        'total_carbs',
        'total_fats',
        'total_fiber',
        'total_sugar',
        'water_intake',
        'total_exercise_minutes',
        'total_steps',
        'calorie_goal',
        'goal_progress',
        'weight_logged',
        'is_complete',
        'last_synced_at',
    ];

    protected $casts = [
        'sync_date' => 'date',
        'total_calories_consumed' => 'decimal:2',
        'total_calories_burned' => 'decimal:2',
        'net_calories' => 'decimal:2',
        'total_protein' => 'decimal:2',
        'total_carbs' => 'decimal:2',
        'total_fats' => 'decimal:2',
        'total_fiber' => 'decimal:2',
        'total_sugar' => 'decimal:2',
        'calorie_goal' => 'decimal:2',
        'goal_progress' => 'decimal:2',
        'weight_logged' => 'decimal:2',
        'is_complete' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    // ==================== العلاقات ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==================== Scopes ====================

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('sync_date', $date);
    }

    public function scopeComplete($query)
    {
        return $query->where('is_complete', true);
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('sync_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('sync_date', now()->month)
            ->whereYear('sync_date', now()->year);
    }

    // ==================== Helper Methods ====================

    public static function updateOrCreateForDate(int $userId, $date): self
    {
        $date = Carbon::parse($date)->toDateString();

        $user = User::find($userId);

        // حساب السعرات المستهلكة
        $foodTotals = FoodLog::where('user_id', $userId)
            ->whereDate('log_date', $date)
            ->selectRaw('
                COALESCE(SUM(calories), 0) as calories,
                COALESCE(SUM(protein), 0) as protein,
                COALESCE(SUM(carbs), 0) as carbs,
                COALESCE(SUM(fats), 0) as fats,
                COALESCE(SUM(fiber), 0) as fiber,
                COALESCE(SUM(sugar), 0) as sugar
            ')
            ->first();

        // حساب السعرات المحروقة
        $exerciseTotals = ExerciseLog::where('user_id', $userId)
            ->whereDate('log_date', $date)
            ->selectRaw('
                COALESCE(SUM(calories_burned), 0) as calories_burned,
                COALESCE(SUM(duration), 0) as duration,
                COALESCE(SUM(steps), 0) as steps
            ')
            ->first();

        $calorieGoal = $user->profile->daily_calories ?? 2000;
        $netCalories = $foodTotals->calories - $exerciseTotals->calories_burned;
        $goalProgress = $calorieGoal > 0
            ? min(100, ($foodTotals->calories / $calorieGoal) * 100)
            : 0;

        return self::updateOrCreate(
            ['user_id' => $userId, 'sync_date' => $date],
            [
                'total_calories_consumed' => $foodTotals->calories,
                'total_calories_burned' => $exerciseTotals->calories_burned,
                'net_calories' => $netCalories,
                'total_protein' => $foodTotals->protein,
                'total_carbs' => $foodTotals->carbs,
                'total_fats' => $foodTotals->fats,
                'total_fiber' => $foodTotals->fiber,
                'total_sugar' => $foodTotals->sugar,
                'total_exercise_minutes' => $exerciseTotals->duration,
                'total_steps' => $exerciseTotals->steps,
                'calorie_goal' => $calorieGoal,
                'goal_progress' => $goalProgress,
                'last_synced_at' => now(),
            ]
        );
    }

    public function getRemainingCalories(): float
    {
        return max(0, $this->calorie_goal - $this->total_calories_consumed);
    }

    public function isGoalMet(): bool
    {
        return $this->goal_progress >= 100;
    }

    public function getMacroPercentages(): array
    {
        $total = $this->total_protein + $this->total_carbs + $this->total_fats;

        if ($total == 0) {
            return ['protein' => 0, 'carbs' => 0, 'fats' => 0];
        }

        return [
            'protein' => round(($this->total_protein / $total) * 100, 1),
            'carbs' => round(($this->total_carbs / $total) * 100, 1),
            'fats' => round(($this->total_fats / $total) * 100, 1),
        ];
    }
}
