<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EditorUploadController extends Controller
{
    /**
     * Handle image upload from Editor.js.
     * 
     * Editor.js expects response format: { success: 1, file: { url: '...' } }
     */
    public function upload(Request $request): JsonResponse
    {
        // Handle URL-based image
        if ($request->query('type') === 'url') {
            $request->validate(['url' => 'required|url']);
            
            return response()->json([
                'success' => 1,
                'file' => [
                    'url' => $request->input('url'),
                ],
            ]);
        }

        // Handle file upload
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
        ]);

        $file = $request->file('image');
        $path = $file->store('editor-uploads/' . date('Y/m'), 'public');

        return response()->json([
            'success' => 1,
            'file' => [
                'url' => asset('storage/' . $path),
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ],
        ]);
    }
}
