<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds the categories table with event categories commonly used in Myanmar.
     * Event categories provide the main organizational structure for events.
     */
    public function run(): void
    {
        $categories = [
            // Entertainment & Arts
            ['name' => 'Entertainment', 'status' => 'active'],
            ['name' => 'Music', 'status' => 'active'],
            ['name' => 'Dance', 'status' => 'active'],
            ['name' => 'Theater', 'status' => 'active'],
            ['name' => 'Film & Cinema', 'status' => 'active'],
            ['name' => 'Visual Arts', 'status' => 'active'],
            ['name' => 'Comedy', 'status' => 'active'],
            ['name' => 'Magic & Illusion', 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('Event categories seeded successfully! Total: ' . count($categories) . ' categories.');
    }
}
