<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visit;

class PruneOldVisits extends Command
{
    protected $signature = 'visiflow:prune';
    protected $description = 'Prune visit records older than 90 days for GDPR compliance.';

    public function handle()
    {
        $count = Visit::where('created_at', '<', now()->subDays(90))->delete();
        $this->info("Successfully pruned {$count} old visit records.");
    }
}
