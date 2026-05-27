<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 外部向け HTTP API の監査ログ（MCP の mcp_audit_logs と対称）。
 */
class ApiAuditLog extends Model
{
    protected $fillable = [
        'action',
        'input',
        'output_summary',
        'executed_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'input' => 'array',
        ];
    }
}
