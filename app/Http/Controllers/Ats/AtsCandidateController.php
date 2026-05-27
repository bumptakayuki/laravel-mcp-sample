<?php

namespace App\Http\Controllers\Ats;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Illuminate\View\View;

class AtsCandidateController extends Controller
{
    public function index(): View
    {
        $candidates = Candidate::query()->orderBy('name')->get();

        return view('ats.candidates', compact('candidates'));
    }

    public function show(Candidate $candidate): View
    {
        $candidate->load([
            'applications' => fn ($q) => $q->orderByDesc('id')->with('job'),
        ]);

        return view('ats.candidates.show', compact('candidate'));
    }
}
