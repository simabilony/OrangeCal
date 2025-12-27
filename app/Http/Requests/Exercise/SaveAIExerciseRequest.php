<?php

namespace App\Http\Requests\Exercise;

use App\Http\Requests\BaseFormRequest;

class SaveAIExerciseRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
            'logDate' => ['required', 'date'],
            'startTime' => ['nullable', 'date_format:H:i'],
            'endTime' => ['nullable', 'date_format:H:i'],
        ];
    }
}






