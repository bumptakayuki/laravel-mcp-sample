<?php

use App\Http\Controllers\Api\CandidateSearchController;
use App\Http\Controllers\Api\PipelineSummaryController;
use App\Http\Controllers\Api\ScoutDraftController;
use Illuminate\Support\Facades\Route;

/*
| 外部 SoR / バックエンド連携用 JSON API（Service 経由のみ）。認証: Sanctum Bearer。
*/

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('candidates', [CandidateSearchController::class, 'index']);
    Route::get('pipeline/summary', [PipelineSummaryController::class, 'index']);
    Route::post('scout/drafts', [ScoutDraftController::class, 'store']);
});
