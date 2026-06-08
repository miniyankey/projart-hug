<?php

namespace App\Http\Controllers;

use App\Models\EligibilityReminder;
use App\Services\BrevoService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EligibilityReminderController extends Controller
{
    public function __construct(private BrevoService $brevo) {}

    /**
     * Langues acceptées pour la programmation du rappel.
     *
     * @var list<string>
     */
    private const SUPPORTED_LOCALES = ['fr', 'en'];

    /**
     * Enregistre une demande de rappel d'éligibilité.
     *
     * Le quiz vit côté front (resources/js/data/eligibilityQuiz.js), source de
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'collect_id' => ['nullable', 'exists:collects,id'],
            'days' => ['required', 'integer', 'min:1', 'max:730'], // borne anti-abus
            'newsletter' => ['boolean'],
        ]);

        $locale = $this->resolveLocale($request);

        $newEligibleAt = now()->addDays($validated['days'])->startOfDay();

        // Une seule demande par e-mail (contrainte unique) : un visiteur qui
        // rejoue met à jour sa demande au lieu de créer un doublon.
        $reminder = EligibilityReminder::firstOrNew([
            'email' => $validated['email'],
        ]);

        // On garde TOUJOURS la date d'éligibilité la plus lointaine : si une
        // demande non encore envoyée existe déjà avec un délai plus long, on la
        // conserve (ne jamais prévenir la personne trop tôt). Un rappel déjà
        // envoyé repart sur le nouveau délai.
        $keepExisting = $reminder->exists
            && $reminder->sent_at === null
            && $reminder->eligible_at !== null
            && $reminder->eligible_at->greaterThanOrEqualTo($newEligibleAt);

        $reminder->locale = $locale;

        if (! $keepExisting) {
            $reminder->collect_id = $validated['collect_id'] ?? null;
            $reminder->eligible_at = $newEligibleAt->toDateString();
        }

        $reminder->sent_at = null;
        $reminder->save();

        // Inscription newsletter optionnelle : secondaire, ne doit jamais faire
        // échouer le rappel si Brevo est indisponible.
        if ($request->boolean('newsletter')) {
            try {
                $this->brevo->subscribe($validated['email'], $locale);
            } catch (RequestException $e) {
                report($e);
            }
        }

        return redirect()->back()->with([
            'success' => 'flash.reminder_scheduled',
            'reminder_id' => $reminder->id,
        ]);
    }

    /**
     * Met à jour la date d'éligibilité d'un rappel déjà enregistré.
     *
     * Appelé en cours de jeu quand une réponse ultérieure change la durée
     * d'inéligibilité maximale : la date est recalculée pour que le visiteur
     * soit prévenu au bon moment. Endpoint « fire-and-forget » (204).
     */
    public function update(Request $request, EligibilityReminder $reminder): Response
    {
        $validated = $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:730'],
        ]);

        // Un rappel déjà envoyé ne doit plus bouger. Sinon on ne recale la date
        // que si le nouveau délai est PLUS LONG : on garde toujours la durée
        // d'inéligibilité maximale, jamais une date plus proche.
        if ($reminder->sent_at === null) {
            $newEligibleAt = now()->addDays($validated['days'])->startOfDay();

            if (
                $reminder->eligible_at === null
                || $newEligibleAt->greaterThan($reminder->eligible_at)
            ) {
                $reminder->update([
                    'eligible_at' => $newEligibleAt->toDateString(),
                ]);
            }
        }

        return response()->noContent();
    }

    /**
     * Locale du visiteur, lue depuis le cookie `locale` (fallback projet).
     */
    private function resolveLocale(Request $request): string
    {
        $locale = $request->cookie('locale');

        return in_array($locale, self::SUPPORTED_LOCALES, true)
            ? $locale
            : config('app.locale');
    }
}
