<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\AutoCheckoutVisitsJob;
use App\Jobs\PurgeTenantDataJob;

Schedule::job(new AutoCheckoutVisitsJob())->dailyAt('23:00');
Schedule::job(new PurgeTenantDataJob())->daily();
