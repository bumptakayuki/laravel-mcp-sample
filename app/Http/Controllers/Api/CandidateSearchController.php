<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\Api\ApiAuditLogger;
use App\Services\Ats\CandidateSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CandidateSearchController extends Controller
{
    public function __construct(
        protected CandidateSearchService $candidates,
        protected ApiAuditLogger $audit,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'skill' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'position' => ['sometimes', 'nullable', 'string', 'max:100'],
        ]);

        try {
            $rows = $this->candidates->search($validated);
            $this->audit->log('candidates.index', $validated, 'returned '.count($rows).' row(s)', $request->user());

            return ApiResponse::success($rows, ['count' => count($rows)]);
        } catch (Throwable $e) {
            $this->audit->log('candidates.index', $validated, 'error: '.$e->getMessage(), $request->user());

            return response()->json([
                'message' => 'candidates.index failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
