<?php

namespace App\Domains\Orders\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Orders\Services\OrderService;
use App\Domains\Orders\Requests\StoreOrderRequest;
use App\Domains\Orders\Requests\UpdateOrderRequest;
use App\Domains\Orders\Requests\RefundOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }


    /**
     * Display a listing of orders.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderService->getOrders(auth()->user(), [
            'per_page' => $request->get('per_page', 15)
        ]);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        try {
            $order = $this->orderService->createOrder($request->user(), $request->validated());
            $order = $this->orderService->getOrderWithRelations($order);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => ['order' => $order],
            ], 201);
        } catch (\Exception $e) {
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
        $order = $this->orderService->getOrderWithRelations($order);

        return response()->json([
            'success' => true,
            'data' => ['order' => $order],
        ]);
    }

    /**
     * Update the specified order.
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $order = $this->orderService->updateOrder($order, $request->validated());

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

        $this->orderService->deleteOrder($order, auth()->user());

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

        try {
            $order = $this->orderService->confirmOrder($order);

            return response()->json([
                'success' => true,
                'message' => 'Order confirmed successfully',
                'data' => ['order' => $order],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cancel the order.
     */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        $this->authorize('cancel', $order);

        try {
            $order = $this->orderService->cancelOrder($order, $request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'data' => ['order' => $order],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Process refund for the order.
     */
        public function refund(RefundOrderRequest $request, Order $order): JsonResponse
    {
        $this->authorize('refund', $order);

        try {
            $validated = $request->validated();
            $order = $this->orderService->processRefund(
                $order,
                $validated['amount'],
                $validated['reason'],
                auth()->user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully',
                'data' => ['order' => $order],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
