<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public const SUPPORTED_LOCALES = ['fr', 'en'];

    public const DEFAULT_LOCALE = 'fr';

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $locale = $request->cookie('locale');

        if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = self::DEFAULT_LOCALE;
        }

        app()->setLocale($locale);

        // on retourne la locale aux vues (dans les props)
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'locale' => $locale,
            'flash' => [
                'success' => $request->session()->get('success'),
            ],
        ];
    }
}
