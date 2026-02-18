<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use App\Models\Visitor;
use App\Models\Visit;

class CheckOutVisitorTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Manually check out a visitor by their email address.';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $email = $request->get('email');

        $visitor = Visitor::where('email', $email)->first();

        if (!$visitor) {
            return Response::error("Visitor with email '{$email}' not found.");
        }

        $visit = Visit::where('visitor_id', $visitor->id)
            ->whereNotNull('checked_in_at')
            ->whereNull('checked_out_at')
            ->latest()
            ->first();

        if (!$visit) {
            return Response::error("No active visit found for '{$visitor->full_name}'.");
        }

        $visit->update(['checked_out_at' => now()]);

        return Response::text("Successfully checked out '{$visitor->full_name}'.");
    }

    /**
     * Get the tool's input schema.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'email' => $schema->string()
                ->description('The email address of the visitor to check out.')
                ->required(),
        ];
    }
}
