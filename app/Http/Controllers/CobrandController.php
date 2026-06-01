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
        $collect = $this->resolveActiveCollect($company);

        return Inertia::render('CoBranded/Collecte', [
            'token' => $token,
            'company' => $this->companyData($company),
            'collect' => $this->collectData($collect),
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
     * Résout la collecte visible de l'entreprise, sinon 404.
     *
     * Fenêtre de visibilité : une collecte est accessible dès qu'elle est
     * prévue (date future) et le reste jusqu'à 7 jours après le jour J.
     * On retient la plus imminente (date la plus proche encore dans sa fenêtre).
     */
    private function resolveActiveCollect(Company $company): Collect
    {
        return $company->collects()
            ->with('place:id,name,address,locality,city,room')
            ->where('is_active', true)
            ->whereDate('day', '>=', now()->subWeek()->toDateString())
            ->orderBy('day')
            ->firstOrFail();
    }

    private function collectData(Collect $collect): array
    {
        return [
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
