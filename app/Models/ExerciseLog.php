<?php
// app/Models/ExerciseLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ExerciseLog extends Model
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $translatable = ['type'];

    protected $fillable = [
        'user_id',
        'type',
        'description',
        'log_date',
        'start_time',
        'end_time',
        'duration',
        'intensity',
        'calories_burned',
        'distance',
        'steps',
        'heart_rate_avg',
        'source',
        'ai_response',
        'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'calories_burned' => 'decimal:2',
        'distance' => 'decimal:2',
        'ai_response' => 'array',
    ];

    // ==================== العلاقات ====================

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function scopeByIntensity($query, string $intensity)
    {
        return $query->where('intensity', $intensity);
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    // ==================== Helper Methods ====================

    public function isFromAI(): bool
    {
        return $this->source === 'ai';
    }

    public function getIntensityLabel(string $locale = 'ar'): string
    {
        $labels = [
            'ar' => ['low' => 'منخفضة', 'mid' => 'متوسطة', 'high' => 'عالية'],
            'en' => ['low' => 'Low', 'mid' => 'Medium', 'high' => 'High'],
        ];

        return $labels[$locale][$this->intensity] ?? $this->intensity;
    }

    protected static function booted()
    {
        static::created(function ($log) {
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
