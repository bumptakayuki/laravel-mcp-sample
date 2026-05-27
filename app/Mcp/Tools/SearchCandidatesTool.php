<?php

namespace App\Mcp\Tools;

use App\Services\Ats\CandidateSearchService;
use App\Services\Mcp\McpToolAuditLogger;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Throwable;

/**
 * MCP: 許可された検索のみ。Service 経由で DB にアクセスし、PII・件数を制限する。
 */
#[Name('search_candidates')]
#[Description('Search candidates in the demo ATS by skill, status, or position. Returns at most 10 candidates and excludes sensitive fields such as email.')]
#[IsReadOnly]
class SearchCandidatesTool extends Tool
{
    public function __construct(
        protected CandidateSearchService $candidates,
        protected McpToolAuditLogger $audit,
    ) {}

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'skill' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'position' => ['sometimes', 'nullable', 'string', 'max:100'],
        ]);

        try {
            $rows = $this->candidates->search($validated);
            $this->audit->log('search_candidates', $validated, 'returned '.count($rows).' row(s)');

            return Response::json($rows);
        } catch (Throwable $e) {
            $this->audit->log('search_candidates', $validated, 'error: '.$e->getMessage());

            return Response::error('search_candidates failed: '.$e->getMessage());
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'skill' => $schema->string()
                ->description('Filter by substring match against skills JSON (e.g. Laravel).'),
            'status' => $schema->string()
                ->description('Candidate status: active, screening, interviewing, offer, rejected, hired.'),
            'position' => $schema->string()
                ->description('Substring match against current_position.'),
        ];
    }
}
