<?php

namespace App\Http\Requests\Food;

use App\Http\Requests\BaseFormRequest;

class BarcodeScanRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barcode' => ['required', 'string'],
        ];
    }
}






