<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtsJobShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_detail_renders_overview(): void
    {
        $job = Job::factory()->create([
            'title' => 'フルスタックエンジニア',
            'department' => 'プロダクト本部',
            'description' => "チーム開発をリードしていただきます。\nGo / TypeScript 経験歓迎。",
        ]);

        $response = $this->get(route('ats.jobs.show', $job));

        $response->assertOk();
        $response->assertSee('フルスタックエンジニア', false);
        $response->assertSee('プロダクト本部', false);
        $response->assertSee('求人概要', false);
        $response->assertSee('Go / TypeScript 経験歓迎。', false);
    }

    public function test_job_detail_lists_applications(): void
    {
        $job = Job::factory()->create(['title' => 'SRE']);
        $candidate = Candidate::factory()->create(['name' => '鈴木 一郎']);
        Application::factory()->create([
            'candidate_id' => $candidate->id,
            'job_id' => $job->id,
            'stage' => 'interview',
            'memo' => '一次面接済',
        ]);

        $response = $this->get(route('ats.jobs.show', $job));

        $response->assertOk();
        $response->assertSee('鈴木 一郎', false);
        $response->assertSee('面接', false);
    }

    public function test_unknown_job_returns_404(): void
    {
        $this->get(route('ats.jobs.show', ['job' => 999_999]))->assertNotFound();
    }
}
