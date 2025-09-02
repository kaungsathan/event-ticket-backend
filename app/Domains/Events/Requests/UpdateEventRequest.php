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
            'title' => 'required|string|max:255',
            'organizer_id' => 'nullable|exists:organizers,id',
            'description' => 'nullable|string',
            'category_id' => 'nullable|string|max:255',
            'tag_id' => 'nullable|string|max:255',
            'type_id' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_attendees' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
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
            'organizer_id.exists' => 'Organizer not found.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.after' => 'Start date must be in the future.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after' => 'End date must be after the start date.',
            'location.string' => 'Location must be a string.',
            'location.max' => 'Location may not be greater than 255 characters.',
            'type_id.string' => 'Type must be a string.',
            'type_id.max' => 'Type may not be greater than 255 characters.',
            'tag_id.string' => 'Tag must be a string.',
            'tag_id.max' => 'Tag may not be greater than 255 characters.',
            'category_id.string' => 'Category must be a string.',
            'category_id.max' => 'Category may not be greater than 255 characters.',
            'status.string' => 'Status must be a string.',
            'status.max' => 'Status may not be greater than 255 characters.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be at least 0.',
            'max_attendees.integer' => 'Maximum attendees must be an integer.',
            'max_attendees.min' => 'Maximum attendees must be at least 1.',
            'is_published.boolean' => 'Published status must be true or false.',
            'image.image' => 'Image must be a valid image file.',
            'image.mimes' => 'Image must be a JPEG, PNG, or WebP file.',
            'image.max' => 'Image cannot exceed 2MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'organizer_id' => 'organizer',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'max_attendees' => 'maximum attendees',
            'is_published' => 'published status',
            'image' => 'image',
            'type_id' => 'type',
            'tag_id' => 'tag',
            'category_id' => 'category',
        ];
    }
}
