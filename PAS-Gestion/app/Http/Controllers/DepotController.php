<?php

namespace App\Http\Controllers;

use App\Models\Article; // Pour valider l'existence de l'article
use Illuminate\Http\Request;

class DepotController extends Controller
{

    public function create($id)
    {
        return inertia('Depot/Create', [
            'fournisseur_id' => $id,
        ]);
    }   
}
