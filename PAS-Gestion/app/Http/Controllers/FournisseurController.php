<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\Snappy\Facades\SnappyPdf;

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

    public function generateFicheDepotFournisseur($id, $depot_id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        
        $articles = $fournisseur->articles->where('depot_id', $depot_id);

        $pdf = SnappyPdf::loadView('fiches.fiche_fournisseur', compact('fournisseur', 'articles'));
        return $pdf->inline('fiche_fournisseur.pdf');
    }
    
    public function generateFicheVenteFournisseur($id,$date_debut)
    {        
        # get all articles of the fournisseur with vente table
        $fournisseur = Fournisseur::with('articles.vente')->find($id);        
        $articles = $fournisseur->articles->where('vente.created_at', '>=', $date_debut);        

        $pdf = SnappyPdf::loadView('fiches.fiche_fournisseur', compact('fournisseur', 'articles'));
        return $pdf->inline('fiche_fournisseur.pdf');
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
