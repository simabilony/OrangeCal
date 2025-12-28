<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class BaseFormRequest extends FormRequest
{
    /**
     * Convert validated data from camelCase to snake_case globally.
     * Handles nested arrays recursively.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        return $this->convertKeysToSnakeCase($validated);
    }

    /**
     * Recursively convert array keys from camelCase to snake_case.
     */
    protected function convertKeysToSnakeCase($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $converted = [];
        foreach ($data as $key => $value) {
            $snakeKey = Str::snake($key);

            // Recursively convert nested arrays
            if (is_array($value)) {
                $converted[$snakeKey] = $this->convertKeysToSnakeCase($value);
            } else {
                $converted[$snakeKey] = $value;
            }
        }

        return $converted;
    }
}
