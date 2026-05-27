<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtsCandidateShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_detail_renders_profile(): void
    {
        $candidate = Candidate::factory()->create([
            'name' => '山田 太郎',
            'email' => 'yamada@example.test',
        ]);

        $response = $this->get(route('ats.candidates.show', $candidate));

        $response->assertOk();
        $response->assertSee('山田 太郎', false);
        $response->assertSee('yamada@example.test', false);
        $response->assertSee('プロフィール', false);
    }

    public function test_candidate_detail_lists_applications(): void
    {
        $job = Job::factory()->create(['title' => 'バックエンドエンジニア']);
        $candidate = Candidate::factory()->create(['name' => '佐藤 花子']);
        Application::factory()->create([
            'candidate_id' => $candidate->id,
            'job_id' => $job->id,
            'stage' => 'screening',
            'memo' => '書類通過',
        ]);

        $response = $this->get(route('ats.candidates.show', $candidate));

        $response->assertOk();
        $response->assertSee('バックエンドエンジニア', false);
        $response->assertSee('書類選考', false);
    }

    public function test_unknown_candidate_returns_404(): void
    {
        $this->get(route('ats.candidates.show', ['candidate' => 999_999]))->assertNotFound();
    }
}
