<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Place;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    public function run(): void
    {
        // Centre de référence : le don se fait aussi au CTS des HUG.
        Place::create([
            'name' => 'HUG Cluse-Roseraie',
            'address' => 'Rue Gabrielle-Perret-Gentil 4',
            'locality' => 1205,
            'city' => 'Genève',
            'room' => 'Centre de transfusion sanguine',
        ]);

        // Un lieu de collecte par entreprise = son propre site (collecte mobile
        // sur place). L'adresse vient directement de la fiche entreprise, ce qui
        // évite de dupliquer la donnée. CollectSeeder retrouve ce lieu par le nom.
        foreach (Company::whereNotNull('street')->get() as $company) {
            Place::create([
                'name' => $company->name,
                'address' => $company->street,
                'locality' => (int) $company->postal_code,
                'city' => $company->city,
                'room' => 'Salle polyvalente',
            ]);
        }
    }
}
