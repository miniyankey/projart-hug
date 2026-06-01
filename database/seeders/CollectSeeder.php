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
            Collect::create([
                'company_id' => $company->id,
                'place_id' => $places[$index % $places->count()]->id,
                'day' => now()->addWeeks($index + 2)->format('Y-m-d'),
                'start_time' => '09:00',
                'end_time' => '17:00',
                'link_appointment' => 'https://www.hug.ch/don-du-sang/prendre-rendez-vous',
                'is_active' => true,
            ]);
        }
    }
}
