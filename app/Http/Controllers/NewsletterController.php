<?php

namespace App\Http\Controllers;

use App\Services\BrevoService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __construct(private BrevoService $brevo) {}

    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc,dns'],
        ]);

        try {
            $this->brevo->subscribe($data['email'], app()->getLocale());
        } catch (RequestException $e) {
            report($e);

            return response()->json(
                ['message' => __('newsletter.error')],
                500,
            );
        }

        return response()->json(['message' => __('newsletter.success')]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc,dns'],
        ]);

        try {
            $this->brevo->unsubscribe($data['email']);
        } catch (RequestException $e) {
            report($e);

            return response()->json(
                ['message' => __('newsletter.unsubscribe_error')],
                500,
            );
        }

        return response()->json(['message' => __('newsletter.unsubscribe_success')]);
    }
}
