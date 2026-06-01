<?php

namespace Database\Seeders;

use App\Enums\QuestionType;
use App\Models\GameChoice;
use App\Models\GameQuestion;
use App\Models\GameView;
use Illuminate\Database\Seeder;

class GameQuestionSeeder extends Seeder
{
    public function run(): void
    {
        GameChoice::query()->delete();
        GameQuestion::query()->delete();
        GameView::query()->delete();

        $viewMedicaments = GameView::create([
            'message' => 'Ton inéligibilité dépend des médicaments que tu as consommé.',
            'text' => 'Tu peux faire appel au CTS pour avoir plus d\'information sur ton éligibilité au don.',
        ]);

        $viewVoyage = GameView::create([
            'message' => 'Ton inéligibilité dépend des pays que tu as visités.',
            'text' => 'Regarde sur le Travel Checker pour savoir la durée d\'attente avant de pouvoir donner. Le maximum est de 6 mois le temps que les éventuels agents pathogènes aient le temps d\'être détectables.',
            'integration_url' => 'integrations.spenderservice.ch/fr/travel_check/irb/',
        ]);

        $viewVaccin = GameView::create([
            'message' => 'Si tu ne sais pas quel type de vaccin tu as fait, tu peux te rendre sur le Check Vaccination et remplir avec les informations de ton vaccin. Tu sauras alors si tu es éligible ou non.',
            'button_text' => 'Check Vaccination',
            'button_url' => 'https://www.blutspende.ch/fr/dates-de-collecte-de-sang',
        ]);

        $questions = [
            [
                'titre' => 'Inéligibilité à vie',
                'question' => 'Coche toutes les situations qui te concernent ou t\'ont concerné.',
                'type' => QuestionType::Multiple,
                'why_question' => 'Certaines personnes ne peuvent pas donner leur sang car cela serait risqué pour elles ou pour les autres.',
                'order' => 1,
                'choices' => [
                    ['text' => 'VIH/SIDA',              'eligible' => false, 'ineligibility_days' => -1,   'order' => 1],
                    ['text' => 'Diabète',                'eligible' => false, 'ineligibility_days' => -1,   'order' => 2],
                    ['text' => 'Drogue par injection',   'eligible' => false, 'ineligibility_days' => -1,   'order' => 3],
                    ['text' => 'Troubles cardiaques',    'eligible' => false, 'ineligibility_days' => -1,   'order' => 4],
                    ['text' => 'Maladies sanguines',     'eligible' => false, 'ineligibility_days' => -1,   'order' => 5],
                    ['text' => 'Hépatite B et C',        'eligible' => false, 'ineligibility_days' => -1,   'order' => 6],
                    ['text' => 'Aucune de ces situations', 'eligible' => true, 'ineligibility_days' => null, 'order' => 7],
                ],
            ],
            [
                'titre' => 'Santé',
                'question' => 'Es-tu en plutôt bonne santé, et exempt de tout symptôme de rhume ou de fièvre ?',
                'type' => QuestionType::Unique,
                'why_question' => 'On ne prend pas de risque ! On préfère que les personnes ayant des légers symptômes se remettent en état avant de donner.',
                'order' => 2,
                'choices' => [
                    ['text' => 'OUI', 'eligible' => true,  'ineligibility_days' => null, 'order' => 1],
                    ['text' => 'Non', 'eligible' => false, 'ineligibility_days' => 14,   'order' => 2],
                ],
            ],
            [
                'titre' => 'Médicaments',
                'question' => 'As-tu pris des médicaments durant les 4 dernières semaines ?',
                'type' => QuestionType::Unique,
                'why_question' => 'Certains médicaments ne permettent pas un don de sang sans risquer d\'affecter le receveur ou le donneur.',
                'order' => 3,
                'choices' => [
                    ['text' => 'oui', 'eligible' => false, 'ineligibility_days' => 28,   'view_id' => $viewMedicaments->id, 'order' => 1],
                    ['text' => 'NON', 'eligible' => true,  'ineligibility_days' => null, 'order' => 2],
                ],
            ],
            [
                'titre' => 'Voyage',
                'question' => 'Dans quelles régions du monde as-tu voyagé les 6 derniers mois ?',
                'type' => QuestionType::Unique,
                'why_question' => 'Dans certaines régions du monde, un agent pathogène peut rentrer en Suisse avec toi et ne pourrait être détectable que 6 mois après !',
                'order' => 4,
                'choices' => [
                    ['text' => 'EUROPE',                    'eligible' => true,  'ineligibility_days' => null, 'order' => 1],
                    ['text' => 'autres régions du monde',   'eligible' => false, 'ineligibility_days' => 180,  'view_id' => $viewVoyage->id, 'order' => 2],
                    ['text' => 'AUCUNE',                    'eligible' => true,  'ineligibility_days' => null, 'order' => 3],
                ],
            ],
            [
                'titre' => 'Partenaire',
                'question' => 'As-tu eu plusieurs partenaires sexuel.les au cours des 12 derniers mois ou as-tu changé de partenaire dans les 4 derniers mois ?',
                'type' => QuestionType::Unique,
                'why_question' => 'J\'entre dans ton intimité mais c\'est important pour minimiser les risques de transmission d\'IST au receveur.',
                'order' => 5,
                'choices' => [
                    ['text' => 'oui', 'eligible' => false, 'ineligibility_days' => 120,  'order' => 1],
                    ['text' => 'NON', 'eligible' => true,  'ineligibility_days' => null, 'order' => 2],
                ],
            ],
            [
                'titre' => 'Hospitalisation',
                'question' => 'As-tu été hospitalisé.e récemment ?',
                'type' => QuestionType::Unique,
                'why_question' => 'Si la personne se remet d\'une grosse opération ou d\'un accouchement, elle a besoin de temps pour se remettre.',
                'order' => 6,
                'choices' => [
                    ['text' => 'NON (aucune hospitalisation dans les 12 derniers mois)',    'eligible' => true,  'ineligibility_days' => null, 'order' => 1],
                    ['text' => 'OUI, urgences/consultation il y a plus de 4 semaines',      'eligible' => true,  'ineligibility_days' => null, 'order' => 2],
                    ['text' => 'oui, urgences/consultation il y a moins de 4 semaines',     'eligible' => false, 'ineligibility_days' => 28,   'order' => 3],
                    ['text' => 'oui, hospitalisation avec admission',                        'eligible' => false, 'ineligibility_days' => 365,  'order' => 4],
                ],
            ],
            [
                'titre' => 'Dentiste',
                'question' => 'As-tu eu un traitement dentaire au cours des 14 derniers jours ?',
                'type' => QuestionType::Unique,
                'why_question' => 'Ces traitements provoquent de petites lésions par lesquelles peuvent s\'infiltrer des bactéries ou des virus et ainsi contaminer le sang.',
                'order' => 7,
                'choices' => [
                    ['text' => 'NON',                                     'eligible' => true,  'ineligibility_days' => null, 'order' => 1],
                    ['text' => 'oui (un détartrage)',                     'eligible' => false, 'ineligibility_days' => 1,    'order' => 2],
                    ['text' => 'oui (une extraction dentaire sans complication)', 'eligible' => false, 'ineligibility_days' => 7, 'order' => 3],
                    ['text' => 'oui (avec complications type antibiotique)', 'eligible' => false, 'ineligibility_days' => 14, 'order' => 4],
                ],
            ],
            [
                'titre' => 'Tatouage',
                'question' => 'Quels styles as-tu ajouté à ton personnage sur les 4 derniers mois ?',
                'type' => QuestionType::Unique,
                'why_question' => 'Ces traitements peuvent causer des petites blessures. Celles-ci peuvent ouvrir la porte aux bactéries et virus.',
                'order' => 8,
                'choices' => [
                    ['text' => 'Un tatouage',                          'eligible' => false, 'ineligibility_days' => 120,  'order' => 1],
                    ['text' => 'un piercing',                          'eligible' => false, 'ineligibility_days' => 120,  'order' => 2],
                    ['text' => 'aucun mais j\'ai fait de l\'acupuncture', 'eligible' => false, 'ineligibility_days' => 120, 'order' => 3],
                    ['text' => 'AUCUN TU ES PARFAIT COMME TU ES',     'eligible' => true,  'ineligibility_days' => null, 'order' => 4],
                ],
            ],
            [
                'titre' => 'Piqûre de tique',
                'question' => 'Es-tu revenu.e d\'une balade extérieure avec un petit compagnon au cours des 4 dernières semaines ?',
                'type' => QuestionType::Unique,
                'why_question' => 'Les tiques sont, comme les moustiques, porteuses de maladies comme la Lyme qui se transmettent par leurs bactéries. Je te recommande de bien vérifier tout ton corps lorsque tu rentres chez toi après une sortie dans la nature.',
                'order' => 9,
                'choices' => [
                    ['text' => 'oui une tique',          'eligible' => false, 'ineligibility_days' => 28,   'order' => 1],
                    ['text' => 'OUI UN AUTRE COMPAGNON', 'eligible' => true,  'ineligibility_days' => null, 'order' => 2],
                    ['text' => 'NON J\'ETAIS SEUL.E',   'eligible' => true,  'ineligibility_days' => null, 'order' => 3],
                ],
            ],
            [
                'titre' => 'Vaccin récent',
                'question' => 'As-tu reçu un vaccin récemment ?',
                'type' => QuestionType::Unique,
                'why_question' => 'Selon le type de vaccin, un délai peut être nécessaire : pour éviter de transmettre un virus vivant atténué au receveur, ou simplement pour s\'assurer que tu ne ressens aucun effet secondaire. Dans la plupart des cas, la vaccination n\'empêche pas le don.',
                'order' => 10,
                'choices' => [
                    ['text' => 'NON (aucun vaccin récent)',                                                          'eligible' => true,  'ineligibility_days' => null, 'order' => 1],
                    ['text' => 'oui (vaccin vivant atténué comme ROR, varicelle, fièvre jaune…) il y a moins de 4 mois', 'eligible' => false, 'ineligibility_days' => 28, 'order' => 2],
                    ['text' => 'oui (vaccin inactivé/ARNm comme grippe, COVID, hépatite, tétanos…) il y a moins de 48h', 'eligible' => false, 'ineligibility_days' => 2,  'order' => 3],
                    ['text' => 'oui, il y a moins de 4 mois (mais je ne sais pas lequel)',                          'eligible' => false, 'ineligibility_days' => null, 'view_id' => $viewVaccin->id, 'order' => 4],
                ],
            ],
        ];

        foreach ($questions as $questionData) {
            $choices = $questionData['choices'];
            unset($questionData['choices']);

            $question = GameQuestion::create($questionData);

            foreach ($choices as $choiceData) {
                $question->choices()->create($choiceData);
            }
        }
    }
}
