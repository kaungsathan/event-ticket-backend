<?php

namespace App\Domains\Events\Services;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class EventService
{
    /**
     * Get paginated events with filtering and sorting.
     */
    public function getEvents(array $params = []): LengthAwarePaginator
    {
        $page = $params['page'] ?? 1;
        $perPage = $params['per_page'] ?? 15;
        $status = $params['status'] ?? null;

        $query = Event::query()->with(['organizer'])
                ->when($status, fn ($query) => $query->where('status', $status));

        if(isset($params['search']))
        {
            $query->where('title', 'like', '%' . $params['search'] . '%')
                ->orWhere('description', 'like', '%' . $params['search'] . '%');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Create a new event.
     */
    public function createEvent(array $eventData, User $user): Event
    {
        $event = Event::create([
            ...$eventData,
            'created_by' => $user->id,
        ]);

        // Log activity - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($event)
        //     ->causedBy($user)
        //     ->log('Event created');

        return $event->load('creator');
    }

    /**
     * Get a single event with optional includes.
     */
    public function getEvent(Event $event, array $includes = ['creator']): Event
    {
        return QueryBuilder::for(Event::where('id', $event->id))
            ->allowedIncludes($includes)
            ->first();
    }

    /**
     * Update an event.
     */
    public function updateEvent(Event $event, array $eventData, User $user): Event
    {
        $event->update($eventData);

        // Log activity - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($event)
        //     ->causedBy($user)
        //     ->log('Event updated');

        return $event->load('creator');
    }

    /**
     * Delete an event.
     */
    public function deleteEvent(Event $event, User $user): void
    {
        // Log activity before deletion - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($event)
        //     ->causedBy($user)
        //     ->log('Event deleted');

        $event->delete();
    }
}
