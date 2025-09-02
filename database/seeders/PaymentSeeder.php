<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds the payments table with popular payment methods available in Myanmar.
     * This includes mobile banking, digital wallets, traditional payment methods,
     * and international payment options.
     */
    public function run(): void
    {
        $paymentMethods = [
            // Traditional Payment Methods
            [
                'name' => 'Cash Payment',
                'code' => 'cash',
                'status' => 'active',
            ],
            [
                'name' => 'Bank Transfer',
                'code' => 'bank_transfer',
                'status' => 'active',
            ],

            // Major Myanmar Mobile Banking & Digital Wallets
            [
                'name' => 'KBZ Pay',
                'code' => 'kbz_pay',
                'status' => 'active',
            ],
            [
                'name' => 'CB Pay',
                'code' => 'cb_pay',
                'status' => 'active',
            ],
            [
                'name' => 'AYA Pay',
                'code' => 'aya_pay',
                'status' => 'active',
            ],
            [
                'name' => 'Wave Pay',
                'code' => 'wave_pay',
                'status' => 'active',
            ],
            [
                'name' => 'UAB Pay',
                'code' => 'uab_pay',
                'status' => 'active',
            ],
            [
                'name' => 'MAB Pay',
                'code' => 'mab_pay',
                'status' => 'active',
            ],
            [
                'name' => 'Yoma Pay',
                'code' => 'yoma_pay',
                'status' => 'active',
            ],
            [
                'name' => 'AGD Pay',
                'code' => 'agd_pay',
                'status' => 'active',
            ],

            // Card Payment Systems
            [
                'name' => 'MPU Card',
                'code' => 'mpu',
                'status' => 'active',
            ],

            // Regional Digital Wallets
            [
                'name' => 'OK Dollar',
                'code' => 'ok_dollar',
                'status' => 'active',
            ],
            [
                'name' => 'ONEPay',
                'code' => 'one_pay',
                'status' => 'active',
            ],
            [
                'name' => 'True Money',
                'code' => 'true_money',
                'status' => 'active',
            ],

            // International Payment Options (inactive by default)
            [
                'name' => 'Credit/Debit Card',
                'code' => 'credit_card',
                'status' => 'inactive',
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'status' => 'inactive',
            ],
            [
                'name' => 'Stripe',
                'code' => 'stripe',
                'status' => 'inactive',
            ],
        ];

        foreach ($paymentMethods as $method) {
            Payment::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }

        $this->command->info('Payment methods seeded successfully! Total: ' . count($paymentMethods) . ' payment methods.');
    }
}
