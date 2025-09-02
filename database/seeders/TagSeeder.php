<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds the tags table with popular event tags commonly used in Myanmar.
     * These tags help categorize and search for events effectively.
     */
    public function run(): void
    {
        $tags = [
            // Music & Entertainment
            ['name' => 'Live Music', 'status' => 'active'],
            ['name' => 'Concert', 'status' => 'active'],
            ['name' => 'Karaoke', 'status' => 'active'],
            ['name' => 'DJ Party', 'status' => 'active'],
            ['name' => 'Traditional Music', 'status' => 'active'],
            ['name' => 'Rock Music', 'status' => 'active'],
            ['name' => 'Pop Music', 'status' => 'active'],
            ['name' => 'Jazz', 'status' => 'active'],
        ];

        foreach ($tags as $tag) {
            Tag::updateOrCreate(
                ['name' => $tag['name']],
                $tag
            );
        }

        $this->command->info('Tags seeded successfully! Total: ' . count($tags) . ' tags.');
    }
}
