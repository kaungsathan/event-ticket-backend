<?php

namespace Database\Seeders;

use App\Models\Payment;
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
                'name' => 'Cash Payment',
                'code' => 'cash',
                'status' => 'active',
            ],
            [
                'name' => 'AYA Pay',
                'code' => 'aya_pay',
                'status' => 'active',
            ],
            [
                'name' => 'KBZ Pay',
                'code' => 'kbz_pay',
                'status' => 'active',
            ],
            [
                'name' => 'Wave Pay',
                'code' => 'wave_pay',
                'status' => 'active',
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
