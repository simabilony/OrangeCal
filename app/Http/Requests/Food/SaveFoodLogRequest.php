<?php

namespace App\Http\Requests\Food;

use App\Http\Requests\BaseFormRequest;

class SaveFoodLogRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'foodId' => ['required', 'integer', 'exists:food,id'],
            'logDate' => ['required', 'date'],
            'logTime' => ['nullable', 'date_format:H:i'],
            'mealType' => ['required', 'string', 'in:breakfast,lunch,dinner,snack'],
            'quantity' => ['required', 'numeric', 'min:0.1'],
            'unit' => ['required', 'string'],
            'grams' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}






