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

Route::get('/fournisseur.show/{id}', [FournisseurController::class, 'show'])->middleware(['auth', 'verified'])->name('fournisseur.show');

Route::get('/fournisseur/{id}/edit', [FournisseurController::class, 'edit'])->name('fournisseur.edit');
Route::put('/fournisseur/{id}', [FournisseurController::class, 'update'])->name('fournisseur.update');

Route::get('/fournisseur.create', [FournisseurController::class, 'create'])->middleware(['auth', 'verified'])->name('fournisseur.create');

Route::post('/fournisseur', [FournisseurController::class, 'store'])->name('fournisseur.store');

Route::get('/article.index', [ArticleController::class, 'index'])->middleware(['auth', 'verified'])->name('article.index');

Route::get('/article.show/{id}', [ArticleController::class, 'show'])->middleware(['auth', 'verified'])->name('article.show');

Route::get('/article.create', [ArticleController::class, 'create'])->middleware(['auth', 'verified'])->name('article.create');

Route::post('/article', [ArticleController::class, 'store'])->name('article.store');

Route::post('/article/{id}/frais', [ArticleController::class, 'storeFrais'])->name('frais.store');

Route::get('/article/{id}/edit', [ArticleController::class, 'edit'])->name('article.edit');
Route::put('/article/{id}', [ArticleController::class, 'update'])->name('article.update');

Route::get('/generate-barcode/{id}', [ArticleController::class, 'generateBarcode']);

Route::get('/parametre.index', function () {
    return Inertia::render('Parametre/Index');
})->middleware(['auth', 'verified'])->name('parametre.index');

Route::get('/caisse.index', function () {
    return Inertia::render('Caisse/Index');
})->middleware(['auth', 'verified'])->name('caisse.index');

Route::get('/depot.create', function () {
    return Inertia::render('Depot/Create');
})->middleware(['auth', 'verified'])->name('depot.create');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
