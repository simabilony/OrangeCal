<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function getNotifications(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 20);

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return NotificationResource::collection($notifications);
    }

    public function markAsRead(Request $request, string $id): NotificationResource
    {
        $user = $request->user();
        $notification = $user->notifications()->findOrFail($id);

        $notification->markAsRead();

        return new NotificationResource($notification->fresh());
    }

    public function testNotification(Request $request): NotificationResource
    {
        $user = $request->user();

        $notification = $this->notificationService->createAndSendNotification($user, [
            'type' => 'test',
            'title' => [
                'ar' => 'اختبار إشعار',
                'en' => 'Test Notification',
            ],
            'body' => [
                'ar' => 'هذا إشعار تجريبي',
                'en' => 'This is a test notification',
            ],
            'send_push' => true,
        ]);

        return new NotificationResource($notification);
    }
}

