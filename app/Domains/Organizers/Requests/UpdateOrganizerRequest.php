<?php

namespace App\Domains\Organizers\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizerRequest extends FormRequest
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
        $organizerId = $this->route('id');

        return [
            'company_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:organizers,email,' . $organizerId,
            'company_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'date' => 'sometimes|date',
            'status' => 'sometimes|string|max:255',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'company_name.string' => 'Organizer name must be a string.',
            'company_name.max' => 'Organizer name may not be greater than 255 characters.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.max' => 'Email may not be greater than 255 characters.',
            'company_phone.string' => 'Phone number must be a string.',
            'company_phone.max' => 'Phone number may not be greater than 20 characters.',
            'description.string' => 'Description must be a string.',
            'description.max' => 'Description may not be greater than 1000 characters.',
            'website.url' => 'Website must be a valid URL.',
            'website.max' => 'Website may not be greater than 255 characters.',
            'address.string' => 'Address must be a string.',
            'address.max' => 'Address may not be greater than 500 characters.',
            'date.date' => 'Date must be a valid date.',
            'status.string' => 'Status must be a string.',
            'status.max' => 'Status may not be greater than 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'company_name' => 'company name',
            'company_phone' => 'company phone',
            'address' => 'address',
            'date' => 'date',
            'status' => 'status',
        ];
    }
}
