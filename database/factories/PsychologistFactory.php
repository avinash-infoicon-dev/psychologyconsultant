<?php

namespace Database\Factories;

use App\Models\Psychologist;
use App\Models\User;
use App\Support\Locales;
use Illuminate\Database\Eloquent\Factories\Factory;

class PsychologistFactory extends Factory
{
    protected $model = Psychologist::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->afterCreating(
                fn (User $user) => $user->assignRbacRole('psychologist')
            ),
            'license_number' => strtoupper(fake()->bothify('PSY-####??')),
            'specialization' => fake()->randomElement([
                'Cognitive Behavioral Therapy', 'Anxiety & Stress', 'Trauma & PTSD',
                'Relationship Counseling', 'Adolescent Psychology',
            ]),
            'bio' => fake()->paragraph(),
            'years_experience' => fake()->numberBetween(1, 25),
            'is_verified' => true,
            'verification_status' => Psychologist::VERIFICATION_VERIFIED,
            'is_accepting_patients' => true,
            'max_active_patients' => 40,
            'languages' => fake()->randomElements(Locales::codes(), min(2, count(Locales::codes()))),
        ];
    }
}
