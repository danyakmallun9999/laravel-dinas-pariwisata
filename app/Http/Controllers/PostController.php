<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Services\FileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Post::class);
        
        $query = Post::latest();

        // Filter by ownership if user doesn't have global access
        if (!auth('admin')->user()->can('view all posts')) {
            $query->where('created_by', auth('admin')->id());
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $posts = $query->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        $this->authorize('create', Post::class);
        
        return view('admin.posts.create');
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $this->authorize('create', Post::class);
        
        $validated = $request->validated();
        $validated['created_by'] = auth('admin')->id(); // Auto-assign ownership

        $validated['slug'] = Str::slug($validated['title']).'-'.time();
        $validated['is_published'] = $request->has('is_published');

        if (! $validated['published_at'] && $validated['is_published']) {
            $validated['published_at'] = now();
        }

        if ($request->filled('image_gallery_url')) {
            $validated['image_path'] = $request->input('image_gallery_url');
        } elseif ($request->hasFile('image')) {
            $validated['image_path'] = $this->fileService->upload($request->file('image'), 'posts');
        }

        // Handle stat widgets
        if ($request->filled('stat_widgets')) {
            $validated['stat_widgets'] = json_decode($request->input('stat_widgets'), true) ?: [];
        }

        Post::create($validated);

        return redirect()->route('admin.posts.index')
            ->with('status', 'Berita/Event berhasil ditambahkan.');
    }

    public function show(Post $post): RedirectResponse
    {
        return redirect()->route('admin.posts.edit', $post);
    }

    public function edit(Post $post): View
    {
        $this->authorize('update', $post);
        
        return view('admin.posts.edit', compact('post'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);
        
        $validated = $request->validated();

        if ($post->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']).'-'.time();
        }

        $validated['is_published'] = $request->has('is_published');

        if ($request->filled('image_gallery_url')) {
            $this->fileService->delete($post->image_path);
            $validated['image_path'] = $request->input('image_gallery_url');
        } elseif ($request->hasFile('image')) {
            $this->fileService->delete($post->image_path);
            $validated['image_path'] = $this->fileService->upload($request->file('image'), 'posts');
        }

        // Handle stat widgets
        if ($request->has('stat_widgets')) {
            $validated['stat_widgets'] = json_decode($request->input('stat_widgets'), true) ?: [];
        }

        $post->update($validated);

        return redirect()->route('admin.posts.index')
            ->with('status', 'Berita/Event berhasil diperbarui.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);
        
        $this->fileService->delete($post->image_path);

        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('status', 'Post successfully deleted!');
    }

    public function uploadImage(Request $request): \Illuminate\Http\JsonResponse
    {
        // HIGH-02: Authorization + strict file validation
        $this->authorize('create', Post::class);

        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,gif,webp|max:5120',
        ]);

        if ($request->hasFile('file')) {
            $url = $this->fileService->upload($request->file('file'), 'uploads/content');

            return response()->json(['location' => $url]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
