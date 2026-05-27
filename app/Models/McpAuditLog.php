<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class McpAuditLog extends Model
{
    protected $fillable = [
        'tool_name',
        'input',
        'output_summary',
        'executed_by',
    ];

    protected function casts(): array
    {
        return [
            'input' => 'array',
        ];
    }
}
