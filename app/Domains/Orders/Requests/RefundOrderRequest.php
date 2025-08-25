<?php

namespace App\Domains\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefundOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Refund amount is required.',
            'amount.numeric' => 'Refund amount must be a number.',
            'amount.min' => 'Refund amount must be at least 0.01.',
            'reason.required' => 'Refund reason is required.',
            'reason.string' => 'Refund reason must be a string.',
            'reason.max' => 'Refund reason may not be greater than 500 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'amount' => 'refund amount',
            'reason' => 'refund reason',
        ];
    }
}
