<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collect;
use App\Models\Company;
// lib pour manipulation des dates, utilisée notamment pour la timeline et le statut des collectes
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class KpiController extends Controller
{
    /**
     * Fenêtre de l'évolution temporelle, en jours.
     */
    private const TIMELINE_DAYS = 30;

    /**
     * Vue globale : KPI agrégés sur l'ensemble des collectes.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Kpi/Index', [
            'companies' => $this->companyStats(),
            'collects' => $this->collectStats(),
            'funnel' => $this->funnelStats(),
            'appointments' => $this->appointmentStats(),
            'contact' => $this->contactStats(),
            'leadTime' => $this->leadTimeStats(),
            'timeline' => $this->timeline(),
            'perCompany' => $this->perCompanyStats(),
            'perCollect' => $this->perCollectStats(),
        ]);
    }

    /**
     * Vue par entreprise : mêmes métriques filtrées sur ses collectes.
     *
     * Le formulaire de contact n'est pas rattaché à une collecte (pas de
     * `collect_id`) : ces conversions restent globales et sont donc omises ici.
     */
    public function show(Company $company): Response
    {
        $collectIds = $company->collects()->pluck('id')->all();

        return Inertia::render('Admin/Kpi/Show', [
            'company' => [
                'name' => $company->name,
                'token' => $company->token,
                'color' => $company->color,
                'logo_url' => $company->logo_url,
                'collects_total' => count($collectIds),
                'collects_active' => $company->collects()->where('is_active', true)->count(),
            ],
            'funnel' => $this->funnelStats($collectIds),
            'appointments' => $this->appointmentStats($collectIds),
            'leadTime' => $this->leadTimeStats($collectIds),
            'timeline' => $this->timeline($collectIds),
            'perCollect' => $this->perCollectStats($collectIds),
        ]);
    }

    /**
     * Compteurs d'entreprises : total, labellisées, actives (≥ 1 collecte active).
     *
     * @return array{total: int, labelled: int, active: int}
     */
    private function companyStats(): array
    {
        return [
            'total' => Company::count(),
            'labelled' => Company::where('is_labelled', true)->count(),
            'active' => Company::whereHas('collects', function ($query) {
                $query->where('is_active', true);
            })->count(),
        ];
    }

    /**
     * @return array{total: int, active: int}
     */
    private function collectStats(): array
    {
        $counts = DB::table('collects')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active')
            ->first();

        return [
            'total' => (int) $counts->total,
            'active' => (int) $counts->active,
        ];
    }

    /**
     * Funnel du jeu d'éligibilité : sessions par étape (décroissant), parcours
     * complétés, abandons et répartition éligible / inéligible / non terminé.
     *
     * @param  list<int>|null  $collectIds
     * @return array{
     *     sessions: int,
     *     steps: list<array{step: int, sessions: int}>,
     *     total_steps: int,
     *     completed: int,
     *     abandoned: int,
     *     eligible: int,
     *     ineligible: int,
     *     completion_rate: float,
     *     dropoff: array{from: int|null, to: int|null, lost: int, rate: float}
     * }
     */
    private function funnelStats(?array $collectIds = null): array
    {
        // Le quiz vit côté front (resources/js/data/eligibilityQuiz.js)
        $totalSteps = max(
            (int) DB::table('events_eligibilite_steps')->max('step'),
            1,
        );

        $steps = DB::table('events_eligibilite_steps')
            ->when($collectIds !== null, fn ($query) => $query->whereIn('collect_id', $collectIds))
            ->select('step', DB::raw('COUNT(DISTINCT session_id) as sessions'))
            ->groupBy('step')
            ->orderBy('step')
            ->get()
            ->map(fn ($row) => [
                'step' => (int) $row->step,
                'sessions' => (int) $row->sessions,
            ])
            ->all();

        $results = DB::table('events_eligibilite_steps')
            ->when($collectIds !== null, fn ($query) => $query->whereIn('collect_id', $collectIds))
            ->selectRaw('COUNT(DISTINCT session_id) as sessions')
            ->selectRaw('COUNT(DISTINCT CASE WHEN completed = 1 THEN session_id END) as completed')
            ->selectRaw("COUNT(DISTINCT CASE WHEN result = 'eligible' THEN session_id END) as eligible")
            ->selectRaw("COUNT(DISTINCT CASE WHEN result = 'ineligible' THEN session_id END) as ineligible")
            ->first();

        $sessions = (int) $results->sessions;
        $completed = (int) $results->completed;

        return [
            'sessions' => $sessions,
            'steps' => $steps,
            'total_steps' => $totalSteps,
            'completed' => $completed,
            'abandoned' => max($sessions - $completed, 0),
            'eligible' => (int) $results->eligible,
            'ineligible' => (int) $results->ineligible,
            'completion_rate' => $this->rate($completed, $sessions),
            'dropoff' => $this->biggestDropoff($steps),
        ];
    }

    /**
     * Identifie la plus forte chute entre deux étapes consécutives du funnel
     *
     * @param  list<array{step: int, sessions: int}>  $steps
     * @return array{from: int|null, to: int|null, lost: int, rate: float}
     */
    private function biggestDropoff(array $steps): array
    {
        $dropoff = ['from' => null, 'to' => null, 'lost' => 0, 'rate' => 0.0];

        for ($i = 1; $i < count($steps); $i++) {
            $lost = $steps[$i - 1]['sessions'] - $steps[$i]['sessions'];

            if ($lost > $dropoff['lost']) {
                $dropoff = [
                    'from' => $steps[$i - 1]['step'],
                    'to' => $steps[$i]['step'],
                    'lost' => $lost,
                    'rate' => $this->rate($lost, $steps[$i - 1]['sessions']),
                ];
            }
        }

        return $dropoff;
    }

    /**
     * Conversion vers la prise de RDV : sessions actives, clics, source et part
     * des clics provenant du jeu d'éligibilité.
     *
     * @param  list<int>|null  $collectIds
     * @return array{
     *     sessions: int,
     *     clicks: int,
     *     rate: float,
     *     game_share: float,
     *     sources: array{collecte: int, jeu: int}
     * }
     */
    private function appointmentStats(?array $collectIds = null): array
    {
        $totals = DB::table('events_conversions')
            ->when($collectIds !== null, fn ($query) => $query->whereIn('collect_id', $collectIds))
            ->selectRaw('COUNT(DISTINCT session_id) as sessions')
            ->selectRaw('COUNT(DISTINCT CASE WHEN appointment_click = 1 THEN session_id END) as clicks')
            ->first();

        $sources = DB::table('events_conversions')
            ->when($collectIds !== null, fn ($query) => $query->whereIn('collect_id', $collectIds))
            ->where('appointment_click', true)
            ->select('source', DB::raw('COUNT(*) as total'))
            ->groupBy('source')
            ->pluck('total', 'source');

        $sessions = (int) $totals->sessions;
        $clicks = (int) $totals->clicks;
        $fromCollecte = (int) ($sources['collecte'] ?? 0);
        $fromGame = (int) ($sources['jeu'] ?? 0);

        return [
            'sessions' => $sessions,
            'clicks' => $clicks,
            'rate' => $this->rate($clicks, $sessions),
            'game_share' => $this->rate($fromGame, $fromCollecte + $fromGame),
            'sources' => [
                'collecte' => $fromCollecte,
                'jeu' => $fromGame,
            ],
        ];
    }

    /**
     * Avance de réservation : délai (en jours) entre le clic vers la prise de
     * RDV et la date réelle de la collecte. Moyenne + distribution par paliers.
     * Un délai négatif signifie un clic après la date de la collecte.
     *
     * @param  list<int>|null  $collectIds
     * @return array{average: float, total: int, buckets: list<int>}
     */
    private function leadTimeStats(?array $collectIds = null): array
    {
        $delay = 'DATEDIFF(c.day, DATE(ec.created_at))';

        $row = DB::table('events_conversions as ec')
            ->join('collects as c', 'c.id', '=', 'ec.collect_id')
            ->where('ec.appointment_click', true)
            ->when($collectIds !== null, fn ($query) => $query->whereIn('ec.collect_id', $collectIds))
            ->selectRaw("AVG({$delay}) as average")
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN {$delay} <= 0 THEN 1 ELSE 0 END) as bucket_0")
            ->selectRaw("SUM(CASE WHEN {$delay} BETWEEN 1 AND 7 THEN 1 ELSE 0 END) as bucket_1")
            ->selectRaw("SUM(CASE WHEN {$delay} BETWEEN 8 AND 14 THEN 1 ELSE 0 END) as bucket_2")
            ->selectRaw("SUM(CASE WHEN {$delay} BETWEEN 15 AND 30 THEN 1 ELSE 0 END) as bucket_3")
            ->selectRaw("SUM(CASE WHEN {$delay} > 30 THEN 1 ELSE 0 END) as bucket_4")
            ->first();

        return [
            'average' => round((float) ($row->average ?? 0), 1),
            'total' => (int) $row->total,
            'buckets' => [
                (int) $row->bucket_0,
                (int) $row->bucket_1,
                (int) $row->bucket_2,
                (int) $row->bucket_3,
                (int) $row->bucket_4,
            ],
        ];
    }

    /**
     * Conversions du formulaire de contact / demande de collecte, dont la part
     * exprimant un intérêt pour le Trophée.
     *
     * @return array{
     *     clicks: int,
     *     sent: int,
     *     rate: float,
     *     trophee: int,
     *     trophee_rate: float,
     *     by_type: array{contact: int, collect_request: int}
     * }
     */
    private function contactStats(): array
    {
        $totals = DB::table('contact_form_conversions')
            ->selectRaw('COUNT(DISTINCT CASE WHEN form_click = 1 THEN session_id END) as clicks')
            ->selectRaw('COUNT(DISTINCT CASE WHEN form_sent = 1 THEN session_id END) as sent')
            ->selectRaw('COUNT(DISTINCT CASE WHEN trophee_participation = 1 THEN session_id END) as trophee')
            ->first();

        $byType = DB::table('contact_form_conversions')
            ->where('form_sent', true)
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $clicks = (int) $totals->clicks;
        $sent = (int) $totals->sent;
        $trophee = (int) $totals->trophee;

        return [
            'clicks' => $clicks,
            'sent' => $sent,
            'rate' => $this->rate($sent, $clicks),
            'trophee' => $trophee,
            'trophee_rate' => $this->rate($trophee, $sent),
            'by_type' => [
                'contact' => (int) ($byType['contact'] ?? 0),
                'collect_request' => (int) ($byType['collect_request'] ?? 0),
            ],
        ];
    }

    /**
     * Évolution sur les N derniers jours : clics RDV et sessions de jeu par jour.
     *
     * @param  list<int>|null  $collectIds
     * @return list<array{date: string, clicks: int, sessions: int}>
     */
    private function timeline(?array $collectIds = null): array
    {
        $since = Carbon::today()->subDays(self::TIMELINE_DAYS - 1);

        $clicksByDay = DB::table('events_conversions')
            ->when($collectIds !== null, fn ($query) => $query->whereIn('collect_id', $collectIds))
            ->where('appointment_click', true)
            ->where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $sessionsByDay = DB::table('events_eligibilite_steps')
            ->when($collectIds !== null, fn ($query) => $query->whereIn('collect_id', $collectIds))
            ->where('step', 1)
            ->where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as day, COUNT(DISTINCT session_id) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $timeline = [];
        for ($offset = 0; $offset < self::TIMELINE_DAYS; $offset++) {
            $key = $since->copy()->addDays($offset)->toDateString();
            $timeline[] = [
                'date' => $key,
                'clicks' => (int) ($clicksByDay[$key] ?? 0),
                'sessions' => (int) ($sessionsByDay[$key] ?? 0),
            ];
        }

        return $timeline;
    }

    /**
     * Tableau récapitulatif par entreprise (sessions, clics RDV, taux), trié
     * par nom. Une seule requête agrégée.
     *
     * @return list<array{name: string, token: string, sessions: int, clicks: int, rate: float}>
     */
    private function perCompanyStats(): array
    {
        $events = DB::table('events_conversions as ec')
            ->join('collects as c', 'c.id', '=', 'ec.collect_id')
            ->selectRaw('c.company_id as company_id')
            ->selectRaw('COUNT(DISTINCT ec.session_id) as sessions')
            ->selectRaw('COUNT(DISTINCT CASE WHEN ec.appointment_click = 1 THEN ec.session_id END) as clicks')
            ->groupBy('c.company_id')
            ->get()
            ->keyBy('company_id');

        return Company::orderBy('name')
            ->get(['id', 'name', 'token'])
            ->map(function (Company $company) use ($events) {
                $row = $events->get($company->id);
                $sessions = (int) ($row->sessions ?? 0);
                $clicks = (int) ($row->clicks ?? 0);

                return [
                    'name' => $company->name,
                    'token' => $company->token,
                    'sessions' => $sessions,
                    'clicks' => $clicks,
                    'rate' => $this->rate($clicks, $sessions),
                ];
            })
            ->all();
    }

    /**
     * Détail par collecte (évite de mélanger plusieurs collectes d'une même
     * entreprise). Une requête agrégée + chargement des relations en lot.
     *
     * @param  list<int>|null  $collectIds
     * @return list<array{
     *     company: string|null,
     *     day: string|null,
     *     city: string|null,
     *     status: 'active'|'upcoming'|'past',
     *     sessions: int,
     *     clicks: int,
     *     rate: float
     * }>
     */
    private function perCollectStats(?array $collectIds = null): array
    {
        $events = DB::table('events_conversions')
            ->when($collectIds !== null, fn ($query) => $query->whereIn('collect_id', $collectIds))
            ->whereNotNull('collect_id')
            ->select('collect_id')
            ->selectRaw('COUNT(DISTINCT session_id) as sessions')
            ->selectRaw('COUNT(DISTINCT CASE WHEN appointment_click = 1 THEN session_id END) as clicks')
            ->groupBy('collect_id')
            ->get()
            ->keyBy('collect_id');

        $today = Carbon::today();

        $rows = Collect::with(['company:id,name', 'place:id,city'])
            ->when($collectIds !== null, fn ($query) => $query->whereIn('id', $collectIds))
            ->get()
            ->map(function (Collect $collect) use ($events, $today) {
                $row = $events->get($collect->id);
                $sessions = (int) ($row->sessions ?? 0);
                $clicks = (int) ($row->clicks ?? 0);

                return [
                    'company' => $collect->company?->name,
                    'day' => $collect->day?->format('Y-m-d'),
                    'city' => $collect->place?->city,
                    'status' => $this->collectStatus($collect, $today),
                    'sessions' => $sessions,
                    'clicks' => $clicks,
                    'rate' => $this->rate($clicks, $sessions),
                ];
            })
            ->all();

        // Tri par statut (en cours → à venir → terminée). Dans un groupe, les
        // collectes à venir/en cours sont triées de la plus proche à la plus
        // lointaine ; les terminées, de la plus récente à la plus ancienne.
        $rank = ['active' => 0, 'upcoming' => 1, 'past' => 2];

        usort($rows, function (array $a, array $b) use ($rank): int {
            if ($rank[$a['status']] !== $rank[$b['status']]) {
                return $rank[$a['status']] <=> $rank[$b['status']];
            }

            return $a['status'] === 'past'
                ? strcmp((string) $b['day'], (string) $a['day'])
                : strcmp((string) $a['day'], (string) $b['day']);
        });

        return $rows;
    }

    /**
     * Statut d'une collecte : active (mise en avant), à venir (date future non
     * active) ou terminée (date passée). `is_active` est un flag métier (collecte
     * promue), pas un indicateur « terminée », d'où la prise en compte de la date.
     *
     * @return 'active'|'upcoming'|'past'
     */
    private function collectStatus(Collect $collect, Carbon $today): string
    {
        if ($collect->is_active) {
            return 'active';
        }

        return $collect->day && $collect->day->gte($today) ? 'upcoming' : 'past';
    }

    /**
     * Taux en pourcentage (0-100, une décimale), 0 si le dénominateur est nul.
     */
    private function rate(int $numerator, int $denominator): float
    {
        if ($denominator === 0) {
            return 0.0;
        }

        return round($numerator / $denominator * 100, 1);
    }
}
