<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send push notification to user.
     */
    public function sendPushNotification(User $user, array $data): bool
    {
        if (!$user->fcm_token) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . config('services.fcm.server_key'),
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $user->fcm_token,
                'notification' => [
                    'title' => $data['title'],
                    'body' => $data['body'],
                    'sound' => 'default',
                ],
                'data' => $data['data'] ?? [],
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('FCM Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create and send notification.
     */
    public function createAndSendNotification(User $user, array $data): Notification
    {
        $notification = Notification::sendToUser($user, $data);

        // Send push if enabled
        if ($data['send_push'] ?? true) {
            $this->sendPushNotification($user, [
                'title' => $notification->getLocalizedTitle(app()->getLocale()),
                'body' => $notification->getLocalizedBody(app()->getLocale()),
                'data' => $data['data'] ?? [],
            ]);
        }

        return $notification;
    }
}






