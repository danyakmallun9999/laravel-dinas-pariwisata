<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlaceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Reuse similar rules as Store but potentially relaxed for updates (though structure is same)
        // Ignoring uniqueness checks for now as slug is handled in service

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'geometry' => ['nullable', 'string'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'opening_hours' => ['nullable', 'string', 'max:255'],
            'contact_info' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'google_maps_link' => ['nullable', 'url', 'max:255'],
            'gallery_images.*' => ['nullable', 'image', 'max:2048'],
            'rides' => ['nullable', 'string'],
            'facilities' => ['nullable', 'string'],
            'ownership_status' => ['nullable', 'string'],
            'manager' => ['nullable', 'string'],
            'social_media' => ['nullable', 'string'],
        ];

        if (! $this->has('geometry') || ! $this->geometry) {
            $rules['latitude'] = ['required', 'numeric', 'between:-90,90'];
            $rules['longitude'] = ['required', 'numeric', 'between:-180,180'];
        }

        return $rules;
    }
}
