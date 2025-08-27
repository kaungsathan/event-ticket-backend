<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Cash',
                'code' => 'cash',
                'description' => 'Cash payment at the venue or upon delivery',
                'type' => 'cash',
                'is_active' => true,
                'processing_fee_percentage' => 0,
                'processing_fee_fixed' => 0,
                'sort_order' => 1,
                'icon' => 'cash-icon',
            ],
            [
                'name' => 'KBZ Pay',
                'code' => 'kbz_pay',
                'description' => 'KBZ Bank mobile payment system',
                'type' => 'mobile_payment',
                'is_active' => true,
                'processing_fee_percentage' => 1.50,
                'processing_fee_fixed' => 0,
                'sort_order' => 2,
                'icon' => 'kbz-icon',
                'settings' => [
                    'merchant_id' => null,
                    'api_key' => null,
                    'sandbox' => true,
                ],
            ],
            [
                'name' => 'MPU',
                'code' => 'mpu',
                'description' => 'Myanmar Payment Union card payment',
                'type' => 'digital',
                'is_active' => true,
                'processing_fee_percentage' => 2.00,
                'processing_fee_fixed' => 0,
                'sort_order' => 3,
                'icon' => 'mpu-icon',
            ],
            [
                'name' => 'Wave Pay',
                'code' => 'wave_pay',
                'description' => 'Wave Money mobile payment',
                'type' => 'mobile_payment',
                'is_active' => true,
                'processing_fee_percentage' => 1.75,
                'processing_fee_fixed' => 0,
                'sort_order' => 4,
                'icon' => 'wave-icon',
            ],
            [
                'name' => 'AYA Pay',
                'code' => 'aya_pay',
                'description' => 'AYA Bank mobile payment system',
                'type' => 'mobile_payment',
                'is_active' => true,
                'processing_fee_percentage' => 1.50,
                'processing_fee_fixed' => 0,
                'sort_order' => 5,
                'icon' => 'aya-icon',
            ],
            [
                'name' => 'Bank Transfer',
                'code' => 'bank_transfer',
                'description' => 'Direct bank transfer payment',
                'type' => 'bank_transfer',
                'is_active' => true,
                'processing_fee_percentage' => 0,
                'processing_fee_fixed' => 0,
                'sort_order' => 8,
                'icon' => 'bank-icon',
            ],
            [
                'name' => 'Credit Card',
                'code' => 'credit_card',
                'description' => 'International credit card payment via Stripe',
                'type' => 'digital',
                'is_active' => false, // Disabled by default until configured
                'processing_fee_percentage' => 2.90,
                'processing_fee_fixed' => 0.30,
                'sort_order' => 9,
                'icon' => 'credit-card-icon',
                'settings' => [
                    'stripe_public_key' => null,
                    'stripe_secret_key' => null,
                    'supported_cards' => ['visa', 'mastercard', 'amex'],
                ],
            ],
        ];

        foreach ($paymentMethods as $method) {
            Payment::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }

        $this->command->info('Payment methods seeded successfully!');
    }
}
