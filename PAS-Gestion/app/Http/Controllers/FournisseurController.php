<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FournisseurController extends Controller
{
    // GET: Récupérer tous les fournisseurs
    public function index()
    {
        $fournisseurs = Fournisseur::all();

        return Inertia::render('Fournisseur/Index', [
            'fournisseurs' => $fournisseurs,
        ]);
    }

    public function create()
    {
        return inertia('Fournisseur/Create', []);
    }

    // POST: Créer un nouveau fournisseur
    public function store(Request $request)
    {

        $fournisseur = Fournisseur::create($request->all());

        return redirect()->route('fournisseur.show', $fournisseur->id)->with('success', 'Fournisseur créé avec succès');;
    }

    public function edit($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);

        return Inertia::render('Fournisseur/Edit', [
            'fournisseur' => $fournisseur,
        ]);
    }

    // GET: Récupérer un fournisseur spécifique
    public function show($id)
    {
        $fournisseur = Fournisseur::with('articles')->find($id);

        if (!$fournisseur) {
            return response()->json(['error' => 'Fournisseur not found'], 404);
        }

        return Inertia::render('Fournisseur/Show', [
            'fournisseur' => $fournisseur,
        ]);
    }

    // PUT/PATCH: Mettre à jour un fournisseur
    public function update(Request $request, $id)
    {

        $fournisseur = Fournisseur::find($id);

        if (!$fournisseur) {
            return response()->json(['error' => 'Fournisseur not found'], 404);
        }

        $fournisseur->update($request->all());

        return redirect()->route('fournisseur.show', $id)->with('message', 'Fournisseur modifié avec succès.');
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
