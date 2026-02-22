<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HeroSettingController extends Controller
{
    public function edit()
    {
        $setting = \App\Models\HeroSetting::first();
        if (!$setting) {
            $setting = new \App\Models\HeroSetting([
               'type' => 'map',
               'badge_id' => 'Portal Resmi Pariwisata',
               'badge_en' => 'Official Tourism Portal',
               'title_id' => 'Jelajah Jepara. <br> Ukir Ceritamu Di Sini',
               'title_en' => 'Explore Jepara. <br> Carve Your Story Here',
               'subtitle_id' => 'Temukan pesona pantai tropis, kekayaan sejarah, dan mahakarya ukiran kayu kelas dunia.',
               'subtitle_en' => 'Discover tropical beaches, rich history, and world-class wood carving masterpieces.',
               'button_text_id' => 'Mulai Jelajah',
               'button_text_en' => 'Start Exploring',
               'button_link' => '#explore'
            ]);
        }
        return view('admin.hero-settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:map,video,image',
            'title_id' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'subtitle_id' => 'nullable|string',
            'subtitle_en' => 'nullable|string',
            'badge_id' => 'nullable|string|max:255',
            'badge_en' => 'nullable|string|max:255',
            'button_text_id' => 'nullable|string|max:255',
            'button_text_en' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:255',
            'video_file' => 'nullable|mimes:mp4,webm,ogg|max:50000', // max 50MB
            'image_files.*' => 'nullable|image|max:10240', // max 10MB per image
            'image_files_gallery_url' => 'nullable|array',
            'image_files_gallery_url.*' => 'nullable|string',
            'remove_media' => 'nullable|boolean',
            'existing_media' => 'nullable|array',
        ]);

        $setting = \App\Models\HeroSetting::first() ?? new \App\Models\HeroSetting();
        $setting->type = $validated['type'];
        $setting->title_id = $validated['title_id'] ?? null;
        $setting->title_en = $validated['title_en'] ?? null;
        $setting->subtitle_id = $validated['subtitle_id'] ?? null;
        $setting->subtitle_en = $validated['subtitle_en'] ?? null;
        $setting->badge_id = $validated['badge_id'] ?? null;
        $setting->badge_en = $validated['badge_en'] ?? null;
        $setting->button_text_id = $validated['button_text_id'] ?? null;
        $setting->button_text_en = $validated['button_text_en'] ?? null;
        $setting->button_link = $validated['button_link'] ?? null;

        $mediaPaths = $setting->media_paths ?? [];

        // Handle media removal if changing types or explicit delete
        if ($request->boolean('remove_media') || $setting->isDirty('type')) {
             if (is_array($mediaPaths)) {
                  foreach($mediaPaths as $path) {
                       \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                  }
             }
             $mediaPaths = [];
        } else {
             // Keep selected existing media (for images)
             $existingMedia = $request->input('existing_media', []);
             $retainedMedia = [];
             foreach($mediaPaths as $path) {
                  if (in_array($path, $existingMedia)) {
                       $retainedMedia[] = $path;
                  } else {
                       \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                  }
             }
             $mediaPaths = $retainedMedia;
        }

        if ($validated['type'] === 'video' && $request->hasFile('video_file')) {
             $path = $request->file('video_file')->store('hero', 'public');
             $mediaPaths = [$path];
        } elseif ($validated['type'] === 'image') {
             // Handle newly selected gallery images
             if ($request->has('image_files_gallery_url')) {
                 foreach($request->input('image_files_gallery_url') as $url) {
                     // Extract relative path from URL
                     $path = str_replace(Storage::url(''), '', $url);
                     if (!in_array($path, $mediaPaths)) {
                         $mediaPaths[] = $path;
                     }
                 }
             }
             
             // Handle newly uploaded files
             if ($request->hasFile('image_files')) {
                 foreach($request->file('image_files') as $file) {
                     $path = $file->store('hero', 'public');
                     $mediaPaths[] = $path;
                 }
             }
        }

        $setting->media_paths = count($mediaPaths) > 0 ? $mediaPaths : null;
        $setting->save();

        return redirect()->back()->with('success', 'Pengaturan Hero berhasil diperbarui.');
    }
}
