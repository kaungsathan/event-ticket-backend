<?php

namespace App\Domains\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
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
            'customer_name' => 'sometimes|string|max:255',
            'customer_email' => 'sometimes|email|max:255',
            'customer_phone' => 'sometimes|string|max:20',
            'status' => ['sometimes', Rule::in(['pending', 'confirmed', 'cancelled'])],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'customer_name.string' => 'Customer name must be a string.',
            'customer_name.max' => 'Customer name may not be greater than 255 characters.',
            'customer_email.email' => 'Customer email must be a valid email address.',
            'customer_email.max' => 'Customer email may not be greater than 255 characters.',
            'customer_phone.string' => 'Customer phone must be a string.',
            'customer_phone.max' => 'Customer phone may not be greater than 20 characters.',
            'status.in' => 'Status must be one of: pending, confirmed, cancelled.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'customer_name' => 'customer name',
            'customer_email' => 'customer email',
            'customer_phone' => 'customer phone',
        ];
    }
}
