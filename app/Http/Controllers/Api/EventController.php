<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class EventController extends Controller
{
    /**
     * Display a listing of events with filtering, sorting, and including.
     */
    public function index(Request $request)
    {
        // Check permission using Spatie Permission
        $this->authorize('viewAny', Event::class);

        $events = QueryBuilder::for(Event::class)
            ->allowedFilters([
                'title',
                'location',
                'is_published',
                AllowedFilter::exact('created_by'),
                AllowedFilter::scope('price_range'),
                AllowedFilter::scope('date_range'),
            ])
            ->allowedSorts([
                'title',
                'start_date',
                'end_date',
                'price',
                'created_at',
                AllowedSort::field('creator_name', 'users.name'),
            ])
            ->allowedIncludes(['creator'])
            ->defaultSort('-created_at')
            ->paginate($request->get('per_page', 15));

        return response()->json($events);
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_attendees' => 'nullable|integer|min:1',
        ]);

        $event = Event::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        // Activity log will automatically log the creation
        activity()
            ->performedOn($event)
            ->causedBy(auth()->user())
            ->log('Event created');

        return response()->json($event->load('creator'), 201);
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $this->authorize('view', $event);

        $event = QueryBuilder::for(Event::where('id', $event->id))
            ->allowedIncludes(['creator'])
            ->first();

        return response()->json($event);
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|date|after:now',
            'end_date' => 'sometimes|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'max_attendees' => 'nullable|integer|min:1',
            'is_published' => 'sometimes|boolean',
        ]);

        $event->update($validated);

        // Activity log will automatically log the changes
        activity()
            ->performedOn($event)
            ->causedBy(auth()->user())
            ->log('Event updated');

        return response()->json($event->load('creator'));
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        activity()
            ->performedOn($event)
            ->causedBy(auth()->user())
            ->log('Event deleted');

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }

    /**
     * Publish/unpublish an event.
     */
    public function togglePublish(Event $event)
    {
        $this->authorize('publish', $event);

        $event->update(['is_published' => !$event->is_published]);

        $action = $event->is_published ? 'published' : 'unpublished';

        activity()
            ->performedOn($event)
            ->causedBy(auth()->user())
            ->log("Event {$action}");

        return response()->json([
            'message' => "Event {$action} successfully",
            'event' => $event->load('creator')
        ]);
    }
}
