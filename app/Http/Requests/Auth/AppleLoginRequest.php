<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class AppleLoginRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appleId' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'name' => ['nullable', 'string', 'max:255'],
            'fcmToken' => ['nullable', 'string'],
            'deviceType' => ['nullable', 'string', Rule::in(['ios', 'android', 'web'])],
            'deviceId' => ['nullable', 'string'],
        ];
    }
}

