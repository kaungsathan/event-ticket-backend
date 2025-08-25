<?php

namespace App\Domains\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'event_id' => 'required|exists:events,id',
            'quantity' => 'required|integer|min:1|max:10',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'sometimes|string|max:20',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'event_id.required' => 'Event is required.',
            'event_id.exists' => 'The selected event does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity may not be greater than 10.',
            'customer_name.required' => 'Customer name is required.',
            'customer_name.string' => 'Customer name must be a string.',
            'customer_name.max' => 'Customer name may not be greater than 255 characters.',
            'customer_email.required' => 'Customer email is required.',
            'customer_email.email' => 'Customer email must be a valid email address.',
            'customer_email.max' => 'Customer email may not be greater than 255 characters.',
            'customer_phone.string' => 'Customer phone must be a string.',
            'customer_phone.max' => 'Customer phone may not be greater than 20 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'event_id' => 'event',
            'customer_name' => 'customer name',
            'customer_email' => 'customer email',
            'customer_phone' => 'customer phone',
        ];
    }
}
