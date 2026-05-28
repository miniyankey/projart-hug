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
            'trophees' => Trophee::with('company')->orderBy('year_of', 'desc')->get(),
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
            'name' => ['required', 'string', 'max:255'],
            'year_of' => [
                'required',
                'integer',
                'min:2000',
                'max:'.now()->year,
                Rule::unique('trophees')->where('company_id', $request->company_id),
            ],
            'description' => ['nullable', 'string'],
        ]);

        Trophee::create($request->validated());

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
            'name' => ['required', 'string', 'max:255'],
            'year_of' => [
                'required',
                'integer',
                'min:2000',
                'max:'.now()->year,
                Rule::unique('trophees')->where('company_id', $request->company_id)->ignore($vainqueur->id),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $vainqueur->update($request->validated());

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
