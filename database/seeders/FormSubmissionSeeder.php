<?php

namespace Database\Seeders;

use App\Enums\FormSubmissionType;
use App\Models\FormSubmission;
use Illuminate\Database\Seeder;

class FormSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        FormSubmission::create([
            'type' => FormSubmissionType::Contact,
            'company_name' => 'Banque Cantonale de Genève',
            'name' => 'Marie Dupont',
            'contact_email' => 'marie.dupont@bcge.ch',
            'message' => 'Nous aimerions en savoir plus sur l\'organisation d\'une collecte de sang dans nos locaux.',
            'trophy_participation' => false,
            'preferred_dates' => null,
        ]);

        FormSubmission::create([
            'type' => FormSubmissionType::CollectRequest,
            'company_name' => 'Rolex SA',
            'name' => 'Jean Müller',
            'contact_email' => 'j.muller@rolex.com',
            'message' => 'Nous souhaitons organiser une collecte et participer au Trophée de la générosité.',
            'trophy_participation' => true,
            'preferred_dates' => [
                now()->addMonths(2)->toDateString(),
                now()->addMonths(2)->addWeek()->toDateString(),
            ],
        ]);

        FormSubmission::create([
            'type' => FormSubmissionType::CollectRequest,
            'company_name' => 'CERN',
            'name' => 'Sophie Rossi',
            'contact_email' => 'sophie.rossi@cern.ch',
            'message' => null,
            'trophy_participation' => false,
            'preferred_dates' => [
                now()->addMonth()->toDateString(),
            ],
        ]);
    }
}
