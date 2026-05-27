<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Api\ApiAuditLogger;
use App\Services\Ats\ScoutDraftService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ScoutDraftController extends Controller
{
    public function __construct(
        protected ScoutDraftService $scoutDrafts,
        protected ApiAuditLogger $audit,
    ) {}

    public function store(Request $request): JsonResponse
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
            $this->audit->log('scout.drafts.store', $validated, 'draft for candidate '.$validated['candidate_id'], $request->user());

            return ApiResponse::success($payload);
        } catch (ModelNotFoundException) {
            $this->audit->log('scout.drafts.store', $validated, 'not_found', $request->user());

            return response()->json([
                'message' => 'Candidate or job not found for the given ids.',
            ], 404);
        } catch (Throwable $e) {
            $this->audit->log('scout.drafts.store', $validated, 'error: '.$e->getMessage(), $request->user());

            return response()->json([
                'message' => 'scout.drafts.store failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
