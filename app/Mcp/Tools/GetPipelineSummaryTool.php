<?php

namespace App\Mcp\Tools;

use App\Services\Ats\PipelineSummaryService;
use App\Services\Mcp\McpToolAuditLogger;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Throwable;

#[Name('get_pipeline_summary')]
#[Description('Get recruitment pipeline summary grouped by job and application stage.')]
#[IsReadOnly]
class GetPipelineSummaryTool extends Tool
{
    public function __construct(
        protected PipelineSummaryService $pipeline,
        protected McpToolAuditLogger $audit,
    ) {}

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'job_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ]);

        try {
            $jobId = $validated['job_id'] ?? null;
            $rows = $this->pipeline->summarize($jobId);
            $this->audit->log('get_pipeline_summary', $validated, 'jobs='.count($rows));

            return Response::json($rows);
        } catch (Throwable $e) {
            $this->audit->log('get_pipeline_summary', $validated, 'error: '.$e->getMessage());

            return Response::error('get_pipeline_summary failed: '.$e->getMessage());
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'job_id' => $schema->integer()
                ->description('Optional job id to restrict summary to a single job posting.'),
        ];
    }
}
