<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $posts = $query->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:news,event',
            'image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
            'author' => 'nullable|string|max:255',
            'image_credit' => 'nullable|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        $validated['is_published'] = $request->has('is_published');
        if (!$validated['published_at'] && $validated['is_published']) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $validated['image_path'] = Storage::url($path);
        }

        Post::create($validated);

        return redirect()->route('admin.posts.index')
            ->with('status', 'Berita/Event berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return redirect()->route('admin.posts.edit', $post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:news,event',
            'image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
            'author' => 'nullable|string|max:255',
            'image_credit' => 'nullable|string|max:255',
        ]);

        if ($post->title !== $validated['title']) {
             $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        }

        $validated['is_published'] = $request->has('is_published');
        
        if ($request->hasFile('image')) {
            if ($post->image_path) {
                // remove old image logic if needed
            }
            $path = $request->file('image')->store('posts', 'public');
            $validated['image_path'] = Storage::url($path);
        }

        $post->update($validated);

        return redirect()->route('admin.posts.index')
            ->with('status', 'Berita/Event berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->image_path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $post->image_path));
        }
        
        $post->delete();
        return redirect()->route('admin.posts.index')->with('status', 'Post successfully deleted!');
    }

    /**
     * Handle TinyMCE Image Upload
     */
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads/content', 'public');
            return response()->json(['location' => '/storage/' . $path]);
        }
        
        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
