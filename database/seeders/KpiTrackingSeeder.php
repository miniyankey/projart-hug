<?php

namespace Database\Seeders;

use App\Enums\FormSubmissionType;
use App\Models\Collect;
use App\Models\ContactFormConversion;
use App\Models\EventsConversion;
use App\Models\EventsEligibiliteStep;
use App\Models\GameQuestion;
use Carbon\CarbonInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Données de démonstration pour les KPI : sessions anonymes, funnel
 * d'éligibilité et conversions. Volontairement généré sans le helper fake(),
 * uniquement avec random_int()/Str::random() et des listes de valeurs.
 *
 * Les created_at sont étalés sur les 30 derniers jours pour alimenter le KPI
 * d'évolution temporelle.
 */
class KpiTrackingSeeder extends Seeder
{
    /**
     * Fenêtre d'étalement des événements, en jours.
     */
    private const SPREAD_DAYS = 30;

    public function run(): void
    {
        // Repart d'une base propre pour rester idempotent (re-seed sans doublon).
        EventsEligibiliteStep::query()->delete();
        EventsConversion::query()->delete();
        ContactFormConversion::query()->delete();

        $collects = Collect::all();
        $totalSteps = GameQuestion::count() ?: 10;

        if ($collects->isEmpty()) {
            return;
        }

        foreach ($collects as $collect) {
            $this->seedCollect($collect, $totalSteps);
        }

        $this->seedContactForm();
    }

    /**
     * Visites + clics RDV + parcours du jeu d'éligibilité pour une collecte.
     */
    private function seedCollect(Collect $collect, int $totalSteps): void
    {
        $visitors = random_int(40, 110);

        for ($i = 0; $i < $visitors; $i++) {
            $sessionId = Str::random(40);
            $date = $this->randomDate();

            // events_conversions : tout visiteur crée une ligne ; une partie
            // clique ensuite vers la prise de RDV (conversion).
            $clicked = $this->chance(28);

            $this->insertTimestamped(new EventsConversion([
                'session_id' => $sessionId,
                'collect_id' => $collect->id,
                'appointment_click' => $clicked,
                'source' => $clicked ? $this->pick(['collecte', 'jeu']) : null,
            ]), $date);

            // ~55 % des visiteurs lancent le jeu d'éligibilité, avec un
            // abandon progressif d'une étape à l'autre.
            if ($this->chance(55)) {
                $this->seedEligibiliteFunnel($sessionId, $collect->id, $totalSteps, $date);
            }
        }
    }

    /**
     * Parcours d'un visiteur dans le jeu : une ligne par étape atteinte, avec
     * décroissance (80 % de chance de passer à l'étape suivante).
     */
    private function seedEligibiliteFunnel(string $sessionId, int $collectId, int $totalSteps, CarbonInterface $date): void
    {
        $maxStep = 1;
        while ($maxStep < $totalSteps && $this->chance(80)) {
            $maxStep++;
        }

        for ($step = 1; $step <= $maxStep; $step++) {
            $completed = $step === $totalSteps;

            $this->insertTimestamped(new EventsEligibiliteStep([
                'session_id' => $sessionId,
                'collect_id' => $collectId,
                'step' => $step,
                'result' => $completed ? $this->pick(['eligible', 'ineligible']) : null,
                'completed' => $completed,
            ]), $date);
        }
    }

    /**
     * Conversions du formulaire de contact / demande de collecte (non liées à
     * une collecte précise).
     */
    private function seedContactForm(): void
    {
        $sessions = random_int(50, 90);

        for ($i = 0; $i < $sessions; $i++) {
            $isCollectRequest = $this->chance(60);
            $type = $isCollectRequest
                ? FormSubmissionType::CollectRequest->value
                : FormSubmissionType::Contact->value;

            $this->insertTimestamped(new ContactFormConversion([
                'session_id' => Str::random(40),
                'type' => $type,
                // Toute ligne traduit au moins une interaction (form_click) ;
                // une partie aboutit à un envoi.
                'form_click' => true,
                'form_sent' => $this->chance(45),
                'trophee_participation' => $isCollectRequest && $this->chance(40),
            ]), $this->randomDate());
        }
    }

    /**
     * Persiste un modèle en forçant ses timestamps (back-dating). Régler
     * created_at directement le rend « dirty », donc Eloquent ne l'écrase pas.
     */
    private function insertTimestamped(object $model, CarbonInterface $date): void
    {
        $model->created_at = $date;
        $model->updated_at = $date;
        $model->save();
    }

    /**
     * Date aléatoire dans la fenêtre d'étalement.
     */
    private function randomDate(): CarbonInterface
    {
        return now()
            ->subDays(random_int(0, self::SPREAD_DAYS))
            ->subMinutes(random_int(0, 1439));
    }

    /**
     * Vrai avec la probabilité donnée (en pourcentage).
     */
    private function chance(int $percent): bool
    {
        return random_int(1, 100) <= $percent;
    }

    /**
     * Renvoie une valeur au hasard parmi la liste.
     *
     * @param  array<int, string>  $values
     */
    private function pick(array $values): string
    {
        return $values[array_rand($values)];
    }
}
