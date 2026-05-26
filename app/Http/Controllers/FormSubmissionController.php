<?php

namespace App\Http\Controllers;

use App\Enums\FormSubmissionType;
use App\Models\FormSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FormSubmissionController extends Controller
{
    /**
     * Store a new collect request submission from a company.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_address' => ['required', 'string', 'max:255'],
            'company_city' => ['required', 'string', 'max:255'],
            'company_postal_code' => ['required', 'string', 'max:10'],
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email'],
            'message' => ['nullable', 'string'],
            'preferred_date' => ['nullable', 'date', 'after:today'],
        ]);

        FormSubmission::create([
            ...$request->validated(),
            'type' => FormSubmissionType::CollectRequest,
        ]);

        return redirect()->route('inscription')
            ->with('success', 'flash.inscription_sent');
    }
}
