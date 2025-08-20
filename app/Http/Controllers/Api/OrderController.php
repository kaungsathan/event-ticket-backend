<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class OrderController extends Controller
{


    /**
     * Display a listing of orders.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = QueryBuilder::for(Order::class)
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
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        $request->validate([
            'event_id' => 'required|exists:events,id',
            'quantity' => 'required|integer|min:1|max:10',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'sometimes|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $event = Event::findOrFail($request->event_id);

            // Calculate pricing
            $quantity = $request->quantity;
            $unitPrice = $event->price ?? 0;
            $subtotal = $quantity * $unitPrice;
            $taxAmount = $subtotal * 0.08; // 8% tax
            $serviceFee = $subtotal * 0.03; // 3% service fee
            $totalAmount = $subtotal + $taxAmount + $serviceFee;

            $order = Order::create([
                'user_id' => $request->user()->id,
                'event_id' => $request->event_id,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'service_fee' => $serviceFee,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            $order->load(['user', 'event']);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => ['order' => $order],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating order: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);
        $order->load(['user', 'event']);

        return response()->json([
            'success' => true,
            'data' => ['order' => $order],
        ]);
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $request->validate([
            'customer_name' => 'sometimes|string|max:255',
            'customer_email' => 'sometimes|email|max:255',
            'customer_phone' => 'sometimes|string|max:20',
            'status' => ['sometimes', Rule::in(['pending', 'confirmed', 'cancelled'])],
        ]);

        $order->update($request->only([
            'customer_name', 'customer_email', 'customer_phone', 'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => ['order' => $order],
        ]);
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
        ]);
    }

    /**
     * Confirm the order.
     */
    public function confirm(Order $order): JsonResponse
    {
        $this->authorize('confirm', $order);
        $order->confirm();

        return response()->json([
            'success' => true,
            'message' => 'Order confirmed successfully',
            'data' => ['order' => $order],
        ]);
    }

    /**
     * Cancel the order.
     */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        $this->authorize('cancel', $order);
        $order->cancel($request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => ['order' => $order],
        ]);
    }

    /**
     * Process refund for the order.
     */
    public function refund(Request $request, Order $order): JsonResponse
    {
        $this->authorize('refund', $order);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
        ]);

        $order->processRefund($request->amount, $request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Refund processed successfully',
            'data' => ['order' => $order],
        ]);
    }
}
