<?php

namespace App\Http\Requests\Food;

use App\Http\Requests\BaseFormRequest;

class UpdateFoodLogRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logDate' => ['sometimes', 'date'],
            'logTime' => ['nullable', 'date_format:H:i'],
            'mealType' => ['sometimes', 'string', 'in:breakfast,lunch,dinner,snack'],
            'quantity' => ['sometimes', 'numeric', 'min:0.1'],
            'unit' => ['sometimes', 'string'],
            'grams' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}






