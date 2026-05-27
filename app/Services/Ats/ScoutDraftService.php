<?php

namespace App\Services\Ats;

use App\Models\Candidate;
use App\Models\Job;

/**
 * スカウト文の下書き（テンプレート）。実送信は行わない。
 *
 * - email 等の PII は返さない。
 * - LLM API は呼ばない（LT デモの再現性のため）。
 */
class ScoutDraftService
{
    public function draft(int $candidateId, int $jobId): array
    {
        $candidate = Candidate::query()->findOrFail($candidateId);
        $job = Job::query()->findOrFail($jobId);

        $skills = array_slice($candidate->skills ?? [], 0, 5);
        $skillText = implode('、', $skills);

        $draft = <<<TXT
【下書き・未送信】{$candidate->name} 様

{$job->title}（{$job->department}）のポジションについてご連絡です。
{$skillText} などのご経歴に注目しており、ぜひ一度カジュアルにお話しできればと思います。

※ 本メッセージは ATS デモのテンプレート下書きであり、送信はされていません。
TXT;

        return [
            'candidate' => [
                'id' => $candidate->id,
                'name' => $candidate->name,
                'current_position' => $candidate->current_position,
                'skills' => $skills,
            ],
            'job' => [
                'id' => $job->id,
                'title' => $job->title,
                'department' => $job->department,
            ],
            'draft_message' => trim($draft),
        ];
    }
}
