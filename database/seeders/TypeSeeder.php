<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds the types table with event types commonly used in Myanmar.
     * Event types represent the main classification of events.
     */
    public function run(): void
    {
        $types = [
            // Entertainment & Recreation
            ['name' => 'Concert', 'status' => 'active'],
            ['name' => 'Party', 'status' => 'active'],
            ['name' => 'Festival', 'status' => 'active'],
            ['name' => 'Show', 'status' => 'active'],
            ['name' => 'Performance', 'status' => 'active'],
            ['name' => 'Entertainment', 'status' => 'active'],
            ['name' => 'Recreation', 'status' => 'active'],
        ];

        foreach ($types as $type) {
            Type::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }

        $this->command->info('Event types seeded successfully! Total: ' . count($types) . ' types.');
    }
}
