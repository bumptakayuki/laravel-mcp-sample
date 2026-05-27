<?php

namespace App\Http\Controllers\Ats;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Job;
use App\Services\Ats\PipelineSummaryService;
use Illuminate\View\View;

class AtsDashboardController extends Controller
{
    public function __construct(
        protected PipelineSummaryService $pipelineSummary,
    ) {}

    public function __invoke(): View
    {
        $summaries = $this->pipelineSummary->summarize(null);
        $stageTotals = array_fill_keys(Application::STAGES, 0);
        foreach ($summaries as $row) {
            foreach ($row['stages'] as $stage => $c) {
                $stageTotals[$stage] += $c;
            }
        }

        $candidateStatusLabels = [
            'active' => 'アクティブ',
            'screening' => '書類',
            'interviewing' => '面接中',
            'offer' => '内定',
            'rejected' => '見送り',
            'hired' => '採用',
        ];
        $candidateStatusCounts = [];
        foreach (Candidate::STATUSES as $status) {
            $candidateStatusCounts[$status] = Candidate::query()->where('status', $status)->count();
        }

        return view('ats.dashboard', [
            'candidateCount' => Candidate::query()->count(),
            'jobCount' => Job::query()->count(),
            'applicationCount' => Application::query()->count(),
            'stageTotals' => $stageTotals,
            'candidateStatusLabels' => $candidateStatusLabels,
            'candidateStatusCounts' => $candidateStatusCounts,
        ]);
    }
}
