<?php

namespace App\Mail;

use App\Enums\FormSubmissionType;
use App\Models\FormSubmission;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Notification interne envoyée à l'équipe Mission Donneur à chaque soumission
// d'un formulaire public (contact ou demande d'organisation de collecte).
// Volontairement non co-brandé : le destinataire est l'équipe, pas une entreprise.
// Envoi synchrone : pas de worker possible sur le mutualisé Infomaniak,
// un mail en file d'attente ne partirait jamais (cf. DEPLOYMENT_INFOMANIAK.md).
class NewFormSubmissionMail extends Mailable
{
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public FormSubmission $submission)
    {
        // Notification interne : toujours en français (équipe romande).
        $this->locale('fr');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $key = $this->submission->type === FormSubmissionType::CollectRequest
            ? 'mail.form_submission.subject_collect'
            : 'mail.form_submission.subject_contact';

        return new Envelope(
            subject: __($key, ['company' => $this->submission->company_name]),
            // L'équipe peut répondre directement à l'entreprise qui a écrit.
            replyTo: [new Address($this->submission->contact_email, $this->submission->name)],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.new-form-submission',
            with: [
                'isCollectRequest' => $this->submission->type === FormSubmissionType::CollectRequest,
                // Notification interne non co-brandée : couleur de marque par défaut (token violet).
                'brandColor' => null,
                // Lien vers le dashboard admin des formulaires (URL absolue, suit APP_URL).
                'dashboardUrl' => route('admin.formulaires.index'),
            ],
        );
    }
}
