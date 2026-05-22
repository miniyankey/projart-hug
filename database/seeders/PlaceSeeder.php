<?php

namespace Database\Seeders;

use App\Models\Place;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    public function run(): void
    {
        Place::create([
            'name' => 'HUG Cluse-Roseraie',
            'address' => 'Rue Gabrielle-Perret-Gentil 4',
            'locality' => 1205,
            'city' => 'Genève',
            'room' => 'Centre de transfusion sanguine',
        ]);

        for ($i = 0; $i < 4; $i++) {
            Place::create([
                'name' => 'Siège '.fake()->unique()->company(),
                'address' => fake()->streetAddress(),
                'locality' => fake()->numberBetween(1000, 9999),
                'city' => fake()->city(),
                'room' => fake()->optional(0.5)->bothify('Salle ##'),
            ]);
        }
    }
}
