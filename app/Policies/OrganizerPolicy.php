<?php

namespace App\Policies;

use App\Models\Organizer;
use App\Models\User;

class OrganizerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view organizers');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organizer $organizer): bool
    {
        return $user->can('view organizers') || $organizer->created_by === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create organizers');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Organizer $organizer): bool
    {
        return $user->can('edit organizers') ||
               ($user->can('edit own organizers') && $organizer->created_by === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Organizer $organizer): bool
    {
        return $user->can('delete organizers') ||
               ($user->can('delete own organizers') && $organizer->created_by === $user->id);
    }

    /**
     * Determine whether the user can verify the organizer.
     */
    public function verify(User $user, Organizer $organizer): bool
    {
        return $user->can('verify organizers');
    }

    /**
     * Determine whether the user can toggle organizer status.
     */
    public function toggleStatus(User $user, Organizer $organizer): bool
    {
        return $user->can('manage organizer status') ||
               ($user->can('manage own organizer status') && $organizer->created_by === $user->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Organizer $organizer): bool
    {
        return $user->can('delete organizers');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Organizer $organizer): bool
    {
        return $user->can('delete organizers');
    }
}
