<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class OnboardingRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mobileId' => ['nullable', 'string', 'max:255'],
            'firebaseToken' => ['nullable', 'string', 'max:500'],
            'caloriesAdded' => ['nullable', 'boolean'],
            'height' => ['required', 'integer', 'min:50', 'max:250'],
            'weight' => ['required', 'integer', 'min:20', 'max:500'],
            'gender' => ['required', 'string', Rule::in(['male', 'female', 'other'])],
            'birthdate' => ['required', 'date', 'before:today'],
            'activity' => ['required', 'string', Rule::in(['sedentary', 'light', 'moderate', 'active', 'very_active'])],
            'tdeeGoal' => ['nullable', 'string'],
            'targetWeight' => ['nullable', 'numeric', 'min:20', 'max:500'],
            'weeklyTarget' => ['nullable', 'numeric'],
            'diet' => ['nullable', 'string'],
            'goal' => ['required', 'string', Rule::in(['lose_weight', 'maintain_weight', 'gain_weight', 'build_muscle'])],
            'triedAnotherApp' => ['nullable', 'boolean'],
            'hearingAboutUs' => ['nullable', 'string', 'max:255'],
            'meals' => ['nullable', 'array'],
            'meals.*.type' => ['required_with:meals', 'string'],
            'meals.*.time' => ['nullable', 'string'],
        ];
    }
}






