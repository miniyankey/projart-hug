<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Pages publiques
Route::inertia('/', 'Home')->name('home');
Route::inertia('/trophee', 'Trophee')->name('trophee');
Route::inertia('/collecte', 'Collecte')->name('collecte');
Route::inertia('/inscription', 'Inscription')->name('inscription');

// Administration
Route::prefix('/admin')->name('admin.')->group(function () {
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
});

// A completer
Route::prefix('/{brandName}/{token}')->name('cobrand.')->group(function () {
    Route::inertia('/collecte', 'CoBranded/Collecte')->name('collecte');
    Route::inertia('/jeu', 'CoBranded/Jeu')->name('jeu');
    Route::inertia('/inscription', 'CoBranded/Inscription')->name('inscription');
});
