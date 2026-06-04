<?php

use App\Http\Controllers\Admin\CollectController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\KpiController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CobrandController;
use App\Http\Controllers\EligibilityReminderController;
use App\Http\Controllers\FormSubmissionController;
use App\Http\Controllers\Kpi\CollectEventController;
use App\Http\Controllers\Kpi\ContactFormConversionController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NewsletterController;
use App\Models\Collect;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Langue, pour changer
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->middleware('throttle:5,1')
    ->name('newsletter.subscribe');
Route::inertia('/newsletter/unsubscribe', 'NewsletterUnsubscribe')->name('newsletter.unsubscribe.page');
Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe'])
    ->middleware('throttle:5,1')
    ->name('newsletter.unsubscribe');

// Pages publiques
Route::inertia('/', 'Home')->name('home');
Route::inertia('/trophee', 'Trophee')->name('trophee');
Route::inertia('/collecte', 'Collecte')->name('collecte');
Route::post('/collecte', [FormSubmissionController::class, 'store'])->name('collecte.store');
Route::inertia('/eligibilite', 'Eligibilite')->name('eligibilite');
Route::post('/eligibilite/rappel', [EligibilityReminderController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('eligibilite.rappel');
Route::inertia('/certification', 'Certification')->name('certification');

// Tracking KPI : endpoints publics « fire-and-forget » appelés depuis le front (pour register evetns)
Route::prefix('/track')->name('track.')->middleware('throttle:60,1')->group(function () {
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
                'active_collects' => Collect::where('is_active', true)->count(),
            ],
            'activeCollects' => Collect::with(['company:id,name,slug,color,logo', 'place:id,city'])
                ->where('is_active', true)
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
        Route::inertia('/', 'Admin/Vainqueurs/Index')->name('index');
        Route::inertia('/create', 'Admin/Vainqueurs/Create')->name('create');
        Route::get('/{vainqueur}/edit', fn () => Inertia::render('Admin/Vainqueurs/Edit'))->name('edit');
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
Route::post('/admin/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1');
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
