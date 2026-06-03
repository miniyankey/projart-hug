<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class EligibiliteController extends Controller
{
    /**
     * Jeu d'éligibilité en mode public (site non co-brandé).
     *
     * Le quiz est entièrement défini côté front (data/eligibilityQuiz.js +
     * locales). Sans entreprise, la couleur de marque retombe sur le défaut.
     */
    public function index(): Response
    {
        return Inertia::render('CoBranded/Jeu');
    }
}
