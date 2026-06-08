<?php

namespace Database\Seeders;

use App\Models\Collect;
use App\Models\Company;
use App\Models\Place;
use Illuminate\Database\Seeder;

class CollectSeeder extends Seeder
{
    /**
     * Lien de prise de rendez-vous (CTA des pages collecte / co-brandées).
     */
    private const APPOINTMENT_URL = 'https://www.hug.ch/don-du-sang/prendre-rendez-vous';

    public function run(): void
    {
        $companies = Company::all();
        $hug = Place::firstWhere('name', 'HUG Cluse-Roseraie');

        if ($companies->isEmpty() || $hug === null) {
            return;
        }

        foreach ($companies->values() as $index => $company) {
            // La collecte se tient sur le site de l'entreprise (créé par
            // PlaceSeeder), avec repli sur le CTS des HUG si absent.
            $place = Place::firstWhere('name', $company->name) ?? $hug;
            $offset = $index + 1;

            // Cycle type par entreprise : une terminée, une en cours (jour J
            // proche), une à venir. Le statut découle de la date (Collect::isOngoing()).
            $this->makeCollect($company, $place, now()->subWeeks($offset + 1));
            $this->makeCollect($company, $place, now()->addDays($offset + 2));
            $this->makeCollect($company, $place, now()->addWeeks($offset + 5));
        }

        // Scénario vedette de la démo : une collecte « pile aujourd'hui » au
        // Stade de Genève (Servette FC) pour montrer la page collecte en cours
        // + CTA prise de RDV.
        $servette = $companies->firstWhere('slug', 'servette-fc');

        if ($servette !== null) {
            $this->makeCollect(
                $servette,
                Place::firstWhere('name', $servette->name) ?? $hug,
                now(),
            );
        }
    }

    private function makeCollect(Company $company, Place $place, \DateTimeInterface $day): void
    {
        Collect::create([
            'company_id' => $company->id,
            'place_id' => $place->id,
            'day' => $day->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'link_appointment' => self::APPOINTMENT_URL,
        ]);
    }
}
