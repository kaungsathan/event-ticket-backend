<?php

namespace Database\Seeders;

use App\Models\Organizer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds the organizers table with sample event organizers commonly found in Myanmar.
     * These organizers represent various types of event management companies and individuals.
     */
    public function run(): void
    {
        // Get a user to assign as creator (assuming UserSeeder has been run)
        $user = User::first();
        $createdBy = $user ? $user->id : 1;

        $organizers = [
            // Major Event Management Companies
            [
                'company_name' => 'Myanmar Event Solutions',
                'description' => 'Leading event management company specializing in corporate events, conferences, and exhibitions across Myanmar.',
                'email' => 'info@myanmareventsolutions.com',
                'company_phone' => '+95 1 234 5678',
                'website' => 'https://myanmareventsolutions.com',
                'address' => '123 Bogyoke Aung San Road, Yangon, Myanmar',
                'logo_url' => null,
                'date' => now(),
                'status' => 'active',
                'created_by' => $createdBy,
            ],
            [
                'company_name' => 'Golden Events Myanmar',
                'description' => 'Premium event planning and management services for weddings, corporate functions, and cultural celebrations.',
                'email' => 'contact@goldeneventsmyanmar.com',
                'company_phone' => '+95 1 345 6789',
                'website' => 'https://goldeneventsmyanmar.com',
                'address' => '456 Strand Road, Yangon, Myanmar',
                'logo_url' => null,
                'date' => now(),
                'status' => 'active',
                'created_by' => $createdBy,
            ],
            [
                'company_name' => 'Mandalay Event Masters',
                'description' => 'Professional event management services in Mandalay region, specializing in cultural and traditional events.',
                'email' => 'hello@mandalayeventmasters.com',
                'company_phone' => '+95 2 456 7890',
                'website' => 'https://mandalayeventmasters.com',
                'address' => '789 78th Street, Mandalay, Myanmar',
                'logo_url' => null,
                'date' => now(),
                'status' => 'active',
                'created_by' => $createdBy,
            ],

            // Cultural & Traditional Event Organizers
            [
                'company_name' => 'Myanmar Cultural Heritage Events',
                'description' => 'Dedicated to preserving and promoting Myanmar\'s rich cultural heritage through traditional events and festivals.',
                'email' => 'culture@myanmarculturalheritage.com',
                'company_phone' => '+95 1 567 8901',
                'website' => 'https://myanmarculturalheritage.com',
                'address' => '321 Anawrahta Road, Yangon, Myanmar',
                'logo_url' => null,
                'date' => now(),
                'status' => 'active',
                'created_by' => $createdBy,
            ],
            [
                'company_name' => 'Bagan Traditional Events',
                'description' => 'Specializing in traditional ceremonies and cultural events in the historic Bagan region.',
                'email' => 'info@bagantraditional.com',
                'company_phone' => '+95 61 234 5678',
                'website' => 'https://bagantraditional.com',
                'address' => '456 Bagan-Nyaung U Road, Bagan, Myanmar',
                'logo_url' => null,
                'date' => now(),
                'status' => 'active',
                'created_by' => $createdBy,
            ],

            // Business & Corporate Event Organizers
            [
                'company_name' => 'Yangon Business Events',
                'description' => 'Professional business event management including conferences, seminars, and corporate meetings.',
                'email' => 'business@yangonevents.com',
                'company_phone' => '+95 1 678 9012',
                'website' => 'https://yangonevents.com',
                'address' => '789 Sule Pagoda Road, Yangon, Myanmar',
                'logo_url' => null,
                'date' => now(),
                'status' => 'active',
                'created_by' => $createdBy,
            ],
            // [
            //     'company_name' => 'Myanmar Tech Events',
            //     'description' => 'Leading technology event organizer for conferences, workshops, and innovation meetups.',
            //     'email' => 'tech@myanmartech.com',
            //     'company_phone' => '+95 1 789 0123',
            //     'website' => 'https://myanmartech.com',
            //     'address' => '123 Hledan Road, Yangon, Myanmar',
            //     'logo_url' => null,
            //     'date' => now(),
            //     'status' => 'active',
            //     'created_by' => $createdBy,
            // ],

            // Entertainment & Music Event Organizers
            [
                'company_name' => 'Myanmar Music Events',
                'description' => 'Organizing live music concerts, festivals, and entertainment events across Myanmar.',
                'email' => 'music@myanmarmusic.com',
                'company_phone' => '+95 1 890 1234',
                'website' => 'https://myanmarmusic.com',
                'address' => '456 Inya Road, Yangon, Myanmar',
                'logo_url' => null,
                'date' => now(),
                'status' => 'active',
                'created_by' => $createdBy,
            ],
            // [
            //     'company_name' => 'Wave Entertainment',
            //     'description' => 'Modern entertainment company specializing in DJ parties, nightlife events, and youth culture events.',
            //     'email' => 'wave@waveentertainment.com',
            //     'company_phone' => '+95 1 901 2345',
            //     'website' => 'https://waveentertainment.com',
            //     'address' => '789 Pyay Road, Yangon, Myanmar',
            //     'logo_url' => null,
            //     'date' => now(),
            //     'status' => 'active',
            //     'created_by' => $createdBy,
            // ],

            // Sports & Fitness Event Organizers
            // [
            //     'company_name' => 'Myanmar Sports Events',
            //     'description' => 'Organizing sports tournaments, fitness events, and athletic competitions throughout Myanmar.',
            //     'email' => 'sports@myanmarsports.com',
            //     'company_phone' => '+95 1 012 3456',
            //     'website' => 'https://myanmarsports.com',
            //     'address' => '123 Thiri Mingalar Road, Yangon, Myanmar',
            //     'logo_url' => null,
            //     'date' => now(),
            //     'status' => 'active',
            //     'created_by' => $createdBy,
            // ],
            // [
            //     'company_name' => 'Fitness Myanmar Events',
            //     'description' => 'Specializing in fitness workshops, yoga retreats, and wellness events across the country.',
            //     'email' => 'fitness@fitnessmyanmar.com',
            //     'company_phone' => '+95 1 123 4567',
            //     'website' => 'https://fitnessmyanmar.com',
            //     'address' => '456 University Avenue, Yangon, Myanmar',
            //     'logo_url' => null,
            //     'date' => now(),
            //     'status' => 'active',
            //     'created_by' => $createdBy,
            // ],

            // Food & Culinary Event Organizers
            // [
            //     'company_name' => 'Myanmar Food Events',
            //     'description' => 'Organizing food festivals, cooking classes, and culinary events showcasing Myanmar cuisine.',
            //     'email' => 'food@myanmarfood.com',
            //     'company_phone' => '+95 1 234 5678',
            //     'website' => 'https://myanmarfood.com',
            //     'address' => '789 Chinatown Road, Yangon, Myanmar',
            //     'logo_url' => null,
            //     'date' => now(),
            //     'status' => 'active',
            //     'created_by' => $createdBy,
            // ],
            // [
            //     'company_name' => 'Culinary Myanmar',
            //     'description' => 'Professional culinary event management for restaurants, food festivals, and cooking workshops.',
            //     'email' => 'culinary@culinarymyanmar.com',
            //     'company_phone' => '+95 1 345 6789',
            //     'website' => 'https://culinarymyanmar.com',
            //     'address' => '123 Dagon Road, Yangon, Myanmar',
            //     'logo_url' => null,
            //     'date' => now(),
            //     'status' => 'active',
            //     'created_by' => $createdBy,
            // ],

            // Art & Creative Event Organizers
            // [
            //     'company_name' => 'Myanmar Art Events',
            //     'description' => 'Organizing art exhibitions, creative workshops, and cultural art events throughout Myanmar.',
            //     'email' => 'art@myanmarart.com',
            //     'company_phone' => '+95 1 456 7890',
            //     'website' => 'https://myanmarart.com',
            //     'address' => '456 Pansodan Road, Yangon, Myanmar',
            //     'logo_url' => null,
            //     'date' => now(),
            //     'status' => 'active',
            //     'created_by' => $createdBy,
            // ],
            // [
            //     'company_name' => 'Creative Myanmar',
            //     'description' => 'Innovative event company focusing on creative workshops, design events, and artistic collaborations.',
            //     'email' => 'creative@creativemyanmar.com',
            //     'company_phone' => '+95 1 567 8901',
            //     'website' => 'https://creativemyanmar.com',
            //     'address' => '789 Merchant Road, Yangon, Myanmar',
            //     'logo_url' => null,
            //     'date' => now(),
            //     'status' => 'active',
            //     'created_by' => $createdBy,
            // ],

            // Educational Event Organizers
            // [
            //     'company_name' => 'Myanmar Education Events',
            //     'description' => 'Organizing educational workshops, academic conferences, and learning events across Myanmar.',
            //     'email' => 'education@myanmareducation.com',
            //     'company_phone' => '+95 1 678 9012',
            //     'website' => 'https://myanmareducation.com',
            //     'address' => '123 University Road, Yangon, Myanmar',
            //     'logo_url' => null,
            //     'date' => now(),
            //     'status' => 'active',
            //     'created_by' => $createdBy,
            // ],
            // [
            //     'company_name' => 'Learning Myanmar',
            //     'description' => 'Professional development and skills training event organizer for professionals and students.',
            //     'email' => 'learning@learningmyanmar.com',
            //     'company_phone' => '+95 1 789 0123',
            //     'website' => 'https://learningmyanmar.com',
            //     'address' => '456 School Road, Yangon, Myanmar',
            //     'logo_url' => null,
            //     'date' => now(),
            //     'status' => 'active',
            //     'created_by' => $createdBy,
            // ],

            // Regional Event Organizers
            // [
            //     'company_name' => 'Naypyidaw Events',
            //     'description' => 'Event management services in the capital city, specializing in government and official events.',
            //     'email' => 'naypyidaw@naypyidawevents.com',
            //     'company_phone' => '+95 67 890 1234',
            //     'website' => 'https://naypyidawevents.com',
            //     'address' => '123 Zayar Thiri Road, Naypyidaw, Myanmar',
            //     'logo_url' => null,
            //     'date' => now(),
            //     'status' => 'active',
            //     'created_by' => $createdBy,
            // ],
            // [
            //     'company_name' => 'Inle Lake Events',
            //     'description' => 'Unique event experiences in the scenic Inle Lake region, perfect for destination events.',
            //     'email' => 'inle@inlelakeevents.com',
            //     'company_phone' => '+95 81 234 5678',
            //     'website' => 'https://inlelakeevents.com',
            //     'address' => '456 Inle Lake Road, Nyaung Shwe, Myanmar',
            //     'logo_url' => null,
            //     'date' => now(),
            //     'status' => 'active',
            //     'created_by' => $createdBy,
            // ],

            // International Event Organizers
            // [
            //     'company_name' => 'Global Events Myanmar',
            //     'description' => 'International event management company with local expertise in Myanmar markets.',
            //     'email' => 'global@globaleventsmyanmar.com',
            //     'company_phone' => '+95 1 890 1234',
            //     'website' => 'https://globaleventsmyanmar.com',
            //     'address' => '789 International Road, Yangon, Myanmar',
            //     'logo_url' => null,
            //     'date' => now(),
            //     'status' => 'active',
            //     'created_by' => $createdBy,
            // ],
            // [
            //     'company_name' => 'ASEAN Events Myanmar',
            //     'description' => 'Regional event organizer specializing in ASEAN-focused events and international conferences.',
            //     'email' => 'asean@aseaneventsmyanmar.com',
            //     'company_phone' => '+95 1 901 2345',
            //     'website' => 'https://aseaneventsmyanmar.com',
            //     'address' => '123 ASEAN Road, Yangon, Myanmar',
            //     'logo_url' => null,
            //     'date' => now(),
            //     'status' => 'active',
            //     'created_by' => $createdBy,
            // ],
        ];

        foreach ($organizers as $organizer) {
            Organizer::updateOrCreate(
                ['company_name' => $organizer['company_name']],
                $organizer
            );
        }

        $this->command->info('Organizers seeded successfully! Total: ' . count($organizers) . ' organizers.');
    }
}
