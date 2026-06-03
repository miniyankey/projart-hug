<?php

namespace App\Http\Controllers;

use App\Models\EligibilityReminder;
use App\Models\GameChoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

// ce controller risque de changer en fonction de la base de données !!!
class EligibilityReminderController extends Controller
{
    /**
     * Langues acceptées pour la programmation du rappel.
     *
     * @var list<string>
     */
    private const SUPPORTED_LOCALES = ['fr', 'en'];

    /**
     * Enregistre une demande de rappel d'éligibilité.
     *
     * La date de ré-éligibilité est calculée côté serveur à partir des choix
     * sélectionnés dans le jeu (`game_choices.ineligibility_days`), jamais à
     * partir d'une valeur envoyée par le client.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'collect_id' => ['nullable', 'exists:collects,id'],
            'choice_ids' => ['required', 'array', 'min:1'],
            'choice_ids.*' => ['integer', 'exists:game_choices,id'],
        ]);

        $days = GameChoice::whereIn('id', $validated['choice_ids'])
            ->whereNotNull('ineligibility_days')
            ->pluck('ineligibility_days');

        // Inéligible à vie : aucun rappel possible
        if ($days->contains(-1)) {
            return redirect()->back()->with('success', 'flash.reminder_lifetime');
        }

        $maxDays = (int) $days->filter(fn (int $value): bool => $value > 0)->max();

        // Déjà éligible : pas de rappel à programmer
        if ($maxDays <= 0) {
            return redirect()->back()->with('success', 'flash.reminder_already_eligible');
        }

        EligibilityReminder::create([
            'email' => $validated['email'],
            'locale' => $this->resolveLocale($request),
            'collect_id' => $validated['collect_id'] ?? null,
            'eligible_at' => now()->addDays($maxDays)->toDateString(),
        ]);

        return redirect()->back()->with('success', 'flash.reminder_scheduled');
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
