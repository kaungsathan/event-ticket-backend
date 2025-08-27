<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
            'status' => 'active',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a user with super admin role.
     */
    public function superAdmin(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('super-admin');
        });
    }

    /**
     * Create a user with admin role.
     */
    public function admin(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('admin');
        });
    }

    /**
     * Create a user with event manager role.
     */
    public function eventManager(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('event-manager');
        });
    }

    /**
     * Create a user with customer service role.
     */
    public function customerService(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('customer-service');
        });
    }

    /**
     * Create a user with customer role.
     */
    public function customer(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('customer');
        });
    }

    /**
     * Create a user with specific role.
     */
    public function withRole(string $role): static
    {
        return $this->afterCreating(function ($user) use ($role) {
            $user->assignRole($role);
        });
    }

    /**
     * Create a user with specific permissions.
     */
    public function withPermissions(array $permissions): static
    {
        return $this->afterCreating(function ($user) use ($permissions) {
            $user->givePermissionTo($permissions);
        });
    }
}
