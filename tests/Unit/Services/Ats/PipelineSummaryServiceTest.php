<?php

namespace Tests\Unit\Services\Ats;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Job;
use App\Services\Ats\PipelineSummaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PipelineSummaryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_summarize_counts_stages_per_job(): void
    {
        $job = Job::factory()->create(['title' => 'Backend']);
        $c1 = Candidate::factory()->create();
        $c2 = Candidate::factory()->create();
        Application::factory()->create(['job_id' => $job->id, 'candidate_id' => $c1->id, 'stage' => 'applied']);
        Application::factory()->create(['job_id' => $job->id, 'candidate_id' => $c2->id, 'stage' => 'applied']);
        Application::factory()->create(['job_id' => $job->id, 'candidate_id' => $c2->id, 'stage' => 'screening']);

        $svc = new PipelineSummaryService;
        $rows = $svc->summarize(null);

        $this->assertCount(1, $rows);
        $this->assertSame(2, $rows[0]['stages']['applied']);
        $this->assertSame(1, $rows[0]['stages']['screening']);
        $this->assertSame(0, $rows[0]['stages']['hired']);
    }

    public function test_summarize_filters_by_job_id(): void
    {
        $j1 = Job::factory()->create();
        $j2 = Job::factory()->create();
        $c = Candidate::factory()->create();
        Application::factory()->create(['job_id' => $j1->id, 'candidate_id' => $c->id, 'stage' => 'interview']);
        Application::factory()->create(['job_id' => $j2->id, 'candidate_id' => $c->id, 'stage' => 'hired']);

        $svc = new PipelineSummaryService;
        $rows = $svc->summarize($j2->id);

        $this->assertCount(1, $rows);
        $this->assertSame($j2->id, $rows[0]['job_id']);
        $this->assertSame(1, $rows[0]['stages']['hired']);
    }
}
