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
            'event_id' => 'required|exists:events,id',
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
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
            'user_id.required' => 'User is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be at least 0.',
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
            'amount' => 'amount',
            'payment_method' => 'payment method',
            'status' => 'status',
        ];
    }
}
