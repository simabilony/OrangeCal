<?php
// app/Models/FoodLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'food_id',
        'user_meal_id',
        'name',
        'arabic_name',
        'english_name',
        'log_date',
        'log_time',
        'meal_type',
        'quantity',
        'unit',
        'grams',
        'calories',
        'protein',
        'carbs',
        'fats',
        'fiber',
        'sugar',
        'source',
        'barcode_scanned',
        'image_url',
        'ai_response',
        'ai_confidence',
        'is_halal',
        'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
        'log_time' => 'datetime:H:i',
        'quantity' => 'decimal:2',
        'grams' => 'decimal:2',
        'calories' => 'decimal:2',
        'protein' => 'decimal:2',
        'carbs' => 'decimal:2',
        'fats' => 'decimal:2',
        'fiber' => 'decimal:2',
        'sugar' => 'decimal:2',
        'ai_response' => 'array',
        'ai_confidence' => 'decimal:2',
        'is_halal' => 'boolean',
    ];

    // ==================== العلاقات ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function userMeal()
    {
        return $this->belongsTo(UserMeal::class);
    }

    // ==================== Scopes ====================

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('log_date', $date);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('log_date', today());
    }

    public function scopeByMealType($query, string $mealType)
    {
        return $query->where('meal_type', $mealType);
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    public function scopeAiScanned($query)
    {
        return $query->where('source', 'ai_scan');
    }

    // ==================== Helper Methods ====================

    public function getLocalizedName(string $locale = 'ar'): string
    {
        if ($locale === 'en') {
            return $this->english_name ?? $this->name ?? '';
        }
        return $this->arabic_name ?? $this->name ?? '';
    }

    public function isFromAI(): bool
    {
        return $this->source === 'ai_scan';
    }

    public function isFromBarcode(): bool
    {
        return $this->source === 'barcode';
    }

    protected static function booted()
    {
        static::created(function ($log) {
            // تحديث المزامنة اليومية عند إضافة سجل جديد
            DailySync::updateOrCreateForDate($log->user_id, $log->log_date);
        });

        static::updated(function ($log) {
            DailySync::updateOrCreateForDate($log->user_id, $log->log_date);
        });

        static::deleted(function ($log) {
            DailySync::updateOrCreateForDate($log->user_id, $log->log_date);
        });
    }
}
