<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255'],
            'gender' => ['sometimes', Rule::in(['male', 'female'])],
            'birthDate' => ['sometimes', 'date', 'before:today'],
            'height' => ['sometimes', 'numeric', 'min:50', 'max:250'],
            'weight' => ['sometimes', 'numeric', 'min:20', 'max:500'],
            'targetWeight' => ['nullable', 'numeric', 'min:20', 'max:500'],
            'goal' => ['sometimes', Rule::in(['lose_weight', 'maintain_weight', 'gain_weight', 'build_muscle'])],
            'activityLevel' => ['sometimes', Rule::in(['sedentary', 'light', 'moderate', 'active', 'very_active'])],
            'dietaryPreferences' => ['nullable', 'array'],
            'allergies' => ['nullable', 'array'],
            'healthConditions' => ['nullable', 'array'],
            'preferredLanguage' => ['nullable', Rule::in(['ar', 'en'])],
            'timezone' => ['nullable', 'string'],
            'notificationsEnabled' => ['nullable', 'boolean'],
            'fcmToken' => ['nullable', 'string'],
        ];
    }
}






