<?php

namespace App\Http\Controllers;

use App\Models\Collect;
use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CobrandController extends Controller
{
    public function collecte(string $brandName, string $token): Response
    {
        $company = $this->resolveCompany($brandName, $token);

        // Le lien co-brandé est stable, mais la page collecte n'est accessible
        // que si une collecte est active pour l'entreprise (sinon 404).
        $this->resolveActiveCollect($company);

        return Inertia::render('CoBranded/Collecte', [
            'token' => $token,
            'company' => $this->companyData($company),
        ]);
    }

    public function jeu(string $brandName, string $token): Response
    {
        $company = $this->resolveCompany($brandName, $token);
        $this->resolveActiveCollect($company);

        return Inertia::render('CoBranded/Jeu', [
            'token' => $token,
            'company' => $this->companyData($company),
        ]);
    }

    public function donSang(string $brandName, string $token): Response
    {
        $company = $this->resolveCompany($brandName, $token);
        $this->resolveActiveCollect($company);

        return Inertia::render('CoBranded/DonSang', [
            'token' => $token,
            'company' => $this->companyData($company),
        ]);
    }

    /**
     * Résout l'entreprise par son slug et son token permanent.
     */
    private function resolveCompany(string $brandName, string $token): Company
    {
        return Company::where('slug', $brandName)
            ->where('token', $token)
            ->firstOrFail();
    }

    /**
     * Garantit qu'une collecte active existe pour l'entreprise, sinon 404.
     */
    private function resolveActiveCollect(Company $company): Collect
    {
        return $company->collects()
            ->where('is_active', true)
            ->latest('day')
            ->firstOrFail();
    }

    /**
     * @return array{name: string, slug: string, color: string|null, logo: string|null}
     */
    private function companyData(Company $company): array
    {
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
