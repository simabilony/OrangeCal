<?php

namespace App\Http\Requests\Exercise;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateExerciseRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'array'],
            'type.ar' => ['sometimes', 'string'],
            'type.en' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'logDate' => ['sometimes', 'date'],
            'startTime' => ['nullable', 'date_format:H:i'],
            'endTime' => ['nullable', 'date_format:H:i'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'intensity' => ['nullable', Rule::in(['low', 'mid', 'high'])],
            'caloriesBurned' => ['nullable', 'numeric', 'min:0'],
            'distance' => ['nullable', 'numeric', 'min:0'],
            'steps' => ['nullable', 'integer', 'min:0'],
            'heartRateAvg' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}






