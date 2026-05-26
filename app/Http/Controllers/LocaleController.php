<?php

namespace App\Http\Controllers;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:'.implode(',', HandleInertiaRequests::SUPPORTED_LOCALES)],
        ]);

        return back()->withCookie(
            Cookie::make('locale', $validated['locale'], 60 * 24 * 365)
        );
    }
}
