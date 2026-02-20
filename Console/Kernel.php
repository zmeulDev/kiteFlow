<?php

use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Auto-checkout visitors at 11:30 PM daily
        $schedule->command('visitors:auto-checkout')
            ->dailyAt('23:30')
            ->withoutOverlapping()
            ->runInBackground();

        // GDPR purge - run weekly on Sunday at 2 AM
        $schedule->command('visitors:purge-gdpr')
            ->weeklyOn(0, '02:00')
            ->withoutOverlapping()
            ->runInBackground();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
