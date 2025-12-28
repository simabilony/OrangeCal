<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class GoogleLoginOnlyRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string'],
            'fcmToken' => ['nullable', 'string'],
            'deviceType' => ['nullable', 'string', Rule::in(['ios', 'android', 'web'])],
            'deviceId' => ['nullable', 'string'],
        ];
    }
}

