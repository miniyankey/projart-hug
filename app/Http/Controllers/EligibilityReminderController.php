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

        $reminder = EligibilityReminder::create([
            'email' => $validated['email'],
            'locale' => $locale,
            'collect_id' => $validated['collect_id'] ?? null,
            'eligible_at' => now()->addDays($validated['days'])->toDateString(),
        ]);

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

        // Un rappel déjà envoyé ne doit plus bouger.
        if ($reminder->sent_at === null) {
            $reminder->update([
                'eligible_at' => now()->addDays($validated['days'])->toDateString(),
            ]);
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
