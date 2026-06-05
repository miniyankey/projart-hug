<?php

namespace App\Http\Controllers;

use App\Models\Trophee;
use Inertia\Inertia;
use Inertia\Response;

class WinnerController extends Controller
{
    /**
     * Display the public trophy winners page.
     */
    public function index(): Response
    {
        // Regroupe les trophées par édition (année) et expose chaque podium
        $editions = Trophee::with('company')
            ->orderByDesc('year_of')
            ->orderBy('rank')
            ->get()
            ->groupBy('year_of')
            ->map(fn ($trophees, $year) => [
                'year' => (int) $year,
                'winners' => $trophees->map(fn (Trophee $trophee) => [
                    'rank' => $trophee->rank,
                    'logo' => $trophee->company->logo_url,
                    'name' => $trophee->company->name,
                ])->values(),
            ])
            ->values();

        return Inertia::render('Trophee', [
            'editions' => $editions,
        ]);
    }
}
