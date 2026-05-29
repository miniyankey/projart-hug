<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collect;
use App\Models\Company;
use App\Models\Place;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CollectController extends Controller
{
    /**
     * Display a listing of all collects.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Collectes/Index', [
            'collects' => Collect::with(['company', 'place'])->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new collect.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Collectes/Create', [
            'companies' => Company::orderBy('name')->get(['id', 'name']),
            'places' => Place::orderBy('name')->get(['id', 'name', 'city']),
        ]);
    }

    /**
     * Store a newly created collect in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'place_id' => ['required', 'exists:places,id'],
            'day' => ['required', 'date'],
            'link_appointment' => ['nullable', 'url'],
            'is_active' => ['boolean'],
        ]);

        Collect::create($validated);

        return redirect()->route('admin.collectes.index')
            ->with('success', 'flash.collect_created');
    }

    /**
     * Show the form for editing the specified collect.
     */
    public function edit(Collect $collecte): Response
    {
        return Inertia::render('Admin/Collectes/Edit', [
            'collect' => $collecte->load(['company', 'place']),
            'companies' => Company::orderBy('name')->get(['id', 'name']),
            'places' => Place::orderBy('name')->get(['id', 'name', 'city']),
        ]);
    }

    /**
     * Update the specified collect in storage.
     */
    public function update(Request $request, Collect $collecte): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'place_id' => ['required', 'exists:places,id'],
            'day' => ['required', 'date'],
            'link_appointment' => ['nullable', 'url'],
            'is_active' => ['boolean'],
        ]);

        $collecte->update($validated);

        return redirect()->route('admin.collectes.index')
            ->with('success', 'flash.collect_updated');
    }

    /**
     * Remove the specified collect from storage.
     */
    public function destroy(Collect $collecte): RedirectResponse
    {
        $collecte->delete();

        return redirect()->route('admin.collectes.index')
            ->with('success', 'flash.collect_deleted');
    }
}
