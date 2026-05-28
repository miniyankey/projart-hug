<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Models\ContactFormConversion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContactFormConversionController extends Controller
{
    /**
     * Track a click on the company contact form button.
     */
    public function click(Request $request): Response
    {
        $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        ContactFormConversion::updateOrCreate(
            ['session_id' => $request->session_id],
            ['form_click' => true]
        );

        return response()->noContent();
    }

    /**
     * Track a successful company contact form submission.
     */
    public function sent(Request $request): Response
    {
        $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        ContactFormConversion::updateOrCreate(
            ['session_id' => $request->session_id],
            ['form_sent' => true]
        );

        return response()->noContent();
    }

    /**
     * Track a trophee participation checkbox interaction on the contact form.
     */
    public function trophee(Request $request): Response
    {
        $request->validate([
            'session_id' => ['required', 'string'],
            'trophee_participation' => ['required', 'boolean'],
        ]);

        ContactFormConversion::updateOrCreate(
            ['session_id' => $request->session_id],
            ['trophee_participation' => $request->boolean('trophee_participation')]
        );

        return response()->noContent();
    }
}
