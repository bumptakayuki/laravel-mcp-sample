<?php

namespace App\Services\Mcp;

use App\Models\McpAuditLog;

/**
 * MCP Tool 実行の監査ログ。AI からの呼び出しを DB 直操作と切り離し、許可された Tool 単位で追跡する。
 */
class McpToolAuditLogger
{
    public function log(string $toolName, array $input, string $outputSummary, ?string $executedBy = null): void
    {
        $summary = mb_substr($outputSummary, 0, 2000);

        McpAuditLog::query()->create([
            'tool_name' => $toolName,
            'input' => $input,
            'output_summary' => $summary,
            'executed_by' => $executedBy ?? 'demo-user',
        ]);
    }
}
