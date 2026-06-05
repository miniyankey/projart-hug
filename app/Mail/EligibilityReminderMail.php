<?php

namespace App\Mail;

use App\Models\EligibilityReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EligibilityReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Page officielle HUG vers laquelle pointe le rappel (stable, non co-brandée).
     * Le lien co-brandé d'une collecte n'est valable que pendant sa fenêtre active,
     * or le rappel part bien plus tard : on vise donc la page HUG pérenne.
     */
    private const DONATE_URL = 'https://www.hug.ch/don-du-sang/donner-mon-sang';

    public function __construct(public EligibilityReminder $reminder)
    {
        $this->locale($reminder->locale);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.reminder.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.eligibility-reminder',
            with: [
                'eligibleDate' => $this->reminder->eligible_at->format('d.m.Y'),
                'donateUrl' => self::DONATE_URL,
                // Rappel volontairement non co-brandé : on neutralise les variables
                // de marque attendues par le layout partagé (fallback HUG).
                'brandColor' => null,
                'companyName' => null,
                'companyLogoPath' => null,
            ],
        );
    }
}
