<?php

namespace Database\Seeders;

use App\Models\Collect;
use App\Models\Company;
use App\Models\Place;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CollectSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        $places = Place::all();

        if ($companies->isEmpty() || $places->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 5; $i++) {
            Collect::create([
                'company_id' => $companies->random()->id,
                'place_id' => $places->random()->id,
                'day' => fake()->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d'),
                'link_appointment' => fake()->optional(0.7)->url(),
                'token' => Str::random(12),
                'is_active' => true,
            ]);
        }
    }
}
