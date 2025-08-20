<?php

namespace App\Domains\Orders\Services;

use App\Models\Order;

class PaymentService
{
    /**
     * Mark order as paid.
     */
    public function markOrderAsPaid(Order $order, string $paymentMethod, string $paymentReference = null): Order
    {
        if ($order->payment_status === 'paid') {
            throw new \InvalidArgumentException('Order is already paid.');
        }

        $order->markAsPaid($paymentMethod, $paymentReference);

        // Trigger payment completed event
        event(new \App\Domains\Orders\Events\PaymentCompleted($order));

        return $order;
    }

    /**
     * Process refund for an order.
     */
    public function processRefund(Order $order, float $amount, string $reason): Order
    {
        if (!$order->canBeRefunded()) {
            throw new \InvalidArgumentException('This order cannot be refunded.');
        }

        if ($amount > $order->getRefundableAmount()) {
            throw new \InvalidArgumentException('Refund amount exceeds refundable amount.');
        }

        $order->processRefund($amount, $reason);

        // Trigger refund processed event
        event(new \App\Domains\Orders\Events\RefundProcessed($order, $amount));

        return $order;
    }

    /**
     * Get payment methods.
     */
    public function getAvailablePaymentMethods(): array
    {
        return [
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
        ];
    }

    /**
     * Validate payment reference.
     */
    public function validatePaymentReference(string $reference, string $method): bool
    {
        // Add validation logic based on payment method
        return !empty($reference) && strlen($reference) >= 10;
    }
}
