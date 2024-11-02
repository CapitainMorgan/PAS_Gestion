<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use App\Models\Article; // Pour valider l'existence de l'article
use Illuminate\Http\Request;

class DepotController extends Controller
{
    // GET: Récupérer tous les dépôts
    public function index()
    {
        $depots = Depot::all();
        return response()->json($depots);
    }

    public function create($id)
    {
        return inertia('Depot/Create', [
            'fournisseur_id' => $id,
        ]);
    }

    // POST: Créer un nouveau dépôt
    public function store(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id', // Valide l'existence de l'article
            'fournisseur_id' => 'required|exists:fournisseurs,id', // Valide l'existence du fournisseur
            'dateDepot' => 'required|date',
            'dateEcheance' => 'required|date',
        ]);

        $depot = Depot::create([
            'article_id' => $request->article_id,
            'fournisseur_id' => $request->fournisseur_id,
            'dateDepot' => $request->dateDepot,
            'dateEcheance' => $request->dateEcheance,
        ]);

        return response()->json($depot, 201);
    }

    // GET: Récupérer un dépôt spécifique
    public function show($id)
    {
        $depot = Depot::find($id);

        if (!$depot) {
            return response()->json(['error' => 'Dépôt not found'], 404);
        }

        return response()->json($depot);
    }

    // PUT/PATCH: Mettre à jour un dépôt
    public function update(Request $request, $id)
    {
        $depot = Depot::find($id);

        if (!$depot) {
            return response()->json(['error' => 'Dépôt not found'], 404);
        }

        $request->validate([
            'article_id' => 'required|exists:articles,id', // Valide que l'article existe
            'fournisseur_id' => 'required|exists:fournisseurs,id', // Valide que le fournisseur existe
            'dateDepot' => 'required|date',
            'dateEcheance' => 'required|date',
        ]);

        $depot->update($request->all());

        return response()->json($depot);
    }

    // DELETE: Supprimer un dépôt
    public function destroy($id)
    {
        $depot = Depot::find($id);

        if (!$depot) {
            return response()->json(['error' => 'Dépôt not found'], 404);
        }

        $depot->delete();

        return response()->json(['message' => 'Dépôt deleted successfully']);
    }
}
