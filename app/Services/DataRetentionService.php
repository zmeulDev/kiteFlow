<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Support\Facades\Storage;

class DataRetentionService
{
    public function getRetentionDays(): int
    {
        return Setting::get('data_retention_days', 90);
    }

    public function cleanupOldVisits(): int
    {
        $retentionDays = $this->getRetentionDays();
        $cutoffDate = now()->subDays($retentionDays);

        $visits = Visit::where('check_in_at', '<', $cutoffDate)
            ->orWhere('check_out_at', '<', $cutoffDate)
            ->get();

        $count = 0;
        foreach ($visits as $visit) {
            $this->deleteVisitWithFiles($visit);
            $count++;
        }

        return $count;
    }

    protected function deleteVisitWithFiles(Visit $visit): void
    {
        if ($visit->photo_path) {
            Storage::delete($visit->photo_path);
        }

        $visit->delete();
    }

    public function cleanupOrphanedVisitors(): int
    {
        $orphans = Visitor::doesntHave('visits')->get();
        $count = 0;

        foreach ($orphans as $visitor) {
            if ($visitor->photo_path) {
                Storage::delete($visitor->photo_path);
            }
            if ($visitor->signature_path) {
                Storage::delete($visitor->signature_path);
            }
            $visitor->delete();
            $count++;
        }

        return $count;
    }
}