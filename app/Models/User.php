<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'google_id',
        'apple_id',
        'mobile_id',
        'name',
        'email',
        'password',
        'fcm_token',
        'device_type',
        'device_id',
        'is_active',
        'is_onboarded',
        'is_premium',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_onboarded' => 'boolean',
        'is_premium' => 'boolean',
    ];

    // ==================== العلاقات ====================

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function mealSchedules()
    {
        return $this->hasMany(MealSchedule::class);
    }

    public function meals()
    {
        return $this->hasMany(UserMeal::class);
    }

    public function foodLogs()
    {
        return $this->hasMany(FoodLog::class);
    }

    public function savedFoods()
    {
        return $this->belongsToMany(Food::class, 'saved_foods')
            ->withPivot(['notes', 'custom_name', 'default_quantity', 'default_unit'])
            ->withTimestamps();
    }

    public function exerciseLogs()
    {
        return $this->hasMany(ExerciseLog::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function dailySyncs()
    {
        return $this->hasMany(DailySync::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // ==================== Scopes ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeOnboarded($query)
    {
        return $query->where('is_onboarded', true);
    }

    // ==================== Helper Methods ====================

    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->exists();
    }

    public function getTodaySync()
    {
        return $this->dailySyncs()->where('sync_date', today())->first();
    }

    public function getTodayCalories(): float
    {
        return $this->foodLogs()
            ->whereDate('log_date', today())
            ->sum('calories');
    }

    public function getUnreadNotificationsCount(): int
    {
        return $this->notifications()->whereNull('read_at')->count();
    }
}
