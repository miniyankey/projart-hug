<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Trophee;
use Illuminate\Database\Seeder;

class TropheeSeeder extends Seeder
{
    /**
     * Palmarès du Trophée de la générosité : un podium (1er, 2e, 3e) par édition,
     * référencé par slug d'entreprise (toutes créées par CompanySeeder).
     *
     * @var array<int, list<string>>
     */
    private const EDITIONS = [
        2026 => ['rolex-sa', 'migros', 'servette-fc'],
        2010 => ['migros', 'rolex-sa', 'tpg'],
        2009 => ['tpg', 'migros', 'servette-fc'],
        2008 => ['rolex-sa', 'migros', 'tpg'],
    ];

    /**
     * Quelques mises en avant éditoriales pour l'édition en cours (rang 1 à 3).
     *
     * @var array<int, string>
     */
    private const LATEST_DESCRIPTIONS = [
        1 => 'Plus de 60 % des collaborateurs mobilisés sur deux collectes : un record d\'engagement sur le canton.',
        2 => 'Une participation en forte hausse, portée par une campagne interne exemplaire.',
        3 => 'Première participation au Trophée et déjà sur le podium.',
    ];

    public function run(): void
    {
        $companies = Company::whereIn('slug', array_unique(array_merge(...array_values(self::EDITIONS))))
            ->get()
            ->keyBy('slug');

        $latestYear = max(array_keys(self::EDITIONS));

        foreach (self::EDITIONS as $year => $podium) {
            foreach ($podium as $position => $slug) {
                $company = $companies->get($slug);

                if ($company === null) {
                    continue;
                }

                $rank = $position + 1;

                Trophee::create([
                    'company_id' => $company->id,
                    'year_of' => $year,
                    'rank' => $rank,
                    'description' => $year === $latestYear ? (self::LATEST_DESCRIPTIONS[$rank] ?? null) : null,
                ]);
            }
        }
    }
}
