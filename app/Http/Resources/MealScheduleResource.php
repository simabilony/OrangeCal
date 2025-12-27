<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->meal_type,
            'time' => $this->scheduled_time ? $this->scheduled_time->format('H:i') : null,
            'isActive' => $this->is_active,
            'reminderEnabled' => $this->reminder_enabled,
        ];
    }
}

