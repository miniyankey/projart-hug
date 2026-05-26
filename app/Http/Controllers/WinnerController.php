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
        $trophees = Trophee::with('company')
            ->orderBy('year_of', 'desc')
            ->get()
            ->groupBy('year_of');

        return Inertia::render('Trophee', [
            'trophees' => $trophees,
        ]);
    }
}
