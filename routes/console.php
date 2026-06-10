<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Rappels d'éligibilité : envoyés le matin du jour de ré-éligibilité.
//
// ⚠️ INUTILISÉ EN PROD (hébergement mutualisé Infomaniak) : ce scheduler ne
// se déclenche que si un cron shell lance `php artisan schedule:run` toutes
// les minutes, ce qui est impossible sur mutualisé. En prod, les rappels sont
// déclenchés par le planificateur Infomaniak via la route HTTP
// `GET /cron/eligibility-reminders?token=<CRON_TOKEN>` (cf. routes/web.php et
// DEPLOYMENT_INFOMANIAK.md, section 16). Pas de risque de doublon si les deux
// mécanismes coexistent : la commande est idempotente (filtre sur `sent_at`).
// Déclaration conservée pour une éventuelle migration vers un serveur avec
// cron shell.
Schedule::command('app:send-eligibility-reminders')->dailyAt('09:00');
