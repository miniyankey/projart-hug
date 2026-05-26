<?php

namespace App\Http\Controllers;

use App\Models\Collect;
use App\Models\Company;
use Inertia\Inertia;
use Inertia\Response;

class CollectController extends Controller
{
    /**
     * Display the public co-branded collect page.
     */
    public function show(Company $company, Collect $collect): Response
    {
        abort_if(! $collect->is_active, 404);

        $collect->load(['place', 'eligibiliteSteps']);

        return Inertia::render('CoBranded/Collecte', [
            'company' => $company,
            'collect' => $collect,
        ]);
    }
}
