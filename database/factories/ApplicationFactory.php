<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'job_id' => Job::factory(),
            'stage' => fake()->randomElement(Application::STAGES),
            'score' => fake()->optional()->numberBetween(1, 5),
            'memo' => fake()->optional()->sentence(),
        ];
    }
}
