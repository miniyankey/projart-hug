<?php

namespace App\Http\Controllers;

use App\Enums\FormSubmissionType;
use App\Mail\NewFormSubmissionMail;
use App\Models\FormSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class FormSubmissionController extends Controller
{
    /**
     * Store a contact message or a collect request sent from the public form.
     */
    public function store(Request $request): RedirectResponse
    {
        // Les champs spécifiques à la collecte n'ont pas de sens pour un contact :
        // on les neutralise avant validation pour éviter qu'ils soient enregistrés (et prennent de la place pour r)
        if ($request->input('type') === FormSubmissionType::Contact->value) {
            $request->merge([
                // si contacte, on force cela
                'trophy_participation' => false,
                'preferred_dates' => null,
            ]);
        }

        $validated = $request->validate([
            'type' => ['required', Rule::enum(FormSubmissionType::class)],
            'company_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'message' => ['required_if:type,'.FormSubmissionType::Contact->value, 'nullable', 'string', 'max:2000'],
            'trophy_participation' => ['boolean'],
            'preferred_dates' => ['required_if:type,'.FormSubmissionType::CollectRequest->value, 'nullable', 'array', 'max:10'],
            'preferred_dates.*' => ['required', 'date', 'after:today'],
        ]);

        $submission = FormSubmission::create($validated);

        // Notifier l'équipe Mission Donneur de la nouvelle soumission (en file d'attente,
        // pour ne pas bloquer la réponse au visiteur si le SMTP est lent/indisponible).
        Mail::to(config('mail.from.address'))->queue(new NewFormSubmissionMail($submission));

        return redirect()->back()
            ->with('success', 'flash.form_submission_sent');
    }
}
