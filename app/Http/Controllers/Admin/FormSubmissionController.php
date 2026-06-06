<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FormSubmissionController extends Controller
{
    /**
     * Display a listing of all form submissions (contact + collect requests).
     * Filtering by type is handled client-side, so everything is returned.
     */
    public function index(): Response
    {
        $submissions = FormSubmission::with('handler:id,name,surname')
            ->latest()
            ->get()
            ->map(fn (FormSubmission $submission) => [
                'id' => $submission->id,
                'type' => $submission->type->value,
                'company_name' => $submission->company_name,
                'name' => $submission->name,
                'contact_email' => $submission->contact_email,
                'created_at' => $submission->created_at?->toIso8601String(),
                'handled_at' => $submission->handled_at?->toIso8601String(),
                'handler' => $submission->handler
                    ? trim($submission->handler->name.' '.$submission->handler->surname)
                    : null,
            ]);

        return Inertia::render('Admin/Formulaires/Index', [
            'submissions' => $submissions,
        ]);
    }

    /**
     * Display the specified form submission.
     */
    public function show(FormSubmission $formulaire): Response
    {
        $formulaire->load('handler:id,name,surname');

        return Inertia::render('Admin/Formulaires/Show', [
            'submission' => [
                'id' => $formulaire->id,
                'type' => $formulaire->type->value,
                'company_name' => $formulaire->company_name,
                'name' => $formulaire->name,
                'contact_email' => $formulaire->contact_email,
                'message' => $formulaire->message,
                'trophy_participation' => $formulaire->trophy_participation,
                'preferred_dates' => $formulaire->preferred_dates,
                'created_at' => $formulaire->created_at?->toIso8601String(),
                'handled_at' => $formulaire->handled_at?->toIso8601String(),
                'handler' => $formulaire->handler
                    ? trim($formulaire->handler->name.' '.$formulaire->handler->surname)
                    : null,
            ],
        ]);
    }

    /**
     * Toggle whether the submission has already been contacted by an admin.
     */
    public function toggleHandled(Request $request, FormSubmission $formulaire): RedirectResponse
    {
        $isHandled = $formulaire->handled_at !== null;

        $formulaire->update([
            'handled_at' => $isHandled ? null : now(),
            'handled_by' => $isHandled ? null : $request->user()->id,
        ]);

        return redirect()->back()
            ->with('success', $isHandled ? 'flash.submission_unhandled' : 'flash.submission_handled');
    }

    /**
     * Remove the specified form submission from storage.
     */
    public function destroy(FormSubmission $formulaire): RedirectResponse
    {
        $formulaire->delete();

        return redirect()->route('admin.formulaires.index')
            ->with('success', 'flash.submission_deleted');
    }
}
