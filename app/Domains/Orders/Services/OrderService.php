<?php

namespace App\Domains\Orders\Services;

use App\Models\Order;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class OrderService
{
    /**
     * Create a new order.
     */
    public function createOrder(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $event = Event::findOrFail($data['event_id']);

            // Calculate pricing using pricing service
            $pricing = app(PricingService::class)->calculateOrderPricing(
                $event,
                $data['quantity']
            );

            return Order::create([
                'user_id' => $user->id,
                'event_id' => $data['event_id'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'quantity' => $data['quantity'],
                'unit_price' => $pricing['unit_price'],
                'subtotal' => $pricing['subtotal'],
                'tax_amount' => $pricing['tax_amount'],
                'service_fee' => $pricing['service_fee'],
                'total_amount' => $pricing['total_amount'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    /**
     * Get paginated orders with filtering and sorting.
     */
    public function getOrders(User $user, array $params = []): LengthAwarePaginator
    {
        $query = Order::query();

        // Non-staff users can only see their own orders
        if (!$user->can('view orders')) {
            $query->where('user_id', $user->id);
        }

        return QueryBuilder::for($query)
            ->allowedFilters([
                'status',
                'payment_status',
                'customer_email',
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('event_id'),
            ])
            ->allowedSorts(['created_at', 'total_amount', 'order_number'])
            ->allowedIncludes(['user', 'event'])
            ->defaultSort('-created_at')
            ->paginate($params['per_page'] ?? 15);
    }

    /**
     * Get orders for a user.
     */
    public function getUserOrders(User $user, array $filters = []): Collection
    {
        $query = $user->orders()->with(['event']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Update order details.
     */
    public function updateOrder(Order $order, array $data): Order
    {
        $order->update(array_filter([
            'customer_name' => $data['customer_name'] ?? null,
            'customer_email' => $data['customer_email'] ?? null,
            'customer_phone' => $data['customer_phone'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? null,
        ]));

        return $order->fresh();
    }

    /**
     * Confirm an order.
     */
    public function confirmOrder(Order $order): Order
    {
        if ($order->status !== 'pending') {
            throw new \InvalidArgumentException('Only pending orders can be confirmed.');
        }

        $order->confirm();

        // Log activity for order confirmation - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($order)
        //     ->causedBy(auth()->user())
        //     ->log('Order confirmed');

        return $order;
    }

    /**
     * Cancel an order.
     */
    public function cancelOrder(Order $order, string $reason = null): Order
    {
        if (!$order->canBeCancelled()) {
            throw new \InvalidArgumentException('This order cannot be cancelled.');
        }

        $order->cancel($reason);

        // Log activity for order cancellation - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($order)
        //     ->causedBy(auth()->user())
        //     ->withProperties(['reason' => $reason])
        //     ->log('Order cancelled');

        return $order;
    }

    /**
     * Get order statistics.
     */
    public function getOrderStatistics(array $filters = []): array
    {
        $query = Order::query();

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return [
            'total_orders' => $query->count(),
            'total_revenue' => $query->where('payment_status', 'paid')->sum('total_amount'),
            'pending_orders' => $query->where('status', 'pending')->count(),
            'confirmed_orders' => $query->where('status', 'confirmed')->count(),
            'cancelled_orders' => $query->where('status', 'cancelled')->count(),
            'paid_orders' => $query->where('payment_status', 'paid')->count(),
            'refunded_amount' => $query->sum('refunded_amount'),
        ];
    }

    /**
     * Process refund for an order.
     */
    public function processRefund(Order $order, float $amount, string $reason, User $user): Order
    {
        if (!$order->canBeRefunded()) {
            throw new \InvalidArgumentException('This order cannot be refunded.');
        }

        $order->processRefund($amount, $reason);

        // Log activity - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($order)
        //     ->causedBy($user)
        //     ->withProperties([
        //         'amount' => $amount,
        //         'reason' => $reason,
        //     ])
        //     ->log('Order refund processed');

        return $order;
    }

    /**
     * Get a single order with relationships.
     */
    public function getOrderWithRelations(Order $order): Order
    {
        return $order->load(['user', 'event']);
    }

    /**
     * Delete an order.
     */
    public function deleteOrder(Order $order, User $user): void
    {
        // Log activity before deletion - TEMPORARILY DISABLED
        // activity()
        //     ->performedOn($order)
        //     ->causedBy($user)
        //     ->log('Order deleted');

        $order->delete();
    }
}
