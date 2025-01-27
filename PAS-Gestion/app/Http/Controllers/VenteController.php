<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Article;  // Pour valider l'existence de l'article
use App\Models\Utilisateur; // Pour valider l'existence de l'utilisateur
use Illuminate\Http\Request;
use Inertia\Inertia;

class VenteController extends Controller
{
    // GET: Récupérer toutes les ventes
    public function index()
    {
        return Inertia::render('Vente/Index', );
    }

    public function search(Request $request)
    {
        $ventes = Vente::with('article.fournisseur')
            ->where('created_at', 'like', '%' . $request->date . '%');

        $ventes = $ventes->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($ventes);
    }

    // POST: Créer une nouvelle vente
    public function store(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id', // Valide l'existence de l'article
            'utilisateur_id' => 'required|exists:utilisateurs,id', // Valide l'existence de l'utilisateur
        ]);

        $vente = Vente::create($request->all());

        return response()->json($vente, 201);
    }

    // GET: Récupérer une vente spécifique
    public function show($id)
    {
        $vente = Vente::find($id);

        if (!$vente) {
            return response()->json(['error' => 'Vente not found'], 404);
        }

        return response()->json($vente);
    }

    // PUT/PATCH: Mettre à jour une vente
    public function update(Request $request, $id)
    {
        $vente = Vente::find($id);

        if (!$vente) {
            return response()->json(['error' => 'Vente not found'], 404);
        }
        
        $vente->update($request->all());

        return response()->json($vente);
    }

    // DELETE: Supprimer une vente
    public function destroy($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $vente = Vente::find($id);

        if (!$vente) {
            return response()->json(['error' => 'Vente not found'], 404);
        }

        // Modifier le statut de l'article à 'En Stock'
        $article = Article::find($vente->article_id);
        $article->status = 'En Stock';
        $article->dateStatus = now();
        $article->quantite = $article->quantite + $vente->quantite;
        $article->save();

        $vente->delete();

        return response()->json(['message' => 'Vente deleted'], 200);
    }
}
