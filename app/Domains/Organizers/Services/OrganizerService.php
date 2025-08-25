<?php

namespace App\Domains\Organizers\Services;

use App\Models\Organizer;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class OrganizerService
{
    /**
     * Get paginated organizers with filtering and sorting.
     */
    public function getOrganizers(array $params = []): LengthAwarePaginator
    {
        return QueryBuilder::for(Organizer::class)
            ->allowedFilters([
                'name',
                'email',
                'is_verified',
                'is_active',
                AllowedFilter::exact('created_by'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
            ])
            ->allowedSorts([
                'name',
                'email',
                'created_at',
                'is_verified',
                'is_active',
                AllowedSort::field('creator_name', 'users.name'),
            ])
            ->allowedIncludes(['creator', 'events'])
            ->defaultSort('-created_at')
            ->paginate($params['per_page'] ?? 15);
    }

    /**
     * Create a new organizer.
     */
    public function createOrganizer(array $organizerData, User $user): Organizer
    {
        $organizer = Organizer::create([
            ...$organizerData,
            'created_by' => $user->id,
            'is_verified' => false, // New organizers start unverified
        ]);

        // Log activity - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($organizer)
        //     ->causedBy($user)
        //     ->log('Organizer created');

        return $organizer->load('creator');
    }

    /**
     * Get a single organizer with optional includes.
     */
    public function getOrganizer(Organizer $organizer, array $includes = ['creator', 'events']): Organizer
    {
        return QueryBuilder::for(Organizer::where('id', $organizer->id))
            ->allowedIncludes($includes)
            ->first();
    }

    /**
     * Update an organizer.
     */
    public function updateOrganizer(Organizer $organizer, array $organizerData, User $user): Organizer
    {
        $organizer->update($organizerData);

        // Log activity - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($organizer)
        //     ->causedBy($user)
        //     ->log('Organizer updated');

        return $organizer->load('creator');
    }

    /**
     * Delete an organizer.
     */
    public function deleteOrganizer(Organizer $organizer, User $user): void
    {
        // Check if organizer has events
        if (!$this->canDeleteOrganizer($organizer)) {
            throw new \InvalidArgumentException('Cannot delete organizer with existing events');
        }

        // Log activity before deletion - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($organizer)
        //     ->causedBy($user)
        //     ->log('Organizer deleted');

        $organizer->delete();
    }

    /**
     * Verify an organizer.
     */
    public function verifyOrganizer(Organizer $organizer, User $user): Organizer
    {
        if ($organizer->is_verified) {
            throw new \InvalidArgumentException('Organizer is already verified.');
        }

        $organizer->update(['is_verified' => true]);

        // Log activity - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($organizer)
        //     ->causedBy($user)
        //     ->log('Organizer verified');

        return $organizer->load('creator');
    }

    /**
     * Unverify an organizer.
     */
    public function unverifyOrganizer(Organizer $organizer, User $user): Organizer
    {
        if (!$organizer->is_verified) {
            throw new \InvalidArgumentException('Organizer is already unverified.');
        }

        $organizer->update(['is_verified' => false]);

        // Log activity - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($organizer)
        //     ->causedBy($user)
        //     ->log('Organizer unverified');

        return $organizer->load('creator');
    }

    /**
     * Toggle organizer status (active/inactive).
     */
    public function toggleOrganizerStatus(Organizer $organizer, User $user): Organizer
    {
        $organizer->update(['is_active' => !$organizer->is_active]);

        $status = $organizer->is_active ? 'activated' : 'deactivated';

        // Log activity - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($organizer)
        //     ->causedBy($user)
        //     ->log("Organizer {$status}");

        return $organizer->load('creator');
    }

    /**
     * Activate an organizer.
     */
    public function activateOrganizer(Organizer $organizer, User $user): Organizer
    {
        if ($organizer->is_active) {
            throw new \InvalidArgumentException('Organizer is already active.');
        }

        $organizer->update(['is_active' => true]);

        // Log activity - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($organizer)
        //     ->causedBy($user)
        //     ->log('Organizer activated');

        return $organizer->load('creator');
    }

    /**
     * Deactivate an organizer.
     */
    public function deactivateOrganizer(Organizer $organizer, User $user): Organizer
    {
        if (!$organizer->is_active) {
            throw new \InvalidArgumentException('Organizer is already inactive.');
        }

        $organizer->update(['is_active' => false]);

        // Log activity - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($organizer)
        //     ->causedBy($user)
        //     ->log('Organizer deactivated');

        return $organizer->load('creator');
    }

    /**
     * Get events for a specific organizer.
     */
    public function getOrganizerEvents(Organizer $organizer, array $params = []): LengthAwarePaginator
    {
        return QueryBuilder::for($organizer->events())
            ->allowedFilters([
                'title',
                'location',
                'is_published',
            ])
            ->allowedSorts([
                'title',
                'start_date',
                'end_date',
                'created_at',
            ])
            ->defaultSort('-created_at')
            ->paginate($params['per_page'] ?? 15);
    }

    /**
     * Get organizers by user.
     */
    public function getOrganizersByUser(User $user, array $filters = []): Collection
    {
        $query = $user->organizers();

        if (isset($filters['is_verified'])) {
            $query->where('is_verified', $filters['is_verified']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Search organizers by name or email.
     */
    public function searchOrganizers(string $query, array $filters = []): Collection
    {
        $queryBuilder = Organizer::where(function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
              ->orWhere('email', 'LIKE', "%{$query}%");
        });

        if (isset($filters['is_verified'])) {
            $queryBuilder->where('is_verified', $filters['is_verified']);
        }

        if (isset($filters['is_active'])) {
            $queryBuilder->where('is_active', $filters['is_active']);
        }

        return $queryBuilder->orderBy('name', 'asc')->get();
    }

    /**
     * Get organizer statistics.
     */
    public function getOrganizerStatistics(array $filters = []): array
    {
        $query = Organizer::query();

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
            'total_organizers' => $query->count(),
            'verified_organizers' => $query->where('is_verified', true)->count(),
            'unverified_organizers' => $query->where('is_verified', false)->count(),
            'active_organizers' => $query->where('is_active', true)->count(),
            'inactive_organizers' => $query->where('is_active', false)->count(),
            'organizers_with_events' => $query->has('events')->count(),
            'organizers_without_events' => $query->doesntHave('events')->count(),
        ];
    }

    /**
     * Check if organizer can be deleted.
     */
    public function canDeleteOrganizer(Organizer $organizer): bool
    {
        return $organizer->events()->count() === 0;
    }

    /**
     * Get top organizers by event count.
     */
    public function getTopOrganizers(int $limit = 10): Collection
    {
        return Organizer::withCount('events')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->orderBy('events_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get organizers requiring verification.
     */
    public function getUnverifiedOrganizers(): Collection
    {
        return Organizer::where('is_verified', false)
            ->where('is_active', true)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
