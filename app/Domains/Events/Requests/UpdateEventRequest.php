<?php

namespace App\Domains\Events\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|date|after:now',
            'end_date' => 'sometimes|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'max_attendees' => 'nullable|integer|min:1',
            'is_published' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.string' => 'Event title must be a string.',
            'title.max' => 'Event title may not be greater than 255 characters.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.after' => 'Start date must be in the future.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after' => 'End date must be after the start date.',
            'location.string' => 'Location must be a string.',
            'location.max' => 'Location may not be greater than 255 characters.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be at least 0.',
            'max_attendees.integer' => 'Maximum attendees must be an integer.',
            'max_attendees.min' => 'Maximum attendees must be at least 1.',
            'is_published.boolean' => 'Published status must be true or false.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'start_date' => 'start date',
            'end_date' => 'end date',
            'max_attendees' => 'maximum attendees',
            'is_published' => 'published status',
        ];
    }
}
