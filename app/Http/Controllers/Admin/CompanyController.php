<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    /**
     * Display a listing of all companies.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Entreprises/Index', [
            'companies' => Company::withCount(['collects', 'trophees'])->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new company.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Entreprises/Create');
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email_contact' => ['required', 'email'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7'],
            'color_secondary' => ['nullable', 'string', 'max:7'],
            'street' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'is_labelled' => ['boolean'],
            'labelled_at' => ['nullable', 'date', 'required_if:is_labelled,true'],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        Company::create([
            ...$validated,
            'slug' => Str::slug($validated['name']),
            'logo' => $logoPath,
        ]);

        return redirect()->route('admin.entreprises.index')
            ->with('success', 'flash.company_created');
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $entreprise): Response
    {
        return Inertia::render('Admin/Entreprises/Edit', [
            'company' => $entreprise,
        ]);
    }

    /**
     * Update the specified company in storage.
     */
    public function update(Request $request, Company $entreprise): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email_contact' => ['required', 'email'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7'],
            'color_secondary' => ['nullable', 'string', 'max:7'],
            'street' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'is_labelled' => ['boolean'],
            'labelled_at' => ['nullable', 'date', 'required_if:is_labelled,true'],
        ]);

        // Le logo ne doit être modifié que si un nouveau fichier est réellement envoyé,
        // sinon une simple édition (sans re-upload) écraserait le logo existant avec null
        unset($validated['logo']);

        if ($request->hasFile('logo')) {
            if ($entreprise->logo) {
                Storage::disk('public')->delete($entreprise->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $entreprise->update([
            ...$validated,
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.entreprises.index')
            ->with('success', 'flash.company_updated');
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(Company $entreprise): RedirectResponse
    {
        if ($entreprise->logo) {
            Storage::disk('public')->delete($entreprise->logo);
        }

        $entreprise->delete();

        return redirect()->route('admin.entreprises.index')
            ->with('success', 'flash.company_deleted');
    }
}
