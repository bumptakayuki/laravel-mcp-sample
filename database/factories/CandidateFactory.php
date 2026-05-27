<?php

namespace Database\Factories;

use App\Models\Candidate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Candidate>
 */
class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'current_company' => fake()->company(),
            'current_position' => fake()->jobTitle(),
            'skills' => ['PHP', 'Laravel'],
            'source' => fake()->randomElement(['referral', 'linkedin', 'event', 'direct']),
            'status' => fake()->randomElement(Candidate::STATUSES),
        ];
    }
}
