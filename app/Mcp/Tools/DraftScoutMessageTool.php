<?php

namespace App\Mcp\Tools;

use App\Services\Ats\ScoutDraftService;
use App\Services\Mcp\McpToolAuditLogger;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Throwable;

/**
 * 下書き生成のみ。メール送信や Write は行わない。
 */
#[Name('draft_scout_message')]
#[Description('Create a draft scout message for a candidate and job. This tool only creates a draft and never sends messages.')]
#[IsReadOnly]
class DraftScoutMessageTool extends Tool
{
    public function __construct(
        protected ScoutDraftService $scoutDrafts,
        protected McpToolAuditLogger $audit,
    ) {}

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'candidate_id' => ['required', 'integer', 'min:1'],
            'job_id' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $payload = $this->scoutDrafts->draft(
                (int) $validated['candidate_id'],
                (int) $validated['job_id'],
            );
            $this->audit->log('draft_scout_message', $validated, 'draft for candidate '.$validated['candidate_id']);

            return Response::json($payload);
        } catch (ModelNotFoundException $e) {
            $this->audit->log('draft_scout_message', $validated, 'not_found');

            return Response::error('Candidate or job not found for the given ids.');
        } catch (Throwable $e) {
            $this->audit->log('draft_scout_message', $validated, 'error: '.$e->getMessage());

            return Response::error('draft_scout_message failed: '.$e->getMessage());
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'candidate_id' => $schema->integer()
                ->description('Candidate primary key (required).'),
            'job_id' => $schema->integer()
                ->description('Job posting primary key (required).'),
        ];
    }
}
