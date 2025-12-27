<?php

namespace App\Http\Requests\Meal;

use App\Http\Requests\BaseFormRequest;

class CreateMealRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.ar' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'mealType' => ['nullable', 'string', 'in:breakfast,lunch,dinner,snack'],
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*.foodId' => ['required', 'integer', 'exists:food,id'],
            'ingredients.*.quantity' => ['required', 'numeric', 'min:0.1'],
            'ingredients.*.unit' => ['required', 'string'],
            'ingredients.*.grams' => ['nullable', 'numeric', 'min:0'],
            'servings' => ['nullable', 'numeric', 'min:0.1'],
            'prepTime' => ['nullable', 'integer', 'min:1'],
            'instructions' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],
            'isFavorite' => ['nullable', 'boolean'],
            'isPublic' => ['nullable', 'boolean'],
        ];
    }
}






