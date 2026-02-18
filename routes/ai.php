<?php

use App\Mcp\Servers\KiteFlowServer;
use Laravel\Mcp\Facades\Mcp;

/*
|--------------------------------------------------------------------------
| AI Routes
|--------------------------------------------------------------------------
|
| Here is where you can register AI servers for your application. These
| servers may be used to expose tools, resources, and prompts to
| AI clients that interact with your application via MCP.
|
*/

Mcp::web('/mcp', KiteFlowServer::class);
Mcp::local('kiteflow', KiteFlowServer::class);
