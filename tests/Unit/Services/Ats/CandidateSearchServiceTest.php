<?php

namespace Tests\Unit\Services\Ats;

use App\Models\Candidate;
use App\Services\Ats\CandidateSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateSearchServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_finds_by_skill_and_excludes_email(): void
    {
        Candidate::factory()->create([
            'skills' => ['PHP', 'Laravel', 'Vue.js'],
            'status' => 'screening',
            'current_position' => 'Backend Engineer',
        ]);
        Candidate::factory()->create([
            'skills' => ['Go'],
            'status' => 'active',
        ]);

        $svc = new CandidateSearchService;
        $rows = $svc->search(['skill' => 'Laravel']);

        $this->assertCount(1, $rows);
        $this->assertArrayNotHasKey('email', $rows[0]);
        $this->assertSame('screening', $rows[0]['status']);
    }

    public function test_search_respects_max_ten(): void
    {
        Candidate::factory()->count(15)->create([
            'skills' => ['Laravel'],
            'status' => 'active',
        ]);

        $svc = new CandidateSearchService;
        $rows = $svc->search(['skill' => 'Laravel']);

        $this->assertCount(10, $rows);
    }

    public function test_invalid_status_filter_is_ignored(): void
    {
        Candidate::factory()->create(['status' => 'screening', 'skills' => ['X']]);

        $svc = new CandidateSearchService;
        $rows = $svc->search(['status' => 'not-a-real-status']);

        $this->assertCount(1, $rows);
    }
}
