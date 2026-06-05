<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Trophee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class WinnerController extends Controller
{
    /**
     * Display a listing of all trophy winners.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Vainqueurs/Index', [
            'trophees' => Trophee::with('company')
                ->orderByDesc('year_of')
                ->orderBy('rank')
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new trophy winner.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Vainqueurs/Create', [
            'companies' => Company::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created trophy winner in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'year_of' => ['required', 'integer', 'min:2008', 'max:'.now()->year],
            'rank' => [
                'required',
                'integer',
                'min:1',
                'max:3',
                Rule::unique('trophees')->where('year_of', $request->year_of),
            ],
            'description' => ['nullable', 'string'],
        ]);

        Trophee::create($request->only('company_id', 'year_of', 'rank', 'description'));

        return redirect()->route('admin.vainqueurs.index')
            ->with('success', 'flash.winner_created');
    }

    /**
     * Show the form for editing the specified trophy winner.
     */
    public function edit(Trophee $vainqueur): Response
    {
        return Inertia::render('Admin/Vainqueurs/Edit', [
            'trophee' => $vainqueur->load('company'),
            'companies' => Company::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified trophy winner in storage.
     */
    public function update(Request $request, Trophee $vainqueur): RedirectResponse
    {
        $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'year_of' => ['required', 'integer', 'min:2008', 'max:'.now()->year],
            'rank' => [
                'required',
                'integer',
                'min:1',
                'max:3',
                Rule::unique('trophees')->where('year_of', $request->year_of)->ignore($vainqueur->id),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $vainqueur->update($request->only('company_id', 'year_of', 'rank', 'description'));

        return redirect()->route('admin.vainqueurs.index')
            ->with('success', 'flash.winner_updated');
    }

    /**
     * Remove the specified trophy winner from storage.
     */
    public function destroy(Trophee $vainqueur): RedirectResponse
    {
        $vainqueur->delete();

        return redirect()->route('admin.vainqueurs.index')
            ->with('success', 'flash.winner_deleted');
    }
}
