<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class CreatePaymentRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'planType' => ['required', Rule::in(['monthly', 'quarterly', 'yearly'])],
            'paymentMethod' => ['required', Rule::in(['apple', 'google', 'credit_card', 'bank_transfer'])],
            'receipt' => ['nullable', 'array'],
            'storeProductId' => ['nullable', 'string'],
            'storeTransactionId' => ['nullable', 'string'],
        ];
    }
}






