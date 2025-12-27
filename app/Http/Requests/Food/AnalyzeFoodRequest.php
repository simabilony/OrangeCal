<?php

namespace App\Http\Requests\Food;

use App\Http\Requests\BaseFormRequest;

class AnalyzeFoodRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
        ];
    }
}






