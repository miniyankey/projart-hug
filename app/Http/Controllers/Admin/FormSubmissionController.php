<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FormSubmissionController extends Controller
{
    /**
     * Display a listing of all form submissions.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Postulations/Index', [
            'submissions' => FormSubmission::latest()->get(),
        ]);
    }

    /**
     * Display the specified form submission.
     */
    public function show(FormSubmission $submission): Response
    {
        return Inertia::render('Admin/Postulations/Show', [
            'submission' => $submission,
        ]);
    }

    /**
     * Remove the specified form submission from storage.
     */
    public function destroy(FormSubmission $submission): RedirectResponse
    {
        $submission->delete();

        return redirect()->route('admin.postulations.index')
            ->with('success', 'flash.submission_deleted');
    }
}
