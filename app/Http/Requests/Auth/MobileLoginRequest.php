<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class MobileLoginRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mobileId' => ['required', 'string'],
            'name' => ['nullable', 'string', 'max:255'],
            'fcmToken' => ['nullable', 'string'],
            'deviceType' => ['nullable', 'string'],
            'deviceId' => ['nullable', 'string'],
        ];
    }
}






