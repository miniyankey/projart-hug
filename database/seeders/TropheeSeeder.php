<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Trophee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TropheeSeeder extends Seeder
{
    public function run(): void
    {

        //on reprend ce qui était codé en dur avant
        $winners = $this->seedWinnerCompanies();

        $editions = [
            2026 => ['rolex', 'migros', 'nestle'],
            2010 => ['nestle', 'rolex', 'migros'],
            2009 => ['migros', 'nestle', 'rolex'],
            2008 => ['rolex', 'migros', 'nestle'],
        ];

        foreach ($editions as $year => $podium) {
            foreach ($podium as $position => $handle) {
                Trophee::create([
                    'company_id' => $winners[$handle]->id,
                    'year_of' => $year,
                    'rank' => $position + 1,
                ]);
            }
        }
    }

    /**
     * Garantit l'existence des entreprises lauréates avec leurs logos d'origine.
     *
     * @return array<string, Company>
     */
    private function seedWinnerCompanies(): array
    {
        // Copie les logos de marque (versionnés dans public/img) vers le disque
        // public, comme le fait CompanySeeder pour ses propres logos.
        foreach (['rolex.svg', 'migros.png', 'nestle.png'] as $logo) {
            Storage::disk('public')->put(
                "logos/{$logo}",
                file_get_contents(public_path("img/{$logo}"))
            );
        }

        // Rolex existe déjà (entreprise de démo co-brandée) : on lui attache son
        // logo pour que le podium s'affiche à l'identique de l'ancienne version.
        $rolex = Company::firstWhere('slug', 'rolex-sa');
        $rolex?->update(['logo' => 'logos/rolex.svg']);

        $migros = Company::firstOrCreate(
            ['slug' => 'migros'],
            [
                'name' => 'Migros',
                'token' => 'migros00',
                'email_contact' => 'collecte@migros.ch',
                'logo' => 'logos/migros.png',
                'color' => '#FF6600',
            ],
        );

        $nestle = Company::firstOrCreate(
            ['slug' => 'nestle'],
            [
                'name' => 'Nestlé',
                'token' => 'nestle00',
                'email_contact' => 'collecte@nestle.ch',
                'logo' => 'logos/nestle.png',
                'color' => '#63B1E5',
            ],
        );

        return [
            'rolex' => $rolex,
            'migros' => $migros,
            'nestle' => $nestle,
        ];
    }
}
