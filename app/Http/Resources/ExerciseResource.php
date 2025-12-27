<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'type' => $this->getTranslation('type', $locale),
            'description' => $this->description,
            'logDate' => $this->log_date,
            'startTime' => $this->start_time,
            'endTime' => $this->end_time,
            'duration' => $this->duration,
            'intensity' => $this->intensity,
            'caloriesBurned' => $this->calories_burned,
            'distance' => $this->distance,
            'steps' => $this->steps,
            'heartRateAvg' => $this->heart_rate_avg,
            'source' => $this->source,
            'notes' => $this->notes,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
