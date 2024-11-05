<?php

namespace App\Http\Controllers;

use App\Models\FraisSociete; // Assurez-vous d'avoir créé ce modèle
use Illuminate\Http\Request;
use Inertia\Inertia;

class FraisSocieteController extends Controller
{
    // GET: Récupérer tous les frais de la société
    public function index()
    {
        $fraisSocietes = FraisSociete::all();
        
        return Inertia::render('FraisSociete/Index', [
            'frais' => $fraisSocietes,
        ]);
    }

    // POST: Créer un nouveau frais de société
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string|max:255',
            'prix' => 'required|numeric', // Le prix est obligatoire
        ]);

        $fraisSociete = FraisSociete::create($request->all());

        return redirect()->route('frais.index')->with('success', 'Frais créé avec succès');;
    }

    // GET: Récupérer un frais de société spécifique
    public function show($id)
    {
        $fraisSociete = FraisSociete::find($id);

        if (!$fraisSociete) {
            return response()->json(['error' => 'Frais de société not found'], 404);
        }

        return Inertia::render('FraisSociete/Show', [
            'frais' => $fraisSociete,
        ]);
    }

    public function create()
    {
        return inertia('FraisSociete/Create', []);
    }

    public function edit($id)
    {
        $fraisSociete = FraisSociete::findOrFail($id);

        return Inertia::render('FraisSociete/Edit', [
            'frais' => $fraisSociete,
        ]);
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

        return redirect()->route('frais.index')->with('success', 'Frais modifié avec succès');
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
