<?php

use App\Http\Controllers\Ats\AtsCandidateController;
use App\Http\Controllers\Ats\AtsDashboardController;
use App\Http\Controllers\Ats\AtsJobController;
use App\Http\Controllers\Ats\AtsPipelineController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/ats'));

Route::prefix('ats')->group(function () {
    Route::get('/', AtsDashboardController::class)->name('ats.dashboard');
    Route::get('/candidates', [AtsCandidateController::class, 'index'])->name('ats.candidates');
    Route::get('/candidates/{candidate}', [AtsCandidateController::class, 'show'])->name('ats.candidates.show');
    Route::get('/jobs', [AtsJobController::class, 'index'])->name('ats.jobs');
    Route::get('/jobs/{job}', [AtsJobController::class, 'show'])->name('ats.jobs.show');
    Route::get('/pipeline', [AtsPipelineController::class, 'index'])->name('ats.pipeline');
});
