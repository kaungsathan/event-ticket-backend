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
            'user_id' => 'sometimes|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:255',
            'customer_address' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'payment_status' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:255',
            'status' => 'sometimes|string|in:pending,confirmed,cancelled,completed',
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
            'customer_name.required' => 'Customer name is required.',
            'customer_name.string' => 'Customer name must be a string.',
            'customer_name.max' => 'Customer name may not be greater than 255 characters.',
            'customer_email.required' => 'Customer email is required.',
            'customer_email.email' => 'Customer email must be a valid email address.',
            'customer_email.max' => 'Customer email may not be greater than 255 characters.',
            'customer_phone.required' => 'Customer phone is required.',
            'customer_phone.string' => 'Customer phone must be a string.',
            'customer_phone.max' => 'Customer phone may not be greater than 255 characters.',
            'customer_address.required' => 'Customer address is required.',
            'customer_address.string' => 'Customer address must be a string.',
            'customer_address.max' => 'Customer address may not be greater than 255 characters.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.numeric' => 'Total amount must be a number.',
            'total_amount.min' => 'Total amount must be at least 0.',
            'payment_status.string' => 'Payment status must be a string.',
            'payment_status.max' => 'Payment status may not be greater than 255 characters.',
            'payment_method.string' => 'Payment method must be a string.',
            'payment_method.max' => 'Payment method may not be greater than 255 characters.',
            'status.string' => 'Status must be a string.',
            'status.in' => 'Status must be one of: pending, confirmed, cancelled, completed.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'event_id' => 'event',
            'user_id' => 'user',
            'customer_name' => 'customer name',
            'customer_email' => 'customer email',
            'customer_phone' => 'customer phone',
            'customer_address' => 'customer address',
            'quantity' => 'quantity',
            'total_amount' => 'total amount',
            'payment_status' => 'payment status',
            'payment_method' => 'payment method',
            'status' => 'status',
        ];
    }
}
