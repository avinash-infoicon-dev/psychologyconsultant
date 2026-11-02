<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



Schedule::command('healthcare:reset-quotas')->monthlyOn(1, '00:05');
Schedule::job(new \App\Jobs\DeleteExpiredFiles())->daily();
Schedule::job(new \App\Jobs\BackupDatabase())->dailyAt('02:00');
Schedule::command('healthcare:send-daily-summary')->dailyAt(
    config('notification.daily_summary.send_time', '20:00')
);
Schedule::command('healthcare:notify-renewals')->dailyAt('09:00');
