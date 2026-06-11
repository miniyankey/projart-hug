<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CollectCreatedMail;
use App\Models\Collect;
use App\Models\Company;
use App\Models\Place;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class CollectController extends Controller
{
    /**
     * Display a listing of all collects.
     */
    public function index(): Response
    {
        // En cours d'abord (prochaine date en tête), puis terminées (la plus récente en tête).
        $collects = Collect::with(['company', 'place'])->get();

        return Inertia::render('Admin/Collectes/Index', [
            'collects' => $collects->filter->isOngoing()->sortBy('day')
                ->concat($collects->reject->isOngoing()->sortByDesc('day'))
                ->values(),
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

        // Une collecte en cours expose immédiatement le lien co-brandé : on en informe le contact.
        if ($collect->isOngoing()) {
            $this->notifyCompanyContact($collect);
        }

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
        $collecte->update($this->resolveCollectAttributes($this->validateCollect($request, $collecte)));

        return redirect()->route('admin.collectes.index')
            ->with('success', 'flash.collect_updated');
    }

    /**
     * Envoie au contact de l'entreprise le lien co-brandé de la collecte.
     *
     * Un échec SMTP ne doit pas faire échouer la requête : la collecte est
     * déjà enregistrée, on logge et l'admin pourra prévenir le contact
     * manuellement.
     */
    private function notifyCompanyContact(Collect $collect): void
    {
        $collect->loadMissing(['company', 'place']);

        if (! $collect->company?->email_contact) {
            return;
        }

        try {
            Mail::to($collect->company->email_contact)->send(new CollectCreatedMail($collect));
        } catch (Throwable $exception) {
            Log::error('Échec de l\'envoi du mail de collecte au contact entreprise.', [
                'collect_id' => $collect->id,
                'email' => $collect->company->email_contact,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Validate a collect payload, including an existing place or a new one.
     *
     * A collect cannot be scheduled in the past. On update, the rule only
     * applies when the day actually changes, so a collect whose day is
     * already past stays editable (link, place, activation...).
     *
     * @return array<string, mixed>
     */
    private function validateCollect(Request $request, ?Collect $collecte = null): array
    {
        $dayRules = ['required', 'date'];

        if ($collecte === null || $request->input('day') !== $collecte->day?->toDateString()) {
            $dayRules[] = 'after_or_equal:today';
        }

        return $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'place_mode' => ['required', 'in:existing,new'],
            'place_id' => ['nullable', 'required_if:place_mode,existing', 'exists:places,id'],
            'place.name' => ['nullable', 'required_if:place_mode,new', 'string', 'max:255'],
            'place.address' => ['nullable', 'required_if:place_mode,new', 'string', 'max:255'],
            'place.locality' => ['nullable', 'required_if:place_mode,new', 'integer'],
            'place.city' => ['nullable', 'required_if:place_mode,new', 'string', 'max:255'],
            'place.room' => ['nullable', 'string', 'max:255'],
            'day' => $dayRules,
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'link_appointment' => ['nullable', 'url'],
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
        ];
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
