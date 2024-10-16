<?php

namespace App\Http\Controllers;

use App\Models\FraisSociete; // Assurez-vous d'avoir créé ce modèle
use Illuminate\Http\Request;

class FraisSocieteController extends Controller
{
    // GET: Récupérer tous les frais de la société
    public function index()
    {
        $fraisSocietes = FraisSociete::all();
        return response()->json($fraisSocietes);
    }

    // POST: Créer un nouveau frais de société
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string|max:255',
            'prix' => 'required|numeric', // Le prix est obligatoire
        ]);

        $fraisSociete = FraisSociete::create($request->all());

        return response()->json($fraisSociete, 201);
    }

    // GET: Récupérer un frais de société spécifique
    public function show($id)
    {
        $fraisSociete = FraisSociete::find($id);

        if (!$fraisSociete) {
            return response()->json(['error' => 'Frais de société not found'], 404);
        }

        return response()->json($fraisSociete);
    }

    // PUT/PATCH: Mettre à jour un frais de société
    public function update(Request $request, $id)
    {
        $fraisSociete = FraisSociete::find($id);

        if (!$fraisSociete) {
            return response()->json(['error' => 'Frais de société not found'], 404);
        }

        $request->validate([
            'description' => 'nullable|string|max:255',
            'prix' => 'required|numeric',
        ]);

        $fraisSociete->update($request->all());

        return response()->json($fraisSociete);
    }

    // DELETE: Supprimer un frais de société
    public function destroy($id)
    {
        $fraisSociete = FraisSociete::find($id);

        if (!$fraisSociete) {
            return response()->json(['error' => 'Frais de société not found'], 404);
        }

        $fraisSociete->delete();

        return response()->json(['message' => 'Frais de société deleted successfully']);
    }
}
