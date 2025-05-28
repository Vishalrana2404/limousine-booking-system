<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();


Schedule::command("app:update-pending-bookings")->everyFifteenMinutes();
Schedule::command("app:send-driver-schedule")
->timezone('Asia/Singapore')
->dailyAt('18:00');
Schedule::command("app:send-driver-off-days")
->timezone('Asia/Singapore')
->dailyAt('18:00');