<?php

namespace App\Http\Requests\Food;

use App\Http\Requests\BaseFormRequest;

class SaveMealLogRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mealId' => ['required', 'integer', 'exists:user_meals,id'],
            'logDate' => ['required', 'date'],
            'logTime' => ['nullable', 'date_format:H:i'],
            'mealType' => ['required', 'string', 'in:breakfast,lunch,dinner,snack'],
            'servings' => ['nullable', 'numeric', 'min:0.1', 'max:10'],
        ];
    }
}






