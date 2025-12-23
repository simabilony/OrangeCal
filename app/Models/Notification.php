<?php
// app/Models/Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Notification extends Model
{
    use HasFactory, HasTranslations;

    protected $keyType = 'string';
    public $incrementing = false;

    public $translatable = ['title', 'body'];

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
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

    public static function sendToUser(User $user, array $data): self
    {
        $notification = new self([
            'user_id' => $user->id,
            'type' => $data['type'] ?? 'general',
            'data' => $data['data'] ?? null,
            'action_type' => $data['action_type'] ?? null,
            'action_value' => $data['action_value'] ?? null,
            'sent_at' => now(),
        ]);

        if (isset($data['title'])) {
            if (is_array($data['title'])) {
                foreach ($data['title'] as $locale => $value) {
                    $notification->setTranslation('title', $locale, $value);
                }
            } else {
                $notification->title = $data['title'];
            }
        }

        if (isset($data['body'])) {
            if (is_array($data['body'])) {
                foreach ($data['body'] as $locale => $value) {
                    $notification->setTranslation('body', $locale, $value);
                }
            } else {
                $notification->body = $data['body'];
            }
        }

        $notification->save();

        // إرسال Push Notification إذا كان FCM token موجود
        if ($user->fcm_token && ($data['send_push'] ?? true)) {
            // يمكن إضافة كود إرسال FCM هنا
            $notification->update(['is_push_sent' => true]);
        }

        return $notification;
    }
}
