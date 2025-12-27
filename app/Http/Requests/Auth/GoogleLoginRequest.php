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
            'googleId' => ['nullable', 'string'],
            'email' => ['required', 'email','unique:users,email'],
            'password' => 'required|string|min:6|confirmed',
            'name' => ['required', 'string', 'max:255'],
            'fcmToken' => ['nullable', 'string'],
            'deviceType' => ['nullable', 'string', Rule::in(['ios', 'android', 'web'])],
            'deviceId' => ['nullable', 'string'],
        ];
    }

}






