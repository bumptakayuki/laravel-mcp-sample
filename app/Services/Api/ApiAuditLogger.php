<?php

namespace App\Services\Api;

use App\Models\ApiAuditLog;
use App\Models\User;

/**
 * 外部 API 呼び出しの監査。本番では保持期間・PII マスキングを別途検討。
 */
class ApiAuditLogger
{
    public function log(string $action, array $input, string $outputSummary, ?User $user = null): void
    {
        $summary = mb_substr($outputSummary, 0, 2000);

        ApiAuditLog::query()->create([
            'action' => $action,
            'input' => $input,
            'output_summary' => $summary,
            'executed_by' => $user !== null ? 'user:'.$user->id : 'unknown',
        ]);
    }
}
