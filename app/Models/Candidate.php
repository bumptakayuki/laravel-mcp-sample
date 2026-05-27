<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    public const STATUSES = [
        'active',
        'screening',
        'interviewing',
        'offer',
        'rejected',
        'hired',
    ];

    protected $fillable = [
        'name',
        'email',
        'current_company',
        'current_position',
        'skills',
        'source',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
