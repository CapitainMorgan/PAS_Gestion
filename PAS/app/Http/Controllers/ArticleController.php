<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // GET: Récupérer tous les articles
    public function index()
    {
        $articles = Article::all();
        return response()->json($articles);
    }

    // POST: Créer un nouvel article
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'taille' => 'nullable|numeric',
            'prixVente' => 'nullable|numeric',
            'prixClient' => 'nullable|numeric',
            'prixSolde' => 'nullable|numeric',
        ]);

        $article = Article::create($request->all());

        return response()->json($article, 201);
    }

    // GET: Récupérer un article spécifique
    public function show($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        return response()->json($article);
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
