<?php

namespace App\Mail;

use App\Models\Collect;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

// Mailable déclare deux méthodes qu'on override pour décrire le mail
// Enveloppe : sujet, expéditeur, etc.
// Contenu : template + données à injecter
//
// Envoi synchrone (pendant la requête HTTP) : l'hébergement mutualisé
// Infomaniak ne permet aucun worker de queue, un mail mis en file
// d'attente ne partirait jamais (cf. DEPLOYMENT_INFOMANIAK.md).
class CollectCreatedMail extends Mailable
{
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  Collect  $collect  Loaded with its `company` and `place` relations.
     */
    public function __construct(public Collect $collect)
    {
        // L'entreprise n'a pas de préférence de langue : on s'en tient au français,
        // langue par défaut du site côté public (contact romand).
        $this->locale('fr');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.collect.subject', ['company' => $this->collect->company->name]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $company = $this->collect->company;

        return new Content(
            view: 'mail.collect-created',
            with: [
                'companyName' => $company->name,
                'contactName' => $company->contact_name,
                'brandColor' => $company->color,
                'companyLogoPath' => $company->logo
                    ? Storage::disk('public')->path($company->logo)
                    : null,
                'cobrandUrl' => route('cobrand.collecte', [
                    'brandName' => $company->slug,
                    'collect' => $this->collect->slug,
                ]),
                'dateLine' => $this->buildDateLine(),
                'placeLine' => $this->buildPlaceLine(),
            ],
        );
    }

    /**
     * Build the human-readable date/time line, e.g. `17.06.2026, 09:00 - 16:00`.
     */
    private function buildDateLine(): string
    {
        $line = $this->collect->day?->format('d.m.Y') ?? '';
        $start = $this->formatTime($this->collect->start_time);
        $end = $this->formatTime($this->collect->end_time);

        if ($start) {
            $line .= ', '.$start;

            if ($end) {
                $line .= ' - '.$end;
            }
        }

        return $line;
    }

    /**
     * Build the location line, e.g. `Salle polyvalente (B12), Genève`, or null.
     */
    private function buildPlaceLine(): ?string
    {
        $place = $this->collect->place;

        if (! $place?->name) {
            return null;
        }

        $line = $place->name;

        // opérateur de concaténation de chaînes en PHP : on ajoute à la suite du nom de lieu, entre parenthèses, le numéro de salle s'il est renseigné, puis la ville si elle est renseignée
        if ($place->room) {
            $line .= ' ('.$place->room.')';
        }

        if ($place->city) {
            $line .= ', '.$place->city;
        }

        return $line;
    }

    /**
     * Normalise a stored time (e.g. `09:00:00`) to a display value (`09:00`).
     */
    private function formatTime(?string $time): ?string
    {
        if (! $time) {
            return null;
        }

        return substr($time, 0, 5);
    }
}
