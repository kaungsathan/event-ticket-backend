<?php

namespace App\Domains\Orders\Services;

use App\Models\Event;

class PricingService
{
    private const TAX_RATE = 0.08; // 8%
    private const SERVICE_FEE_RATE = 0.03; // 3%

    /**
     * Calculate pricing for an order.
     */
    public function calculateOrderPricing(Event $event, int $quantity): array
    {
        $unitPrice = $event->price ?? 0;
        $subtotal = $quantity * $unitPrice;
        $taxAmount = $subtotal * self::TAX_RATE;
        $serviceFee = $subtotal * self::SERVICE_FEE_RATE;
        $totalAmount = $subtotal + $taxAmount + $serviceFee;

        return [
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'service_fee' => $serviceFee,
            'total_amount' => $totalAmount,
            'tax_rate' => self::TAX_RATE,
            'service_fee_rate' => self::SERVICE_FEE_RATE,
        ];
    }

    /**
     * Calculate refund amount.
     */
    public function calculateRefundAmount(float $originalAmount, float $refundPercentage = 1.0): float
    {
        return $originalAmount * $refundPercentage;
    }

    /**
     * Get pricing breakdown for display.
     */
    public function getPricingBreakdown(array $pricing): array
    {
        return [
            'subtotal' => '$' . number_format($pricing['subtotal'], 2),
            'tax' => '$' . number_format($pricing['tax_amount'], 2) . ' (' . ($pricing['tax_rate'] * 100) . '%)',
            'service_fee' => '$' . number_format($pricing['service_fee'], 2) . ' (' . ($pricing['service_fee_rate'] * 100) . '%)',
            'total' => '$' . number_format($pricing['total_amount'], 2),
        ];
    }
}
