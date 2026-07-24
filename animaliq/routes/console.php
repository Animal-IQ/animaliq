<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Awareness day emails: 08:00 Africa/Nairobi (annual month/day match inside the command)
Schedule::command('animaliq:awareness-day-emails')
    ->dailyAt('08:00')
    ->timezone('Africa/Nairobi')
    ->withoutOverlapping(120);

Schedule::command('animaliq:monthly-leaderboard')
    ->monthlyOn(1, '09:00')
    ->timezone('Africa/Nairobi')
    ->withoutOverlapping(120);
