<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\Depot;
use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

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

    public function generateFicheDepotFournisseur($id, $depot_id, $conditionGenerale)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        
        $articles = $fournisseur->articles->where('depot_id', $depot_id);
        $depot = Depot::findOrFail($depot_id);

        $conditionsArray = explode("\n", $conditionGenerale);

        $pdf = PDF::loadView('fiches.fiche_fournisseur', compact('fournisseur', 'articles','depot', 'conditionsArray'));
        return $pdf->download('fiche_fournisseur.pdf');
    }
    
    public function generateFicheVenteFournisseur($id,$date_debut, $conditionGenerale)
    {        
        # get all articles of the fournisseur with vente table
        $fournisseur = Fournisseur::with('articles.vente')->find($id);        
        $articles = $fournisseur->articles->where('vente.created_at', '>=', $date_debut);    
        $depot = NULL;   
        
        $conditionsArray = explode("\n", $conditionGenerale);

        $pdf = PDF::loadView('fiches.fiche_fournisseur', compact('fournisseur', 'articles','depot',  'conditionsArray'));
        return $pdf->download('fiche_fournisseur.pdf');
    }

    public function generateFicheFournisseur($id, $conditionGenerale)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $articles = NULL;
        $depot = NULL;  

        $conditionsArray = explode("\n", $conditionGenerale);

        $pdf = PDF::loadView('fiches.fiche_fournisseur', compact('fournisseur','articles','depot',  'conditionsArray'));
        return $pdf->download('fiche_fournisseur.pdf');
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

        $conditionGenerale = config('app_settings.conditions_generales');

        if (!$fournisseur) {
            return response()->json(['error' => 'Fournisseur not found'], 404);
        }

        return Inertia::render('Fournisseur/Show', [
            'fournisseur' => $fournisseur,
            'conditionGenerale' => $conditionGenerale,
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
