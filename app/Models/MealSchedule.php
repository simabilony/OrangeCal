<?php
// app/Models/MealSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'meal_type',
        'en_meal_type',
        'scheduled_time',
        'reminder_enabled',
        'reminder_minutes_before',
        'target_calories',
        'is_active',
    ];

    protected $casts = [
        'scheduled_time' => 'datetime:H:i',
        'reminder_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ==================== العلاقات ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==================== Scopes ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithReminder($query)
    {
        return $query->where('reminder_enabled', true);
    }

    // ==================== Helper Methods ====================

    public function getReminderTime()
    {
        return $this->scheduled_time->subMinutes($this->reminder_minutes_before);
    }

    public function getLocalizedMealType(string $locale = 'ar'): string
    {
        return $locale === 'en' ? ($this->en_meal_type ?? $this->meal_type) : $this->meal_type;
    }
}
