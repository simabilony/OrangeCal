<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'type' => $this->type,
            'title' => $this->getTranslation('title', $locale),
            'body' => $this->getTranslation('body', $locale),
            'data' => $this->data,
            'actionType' => $this->action_type,
            'actionValue' => $this->action_value,
            'readAt' => $this->read_at,
            'isRead' => $this->isRead(),
            'sentAt' => $this->sent_at,
            'isPushSent' => $this->is_push_sent,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
