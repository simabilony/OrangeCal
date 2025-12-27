<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class BaseFormRequest extends FormRequest
{
    /**
     * Convert validated data from camelCase to snake_case globally.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        return collect($validated)
            ->mapWithKeys(fn ($value, $key) => [Str::snake($key) => $value])
            ->toArray();
    }
}
