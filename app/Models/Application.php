<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    public const STAGES = [
        'applied',
        'screening',
        'interview',
        'offer',
        'rejected',
        'hired',
    ];

    protected $fillable = [
        'candidate_id',
        'job_id',
        'stage',
        'score',
        'memo',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
