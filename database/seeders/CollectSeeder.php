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

            // Par entreprise : une collecte passée (terminée), une active à
            // venir (mise en avant / co-brandée) et une future non encore
            // promue. Une seule est active à la fois, conformément à la règle
            // métier appliquée par CollectController::enforceSingleActiveCollect.
            $schedule = [
                ['day' => now()->subWeeks($base), 'is_active' => false],
                ['day' => now()->addWeeks($base), 'is_active' => true],
                ['day' => now()->addWeeks($base + 4), 'is_active' => false],
            ];

            foreach ($schedule as $slot => $config) {
                Collect::create([
                    'company_id' => $company->id,
                    'place_id' => $places[($index + $slot) % $places->count()]->id,
                    'day' => $config['day']->format('Y-m-d'),
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'link_appointment' => 'https://www.hug.ch/don-du-sang/prendre-rendez-vous',
                    'is_active' => $config['is_active'],
                ]);
            }
        }
    }
}
