<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Models\EventsConversion;
use App\Models\EventsEligibiliteStep;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CollectEventController extends Controller
{
    /**
     * Track an eligibility step reached by the user.
     * Creates or updates the step record for this session and collect.
     */
    public function eligibiliteStep(Request $request): Response
    {
        $request->validate([
            'session_id' => ['required', 'string'],
            'collect_id' => ['required', 'exists:collects,id'],
            'step' => ['required', 'integer', 'min:1'],
            'result' => ['nullable', 'string'],
            'completed' => ['boolean'],
        ]);

        EventsEligibiliteStep::updateOrCreate(
            [
                'session_id' => $request->session_id,
                'collect_id' => $request->collect_id,
                'step' => $request->step,
            ],
            [
                'result' => $request->result,
                'completed' => $request->boolean('completed', false),
            ]
        );

        return response()->noContent();
    }

    /**
     * Track a click on the appointment link (blood donation conversion).
     */
    public function appointmentClick(Request $request): Response
    {
        $request->validate([
            'session_id' => ['required', 'string'],
            'collect_id' => ['required', 'exists:collects,id'],
            'source' => ['nullable', 'string'],
        ]);

        EventsConversion::updateOrCreate(
            [
                'session_id' => $request->session_id,
                'collect_id' => $request->collect_id,
            ],
            [
                'appointment_click' => true,
                'source' => $request->source,
            ]
        );

        return response()->noContent();
    }
}
