<?php

namespace App\Domains\Events\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Events\Services\EventService;
use App\Domains\Events\Requests\StoreEventRequest;
use App\Domains\Events\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }
    /**
     * Display a listing of events with filtering, sorting, and including.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Event::class);

        $events = $this->eventService->getEvents([
            'per_page' => $request->get('per_page', 15)
        ]);

        return response()->json($events);
    }

    /**
     * Store a newly created event.
     */
    public function store(StoreEventRequest $request)
    {
        $this->authorize('create', Event::class);

        $event = $this->eventService->createEvent($request->validated(), auth()->user());

        return response()->json($event, 201);
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $this->authorize('view', $event);

        $event = $this->eventService->getEvent($event);

        return response()->json($event);
    }

    /**
     * Update the specified event.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $this->authorize('update', $event);

        $event = $this->eventService->updateEvent($event, $request->validated(), auth()->user());

        return response()->json($event);
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $this->eventService->deleteEvent($event, auth()->user());

        return response()->json(['message' => 'Event deleted successfully']);
    }

    /**
     * Publish/unpublish an event.
     */
    public function togglePublish(Event $event)
    {
        $this->authorize('publish', $event);

        $updatedEvent = $this->eventService->togglePublishStatus($event, auth()->user());
        $action = $updatedEvent->is_published ? 'published' : 'unpublished';

        return response()->json([
            'message' => "Event {$action} successfully",
            'event' => $updatedEvent
        ]);
    }
}
