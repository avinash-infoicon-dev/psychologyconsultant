<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Psychologist;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->afterCreating(
                fn (User $user) => $user->assignRbacRole('patient')
            ),
            'psychologist_id' => Psychologist::factory(),
            'date_of_birth' => fake()->dateTimeBetween('-70 years', '-18 years'),
            'gender' => fake()->randomElement(['female', 'male', 'non_binary', 'prefer_not_to_say']),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'country' => fake()->randomElement(['CH', 'DE', 'FR', 'IT', 'AT']),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->e164PhoneNumber(),
        ];
    }
}
