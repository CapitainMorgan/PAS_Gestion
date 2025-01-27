<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\Article;
use App\Models\Vente;
use App\Models\Frais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use DateTime;

class FournisseurController extends Controller
{
    // GET: Récupérer tous les fournisseurs
    public function index()
    {
        //$fournisseurs = Fournisseur::all();

        return Inertia::render('Fournisseur/Index', [
            //'fournisseurs' => $fournisseurs,
        ]);
    }

    // GET: Rechercher un fournisseur
    public function search(Request $request)
    {
        $search = $request->search;
        
        $keywords = explode(' ', $search);

        $fournisseurs = Fournisseur::where(function ($query) use ($keywords) {
            foreach ($keywords as $keyword) {
                $query->where(function ($subquery) use ($keyword) {
                    $subquery->where('nom', 'like', "%{$keyword}%")
                        ->orWhere('prenom', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('telephone', 'like', "%{$keyword}%")
                        ->orWhere('id', 'like', "%{$keyword}%")
                        ->orWhere('mobile', 'like', "%{$keyword}%")
                        ->orWhere('numProf', 'like', "%{$keyword}%");
                });
            }
        });

        $fournisseurs = $fournisseurs->orderBy('id', 'asc')->paginate(10);

        return response()->json($fournisseurs);
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

    public function generateFicheDepotFournisseur($id, $date_depot, $conditionGenerale)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        
        $articles = $fournisseur->articles->where('dateDepot', '==', $date_depot);

        $conditionsArray = explode("\n", $conditionGenerale);

        $status = 'depot';

        $pdf = PDF::loadView('fiches.fiche_fournisseur', compact('fournisseur', 'articles','conditionsArray', 'status', 'date_depot'));
        return $pdf->stream('fiche_fournisseur.pdf');
    }
    
    public function generateFicheVenteFournisseur($id, $date_debut, $conditionGenerale)
    {        
        # get all articles of the fournisseur with vente table
        $fournisseur = Fournisseur::with('articles')->find($id);        
        $articles = $fournisseur->articles;  
        
        // get all ventes where article_id is in the articles array
        $ventes = Vente::with('article')->whereIn('article_id', $articles->pluck('id'))->get();        

        // remove ventes that are not in the date range
        $ventes = $ventes->filter(function ($vente) use ($date_debut) {
            return new DateTime($vente->created_at) >= new DateTime($date_debut);
        });
        
        $conditionsArray = explode("\n", $conditionGenerale);

        $status = 'vente';

        $pdf = PDF::loadView('fiches.fiche_fournisseur_vente', compact('fournisseur', 'ventes',  'conditionsArray', 'status'))
            ->setPaper('a4', 'portrait')
            ->setOptions(['isHtml5ParserEnabled' => true, 'isPhpEnabled' => true]);

        return $pdf->stream('fiche_fournisseur_vente.pdf');
    }

    public function generateFicheFournisseur($id, $conditionGenerale)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $articles = NULL;

        $conditionsArray = explode("\n", $conditionGenerale);

        $status = 'fournisseur';

        $pdf = PDF::loadView('fiches.fiche_fournisseur', compact('fournisseur','articles',  'conditionsArray', 'status'));
        return $pdf->stream('fiche_fournisseur.pdf');
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
        $fournisseur = Fournisseur::with(['articles' => function ($query) {
            $query->orderBy('created_at', 'desc'); 
        }])->find($id);

        $articles = $fournisseur->articles;

        // get all frais of the articles and sum them and add the result to the article
        $articles->map(function ($article) {
            $article->frais = Frais::where('article_id', $article->id)->sum('prix');
            return $article;
        });

        // get all vente of the articles and sum all prix_unitaire * quantite them and add the result to the article
        $articles->map(function ($article) {
            $article->vente_total = Vente::where('article_id', $article->id)
                ->select(DB::raw('SUM(prix_unitaire * quantite) as total'))
                ->value('total');
            if ($article->vente_total == null) {
                $article->vente_total = 0;
            }
            return $article;
        });

        //sort article by created_at
        $articles = $articles->sortByDesc('created_at');

        $fournisseur->articles = $articles;

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
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $fournisseur = Fournisseur::find($id);

        if (!$fournisseur) {
            return response()->json(['error' => 'Fournisseur not found'], 404);
        }

        $fournisseur->delete();

        return redirect()->route('fournisseur.index')->with('message', 'Fournisseur supprimer avec succès.');
    }
}
