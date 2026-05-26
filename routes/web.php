<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Langue, pour changer
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

// Pages publiques
Route::inertia('/', 'Home')->name('home');
Route::inertia('/trophee', 'Trophee')->name('trophee');
Route::inertia('/collecte', 'Collecte')->name('collecte');
Route::inertia('/inscription', 'Inscription')->name('inscription');

// Administration
Route::prefix('/admin')->name('admin.')->middleware('auth')->group(function () {
    Route::inertia('/', 'Admin/Index')->name('index');

    Route::prefix('/vainqueurs')->name('vainqueurs.')->group(function () {
        Route::inertia('/', 'Admin/Vainqueurs/Index')->name('index');
        Route::inertia('/create', 'Admin/Vainqueurs/Create')->name('create');
        Route::get('/{vainqueur}/edit', fn () => Inertia::render('Admin/Vainqueurs/Edit'))->name('edit');
    });

    Route::prefix('/entreprises')->name('entreprises.')->group(function () {
        Route::inertia('/', 'Admin/Entreprises/Index')->name('index');
        Route::inertia('/create', 'Admin/Entreprises/Create')->name('create');
        Route::get('/{entreprise}/edit', fn () => Inertia::render('Admin/Entreprises/Edit'))->name('edit');
    });

    Route::prefix('/collectes')->name('collectes.')->group(function () {
        Route::inertia('/', 'Admin/Collectes/Index')->name('index');
        Route::inertia('/create', 'Admin/Collectes/Create')->name('create');
        Route::get('/{collecte}/edit', fn () => Inertia::render('Admin/Collectes/Edit'))->name('edit');
    });

    Route::prefix('/kpi')->name('kpi.')->group(function () {
        Route::inertia('/', 'Admin/Kpi/Index')->name('index');
        Route::get('/{token}', fn () => Inertia::render('Admin/Kpi/Show'))->name('show');
    });

    Route::get('/register', [AdminAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AdminAuthController::class, 'register']);
});

// Routes de login et logout
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('login');
//throttle pour limiter les tentatives de login à 5 par minute (cf bruteforce)
Route::post('/admin/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('logout');

// A completer
Route::prefix('/{brandName}/{token}')->name('cobrand.')->group(function () {
    Route::inertia('/collecte', 'CoBranded/Collecte')->name('collecte');
    Route::inertia('/jeu', 'CoBranded/Jeu')->name('jeu');
    Route::inertia('/inscription', 'CoBranded/Inscription')->name('inscription');
});
