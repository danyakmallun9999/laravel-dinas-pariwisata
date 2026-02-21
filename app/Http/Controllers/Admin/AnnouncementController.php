<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')->latest()->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'nullable|string|max:255',
            'content'      => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'image_format' => 'nullable|in:landscape,portrait',
            'button_text'  => 'nullable|string|max:100',
            'button_link'  => 'nullable|url|max:500',
            'is_active'    => 'nullable|boolean',
            'starts_at'    => 'nullable|date',
            'ends_at'      => 'nullable|date|after_or_equal:starts_at',
        ]);
        $validated['image_format'] = $request->input('image_format', 'landscape');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['created_by'] = Auth::guard('admin')->id();



        Announcement::create($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title'        => 'nullable|string|max:255',
            'content'      => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'image_format' => 'nullable|in:landscape,portrait,square,banner',
            'button_text'  => 'nullable|string|max:100',
            'button_link'  => 'nullable|url|max:500',
            'is_active'    => 'nullable|boolean',
            'starts_at'    => 'nullable|date',
            'ends_at'      => 'nullable|date|after_or_equal:starts_at',
        ]);
        $validated['image_format'] = $request->input('image_format', 'landscape');

        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image);
            }
            $validated['image'] = $request->file('image')->store('announcements', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');



        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->image) {
            Storage::disk('public')->delete($announcement->image);
        }
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function toggleActive(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);
        $message = $announcement->is_active
            ? 'Pengumuman berhasil diaktifkan.'
            : 'Pengumuman berhasil dinonaktifkan.';

        return back()->with('success', $message);
    }
}
