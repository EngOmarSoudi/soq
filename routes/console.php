<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule product import commands
Schedule::command('import:temu-products --limit=50')->dailyAt('02:00');
Schedule::command('import:cj-products electronics --limit=50')->dailyAt('03:00');
Schedule::command('import:shein-products --limit=50')->dailyAt('04:00');

// Schedule stock and price sync
Schedule::command('sync:stock-price')->everySixHours();

// Schedule tracking updates
Schedule::command('update:tracking-info')->hourly();