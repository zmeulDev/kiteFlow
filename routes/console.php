<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\AutoCheckoutVisitors;
use App\Console\Commands\PurgeGdprData;

Schedule::command(AutoCheckoutVisitors::class)->dailyAt('18:00');
Schedule::command(PurgeGdprData::class)->dailyAt('02:00');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands
|--------------------------------------------------------------------------
|
| Schedule automated tasks for the Visitor Management System
|
*/

// Auto-checkout visitors every hour
Schedule::command('visitors:auto-checkout')->hourly();

// GDPR data purge - run daily at 2 AM
Schedule::command('visitors:purge-gdpr')->dailyAt('02:00');
