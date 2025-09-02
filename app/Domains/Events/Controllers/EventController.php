<?php

namespace App\Domains\Events\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Domains\Events\Services\EventService;
use App\Domains\Events\Requests\StoreEventRequest;
use App\Domains\Events\Requests\UpdateEventRequest;

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
        $events = $this->eventService->getEvents($request->all());

        return response()->json([
            'message' => 'Events fetched successfully',
            'data' => $events
        ]);
    }

    /**
     * Store a newly created event.
     */
    public function store(StoreEventRequest $request)
    {
        $payload = $request->validated();
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            $payload['image'] = $imagePath;
        }

        $event = $this->eventService->createEvent($payload, auth()->user());

        return response()->json($event, 201);
    }

    /**
     * Display the specified event.
     */
    public function show(int $id)
    {
        $event = $this->eventService->getEvent($id);

        return response()->json([
            'message' => 'Event fetched successfully',
            'data' => $event
        ]);
    }

    /**
     * Update the specified event.
     */
    public function update(int $id, UpdateEventRequest $request)
    {
        $payload = $request->validated();

        if ($request->hasFile('image')) {
            $event = Event::find($id);
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }

            $imagePath = $request->file('image')->store('events', 'public');
            $payload['image'] = $imagePath;
        }

        $event = $this->eventService->updateEvent($id, $payload);

        return response()->json([
            'message' => 'Event updated successfully',
            'data' => $event
        ]);
    }

    /**
     * Remove the specified event.
     */
    public function destroy(int $id)
    {
        $this->eventService->deleteEvent($id);

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
