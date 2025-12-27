<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GoogleLoginRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'googleId' => ['required', 'string'],
            'email' => ['required', 'email'],
            'name' => ['required', 'string', 'max:255'],
            'fcmToken' => ['nullable', 'string'],
            'deviceType' => ['nullable', 'string', Rule::in(['ios', 'android', 'web'])],
            'deviceId' => ['nullable', 'string'],
        ];
    }

}

