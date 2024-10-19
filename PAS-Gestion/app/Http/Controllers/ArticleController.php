<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Depot;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleController extends Controller
{
    // GET: Récupérer tous les articles
    public function index()
    {
        $articles = Article::all();
        return Inertia::render('Article/Index', [
            'articles' => $articles,
        ]);
    }

    public function create()
    {
        $fournisseurs = Fournisseur::all(); // Récupère tous les fournisseurs
        return inertia('Article/Create', [
            'fournisseurs' => $fournisseurs,
        ]);
    }

    // POST: Créer un nouvel article
    public function store(Request $request)
    {
        // Crée un dépôt liant l'article et le fournisseur
        $depot = Depot::create([
            'fournisseur_id' => $request->fournisseur_id,
            'dateDepot' => now(),
            // now + 30 jours
            'dateEcheance' => now()->addDays(30),
        ]);

        $request->validate([
            'description' => 'required|string|max:255',
            'taille' => 'nullable|numeric',
            'prixVente' => 'nullable|numeric',
            'prixClient' => 'nullable|numeric',
            'prixSolde' => 'nullable|numeric',
        ]);        

        //add depot_id to request
        $request->merge(['depot_id' => $depot->id]);

        //add status to request
        $request->merge(['status' => 'En stock']);

        $article = Article::create($request->all());        

        return redirect()->route('article.index')->with('success', 'Article créé avec succès');;
    }

    // GET: Récupérer un article spécifique
    public function show($id)
    {
        $article = Article::with("fournisseur")->find($id);

        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        return Inertia::render('Article/Show', [
            'article' => $article,
        ]);
    }

    // PUT/PATCH: Mettre à jour un article
    public function update(Request $request, $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        $article->update($request->all());

        return response()->json($article);
    }

    // DELETE: Supprimer un article
    public function destroy($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        $article->delete();

        return response()->json(['message' => 'Article deleted successfully']);
    }
}
