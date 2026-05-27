<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'department' => fake()->randomElement(['Product Development', 'Engineering', 'People']),
            'required_skills' => ['PHP', 'Teamwork'],
            'description' => fake()->paragraph(),
        ];
    }
}
