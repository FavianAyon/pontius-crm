<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('crm:notify-overdue-tasks')
    ->hourly();

Schedule::command('crm:notify-due-soon-tasks')
    ->everyFifteenMinutes();
