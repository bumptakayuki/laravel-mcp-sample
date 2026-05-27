<?php

namespace App\Services\Ats;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Support\Facades\DB;

/**
 * 求人別パイプライン集計。MCP / 画面で同じ集計ロジックを再利用する。
 *
 * - DB 直接公開はせず、集計結果のみ返す。
 */
class PipelineSummaryService
{
    public function summarize(?int $jobId = null): array
    {
        $stages = Application::STAGES;

        $jobQuery = Job::query()->orderBy('id');
        if ($jobId !== null) {
            $jobQuery->where('id', $jobId);
        }

        $jobs = $jobQuery->get();

        $counts = Application::query()
            ->select('job_id', 'stage', DB::raw('count(*) as c'))
            ->when($jobId !== null, fn ($q) => $q->where('job_id', $jobId))
            ->groupBy('job_id', 'stage')
            ->get()
            ->groupBy('job_id');

        $result = [];
        foreach ($jobs as $job) {
            $rowStages = array_fill_keys($stages, 0);
            foreach ($counts->get($job->id, collect()) as $row) {
                if (in_array($row->stage, $stages, true)) {
                    $rowStages[$row->stage] = (int) $row->c;
                }
            }
            $result[] = [
                'job_id' => $job->id,
                'job_title' => $job->title,
                'stages' => $rowStages,
            ];
        }

        return $result;
    }
}
