<?php

namespace App\Http\Controllers;

use App\Models\Collect;
use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CobrandController extends Controller
{
    public function collecte(string $brandName, string $collect): Response
    {
        $company = $this->resolveCompany($brandName);
        $collect = $this->resolveCollect($company, $collect);

        return Inertia::render('CoBranded/Collecte', [
            'collectSlug' => $collect->slug,
            'company' => $this->companyData($company),
            'collect' => $this->collectData($collect),
        ]);
    }

    public function jeu(string $brandName, string $collect): Response
    {
        $company = $this->resolveCompany($brandName);
        $collect = $this->resolveCollect($company, $collect);

        // Le quiz est défini côté front (data/eligibilityQuiz.js + locales),
        // aucune donnée de question n'est envoyée par le serveur.
        return Inertia::render('CoBranded/Jeu', [
            'collectSlug' => $collect->slug,
            'company' => $this->companyData($company),
            'collect_id' => $collect->id,
            'link_appointment' => $collect->link_appointment,
        ]);
    }

    public function donSang(string $brandName, string $collect): Response
    {
        $company = $this->resolveCompany($brandName);
        $collect = $this->resolveCollect($company, $collect);

        return Inertia::render('CoBranded/DonSang', [
            'collectSlug' => $collect->slug,
            'company' => $this->companyData($company),
        ]);
    }

    /**
     * Résout l'entreprise par son slug.
     */
    private function resolveCompany(string $brandName): Company
    {
        return Company::where('slug', $brandName)->firstOrFail();
    }

    /**
     * Résout la collecte de l'entreprise par son slug, sinon 404.
     *
     * Une collecte désactivée (is_active = false) n'est pas accessible publiquement.
     */
    private function resolveCollect(Company $company, string $collectSlug): Collect
    {
        return $company->collects()
            ->with('place:id,name,address,locality,city,room')
            ->where('slug', $collectSlug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    private function collectData(Collect $collect): array
    {
        return [
            'id' => $collect->id,
            'day' => $collect->day?->format('Y-m-d'),
            'start_time' => $collect->start_time ? substr((string) $collect->start_time, 0, 5) : null,
            'end_time' => $collect->end_time ? substr((string) $collect->end_time, 0, 5) : null,
            'link_appointment' => $collect->link_appointment,
            'place' => $collect->place ? [
                'name' => $collect->place->name,
                'address' => $collect->place->address,
                'locality' => $collect->place->locality,
                'city' => $collect->place->city,
                'room' => $collect->place->room,
            ] : null,
        ];
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
