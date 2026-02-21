<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Legend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LegendController extends Controller
{
    public function index()
    {
        $legends = Legend::orderBy('order')->get();
        return view('admin.legends.index', compact('legends'));
    }

    public function create()
    {
        return view('admin.legends.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'quote_id'       => 'nullable|string',
            'quote_en'       => 'nullable|string',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
            'order'          => 'nullable|integer',
            'is_active'      => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('legends', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['order'] = $request->input('order', 0);

        Legend::create($validated);

        return redirect()->route('admin.legends.index')
            ->with('success', 'Tokoh Sejarah berhasil ditambahkan.');
    }

    public function edit(Legend $legend)
    {
        return view('admin.legends.edit', compact('legend'));
    }

    public function update(Request $request, Legend $legend)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'quote_id'       => 'nullable|string',
            'quote_en'       => 'nullable|string',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
            'order'          => 'nullable|integer',
            'is_active'      => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($legend->image && !str_starts_with($legend->image, 'images/')) {
                Storage::disk('public')->delete($legend->image);
            }
            $validated['image'] = $request->file('image')->store('legends', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['order'] = $request->input('order', 0);

        $legend->update($validated);

        return redirect()->route('admin.legends.index')
            ->with('success', 'Tokoh Sejarah berhasil diperbarui.');
    }

    public function destroy(Legend $legend)
    {
        if ($legend->image && !str_starts_with($legend->image, 'images/')) {
            Storage::disk('public')->delete($legend->image);
        }
        $legend->delete();

        return redirect()->route('admin.legends.index')
            ->with('success', 'Tokoh Sejarah berhasil dihapus.');
    }

    public function toggleActive(Legend $legend)
    {
        $legend->update(['is_active' => !$legend->is_active]);
        return back()->with('success', 'Status berhasil diubah.');
    }
}
