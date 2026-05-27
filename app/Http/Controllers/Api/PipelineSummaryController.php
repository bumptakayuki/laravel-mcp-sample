<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Api\ApiAuditLogger;
use App\Services\Ats\PipelineSummaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class PipelineSummaryController extends Controller
{
    public function __construct(
        protected PipelineSummaryService $pipeline,
        protected ApiAuditLogger $audit,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'job_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ]);

        try {
            $jobId = $validated['job_id'] ?? null;
            $rows = $this->pipeline->summarize($jobId);
            $this->audit->log('pipeline.summary', $validated, 'jobs='.count($rows), $request->user());

            return ApiResponse::success($rows, ['jobs' => count($rows)]);
        } catch (Throwable $e) {
            $this->audit->log('pipeline.summary', $validated, 'error: '.$e->getMessage(), $request->user());

            return response()->json([
                'message' => 'pipeline.summary failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
