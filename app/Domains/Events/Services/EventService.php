<?php

namespace App\Domains\Events\Services;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class EventService
{
    /**
     * Get paginated events with filtering and sorting.
     */
    public function getEvents(array $params = []): LengthAwarePaginator
    {
        return QueryBuilder::for(Event::class)
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
            ->paginate($params['per_page'] ?? 15);
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

    /**
     * Toggle event publication status.
     */
    public function togglePublishStatus(Event $event, User $user): Event
    {
        $event->update(['is_published' => !$event->is_published]);

        $action = $event->is_published ? 'published' : 'unpublished';

        // Log activity - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($event)
        //     ->causedBy($user)
        //     ->log("Event {$action}");

        return $event->load('creator');
    }

    /**
     * Publish an event.
     */
    public function publishEvent(Event $event, User $user): Event
    {
        if ($event->is_published) {
            throw new \InvalidArgumentException('Event is already published.');
        }

        $event->update(['is_published' => true]);

        // Log activity - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($event)
        //     ->causedBy($user)
        //     ->log('Event published');

        return $event->load('creator');
    }

    /**
     * Unpublish an event.
     */
    public function unpublishEvent(Event $event, User $user): Event
    {
        if (!$event->is_published) {
            throw new \InvalidArgumentException('Event is already unpublished.');
        }

        $event->update(['is_published' => false]);

        // Log activity - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($event)
        //     ->causedBy($user)
        //     ->log('Event unpublished');

        return $event->load('creator');
    }

    /**
     * Get events by user.
     */
    public function getEventsByUser(User $user, array $filters = []): Collection
    {
        $query = $user->events();

        if (isset($filters['status'])) {
            $query->where('is_published', $filters['status'] === 'published');
        }

        if (isset($filters['date_from'])) {
            $query->where('start_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('end_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    /**
     * Get event statistics.
     */
    public function getEventStatistics(array $filters = []): array
    {
        $query = Event::query();

        if (isset($filters['user_id'])) {
            $query->where('created_by', $filters['user_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return [
            'total_events' => $query->count(),
            'published_events' => $query->where('is_published', true)->count(),
            'unpublished_events' => $query->where('is_published', false)->count(),
            'upcoming_events' => $query->where('start_date', '>', now())->count(),
            'past_events' => $query->where('end_date', '<', now())->count(),
            'total_capacity' => $query->sum('max_attendees'),
            'average_price' => $query->avg('price'),
        ];
    }

    /**
     * Check if event can be deleted.
     */
    public function canDeleteEvent(Event $event): bool
    {
        // Check if event has orders
        return $event->orders()->count() === 0;
    }

    /**
     * Get upcoming events.
     */
    public function getUpcomingEvents(int $limit = 10): Collection
    {
        return Event::where('is_published', true)
            ->where('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search events by title or description.
     */
    public function searchEvents(string $query, array $filters = []): Collection
    {
        $queryBuilder = Event::where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            });

        if (isset($filters['location'])) {
            $queryBuilder->where('location', 'LIKE', "%{$filters['location']}%");
        }

        if (isset($filters['price_min'])) {
            $queryBuilder->where('price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $queryBuilder->where('price', '<=', $filters['price_max']);
        }

        return $queryBuilder->orderBy('start_date', 'asc')->get();
    }
}
