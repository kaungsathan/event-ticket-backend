<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view orders');
    }

    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        // Users can view their own orders
        if ($user->id === $order->user_id) {
            return true;
        }

        // Staff can view all orders
        return $user->can('view orders');
    }

    /**
     * Determine whether the user can create orders.
     */
    public function create(User $user): bool
    {
        return $user->can('create orders');
    }

    /**
     * Determine whether the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        // Users can only update their own orders if they're still pending
        if ($user->id === $order->user_id && $order->status === 'pending') {
            return true;
        }

        // Staff can update any order
        return $user->can('edit orders');
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        // Only staff can delete orders
        return $user->can('delete orders');
    }

    /**
     * Determine whether the user can cancel the order.
     */
    public function cancel(User $user, Order $order): bool
    {
        // Users can cancel their own orders if cancellable
        if ($user->id === $order->user_id && $order->canBeCancelled()) {
            return true;
        }

        // Staff can cancel any order
        return $user->can('edit orders');
    }

    /**
     * Determine whether the user can refund the order.
     */
    public function refund(User $user, Order $order): bool
    {
        // Only staff with refund permission can process refunds
        return $user->can('refund orders') && $order->canBeRefunded();
    }

    /**
     * Determine whether the user can confirm the order.
     */
    public function confirm(User $user, Order $order): bool
    {
        // Only staff can confirm orders
        return $user->can('edit orders');
    }

    /**
     * Determine whether the user can view order payment details.
     */
    public function viewPaymentDetails(User $user, Order $order): bool
    {
        // Users can view their own payment details
        if ($user->id === $order->user_id) {
            return true;
        }

        // Staff can view all payment details
        return $user->can('view orders');
    }

    /**
     * Determine whether the user can modify payment status.
     */
    public function updatePaymentStatus(User $user, Order $order): bool
    {
        // Only staff can update payment status
        return $user->can('edit orders');
    }
}
