<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Trophee;
use Illuminate\Database\Seeder;

class TropheeSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::inRandomOrder()->get();

        if ($companies->isEmpty()) {
            return;
        }

        $years = [2008, 2009, 2010, now()->year];

        // Répartit chaque année sur les entreprises disponibles en cyclant :
        // une même entreprise peut gagner plusieurs éditions (années distinctes).
        foreach ($years as $i => $year) {
            Trophee::create([
                'company_id' => $companies[$i % $companies->count()]->id,
                'name' => 'Trophée de la générosité '.$year,
                'year_of' => $year,
                'description' => fake()->optional(0.6)->sentence(),
            ]);
        }
    }
}
