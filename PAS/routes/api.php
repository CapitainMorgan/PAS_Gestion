<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes protégées par le middleware pour Article
Route::middleware('checkArticlePermissions')->group(function () {
    Route::get('/articles', 'ArticleController@index');
    Route::post('/articles', 'ArticleController@store');
    Route::put('/articles/{id}', 'ArticleController@update');
    Route::delete('/articles/{id}', 'ArticleController@destroy');
});

// Routes protégées par le middleware pour Fournisseur
Route::middleware('checkFournisseurPermissions')->group(function () {
    Route::get('/fournisseurs', 'FournisseurController@index');
    Route::post('/fournisseurs', 'FournisseurController@store');
    Route::put('/fournisseurs/{id}', 'FournisseurController@update');
    Route::delete('/fournisseurs/{id}', 'FournisseurController@destroy');
});

// Routes protégées par le middleware pour Vente
Route::middleware('checkVentePermissions')->group(function () {
    Route::get('/ventes', 'VenteController@index');
    Route::post('/ventes', 'VenteController@store');
    Route::put('/ventes/{id}', 'VenteController@update');
    Route::delete('/ventes/{id}', 'VenteController@destroy');
});

// Routes protégées par le middleware pour User
Route::middleware('checkUserPermissions')->group(function () {
    Route::get('/users', 'UserController@index');
    Route::post('/users', 'UserController@store');
    Route::put('/users/{id}', 'UserController@update');
    Route::delete('/users/{id}', 'UserController@destroy');
});

// Routes protégées par le middleware pour Depot
Route::middleware('checkDepotPermissions')->group(function () {
    Route::get('/depots', 'DepotController@index');
    Route::post('/depots', 'DepotController@store');
    Route::put('/depots/{id}', 'DepotController@update');
    Route::delete('/depots/{id}', 'DepotController@destroy');
});

// Routes protégées par le middleware pour FraisSociete
Route::middleware('checkFraisSocietePermissions')->group(function () {
    Route::get('/frais_societes', 'FraisSocieteController@index');
    Route::post('/frais_societes', 'FraisSocieteController@store');
    Route::put('/frais_societes/{id}', 'FraisSocieteController@update');
    Route::delete('/frais_societes/{id}', 'FraisSocieteController@destroy');
});

// Routes protégées par le middleware pour Frais
Route::middleware('checkFraisPermissions')->group(function () {
    Route::get('/frais', 'FraisController@index');
    Route::post('/frais', 'FraisController@store');
    Route::put('/frais/{id}', 'FraisController@update');
    Route::delete('/frais/{id}', 'FraisController@destroy');
});
