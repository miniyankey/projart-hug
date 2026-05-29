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

        // Collecte d'exemple pour la démo co-brandée HEIG-VD.
        $heigVd = $companies->firstWhere('slug', 'heig-vd');
        if ($heigVd) {
            Collect::create([
                'company_id' => $heigVd->id,
                'place_id' => $places->random()->id,
                'day' => now()->addWeeks(2)->format('Y-m-d'),
                'link_appointment' => null,
                'is_active' => true,
            ]);
        }

        for ($i = 0; $i < 5; $i++) {
            Collect::create([
                'company_id' => $companies->random()->id,
                'place_id' => $places->random()->id,
                'day' => fake()->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d'),
                'link_appointment' => fake()->optional(0.7)->url(),
                'is_active' => true,
            ]);
        }
    }
}
