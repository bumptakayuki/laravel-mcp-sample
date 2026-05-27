<?php

use App\Mcp\Servers\AtsMcpServer;
use Laravel\Mcp\Facades\Mcp;

/*
|--------------------------------------------------------------------------
| MCP (loaded by laravel/mcp when routes/ai.php exists)
|--------------------------------------------------------------------------
| Claude Code / MCP client:
|   php artisan mcp:start ats-demo
*/

Mcp::local('ats-demo', AtsMcpServer::class);
