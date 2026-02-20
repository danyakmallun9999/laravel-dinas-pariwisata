<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Culture;
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
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'time' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images/culture', 'public');
        }

        Culture::create($data);

        return redirect()->route('admin.cultures.index')->with('success', 'Culture created successfully.');
    }

    public function edit(Culture $culture)
    {
        $this->authorizeSuperAdmin();
        return view('admin.cultures.edit', compact('culture'));
    }

    public function update(Request $request, Culture $culture)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'time' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            if ($culture->image) {
                Storage::disk('public')->delete($culture->image);
            }
            $data['image'] = $request->file('image')->store('images/culture', 'public');
        }

        $culture->update($data);

        return redirect()->route('admin.cultures.index')->with('success', 'Culture updated successfully.');
    }

    public function destroy(Culture $culture)
    {
        $this->authorizeSuperAdmin();

        if ($culture->image) {
            Storage::disk('public')->delete($culture->image);
        }

        $culture->delete();

        return redirect()->route('admin.cultures.index')->with('success', 'Culture deleted successfully.');
    }
}
