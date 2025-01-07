<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FraisSocieteController;
use App\Http\Controllers\FraisController;
use App\Http\Controllers\DepotController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Exports\ArticlesExport;
use Maatwebsite\Excel\Facades\Excel;
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
Route::get('/fournisseur/{id}/edit', [FournisseurController::class, 'edit'])->middleware(['auth', 'verified'])->name('fournisseur.edit');
Route::put('/fournisseur/{id}', [FournisseurController::class, 'update'])->middleware(['auth', 'verified'])->name('fournisseur.update');
Route::get('/fournisseur.create', [FournisseurController::class, 'create'])->middleware(['auth', 'verified'])->name('fournisseur.create');
Route::post('/fournisseur', [FournisseurController::class, 'store'])->middleware(['auth', 'verified'])->name('fournisseur.store');

Route::get('/fiche-fournisseur-depot/{id}/{date_depot}/{conditionGenerale}', [FournisseurController::class, 'generateFicheDepotFournisseur'])->middleware(['auth', 'verified'])->name('fiche.fournisseur_depot');
Route::get('/fiche-fournisseur-vente/{id}/{date_debut}/{conditionGenerale}', [FournisseurController::class, 'generateFicheVenteFournisseur'])->middleware(['auth', 'verified'])->name('fiche.fournisseur_vente');
Route::get('/fiche-fournisseur/{id}/{conditionGenerale}', [FournisseurController::class, 'generateFicheFournisseur'])->middleware(['auth', 'verified'])->name('fiche.fournisseur');

Route::get('/depot/{id}', [DepotController::class, 'create'])->middleware(['auth', 'verified'])->name('depot.create');
Route::post('/depot/{EcheanceDays}', [ArticleController::class, 'storeGroupedArticles'])->middleware(['auth', 'verified'])->name('depot.store');



Route::get('/article.index', [ArticleController::class, 'index'])->middleware(['auth', 'verified'])->name('article.index');
Route::get('/articles/search', [ArticleController::class, 'search'])->middleware(['auth', 'verified'])->name('article.search');
Route::get('/article.show/{id}', [ArticleController::class, 'show'])->middleware(['auth', 'verified'])->name('article.show');
Route::get('/article.create/', [ArticleController::class, 'create'])->middleware(['auth', 'verified'])->name('article.create');
Route::post('/article/{EchanceDays}', [ArticleController::class, 'store'])->middleware(['auth', 'verified'])->name('article.store');
Route::post('/article/{id}/frais', [ArticleController::class, 'storeFrais'])->middleware(['auth', 'verified'])->name('fraisArticle.store');
Route::put('/article/{id}/frais', [ArticleController::class, 'updateFrais'])->middleware(['auth', 'verified'])->name('fraisArticle.update');
Route::get('/article/{id}/edit', [ArticleController::class, 'edit'])->middleware(['auth', 'verified'])->name('article.edit');
Route::put('/article/{id}', [ArticleController::class, 'update'])->middleware(['auth', 'verified'])->name('article.update');
Route::post('/articles/updateStatus', [ArticleController::class, 'changeStatusArticles'])->middleware(['auth', 'verified'])->name('article.updateStatus');
Route::get('/generate-barcode/{id}', [ArticleController::class, 'generateBarcode'])->middleware(['auth', 'verified']);
Route::post('/article/barcode/{barcode}', [ArticleController::class, 'getArticleByBarcode'])
    ->middleware(['auth', 'verified'])
    ->name('article.barcode');


Route::get('/frais.index', [FraisSocieteController::Class, 'index'])->middleware(['auth', 'verified'])->name('frais.index');
Route::get('/frais.create', [FraisSocieteController::Class, 'create'])->middleware(['auth', 'verified'])->name('frais.create');
Route::post('/frais', [FraisSocieteController::Class, 'store'])->middleware(['auth', 'verified'])->name('frais.store');
Route::get('/frais/{id}/edit', [FraisSocieteController::Class, 'edit'])->middleware(['auth', 'verified'])->name('frais.edit');
Route::put('/frais/{id}', [FraisSocieteController::Class, 'update'])->middleware(['auth', 'verified'])->name('frais.update');


Route::get('/parametre.index', [ParametreController::class, 'index'])->middleware(['auth', 'verified'])->name('parametre.index');
Route::put('/parametre.update', [ParametreController::class, 'update'])->middleware(['auth', 'verified'])->name('parametre.update');

Route::post('register', [RegisteredUserController::class, 'store'])->middleware(['auth', 'verified'])->name('register');

Route::get('/newUser', function () {
    return Inertia::render('Auth/Register');
})->middleware(['auth', 'verified'])->name('newUser');

Route::get('/caisse.index', function () {
    return Inertia::render('Caisse/Index');
})->middleware(['auth', 'verified'])->name('caisse.index');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ArticleController::class, 'getArticlesByEndDate'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');    
});

Route::post('/user/{id}/delete', [UserController::class, 'destroy'])->middleware(['auth', 'verified'])->name('user.destroy');
Route::put('/user/{id}/update', [UserController::class, 'update'])->middleware(['auth', 'verified'])->name('user.update');
Route::put('/user/{id}/update-password', [UserController::class, 'updatePassword'])->middleware(['auth', 'verified'])->name('user.update-password');

Route::middleware(['auth'])->group(function () {
    Route::delete('/fournisseur/{id}', [FournisseurController::class, 'destroy'])->name('fournisseur.destroy');
    Route::delete('/article/{id}', [ArticleController::class, 'destroy'])->name('article.destroy');
    Route::delete('/frais/{id}', [FraisController::class, 'destroy'])->name('frais.destroy');
    Route::delete('/frais-societe/{id}', [FraisSocieteController::class, 'destroy'])->name('frais-societe.destroy');
    Route::delete('/vente/{id}', [VenteController::class, 'destroy'])->name('vente.destroy');
});

Route::post('/send-reminder/articles', [ArticleController::class, 'sendReminderForArticles'])
    ->middleware(['auth', 'verified'])
    ->name('send-reminder');

Route::post('/api/cart/add', [CartController::class, 'addToCart']);
Route::get('/api/cart', [CartController::class, 'getCart']);
Route::post('/api/cart/clear', [CartController::class, 'clearCart']);
Route::post('/api/cart/update', [CartController::class, 'updateArticle']);

Route::get('/export-articles', [ArticleController::class, 'exportArticles'])
    ->middleware('auth')
    ->name('export.articles');

require __DIR__.'/auth.php';
