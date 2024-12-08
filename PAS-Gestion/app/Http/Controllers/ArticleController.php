<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Depot;
use App\Models\Frais;
use App\Models\Fournisseur;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ArticleEndReminderMail;
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

    public function getArticleByBarcode($barcode)
    {
        //parse the barcode 
        $barcode = explode('\'', $barcode);
        
        $article = Article::find($barcode[1]);

        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        return response()->json([
            'success' => true,
            'article' => $article, // Retourne l'article trouvé
        ]);
    }


    public function getArticlesByEndDate()
    {
        $articles = Article::where('dateEcheance', '<', now())->where('statusMail', 0)->with('fournisseur')->get();
        return Inertia::render('Dashboard', [
            'articles' => $articles,
        ]);
    }

    public function sendReminderForArticles(Request $request)
    {
        $articleIds = $request->input('article_ids');

        $articles = Article::whereIn('id', $articleIds)->get();

        if ($articles->isEmpty()) {
            return response()->json(['error' => 'Aucun article trouvé'], 404);
        }

        // Trouver les utilisateurs liés à ces articles
        $user = $articles->first()->user;

        if (!$user || !$user->email) {
            return response()->json(['error' => 'Aucun utilisateur ou email trouvé pour ces articles'], 404);
        }

        // Envoi de l'email
        Mail::to($user->email)->send(new ArticleEndReminderMail($articles));

        // Mettre à jour le statut des articles
        Article::whereIn('id', $articleIds)->update(['statusMail' => 1]);

        return response()->route('dashboard')->with('success', 'Email envoyé avec succès');
    }

    public function generateBarcode($id)
    {
        $article = Article::with("fournisseur")->find($id);

        // Générer le code-barres
        $code = $article->fournisseur->id . '-' . $article->id . '-' . $article->created_at->format('Ymd');
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128);
        
        // Convertir en base64 pour l'afficher dans une vue HTML
        $barcodeBase64 = base64_encode($barcode);
        $barcodeImage = 'data:image/png;base64,' . $barcodeBase64;

        return view('pdf.barcode', compact('barcodeImage', 'code'));
    }

    public function create()
    {
        $fournisseurs = Fournisseur::all(); // Récupère tous les fournisseurs
        return inertia('Article/Create', [
            'fournisseurs' => $fournisseurs,
        ]);
    }

    private function createVenteIfStatusIsVendu($articleId, $status, $status_vente = 'Cash', $quantite = 1)
    {
        if ($status_vente == NULL) {
            $status_vente = 'Cash';
        }

        if ($status == 'Vendu') {
            $vente = new Vente();
            $vente->quantite = $quantite;
            $vente->status = $status_vente;
            $vente->utilisateur_id = auth()->user()->id;
            $vente->save();

            return $vente;
        }

        return null;
    }

    public function edit($id)
    {
        $article = Article::findOrFail($id);

        return Inertia::render('Article/Edit', [
            'article' => $article,
        ]);
    }

    // POST: Créer un nouvel article
    public function store(Request $request, $EchanceDays = 30)
    {
        //add dateDepot to request
        $request->merge(['dateDepot' => now()]);

        //if $EchanceDays is not int convert it to int
        if (!is_int($EchanceDays)) {
            $EchanceDays = (int) $EchanceDays;
        }
        

        $request->merge(['dateEcheance' => now()->addDays($EchanceDays)]);

        //add utilisateur_id to request

        $request->merge(['utilisateur_id' => auth()->user()->id]);

        //add status to request
        $request->merge(['status' => 'En stock']);

        $article = Article::create($request->all());  

        return redirect()->route('article.show', $article->id)->with('success', 'Article créé avec succès');;
    }

    public function storeGroupedArticles(Request $request, $EchanceDays = 30)
    {        

        //get array of articles from request
        $articles = $request->articles;

        print_r( $articles);

        //if $EchanceDays is not int convert it to int
        if (!is_int($EchanceDays)) {
            $EchanceDays = (int) $EchanceDays;
        }

        //get fournisseur_id from request
        $fournisseur_id = $request->articles[0]['fournisseur_id'];

        foreach ($articles as $article) {
            $article['status'] = 'En stock';
            $article['vente_id'] = null;
            $article['fournisseur_id'] = $fournisseur_id;
            $article['dateDepot'] = now();
            $article['dateEcheance'] = now()->addDays($EchanceDays);
            $article['dateEcheance'] = now()->addDays(30);
            $article['utilisateur_id'] = auth()->user()->id;

            Article::create($article);
        }

        return redirect()->route('fournisseur.show', $fournisseur_id)->with('success', 'Articles créés avec succès');
    }

    // GET: Récupérer un article spécifique
    public function show($id)
    {
        $article = Article::with("fournisseur")->with('frais')->with('user')->find($id);

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

    public function updateFrais(Request $request, $fraisId)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'prix' => 'required|numeric',
        ]);

        $frais = Frais::find($fraisId);
        $frais->description = $validated['description'];
        $frais->prix = $validated['prix'];
        $frais->save();

        return redirect()->back()->with('message', 'Frais modifié avec succès.');
    }

    // PUT/PATCH: Mettre à jour un article
    public function update(Request $request, $id)
    {

        $article = Article::with('vente')->find($id);

        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }
        # if status is changed
        if ($request->status != $article->status) {
            $vente = $this->createVenteIfStatusIsVendu($request->id, $request->status, $request->status_vente, $request->quantite);

            if ($vente) {
                $request->merge(['vente_id' => $vente->id]);
            }
        }

        $article->update($request->all());

        return redirect()->route('article.show', $id)->with('message', 'Article modifié avec succès.');
    }

    // DELETE: Supprimer un article
    public function destroy($id)
    {
        
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $article = Article::find($id);

        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        $article->delete();

        return redirect()->route('article.index')->with('message', 'Article supprimer avec succès.');
    }
}
