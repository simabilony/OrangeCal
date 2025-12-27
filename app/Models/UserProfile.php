<?php
// app/Models/UserProfile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'birth_date',
        'age',
        'height',
        'weight',
        'target_weight',
        'weekly_target',
        'bmi',
        'goal',
        'activity_level',
        'daily_calories',
        'daily_protein',
        'daily_carbs',
        'daily_fats',
        'daily_water',
        'dietary_preferences',
        'allergies',
        'health_conditions',
        'preferred_language',
        'timezone',
        'notifications_enabled',
        'tdee_goal',
        'tried_another_app',
        'hearing_about_us',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'target_weight' => 'decimal:2',
        'weekly_target' => 'decimal:2',
        'bmi' => 'decimal:2',
        'notifications_enabled' => 'boolean',
        'tried_another_app' => 'boolean',
        'dietary_preferences' => 'array',
        'allergies' => 'array',
        'health_conditions' => 'array',
    ];

    // ==================== العلاقات ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==================== Helper Methods ====================

    public function calculateBMI(): float
    {
        if ($this->height && $this->weight) {
            $heightInMeters = (float) $this->height / 100;
            return round((float) $this->weight / ($heightInMeters * $heightInMeters), 2);
        }
        return 0.0;
    }

    public function updateBMI(): void
    {
        $calculatedBMI = $this->calculateBMI();
        $this->attributes['bmi'] = $calculatedBMI;
        $this->save();
    }

    public function calculateAge(): int
    {
        return $this->birth_date ? \Carbon\Carbon::parse($this->birth_date)->age : 0;
    }

    public function getWeightToLose(): float
    {
        if ($this->weight && $this->target_weight) {
            return $this->weight - $this->target_weight;
        }
        return 0;
    }

    public function getRemainingCalories(float $consumed): float
    {
        return max(0, ($this->daily_calories ?? 0) - $consumed);
    }
}
