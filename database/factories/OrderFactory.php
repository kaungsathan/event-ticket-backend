<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomFloat(2, 25, 500);
        $subtotal = $quantity * $unitPrice;
        $taxRate = 0.08; // 8% tax
        $taxAmount = $subtotal * $taxRate;
        $serviceFee = $subtotal * 0.03; // 3% service fee
        $totalAmount = $subtotal + $taxAmount + $serviceFee;

        return [
            'order_number' => 'ORD-' . strtoupper(fake()->unique()->bothify('??######')),
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->optional()->phoneNumber(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'service_fee' => $serviceFee,
            'total_amount' => $totalAmount,
            'payment_status' => fake()->randomElement(['pending', 'paid', 'failed']),
            'payment_method' => fake()->optional()->randomElement(['credit_card', 'paypal', 'stripe', 'bank_transfer']),
            'payment_reference' => fake()->optional()->uuid(),
            'paid_at' => fake()->optional()->dateTimeBetween('-30 days', 'now'),
            'status' => fake()->randomElement(['pending', 'confirmed', 'cancelled']),
            'fulfillment_status' => fake()->randomElement(['unfulfilled', 'fulfilled']),
            'notes' => fake()->optional()->text(100),
            'metadata' => fake()->optional()->randomElements([
                'gateway' => 'stripe',
                'transaction_id' => fake()->uuid(),
                'customer_ip' => fake()->ipv4(),
            ]),
            'refunded_amount' => 0,
            'refunded_at' => null,
            'refund_reason' => null,
            'confirmed_at' => fake()->optional()->dateTimeBetween('-20 days', 'now'),
            'cancelled_at' => null,
        ];
    }

    /**
     * Create a pending order.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
            'paid_at' => null,
            'confirmed_at' => null,
            'cancelled_at' => null,
        ]);
    }

    /**
     * Create a paid order.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'stripe']),
            'payment_reference' => fake()->uuid(),
            'paid_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Create a confirmed order.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'stripe']),
            'payment_reference' => fake()->uuid(),
            'paid_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'confirmed_at' => fake()->dateTimeBetween('-25 days', 'now'),
        ]);
    }

    /**
     * Create a cancelled order.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => fake()->dateTimeBetween('-20 days', 'now'),
            'notes' => 'Order cancelled by customer',
        ]);
    }

    /**
     * Create a refunded order.
     */
    public function refunded(): static
    {
        return $this->state(function (array $attributes) {
            $totalAmount = $attributes['total_amount'] ?? 100;
            return [
                'payment_status' => 'refunded',
                'refunded_amount' => $totalAmount,
                'refunded_at' => fake()->dateTimeBetween('-15 days', 'now'),
                'refund_reason' => fake()->randomElement([
                    'Customer requested refund',
                    'Event cancelled',
                    'Duplicate payment',
                    'Processing error'
                ]),
            ];
        });
    }

    /**
     * Create a partially refunded order.
     */
    public function partiallyRefunded(): static
    {
        return $this->state(function (array $attributes) {
            $totalAmount = $attributes['total_amount'] ?? 100;
            $refundedAmount = $totalAmount * fake()->randomFloat(2, 0.2, 0.8); // 20-80% refunded

            return [
                'payment_status' => 'partially_refunded',
                'refunded_amount' => $refundedAmount,
                'refunded_at' => fake()->dateTimeBetween('-15 days', 'now'),
                'refund_reason' => 'Partial refund processed',
            ];
        });
    }

    /**
     * Create an order for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone ?? fake()->optional()->phoneNumber(),
        ]);
    }

    /**
     * Create an order for a specific event.
     */
    public function forEvent(Event $event): static
    {
        return $this->state(fn (array $attributes) => [
            'event_id' => $event->id,
        ]);
    }

    /**
     * Create a high-value order.
     */
    public function highValue(): static
    {
        return $this->state(function (array $attributes) {
            $quantity = fake()->numberBetween(3, 10);
            $unitPrice = fake()->randomFloat(2, 200, 1000);
            $subtotal = $quantity * $unitPrice;
            $taxAmount = $subtotal * 0.08;
            $serviceFee = $subtotal * 0.03;
            $totalAmount = $subtotal + $taxAmount + $serviceFee;

            return [
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'service_fee' => $serviceFee,
                'total_amount' => $totalAmount,
            ];
        });
    }

    /**
     * Create recent orders.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-7 days', 'now'),
            'updated_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
