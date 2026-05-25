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
            'company_address' => 'Quai de l\'Île 17',
            'company_city' => 'Genève',
            'company_postal_code' => '1204',
            'firstname' => 'Marie',
            'lastname' => 'Dupont',
            'contact_email' => 'marie.dupont@bcge.ch',
            'message' => 'Nous aimerions organiser une collecte de sang dans nos locaux.',
            'preferred_date' => now()->addMonths(2)->toDateString(),
        ]);

        FormSubmission::create([
            'type' => FormSubmissionType::TropheeParticipation,
            'company_name' => 'Rolex SA',
            'company_address' => 'Rue François-Dussaud 3-7',
            'company_city' => 'Genève',
            'company_postal_code' => '1211',
            'firstname' => 'Jean',
            'lastname' => 'Müller',
            'contact_email' => 'j.muller@rolex.com',
            'message' => 'Nous souhaitons participer à l\'édition de cette année.',
            'preferred_date' => null,
        ]);

        FormSubmission::create([
            'type' => FormSubmissionType::CollectRequest,
            'company_name' => 'CERN',
            'company_address' => 'Esplanade des Particules 1',
            'company_city' => 'Meyrin',
            'company_postal_code' => '1217',
            'firstname' => 'Sophie',
            'lastname' => 'Rossi',
            'contact_email' => 'sophie.rossi@cern.ch',
            'message' => null,
            'preferred_date' => now()->addMonth()->toDateString(),
        ]);
    }
}
