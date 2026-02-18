<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;
use App\Mcp\Tools\GetActiveVisitorsTool;
use App\Mcp\Tools\CheckOutVisitorTool;
use App\Mcp\Tools\InviteGuestTool;

class KiteFlowServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'KiteFlow API';

    /**
     * The MCP server's version.
     */
    protected string $version = '1.0.0';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = 'This server allows interaction with the KiteFlow visitor management system, enabling listing active visitors, checking out visitors, and inviting new guests.';

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        GetActiveVisitorsTool::class,
        CheckOutVisitorTool::class,
        InviteGuestTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
