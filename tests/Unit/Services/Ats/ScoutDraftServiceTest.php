<?php

namespace Tests\Unit\Services\Ats;

use App\Models\Candidate;
use App\Models\Job;
use App\Services\Ats\ScoutDraftService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoutDraftServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_returns_template_and_skips_email(): void
    {
        $c = Candidate::factory()->create([
            'name' => '山田 太郎',
            'skills' => ['PHP', 'Laravel'],
            'current_position' => 'Backend Engineer',
        ]);
        $j = Job::factory()->create(['title' => 'バックエンドエンジニア', 'department' => 'Product Development']);

        $svc = new ScoutDraftService;
        $payload = $svc->draft($c->id, $j->id);

        $this->assertStringContainsString('【下書き・未送信】', $payload['draft_message']);
        $this->assertArrayNotHasKey('email', $payload['candidate']);
        $this->assertSame('バックエンドエンジニア', $payload['job']['title']);
    }

    public function test_draft_throws_when_candidate_missing(): void
    {
        $j = Job::factory()->create();

        $this->expectException(ModelNotFoundException::class);

        (new ScoutDraftService)->draft(999999, $j->id);
    }

    public function test_draft_throws_when_job_missing(): void
    {
        $c = Candidate::factory()->create();

        $this->expectException(ModelNotFoundException::class);

        (new ScoutDraftService)->draft($c->id, 999999);
    }
}
