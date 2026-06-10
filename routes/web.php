<?php

use App\Http\Controllers\Admin\CollectController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\FormSubmissionController as AdminFormSubmissionController;
use App\Http\Controllers\Admin\KpiController;
use App\Http\Controllers\Admin\WinnerController as AdminWinnerController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CobrandController;
use App\Http\Controllers\EligibiliteController;
use App\Http\Controllers\EligibilityReminderController;
use App\Http\Controllers\FormSubmissionController;
use App\Http\Controllers\Kpi\CollectEventController;
use App\Http\Controllers\Kpi\ContactFormConversionController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\WinnerController;
use App\Models\Collect;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Déclencheur HTTP des rappels d'éligibilité, pour le planificateur de tâches
// de l'hébergement mutualisé (Infomaniak n'accepte qu'une URL, pas de commande
// shell). Protégé par le token secret CRON_TOKEN ; 404 tant qu'il n'est pas
// configuré, 403 si le token fourni ne correspond pas.
Route::get('/cron/eligibility-reminders', function (Request $request) {
    $token = (string) config('app.cron_token');

    abort_if($token === '', 404);
    abort_unless(hash_equals($token, (string) $request->query('token')), 403);

    Artisan::call('app:send-eligibility-reminders');

    return response(Artisan::output(), 200)->header('Content-Type', 'text/plain');
})->name('cron.eligibility-reminders');

// Langue, pour changer
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->middleware('throttle:newsletter')
    ->name('newsletter.subscribe');
Route::inertia('/newsletter/unsubscribe', 'NewsletterUnsubscribe')->name('newsletter.unsubscribe.page');
Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe'])
    ->middleware('throttle:newsletter')
    ->name('newsletter.unsubscribe');

// Pages publiques
Route::inertia('/', 'Home')->name('home');
Route::get('/trophee', [WinnerController::class, 'index'])->name('trophee');
Route::inertia('/collecte', 'Collecte')->name('collecte');
Route::post('/collecte', [FormSubmissionController::class, 'store'])->name('collecte.store');
Route::get('/jeu', [EligibiliteController::class, 'index'])->name('eligibilite');
Route::post('/eligibilite/rappel', [EligibilityReminderController::class, 'store'])
    ->middleware('throttle:reminder')
    ->name('eligibilite.rappel');
Route::post('/eligibilite/rappel/{reminder}', [EligibilityReminderController::class, 'update'])
    ->middleware('throttle:30,1')
    ->name('eligibilite.rappel.update');
Route::get('/certification', function () {
    return Inertia::render('Certification', [
        'labelledCompanies' => Company::where('is_labelled', true)
            ->orderByDesc('labelled_at')
            ->get(['id', 'name', 'logo'])
            ->map(fn (Company $company) => [
                'id' => $company->id,
                'name' => $company->name,
                'logo_url' => $company->logo_url,
            ])
            ->values(),
    ]);
})->name('certification');

// Tracking KPI : endpoints publics « fire-and-forget » appelés depuis le front (pour register evetns)
Route::prefix('/track')->name('track.')->middleware('throttle:tracking')->group(function () {
    Route::post('/collecte-view', [CollectEventController::class, 'collecteView'])->name('collecte-view');
    Route::post('/eligibilite-step', [CollectEventController::class, 'eligibiliteStep'])->name('eligibilite-step');
    Route::post('/appointment-click', [CollectEventController::class, 'appointmentClick'])->name('appointment-click');
    Route::post('/contact/click', [ContactFormConversionController::class, 'click'])->name('contact.click');
    Route::post('/contact/sent', [ContactFormConversionController::class, 'sent'])->name('contact.sent');
    Route::post('/contact/trophee', [ContactFormConversionController::class, 'trophee'])->name('contact.trophee');
});

// Administration
Route::prefix('/admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Admin/Index', [
            'stats' => [
                'companies' => Company::count(),
                'labelled' => Company::where('is_labelled', true)->count(),
                'active_collects' => Collect::ongoing()->count(),
            ],
            'activeCollects' => Collect::with(['company:id,name,slug,color,logo', 'place:id,city'])
                ->ongoing()
                ->orderBy('day')
                ->get()
                ->map(fn (Collect $collect) => [
                    'id' => $collect->id,
                    'day' => $collect->day?->format('Y-m-d'),
                    'company' => $collect->company?->name,
                    'company_slug' => $collect->company?->slug,
                    'collect_slug' => $collect->slug,
                    'color' => $collect->company?->color,
                    'logo_url' => $collect->company?->logo_url,
                    'city' => $collect->place?->city,
                ]),
        ]);
    })->name('index');

    Route::prefix('/vainqueurs')->name('vainqueurs.')->group(function () {
        Route::get('/', [AdminWinnerController::class, 'index'])->name('index');
        Route::get('/create', [AdminWinnerController::class, 'create'])->name('create');
        Route::post('/', [AdminWinnerController::class, 'store'])->name('store');
        Route::get('/{vainqueur}/edit', [AdminWinnerController::class, 'edit'])->name('edit');
        Route::put('/{vainqueur}', [AdminWinnerController::class, 'update'])->name('update');
        Route::delete('/{vainqueur}', [AdminWinnerController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/entreprises')->name('entreprises.')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('index');
        Route::get('/create', [CompanyController::class, 'create'])->name('create');
        Route::post('/', [CompanyController::class, 'store'])->name('store');
        Route::get('/{entreprise}/edit', [CompanyController::class, 'edit'])->name('edit');
        Route::put('/{entreprise}', [CompanyController::class, 'update'])->name('update');
        Route::delete('/{entreprise}', [CompanyController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/collectes')->name('collectes.')->group(function () {
        Route::get('/', [CollectController::class, 'index'])->name('index');
        Route::get('/create', [CollectController::class, 'create'])->name('create');
        Route::post('/', [CollectController::class, 'store'])->name('store');
        Route::get('/{collecte}/edit', [CollectController::class, 'edit'])->name('edit');
        Route::put('/{collecte}', [CollectController::class, 'update'])->name('update');
        Route::delete('/{collecte}', [CollectController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/formulaires')->name('formulaires.')->group(function () {
        Route::get('/', [AdminFormSubmissionController::class, 'index'])->name('index');
        Route::get('/{formulaire}', [AdminFormSubmissionController::class, 'show'])->name('show');
        Route::patch('/{formulaire}/handled', [AdminFormSubmissionController::class, 'toggleHandled'])->name('handled');
        Route::delete('/{formulaire}', [AdminFormSubmissionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/kpi')->name('kpi.')->group(function () {
        Route::get('/', [KpiController::class, 'index'])->name('index');
        Route::get('/{company:token}', [KpiController::class, 'show'])->name('show');
    });

    Route::get('/register', [AdminAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AdminAuthController::class, 'register']);
});

// Routes de login et logout
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('login');
// throttle pour limiter les tentatives de login à 5 par minute (cf bruteforce)
Route::post('/admin/login', [AdminAuthController::class, 'login'])->middleware('throttle:login');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('logout');

// Pages co-brandées : l'entreprise est résolue via {brandName} (son slug) et la
// collecte via {collect} (son slug propre). Un lien unique par collecte.
Route::prefix('/{brandName}/{collect}')->name('cobrand.')->group(function () {
    Route::get('/collecte', [CobrandController::class, 'collecte'])->name('collecte');
    Route::get('/jeu', [CobrandController::class, 'jeu'])->name('jeu');
    Route::get('/don-du-sang', [CobrandController::class, 'donSang'])->name('don-sang');
});

// Page 404 personnalisée
Route::inertia('/erreur-404', 'Error404')->name('error.404');

// Fallback : toute route non définie affiche la page 404
Route::fallback(function (Request $request) {
    return Inertia::render('Error404')
        ->toResponse($request)
        ->setStatusCode(404);
});
