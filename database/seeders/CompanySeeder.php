<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Entreprise d'exemple labellisée, support de la démo co-brandée.
        Company::create([
            'name' => 'HEIG-VD',
            'slug' => 'heig-vd',
            'token' => 'heigvd00',
            'email_contact' => 'collecte@heig-vd.ch',
            'contact_name' => 'Jean Dupont',
            'contact_phone' => '+41 24 557 63 30',
            'street' => 'Route de Cheseaux 1',
            'postal_code' => '1401',
            'city' => 'Yverdon-les-Bains',
            'logo' => 'logos/heig.png',
            'color' => '#E40521',
            'is_labelled' => true,
            'labelled_at' => now(),
        ]);

        // Seconde entreprise d'exemple, non labellisée (montre l'autre état).
        Company::create([
            'name' => 'Rolex SA',
            'slug' => 'rolex-sa',
            'token' => 'rolex000',
            'email_contact' => 'collecte@rolex.ch',
            'contact_name' => 'Marie Martin',
            'contact_phone' => '+41 22 302 22 00',
            'street' => 'Rue François-Dussaud 3',
            'postal_code' => '1211',
            'city' => 'Genève',
            'color' => '#127749',
            'is_labelled' => false,
        ]);
    }
}
