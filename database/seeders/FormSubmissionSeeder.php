<?php

namespace Database\Seeders;

use App\Enums\FormSubmissionType;
use App\Models\Admin;
use App\Models\FormSubmission;
use Illuminate\Database\Seeder;

class FormSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();

        // Demandes entrantes du formulaire public (entreprises genevoises pas
        // encore dans le système = prospects), mêlant contact simple et demande
        // de collecte, certaines déjà traitées par le CTS (handled_at/handled_by).
        $submissions = [
            [
                'type' => FormSubmissionType::CollectRequest,
                'company_name' => 'Pictet Group',
                'city' => 'Genève',
                'name' => 'Camille Berthoud',
                'contact_email' => 'camille.berthoud@pictet.com',
                'message' => 'Nous souhaitons organiser une première collecte sur notre site de Carouge et participer au Trophée.',
                'trophy_participation' => true,
                'preferred_dates' => [
                    now()->addMonths(2)->toDateString(),
                    now()->addMonths(2)->addWeek()->toDateString(),
                ],
                'handled' => false,
            ],
            [
                'type' => FormSubmissionType::CollectRequest,
                'company_name' => 'Genève Aéroport',
                'city' => 'Le Grand-Saconnex',
                'name' => 'Olivier Maire',
                'contact_email' => 'o.maire@gva.ch',
                'message' => 'Forte affluence de collaborateurs, nous aimerions une collecte sur deux jours.',
                'trophy_participation' => true,
                'preferred_dates' => [
                    now()->addMonth()->toDateString(),
                ],
                'handled' => false,
            ],
            [
                'type' => FormSubmissionType::Contact,
                'company_name' => 'Lombard Odier',
                'city' => 'Genève',
                'name' => 'Sophie Girard',
                'contact_email' => 'sophie.girard@lombardodier.com',
                'message' => 'Pouvez-vous nous présenter le Label CTS RSE et ses conditions ?',
                'trophy_participation' => false,
                'preferred_dates' => null,
                'handled' => true,
            ],
            [
                'type' => FormSubmissionType::Contact,
                'company_name' => 'Université de Genève',
                'city' => 'Genève',
                'name' => 'Thomas Roulet',
                'contact_email' => 'thomas.roulet@unige.ch',
                'message' => 'Nous voulons sensibiliser nos étudiants au don du sang : comment co-construire une campagne ?',
                'trophy_participation' => false,
                'preferred_dates' => null,
                'handled' => false,
            ],
            [
                'type' => FormSubmissionType::CollectRequest,
                'company_name' => 'JTI (Japan Tobacco International)',
                'city' => 'Genève',
                'name' => 'Anita Frei',
                'contact_email' => 'anita.frei@jti.com',
                'message' => null,
                'trophy_participation' => false,
                'preferred_dates' => [
                    now()->addWeeks(6)->toDateString(),
                ],
                'handled' => true,
            ],
            [
                'type' => FormSubmissionType::Contact,
                'company_name' => 'STMicroelectronics',
                'city' => 'Plan-les-Ouates',
                'name' => 'Marco Bianchi',
                'contact_email' => 'marco.bianchi@st.com',
                'message' => 'Quel est le matériel mis à disposition par le CTS pour une collecte sur site ?',
                'trophy_participation' => false,
                'preferred_dates' => null,
                'handled' => false,
            ],
        ];

        foreach ($submissions as $data) {
            $handled = $data['handled'];
            unset($data['handled']);

            FormSubmission::create([
                ...$data,
                'handled_at' => $handled ? now()->subDays(rand(1, 10)) : null,
                'handled_by' => $handled ? $admin?->id : null,
            ]);
        }
    }
}
