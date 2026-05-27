<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\DraftScoutMessageTool;
use App\Mcp\Tools\GetPipelineSummaryTool;
use App\Mcp\Tools\SearchCandidatesTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Tool;

/**
 * LT 用 ATS MCP サーバー。Tool 経由でのみ業務操作を公開する。
 */
#[Name('AtsMcpServer')]
#[Version('1.0.0')]
#[Instructions('Demo ATS MCP server. Read-only tools go through Laravel services (AI must not touch the DB directly).')]
class AtsMcpServer extends Server
{
    /**
     * @var array<int, class-string<Tool>>
     */
    protected array $tools = [
        SearchCandidatesTool::class,
        GetPipelineSummaryTool::class,
        DraftScoutMessageTool::class,
    ];

    /**
     * @var array<int, class-string<Server\Resource>>
     */
    protected array $resources = [];

    /**
     * @var array<int, class-string<Prompt>>
     */
    protected array $prompts = [];
}
