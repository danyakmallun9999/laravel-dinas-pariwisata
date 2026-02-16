<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Services\FileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function calendar(Request $request): View
    {
        $this->authorize('viewAny', Event::class);
        
        $year = $request->get('year', now()->year);
        
        $query = Event::query()
            ->whereYear('start_date', $year);

        if (!auth('admin')->user()->can('view all events')) {
            $query->where('created_by', auth('admin')->id());
        }

        $eventsByMonth = $query->orderBy('start_date')
            ->get()
            ->groupBy(fn($event) => $event->start_date->month);

        return view('admin.events.calendar', compact('eventsByMonth', 'year'));
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Event::class);
        
        $query = Event::query();

        // Filter by ownership if user doesn't have global access
        if (!auth('admin')->user()->can('view all events')) {
            $query->where('created_by', auth('admin')->id());
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%')
                ->orWhere('location', 'like', '%'.$request->search.'%');
        }

        $events = $query->latest('start_date')->paginate(10);

        // Stats for the dashboard
        if (auth('admin')->user()->can('view all events')) {
            $stats = [
                'total' => Event::count(),
                'published' => Event::where('is_published', true)->count(),
                'upcoming' => Event::where('start_date', '>=', now())->count(),
            ];
        } else {
            // Show only user's own stats
            $stats = [
                'total' => Event::where('created_by', auth('admin')->id())->count(),
                'published' => Event::where('created_by', auth('admin')->id())->where('is_published', true)->count(),
                'upcoming' => Event::where('created_by', auth('admin')->id())->where('start_date', '>=', now())->count(),
            ];
        }

        return view('admin.events.index', compact('events', 'stats'));
    }

    public function create(): View
    {
        $this->authorize('create', Event::class);
        
        return view('admin.events.create');
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $this->authorize('create', Event::class);
        
        $validated = $request->validated();
        $validated['created_by'] = auth('admin')->id(); // Auto-assign ownership

        if ($request->hasFile('image')) {
            $validated['image'] = $this->fileService->upload($request->file('image'), 'events');
        }

        if (! isset($validated['is_published'])) {
            $validated['is_published'] = false;
        }

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dibuat!');
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);
        
        return view('admin.events.edit', compact('event'));
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);
        
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $this->fileService->delete($event->image);
            $validated['image'] = $this->fileService->upload($request->file('image'), 'events');
        }

        $validated['is_published'] = $request->has('is_published');

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil diperbarui!');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);
        
        $this->fileService->delete($event->image);

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus!');
    }
}
