<?php

namespace Tests\Feature\Api;

use App\Models\ApiAuditLog;
use App\Models\Candidate;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExternalSoRApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function authHeaders(User $user): array
    {
        return [
            'Authorization' => 'Bearer '.$user->createToken('test')->plainTextToken,
            'Accept' => 'application/json',
        ];
    }

    public function test_api_requires_authentication(): void
    {
        $this->getJson('/api/v1/candidates')->assertUnauthorized();
    }

    public function test_candidates_search_returns_data_shape_and_audits(): void
    {
        $user = User::factory()->create();
        Candidate::factory()->create([
            'skills' => ['Laravel'],
            'status' => 'screening',
        ]);

        $response = $this->getJson('/api/v1/candidates?skill=Laravel', $this->authHeaders($user));

        $response->assertOk();
        $response->assertJsonStructure(['data', 'meta']);
        $response->assertJsonPath('meta.count', 1);
        $response->assertJsonMissingPath('data.0.email');

        $this->assertSame(1, ApiAuditLog::query()->count());
        $this->assertSame('candidates.index', ApiAuditLog::query()->firstOrFail()->action);
    }

    public function test_pipeline_summary(): void
    {
        $user = User::factory()->create();
        Job::factory()->create();

        $response = $this->getJson('/api/v1/pipeline/summary', $this->authHeaders($user));

        $response->assertOk();
        $response->assertJsonStructure(['data', 'meta']);
        $response->assertJsonPath('meta.jobs', 1);
    }

    public function test_scout_draft_validation(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/v1/scout/drafts', [], $this->authHeaders($user))
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_scout_draft_not_found(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/v1/scout/drafts', [
            'candidate_id' => 999,
            'job_id' => 999,
        ], $this->authHeaders($user))
            ->assertNotFound()
            ->assertJsonPath('message', 'Candidate or job not found for the given ids.');
    }

    public function test_scout_draft_success(): void
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create();
        $job = Job::factory()->create();

        $response = $this->postJson('/api/v1/scout/drafts', [
            'candidate_id' => $candidate->id,
            'job_id' => $job->id,
        ], $this->authHeaders($user));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['candidate', 'job', 'draft_message'],
            'meta',
        ]);
    }
}
