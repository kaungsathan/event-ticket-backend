<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization will be handled in the controller/policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'start_date' => ['required', 'date', 'after:now'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'location' => ['nullable', 'string', 'max:500'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'max_attendees' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'is_published' => ['boolean'],
            'organizer_id' => ['nullable', 'exists:organizers,id'],
            'image_alt_text' => ['nullable', 'string', 'max:255'],
        ];

        // Add image validation rules based on the request method
        if ($this->isMethod('POST')) {
            // Creating new event
            $rules['featured_image'] = ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120']; // 5MB
            $rules['gallery_images'] = ['nullable', 'array', 'max:10']; // Max 10 images
            $rules['gallery_images.*'] = ['image', 'mimes:jpeg,jpg,png,webp', 'max:5120'];
        } else {
            // Updating existing event
            $rules['featured_image'] = ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'];
            $rules['gallery_images'] = ['nullable', 'array', 'max:10'];
            $rules['gallery_images.*'] = ['image', 'mimes:jpeg,jpg,png,webp', 'max:5120'];
        }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Event title is required.',
            'title.max' => 'Event title cannot exceed 255 characters.',
            'start_date.required' => 'Start date is required.',
            'start_date.after' => 'Start date must be in the future.',
            'end_date.required' => 'End date is required.',
            'end_date.after' => 'End date must be after start date.',
            'price.min' => 'Price cannot be negative.',
            'price.max' => 'Price cannot exceed 999,999.99.',
            'max_attendees.min' => 'Maximum attendees must be at least 1.',
            'max_attendees.max' => 'Maximum attendees cannot exceed 100,000.',
            'featured_image.image' => 'Featured image must be a valid image file.',
            'featured_image.mimes' => 'Featured image must be a JPEG, PNG, or WebP file.',
            'featured_image.max' => 'Featured image cannot exceed 5MB.',
            'gallery_images.max' => 'You can upload a maximum of 10 gallery images.',
            'gallery_images.*.image' => 'Gallery images must be valid image files.',
            'gallery_images.*.mimes' => 'Gallery images must be JPEG, PNG, or WebP files.',
            'gallery_images.*.max' => 'Each gallery image cannot exceed 5MB.',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'title' => 'event title',
            'description' => 'event description',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'location' => 'event location',
            'price' => 'event price',
            'max_attendees' => 'maximum attendees',
            'featured_image' => 'featured image',
            'gallery_images' => 'gallery images',
            'image_alt_text' => 'image alt text',
        ];
    }
}
