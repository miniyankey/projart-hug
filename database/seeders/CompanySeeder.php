<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CompanySeeder extends Seeder
{
    /**
     * Casting de démo : entreprises réelles du canton de Genève (+ HEIG-VD,
     * conservée comme support de la démo co-brandée). Chaque entreprise fournit
     * son propre site de collecte (street/postal_code/city), réutilisé par
     * PlaceSeeder, et son token alimente le lien co-brandé /{slug}/{token}/collecte.
     *
     * Logos versionnés dans resources/images/logos/{fichier} (copiés vers le
     * disque public au seed). Si un logo manque encore, l'entreprise est créée
     * sans logo plutôt que de planter le seed.
     *
     * @var list<array<string, mixed>>
     */
    private const COMPANIES = [
        [
            'name' => 'Rolex SA',
            'slug' => 'rolex-sa',
            'token' => 'rolex000',
            'email_contact' => 'collecte@rolex.com',
            'contact_name' => 'Sophie Baumann',
            'contact_phone' => '+41 22 302 22 00',
            'street' => 'Rue François-Dussaud 3',
            'postal_code' => '1211',
            'city' => 'Genève',
            'logo' => 'rolex.png',
            'color' => '#127749',
            'is_labelled' => true,
        ],
        [
            'name' => 'Migros Genève',
            'slug' => 'migros',
            'token' => 'migros00',
            'email_contact' => 'collecte@migrosgeneve.ch',
            'contact_name' => 'Marc Favre',
            'contact_phone' => '+41 22 307 21 11',
            'street' => 'Rue Alexandre-Gavard 35',
            'postal_code' => '1227',
            'city' => 'Carouge',
            'logo' => 'migros.png',
            'color' => '#FF6600',
            'is_labelled' => true,
        ],
        [
            'name' => 'Transports Publics Genevois',
            'slug' => 'tpg',
            'token' => 'tpg00000',
            'email_contact' => 'collecte@tpg.ch',
            'contact_name' => 'Laurent Berset',
            'contact_phone' => '+41 22 308 33 11',
            'street' => 'Route de la Chapelle 1',
            'postal_code' => '1212',
            'city' => 'Grand-Lancy',
            'logo' => 'tpg.png',
            'color' => '#EF7D00',
            'is_labelled' => true,
        ],
        [
            'name' => 'Servette FC',
            'slug' => 'servette-fc',
            'token' => 'servette',
            'email_contact' => 'collecte@servettefc.ch',
            'contact_name' => 'Isabelle Court',
            'contact_phone' => '+41 22 304 00 00',
            'street' => 'Route des Jeunes 1',
            'postal_code' => '1227',
            'city' => 'Les Acacias',
            'logo' => 'servette.png',
            'color' => '#8A1538',
            'is_labelled' => false,
        ],
        [
            'name' => 'HEIG-VD',
            'slug' => 'heig-vd',
            'token' => 'heigvd00',
            'email_contact' => 'collecte@heig-vd.ch',
            'contact_name' => 'Jean Dupont',
            'contact_phone' => '+41 24 557 63 30',
            'street' => 'Route de Cheseaux 1',
            'postal_code' => '1401',
            'city' => 'Yverdon-les-Bains',
            'logo' => 'heig.png',
            'color' => '#E40521',
            'is_labelled' => true,
        ],
    ];

    public function run(): void
    {
        // Logo institutionnel HUG, réutilisé ailleurs dans l'UI (label CTS).
        $this->copyLogo('hug.png');

        $labelledOrder = 0;

        foreach (self::COMPANIES as $data) {
            $logo = $this->copyLogo($data['logo']) ? "logos/{$data['logo']}" : null;

            Company::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'token' => $data['token'],
                'email_contact' => $data['email_contact'],
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'street' => $data['street'],
                'postal_code' => $data['postal_code'],
                'city' => $data['city'],
                'logo' => $logo,
                'color' => $data['color'],
                'is_labelled' => $data['is_labelled'],
                // Échelonne les dates de labellisation pour un rendu réaliste.
                'labelled_at' => $data['is_labelled'] ? now()->subMonths(++$labelledOrder) : null,
            ]);
        }
    }

    /**
     * Copie un logo versionné (resources/images/logos) vers le disque public.
     * Renvoie false si le fichier n'existe pas encore (logo à fournir).
     */
    private function copyLogo(string $file): bool
    {
        $source = resource_path("images/logos/{$file}");

        if (! is_file($source)) {
            return false;
        }

        Storage::disk('public')->put("logos/{$file}", file_get_contents($source));

        return true;
    }
}
