<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Entreprise d'exemple pour les pages co-brandées :
        Company::create([
            'name' => 'HEIG-VD',
            'slug' => 'heig-vd',
            'email_contact' => 'collecte@heig-vd.ch',
            'logo' => 'logos/heig.png',
            'color' => '#E40521',
            'is_labelled' => true,
            'labelled_at' => now(),
        ]);

        for ($i = 0; $i < 6; $i++) {
            $name = fake()->unique()->company();

            Company::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'email_contact' => fake()->unique()->companyEmail(),
                'color' => fake()->hexColor(),
                'is_labelled' => false,
            ]);
        }

        for ($i = 0; $i < 4; $i++) {
            $name = fake()->unique()->company();

            Company::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'email_contact' => fake()->unique()->companyEmail(),
                'color' => fake()->hexColor(),
                'is_labelled' => true,
                'labelled_at' => fake()->dateTimeBetween('-2 years', 'now'),
            ]);
        }
    }
}
