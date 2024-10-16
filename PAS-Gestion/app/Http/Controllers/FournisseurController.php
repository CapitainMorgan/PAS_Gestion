<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    // GET: Récupérer tous les fournisseurs
    public function index()
    {
        $fournisseurs = Fournisseur::all();
        return response()->json($fournisseurs);
    }

    // POST: Créer un nouveau fournisseur
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:50',
            'prénom' => 'required|string|max:50',
            'rue' => 'nullable|string|max:50',
            'ville' => 'nullable|string|max:50',
            'npa' => 'nullable|string|max:10',
            'pays' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:15',
            'email' => 'nullable|string|max:50',
        ]);

        $fournisseur = Fournisseur::create($request->all());

        return response()->json($fournisseur, 201);
    }

    // GET: Récupérer un fournisseur spécifique
    public function show($id)
    {
        $fournisseur = Fournisseur::find($id);

        if (!$fournisseur) {
            return response()->json(['error' => 'Fournisseur not found'], 404);
        }

        return response()->json($fournisseur);
    }

    // PUT/PATCH: Mettre à jour un fournisseur
    public function update(Request $request, $id)
    {
        $fournisseur = Fournisseur::find($id);

        if (!$fournisseur) {
            return response()->json(['error' => 'Fournisseur not found'], 404);
        }

        $fournisseur->update($request->all());

        return response()->json($fournisseur);
    }

    // DELETE: Supprimer un fournisseur
    public function destroy($id)
    {
        $fournisseur = Fournisseur::find($id);

        if (!$fournisseur) {
            return response()->json(['error' => 'Fournisseur not found'], 404);
        }

        $fournisseur->delete();

        return response()->json(['message' => 'Fournisseur deleted successfully']);
    }
}
