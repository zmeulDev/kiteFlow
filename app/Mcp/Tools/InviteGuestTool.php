<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\Tenant;
use Illuminate\Support\Str;

class InviteGuestTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Pre-register a guest and generate a Fast Pass token.';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'purpose' => 'required|string',
        ]);

        $tenant = Tenant::first(); // Assuming first tenant for now
        $host = $tenant->users()->first();

        $visitor = Visitor::updateOrCreate(
            ['email' => $validated['email']],
            [
                'tenant_id' => $tenant->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
            ]
        );

        $visit = Visit::create([
            'tenant_id' => $tenant->id,
            'check_in_token' => Str::random(32),
            'visitor_id' => $visitor->id,
            'user_id' => $host->id,
            'purpose' => $validated['purpose'],
            'checked_in_at' => null,
        ]);

        return Response::text("Guest '{$visitor->full_name}' invited successfully. Fast Pass Token: {$visit->check_in_token}");
    }

    /**
     * Get the tool's input schema.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'first_name' => $schema->string()->required(),
            'last_name' => $schema->string()->required(),
            'email' => $schema->string()->required(),
            'purpose' => $schema->string()->required(),
        ];
    }
}
