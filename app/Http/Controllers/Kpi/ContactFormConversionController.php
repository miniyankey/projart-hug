<?php

namespace App\Http\Controllers\Kpi;

use App\Enums\FormSubmissionType;
use App\Http\Controllers\Controller;
use App\Models\ContactFormConversion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ContactFormConversionController extends Controller
{
    /**
     * Track a click on the company contact form button.
     */
    public function click(Request $request): Response
    {
        $request->validate([
            'type' => ['required', Rule::enum(FormSubmissionType::class)],
        ]);

        ContactFormConversion::updateOrCreate(
            ['session_id' => $request->session()->getId()],
            ['type' => $request->type, 'form_click' => true]
        );

        return response()->noContent();
    }

    /**
     * Track a successful company contact form submission.
     */
    public function sent(Request $request): Response
    {
        $request->validate([
            'type' => ['required', Rule::enum(FormSubmissionType::class)],
        ]);

        ContactFormConversion::updateOrCreate(
            ['session_id' => $request->session()->getId()],
            ['type' => $request->type, 'form_sent' => true]
        );

        return response()->noContent();
    }

    /**
     * Track a trophee participation checkbox interaction on the contact form.
     */
    public function trophee(Request $request): Response
    {
        $request->validate([
            'type' => ['required', Rule::enum(FormSubmissionType::class)],
            'trophee_participation' => ['required', 'boolean'],
        ]);

        ContactFormConversion::updateOrCreate(
            ['session_id' => $request->session()->getId()],
            [
                'type' => $request->type,
                'trophee_participation' => $request->boolean('trophee_participation'),
            ]
        );

        return response()->noContent();
    }
}
