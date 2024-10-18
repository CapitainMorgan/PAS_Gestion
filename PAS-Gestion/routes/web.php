<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ArticleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/fournisseur.index', [FournisseurController::class, 'index'])->middleware(['auth', 'verified'])->name('fournisseur.index');

Route::get('/fournisseur.create', [FournisseurController::class, 'create'])->middleware(['auth', 'verified'])->name('fournisseur.create');

Route::get('/fournisseur.edit', [FournisseurController::class, 'edit'])->middleware(['auth', 'verified'])->name('fournisseur.edit');

Route::get('/article.index', [ArticleController::class, 'index'])->middleware(['auth', 'verified'])->name('article.index');

Route::get('/article.create', [ArticleController::class, 'create'])->middleware(['auth', 'verified'])->name('article.create');

Route::get('/article.edit', [ArticleController::class, 'edit'])->middleware(['auth', 'verified'])->name('article.edit');

Route::get('/parametre.index', function () {
    return Inertia::render('Parametre/Index');
})->middleware(['auth', 'verified'])->name('parametre.index');

Route::get('/caisse.index', function () {
    return Inertia::render('Caisse/Index');
})->middleware(['auth', 'verified'])->name('caisse.index');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
