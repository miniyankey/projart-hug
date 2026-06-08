<?php

namespace Database\Seeders;

use App\Models\Collect;
use App\Models\Company;
use App\Models\Place;
use Illuminate\Database\Seeder;

class CollectSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        $places = Place::all();

        if ($companies->isEmpty() || $places->isEmpty()) {
            return;
        }

        foreach ($companies->values() as $index => $company) {
            $base = $index + 2;

            // Par entreprise : une collecte terminée (plus d'une semaine passée),
            // une en cours (jour J proche) et une à venir. Le statut découle
            // uniquement de la date (cf. Collect::isOngoing()).
            $days = [
                now()->subWeeks($base),
                now()->addWeeks($base),
                now()->addWeeks($base + 4),
            ];

            foreach ($days as $slot => $day) {
                Collect::create([
                    'company_id' => $company->id,
                    'place_id' => $places[($index + $slot) % $places->count()]->id,
                    'day' => $day->format('Y-m-d'),
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'link_appointment' => 'https://www.hug.ch/don-du-sang/prendre-rendez-vous',
                ]);
            }
        }
    }
}
