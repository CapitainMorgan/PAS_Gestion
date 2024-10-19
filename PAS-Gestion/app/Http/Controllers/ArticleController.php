<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Depot;
use App\Models\Frais;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Milon\Barcode\BarcodeGenerator;
use Milon\Barcode\DNS1D;
use Barryvdh\Snappy\Facades\SnappyPdf;

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

    public function generateBarcode($id)
    {
        $article = Article::with("fournisseur")->find($id);

        // Générer le code-barres
        $code = $article->fournisseur[0]->id . '-' . $article->id . '-' . $article->created_at->format('Ymd');
        $generator = new DNS1D();
        $barcode = $generator->getBarcodeHTML($code, 'C39');
        
        // Créer un PDF avec le code-barres
        $pdf = SnappyPdf::loadView('pdf.barcode', ['barcode' => base64_encode($barcode), 'code' => $code]);
        // Retourner le PDF au navigateur
        return $pdf->inline('barcode.pdf');
    }

    public function create()
    {
        $fournisseurs = Fournisseur::all(); // Récupère tous les fournisseurs
        return inertia('Article/Create', [
            'fournisseurs' => $fournisseurs,
        ]);
    }

    public function edit($id)
    {
        $article = Article::findOrFail($id);

        return Inertia::render('Article/Edit', [
            'article' => $article,
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

        //add depot_id to request
        $request->merge(['depot_id' => $depot->id]);

        //add status to request
        $request->merge(['status' => 'En stock']);

        $article = Article::create($request->all());        

        return redirect()->route('article.show', $article->id)->with('success', 'Article créé avec succès');;
    }

    // GET: Récupérer un article spécifique
    public function show($id)
    {
        $article = Article::with("fournisseur")->with('frais')->find($id);


        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        return Inertia::render('Article/Show', [
            'article' => $article,
        ]);
    }

    public function storeFrais(Request $request, $articleId)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'prix' => 'required|numeric',
        ]);

        $frais = new Frais();
        $frais->description = $validated['description'];
        $frais->prix = $validated['prix'];
        $frais->article_id = $articleId;
        $frais->save();

        return redirect()->back()->with('message', 'Frais ajouté avec succès.');
    }

    // PUT/PATCH: Mettre à jour un article
    public function update(Request $request, $id)
    {

        $article = Article::find($id);

        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        $article->update($request->all());

        return redirect()->route('article.show', $id)->with('message', 'Article modifié avec succès.');
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
