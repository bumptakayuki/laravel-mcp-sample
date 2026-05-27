<?php

namespace App\Http\Controllers\Ats;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\View\View;

class AtsJobController extends Controller
{
    public function index(): View
    {
        $jobs = Job::query()->orderBy('title')->get();

        return view('ats.jobs', compact('jobs'));
    }

    public function show(Job $job): View
    {
        $job->load([
            'applications' => fn ($q) => $q->orderByDesc('id')->with('candidate'),
        ]);

        return view('ats.jobs.show', compact('job'));
    }
}
