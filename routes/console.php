<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Rappels d'éligibilité : envoyés le matin du jour de ré-éligibilité
Schedule::command('app:send-eligibility-reminders')->dailyAt('09:00');
