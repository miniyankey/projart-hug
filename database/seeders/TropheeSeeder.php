<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Trophee;
use Illuminate\Database\Seeder;

class TropheeSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::inRandomOrder()->take(4)->get();

        if ($companies->count() < 4) {
            return;
        }

        $years = [2008, 2009, 2010, now()->year];

        foreach ($years as $i => $year) {
            Trophee::create([
                'company_id' => $companies[$i]->id,
                'name' => 'Trophée de la générosité '.$year,
                'year_of' => $year,
                'description' => fake()->optional(0.6)->sentence(),
            ]);
        }
    }
}
