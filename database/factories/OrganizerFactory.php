<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organizer>
 */
class OrganizerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'description' => fake()->paragraph(2),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'address' => fake()->address(),
            'logo_url' => fake()->imageUrl(200, 200, 'business'),
            'is_verified' => fake()->boolean(70), // 70% chance of being verified
            'is_active' => fake()->boolean(90), // 90% chance of being active
            'created_by' => \App\Models\User::factory(),
        ];
    }
}
