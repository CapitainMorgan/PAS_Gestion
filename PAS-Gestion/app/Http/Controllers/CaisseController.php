<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CaisseController extends Controller
{

    public function index()
    {
        
        $fournisseurs = Fournisseur::select('id', 'nom', 'prenom')->get();

        return Inertia::render('Caisse/Index', [
            'fournisseurs' => $fournisseurs
        ]);
    }
}
