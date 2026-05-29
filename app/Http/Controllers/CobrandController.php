<?php

namespace App\Http\Controllers;

use App\Models\Collect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CobrandController extends Controller
{
    public function collecte(string $brandName, string $token): Response
    {
        $collect = $this->resolveCollect($brandName, $token);

        return Inertia::render('CoBranded/Collecte', [
            'token' => $token,
            'company' => $this->companyData($collect),
        ]);
    }

    public function jeu(string $brandName, string $token): Response
    {
        $collect = $this->resolveCollect($brandName, $token);

        return Inertia::render('CoBranded/Jeu', [
            'token' => $token,
            'company' => $this->companyData($collect),
        ]);
    }

    public function donSang(string $brandName, string $token): Response
    {
        $collect = $this->resolveCollect($brandName, $token);

        return Inertia::render('CoBranded/DonSang', [
            'token' => $token,
            'company' => $this->companyData($collect),
        ]);
    }

    /**
     * Résout la collecte par son token en vérifiant que le slug de l'entreprise
     */
    private function resolveCollect(string $brandName, string $token): Collect
    {
        return Collect::with('company')
            ->where('token', $token)
            ->whereHas('company', fn ($q) => $q->where('slug', $brandName))
            ->firstOrFail();
    }

    /**
     * @return array{name: string, slug: string, color: string|null, logo: string|null}
     */
    private function companyData(Collect $collect): array
    {
        $company = $collect->company;

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
