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
            'companies' => Company::orderBy('name')->get(['id', 'name', 'color', 'logo']),
            'places' => Place::orderBy('name')->get(['id', 'name', 'city']),
        ]);
    }

    /**
     * Store a newly created collect in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $collect = Collect::create($this->resolveCollectAttributes($this->validateCollect($request)));
        $this->enforceSingleActiveCollect($collect);

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
            'companies' => Company::orderBy('name')->get(['id', 'name', 'color', 'logo']),
            'places' => Place::orderBy('name')->get(['id', 'name', 'city']),
        ]);
    }

    /**
     * Update the specified collect in storage.
     */
    public function update(Request $request, Collect $collecte): RedirectResponse
    {
        $collecte->update($this->resolveCollectAttributes($this->validateCollect($request)));
        $this->enforceSingleActiveCollect($collecte);

        return redirect()->route('admin.collectes.index')
            ->with('success', 'flash.collect_updated');
    }

    /**
     * Validate a collect payload, including an existing place or a new one.
     *
     * @return array<string, mixed>
     */
    private function validateCollect(Request $request): array
    {
        return $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'place_mode' => ['required', 'in:existing,new'],
            'place_id' => ['nullable', 'required_if:place_mode,existing', 'exists:places,id'],
            'place.name' => ['nullable', 'required_if:place_mode,new', 'string', 'max:255'],
            'place.address' => ['nullable', 'required_if:place_mode,new', 'string', 'max:255'],
            'place.locality' => ['nullable', 'required_if:place_mode,new', 'integer'],
            'place.city' => ['nullable', 'required_if:place_mode,new', 'string', 'max:255'],
            'place.room' => ['nullable', 'string', 'max:255'],
            'day' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'link_appointment' => ['nullable', 'url'],
            'is_active' => ['boolean'],
        ]);
    }

    /**
     * Build the collect attributes, creating the place first if a new one was submitted.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function resolveCollectAttributes(array $validated): array
    {
        $placeId = $validated['place_mode'] === 'new'
            ? Place::create($validated['place'])->id
            : $validated['place_id'];

        return [
            'company_id' => $validated['company_id'],
            'place_id' => $placeId,
            'day' => $validated['day'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'link_appointment' => $validated['link_appointment'] ?? null,
            'is_active' => $validated['is_active'] ?? false,
        ];
    }

    /**
     * Ensure a company exposes a single active collect at a time.
     *
     * When the saved collect is active, every other active collect of the same
     * company is deactivated, so the co-branded link always resolves to it
     * that might be improved in the future, depending if a company has multiple collects in the same time window or not
     */
    private function enforceSingleActiveCollect(Collect $collect): void
    {
        if (! $collect->is_active) {
            return;
        }

        Collect::where('company_id', $collect->company_id)
            ->whereKeyNot($collect->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);
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
