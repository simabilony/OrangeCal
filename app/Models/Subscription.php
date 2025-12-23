<?php
// app/Models/Subscription.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_name',
        'ar_plan_name',
        'en_plan_name',
        'plan_type',
        'price',
        'currency',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'status',
        'auto_renew',
        'payment_method',
        'transaction_id',
        'receipt_url',
        'store_product_id',
        'store_transaction_id',
        'store_receipt',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'auto_renew' => 'boolean',
        'store_receipt' => 'array',
    ];

    // ==================== العلاقات ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==================== Scopes ====================

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('ends_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere('ends_at', '<=', now());
    }

    public function scopeByPlanType($query, string $planType)
    {
        return $query->where('plan_type', $planType);
    }

    // ==================== Helper Methods ====================

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at > now();
    }

    public function isExpired(): bool
    {
        return $this->ends_at <= now();
    }

    public function isInTrial(): bool
    {
        return $this->status === 'trial' && $this->trial_ends_at > now();
    }

    public function daysRemaining(): int
    {
        return max(0, now()->diffInDays($this->ends_at, false));
    }

    public function getLocalizedPlanName(string $locale = 'ar'): string
    {
        return $locale === 'en'
            ? ($this->en_plan_name ?? $this->plan_name)
            : ($this->ar_plan_name ?? $this->plan_name);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'auto_renew' => false,
        ]);

        $this->user->update(['is_premium' => false]);
    }

    public function renew(int $days = 30): void
    {
        $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays($days),
        ]);

        $this->user->update(['is_premium' => true]);
    }
}
