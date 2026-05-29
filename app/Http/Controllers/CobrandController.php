<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CobrandController extends Controller
{
    /**
     * Page « Informations sur la collecte » co-brandée.
     */
    public function collecte(string $brandName, string $token): Response
    {
        return Inertia::render('CoBranded/Collecte', [
            'token' => $token,
            'company' => $this->resolveCompany($brandName),
        ]);
    }

    /**
     * Page « Pouvez-vous donner ? » co-brandée.
     */
    public function jeu(string $brandName, string $token): Response
    {
        return Inertia::render('CoBranded/Jeu', [
            'token' => $token,
            'company' => $this->resolveCompany($brandName),
        ]);
    }

    /**
     * Résout l'entreprise à partir de son slug et expose au front les seules
     * informations de co-branding : nom, couleur et URL publique du logo.
     *
     */
    private function resolveCompany(string $brandName): array
    {
        $company = Company::where('slug', $brandName)->firstOrFail();

        return [
            'name' => $company->name,
            'slug' => $company->slug,
            'color' => $company->color,
            'logo' => $company->logo
                ? Storage::disk('public')->url($company->logo)
                : null,
        ];
    }
}
