<?php

namespace App\Domains\Organizers\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizerRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:organizers,email',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Organizer name is required.',
            'name.string' => 'Organizer name must be a string.',
            'name.max' => 'Organizer name may not be greater than 255 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.max' => 'Email may not be greater than 255 characters.',
            'phone.string' => 'Phone number must be a string.',
            'phone.max' => 'Phone number may not be greater than 20 characters.',
            'description.string' => 'Description must be a string.',
            'description.max' => 'Description may not be greater than 1000 characters.',
            'website.url' => 'Website must be a valid URL.',
            'website.max' => 'Website may not be greater than 255 characters.',
            'address.string' => 'Address must be a string.',
            'address.max' => 'Address may not be greater than 500 characters.',
            'is_active.boolean' => 'Active status must be true or false.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'is_active' => 'active status',
            'phone' => 'phone number',
        ];
    }
}
