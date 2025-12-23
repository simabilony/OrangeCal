<?php
// app/Models/Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Notification extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'ar_title',
        'en_title',
        'body',
        'ar_body',
        'en_body',
        'data',
        'action_type',
        'action_value',
        'read_at',
        'sent_at',
        'is_push_sent',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_push_sent' => 'boolean',
    ];

    // ==================== Boot ====================

    protected static function booted()
    {
        static::creating(function ($notification) {
            if (empty($notification->id)) {
                $notification->id = Str::uuid()->toString();
            }
        });
    }

    // ==================== العلاقات ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ==================== Scopes ====================

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ==================== Helper Methods ====================

    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function getLocalizedTitle(string $locale = 'ar'): string
    {
        return $locale === 'en'
            ? ($this->en_title ?? $this->title)
            : ($this->ar_title ?? $this->title);
    }

    public function getLocalizedBody(string $locale = 'ar'): string
    {
        return $locale === 'en'
            ? ($this->en_body ?? $this->body)
            : ($this->ar_body ?? $this->body);
    }

    public static function sendToUser(User $user, array $data): self
    {
        $notification = self::create([
            'user_id' => $user->id,
            'type' => $data['type'] ?? 'general',
            'title' => $data['title'],
            'ar_title' => $data['ar_title'] ?? null,
            'en_title' => $data['en_title'] ?? null,
            'body' => $data['body'],
            'ar_body' => $data['ar_body'] ?? null,
            'en_body' => $data['en_body'] ?? null,
            'data' => $data['data'] ?? null,
            'action_type' => $data['action_type'] ?? null,
            'action_value' => $data['action_value'] ?? null,
            'sent_at' => now(),
        ]);

        // إرسال Push Notification إذا كان FCM token موجود
        if ($user->fcm_token && ($data['send_push'] ?? true)) {
            // يمكن إضافة كود إرسال FCM هنا
            $notification->update(['is_push_sent' => true]);
        }

        return $notification;
    }
}
