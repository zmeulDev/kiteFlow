<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use App\Models\Visit;

class GetActiveVisitorsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Get a list of all visitors currently in the building.';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $visits = Visit::with(['visitor', 'host', 'location'])
            ->whereNotNull('checked_in_at')
            ->whereNull('checked_out_at')
            ->get();

        if ($visits->isEmpty()) {
            return Response::text('There are currently no active visitors in the building.');
        }

        $visitorList = $visits->map(function ($visit) {
            $locationName = $visit->location?->name ?? 'Main Desk';
            return "- {$visit->visitor->full_name} ({$visit->visitor->email}) visiting {$visit->host->name} at {$locationName} since {$visit->checked_in_at->format('H:i')}.";
        })->implode("\n");

        return Response::text("Active Visitors:\n" . $visitorList);
    }

    /**
     * Get the tool's input schema.
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
