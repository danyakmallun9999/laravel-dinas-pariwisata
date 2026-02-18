<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
            'type' => 'required|in:news,event',
            'image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
            'author' => 'nullable|string|max:255',
            'image_credit' => 'nullable|string|max:255',
            'is_published' => 'sometimes|boolean',
            'title_en' => 'nullable|string|max:255',
            'content_en' => 'nullable|string|max:10000',
        ];
    }
}
