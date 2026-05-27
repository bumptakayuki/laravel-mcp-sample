<?php

namespace App\Http\Controllers\Ats;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\Ats\PipelineSummaryService;
use Illuminate\View\View;

class AtsPipelineController extends Controller
{
    public function __construct(
        protected PipelineSummaryService $pipelineSummary,
    ) {}

    public function index(): View
    {
        $summaries = $this->pipelineSummary->summarize(null);

        $stageTotals = array_fill_keys(Application::STAGES, 0);
        foreach ($summaries as $row) {
            foreach ($row['stages'] as $stage => $c) {
                $stageTotals[$stage] += $c;
            }
        }

        return view('ats.pipeline', compact('summaries', 'stageTotals'));
    }
}
