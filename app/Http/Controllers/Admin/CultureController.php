<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Culture;
use App\Models\CultureImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CultureController extends Controller
{
    private function authorizeSuperAdmin()
    {
        if (!auth('admin')->user()?->hasRole('super_admin')) {
            abort(403, 'Unauthorized access.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        $categories = Culture::select('category')->distinct()->pluck('category');

        $cultures = Culture::query()
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->category, function ($query) use ($request) {
                $query->where('category', $request->category);
            })
            ->latest()
            ->paginate(10);

        return view('admin.cultures.index', compact('cultures', 'categories'));
    }

    public function create()
    {
        $this->authorizeSuperAdmin();
        return view('admin.cultures.create');
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:255',
            'image'       => 'nullable|image|max:4096',
            'images.*'    => 'nullable|image|max:4096',
            'description' => 'required|string',
            'content'     => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'time'        => 'nullable|string|max:255',
            'youtube_url' => 'nullable|url|max:500',
        ]);

        $data = $request->only(['name','category','description','content','location','time','youtube_url']);
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images/culture', 'public');
        }

        $culture = Culture::create($data);

        // Simpan foto-foto tambahan
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('images/culture', 'public');
                $culture->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('admin.cultures.index')->with('success', 'Budaya berhasil ditambahkan.');
    }

    public function edit(Culture $culture)
    {
        $this->authorizeSuperAdmin();
        $culture->load('images');
        return view('admin.cultures.edit', compact('culture'));
    }

    public function update(Request $request, Culture $culture)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:255',
            'image'       => 'nullable|image|max:4096',
            'images.*'    => 'nullable|image|max:4096',
            'description' => 'required|string',
            'content'     => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'time'        => 'nullable|string|max:255',
            'youtube_url' => 'nullable|url|max:500',
        ]);

        $data = $request->only(['name','category','description','content','location','time','youtube_url']);
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            if ($culture->image) {
                Storage::disk('public')->delete($culture->image);
            }
            $data['image'] = $request->file('image')->store('images/culture', 'public');
        }

        $culture->update($data);

        // Simpan foto-foto tambahan baru
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('images/culture', 'public');
                $culture->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('admin.cultures.index')->with('success', 'Budaya berhasil diperbarui.');
    }

    public function destroy(Culture $culture)
    {
        $this->authorizeSuperAdmin();

        if ($culture->image) {
            Storage::disk('public')->delete($culture->image);
        }

        // Hapus semua foto tambahan
        foreach ($culture->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        $culture->delete();

        return redirect()->route('admin.cultures.index')->with('success', 'Budaya berhasil dihapus.');
    }

    public function destroyImage(CultureImage $image)
    {
        $this->authorizeSuperAdmin();

        Storage::disk('public')->delete($image->image_path);
        $cultureId = $image->culture_id;
        $image->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
