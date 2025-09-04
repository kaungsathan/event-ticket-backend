<?php

namespace App\Domains\Events\Services;

use App\Models\Event;
use App\Models\User;
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
        $organizerId = $params['organizer_id'] ?? null;
        $typeId = $params['type_id'] ?? null;
        $categoryId = $params['category_id'] ?? null;
        $tagId = $params['tag_id'] ?? null;

        $query = Event::query()
                ->leftJoin('organizers', 'events.organizer_id', 'organizers.id')
                ->leftJoin('types', 'events.type_id', 'types.id')
                ->leftJoin('categories', 'events.category_id', 'categories.id')
                ->leftJoin('tags', 'events.tag_id', 'tags.id')
                ->select('events.*', 'organizers.company_name as organizer_name', 'types.name as type_name', 'categories.name as category_name', 'tags.name as tag_name')
                ->when($status, fn ($query) => $query->where('events.status', $status))
                ->when($organizerId, fn ($query) => $query->where('events.organizer_id', $organizerId))
                ->when($typeId, fn ($query) => $query->where('events.type_id', $typeId))
                ->when($categoryId, fn ($query) => $query->where('events.category_id', $categoryId))
                ->when($tagId, fn ($query) => $query->where('events.tag_id', $tagId));


        if(isset($params['search']))
        {
            $query->where('title', 'like', '%' . $params['search'] . '%');
        }

        $query->orderBy('created_at', 'desc');

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
    public function getEvent(int $id, array $includes = ['creator']): Event
    {
        return QueryBuilder::for(Event::where('id', $id))
            ->allowedIncludes($includes)
            ->first();
    }

    /**
     * Update an event.
     */
    public function updateEvent(int $id, array $eventData): Event
    {
        $event = Event::find($id);
        $event->update($eventData);

        // Log activity - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($event)
        //     ->causedBy($user)
        //     ->log('Event updated');

        return $event;
    }

    /**
     * Delete an event.
     */
    public function deleteEvent(int $id): void
    {
        $event = Event::findOrFail($id);


        $event->delete();
    }
}
