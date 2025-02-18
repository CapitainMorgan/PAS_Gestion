<?php
namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Depot;
use App\Models\Frais;
use App\Models\Fournisseur;
use App\Models\Vente;
use App\Models\User;
use App\Models\FraisSociete;
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
        //$articles = Article::orderBy('created_at', 'desc')->with('fournisseur')->get();
        return Inertia::render('Article/Index', [
            //'articles' => $articles,
        ]);
    }

    public function search(Request $request)
    {
        $articles = Article::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $keywords = explode(' ', $search);
            $status = $request->input('status');
            $articles->where(function ($query) use ($keywords, $status) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($subQuery) use ($keyword, $status) {
                        $subQuery->where('description', 'like', "%{$keyword}%")
                                 ->orWhere('localisation', 'like', "%{$keyword}%")
                                 ->orWhere('taille', 'like', "%{$keyword}%")
                                 ->orWhere('id', 'like', "%{$keyword}%");
                    });
                }
            });
            
            if (!empty($status)) {
                $articles->where('status', '=', $status);
            }
        }

        $articles = $articles->orderBy('created_at', 'desc')->paginate(10); // 10 articles par page
        return response()->json($articles);
    }

    public function getArticleByBarcode($barcode)
    {           
        //check if there is ' in the barcode

        if (strpos($barcode, '\'') === false && strpos($barcode, '-') === false) {
            $id = $barcode;
        } else {     
            // idbarcode = barcode without '
            $idbarcode = str_replace("'", "", $barcode);
            $idbarcode = str_replace("-", "", $idbarcode);
            //parse the barcode 
            $barcode = explode('\'', $barcode);
            if (count($barcode) == 1) {
                $barcode = explode('-', $barcode[0]);
            }
            $id = $barcode[1];
        }
        
        $article = Article::find($id);

        if (!$article) {
            # check with full barcode without '
            $article = Article::find($idbarcode);
            if (!$article) {
                return response()->json(['error' => $idbarcode], 404);
            }
        }

        return response()->json([
            'article' => $article, // Retourne l'article trouvé
        ]);
    }

    public function changeStatusArticles(Request $request)
    {
        $articles = $request->input('articles');

        $status = $request->input('status');

        $status_vente = $request->input('vente_status');

        foreach ($articles as $article) {

            $articleItem = Article::find($article['id']);

            if (!$articleItem) {
                return response()->json(['error' => 'Article not found'], 404);
            }
            // check if prixSolde is set
            if ($article['prixSolde'] != NULL && $article['prixSolde'] != 0) {
                $prix = $article['prixSolde'];
            }else{
                $prix = $article['prixVente'];
            }
            $vente = $this->createVenteIfStatusIsVendu($article['id'], $status, $status_vente, $prix ,$article['quantiteVente']);

            if ($vente) {
                $articleItem->vente_id = $vente->id;
            }        
            
            if( $status == 'Vendu'){
                if ($article['quantiteVente'] > $article['quantite']) {
                    return response()->json(['error' => 'La quantité vendue ne peut pas être supérieure à la quantité en stock'], 400);
                }
                
                // met a jour la quantite
                $articleItem->quantite = $article['quantite'] - $article['quantiteVente'] ;
            }

            if ($status == 'Vendu' && $articleItem->quantite != 0) {
                $articleItem->status = 'En Stock';
            } else {                
                $articleItem->status = $status;
            }

            if ($status == 'Rendu' && $status_vente == 'Cash')
            {
                $articleItem->color = "#00ff2a";
            }

            $articleItem->dateStatus = now();

            $articleItem->save();

        }

        return response()->json(['message' => 'Articles mis à jour avec succès']);
    }    


    public function getArticlesByEndDate()
    {
        $articles = Article::where('dateEcheance', '<', now())->where('statusMail', 0)->where('status', 'En Stock')->with('fournisseur')->orderBy('dateEcheance', 'asc')->get();
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

        // Trouver le fournisseur de l'article
        $user = Fournisseur::find($articles[0]->fournisseur_id);

        if (!$user || !$user->email) {
            return response()->json(['error' => 'Aucun utilisateur ou email trouvé pour ces articles'], 404);
        }

        // Envoi de l'email
        Mail::to($user->email)->send(new ArticleEndReminderMail($articles));

        // Mettre à jour le statut des articles
        Article::whereIn('id', $articleIds)->update(['statusMail' => 1]);

        $articles = Article::where('dateEcheance', '<', now())->where('statusMail', 0)->where('status', 'En Stock')->with('fournisseur')->orderBy('dateEcheance', 'asc')->get();
        return Inertia::render('Dashboard', [
            'articles' => $articles,
        ]);
    }

    public function generateBarcode($id)
    {
        $article = Article::with("fournisseur")->find($id);

        $id = $article->id;

        //test si l'id de l'article commence par l'id du fournisseur et fini par la date de creation "Ymd"
        if (str_starts_with($id, $article->fournisseur->id) && str_ends_with($id, $article->created_at->format('dmy'))) {
            //id = id de l'article sans le fournisseur et la date de creation
            $id = str_replace($article->fournisseur->id, "", $id);
            $id = str_replace($article->created_at->format('dmy'), "", $id);            
        }

        // Générer le code-barres
        $code = $article->fournisseur->id . '-' . $id . '-' . $article->created_at->format('dmy');
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128);
        
        // Convertir en base64 pour l'afficher dans une vue HTML
        $barcodeBase64 = base64_encode($barcode);
        $barcodeImage = 'data:image/png;base64,' . $barcodeBase64;

        $barcodes[] = [
            'barcodeImage' => 'data:image/png;base64,' . $barcodeBase64,
            'code' => $code,
            'article' => $article
        ];

        return view('pdf.barcode', compact('barcodes'));
    }


    public function generateBarcodesForArticles($id, $date)
    {
        // Get all articles of a fournisseur generated at the date
        $articles = Article::with('fournisseur')->where('fournisseur_id', $id)->whereDate('created_at', $date)->get();
        $barcodes = [];

        foreach ($articles as $article) {
            $id = $article->id;

            if (str_starts_with($id, $article->fournisseur->id) && str_ends_with($id, $article->created_at->format('dmy'))) {
                $id = str_replace($article->fournisseur->id, "", $id);
                $id = str_replace($article->created_at->format('dmy'), "", $id);
            }

            $code = $article->fournisseur->id . '-' . $id . '-' . $article->created_at->format('dmy');
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128);
            $barcodeBase64 = base64_encode($barcode);

            $barcodes[] = [
                'barcodeImage' => 'data:image/png;base64,' . $barcodeBase64,
                'code' => $code,
                'article' => $article
            ];
        }

        return view('pdf.barcode', compact('barcodes'));
    }

    public function create()
    {
        $fournisseurs = Fournisseur::all(); // Récupère tous les fournisseurs
        return inertia('Article/Create', [
            'fournisseurs' => $fournisseurs,
        ]);
    }

    private function createVenteIfStatusIsVendu($articleId, $status, $status_vente, $prix, $quantite = 1)
    {
        if ($status_vente == NULL) {
            $status_vente = 'Cash';
        }

        if ($status == 'Vendu') {
            $vente = new Vente();
            $vente->quantite = $quantite;
            $vente->prix_unitaire = $prix;
            $vente->article_id = $articleId;
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
        //get number of articles created today of the same fournisseur
        $index = Article::where('fournisseur_id', $request->fournisseur_id)->whereDate('created_at', now())->count();

        //build id of the article with fournisseur_id + index + date
        $id = $request->fournisseur_id . $index + 1 . now()->format('dmy');

        //put the id in the request
        $request->merge(['id' => $id]);

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
        $request->merge(['status' => 'En Stock']);

        $request->merge(['dateStatus' => now()]);

        $article = Article::create($request->all());  

        return redirect()->route('article.show', $article->id)->with('success', 'Article créé avec succès');;
    }

    public function storeGroupedArticles(Request $request, $EchanceDays = 30)
    {        
        

        //get array of articles from request
        $articles = $request->articles;

        //if $EchanceDays is not int convert it to int
        if (!is_int($EchanceDays)) {
            $EchanceDays = (int) $EchanceDays;
        }

        //get fournisseur_id from request
        $fournisseur_id = $request->articles[0]['fournisseur_id'];

        //get number of articles created today of the same fournisseur
        $index = Article::where('fournisseur_id', $fournisseur_id)->whereDate('created_at', now())->count();

        foreach ($articles as $article) {
            $index++;
            $article['id'] = $fournisseur_id . $index . now()->format('dmy');
            $article['status'] = 'En Stock';
            $article['vente_id'] = null;
            $article['fournisseur_id'] = $fournisseur_id;
            $article['dateDepot'] = now();
            $article['dateEcheance'] = now()->addDays($EchanceDays);
            $article['dateStatus'] = now();
            $article['utilisateur_id'] = auth()->user()->id;

            $createdArticle = Article::create($article);

            $barcodeUrls[] = `/generate-barcode/` . $createdArticle->id;
        }


        return response()->json([
            'success' => true,
            'barcodeUrls' => $barcodeUrls,
        ]);
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

        $article = Article::find($id);

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

        //check if status changed
        if ($request->status != $article->status) {
            if($request->dateStatus == $article->dateStatus){
                $request->merge(['dateStatus' => now()]);
            }
        }

        $article->update($request->all());
        
        return redirect()->route('article.show', $id)->with('message', 'Article modifié avec succès.');
    }

    // PUT/PATCH: Mettre à jour un article
    public function updateIsPaid(Request $request, $id)
    {

        $article = Article::find($id);

        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        $article->update($request->article);
        
        return response()->json(['success' => true,'message' => 'Article modifié avec succès']);
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

    public function exportArticles(Request $request)
    {
        // Récupérer les données depuis la vue
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $articles = Article::with('vente', 'user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $ventes = Vente::whereBetween('created_at', [$startDate, $endDate])->with('article')->with('user')->get();

        $fournisseurs = Fournisseur::whereBetween('created_at', [$startDate, $endDate])->get();

        $articlesStatus = Article::whereBetween('dateStatus', [$startDate, $endDate])->get();

        $fraisSocietes = FraisSociete::whereBetween('created_at', [$startDate, $endDate])->get();
        
        // Création du fichier Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        // Onglet 0 : Informations générales
        $sheet0 = $spreadsheet->getActiveSheet();
        $sheet0->setTitle('Informations');
        $sheet0->setCellValue('A1', 'Date Export');
        $sheet0->setCellValue('B1', 'Date Début');
        $sheet0->setCellValue('C1', 'Date Fin');
        $sheet0->setCellValue('A2', now());
        $sheet0->setCellValue('B2', $startDate);
        $sheet0->setCellValue('C2', $endDate);


        // Onglet 1 : Ventes
        $sheet1 = $spreadsheet->createSheet();
        $sheet1->setTitle('Ventes');
        $sheet1->setCellValue('A1', 'ID');
        $sheet1->setCellValue('B1', 'Date');
        $sheet1->setCellValue('C1', 'Prix Unitaire');
        $sheet1->setCellValue('D1', 'Quantité');
        $sheet1->setCellValue('E1', 'Total');
        $sheet1->setCellValue('F1', 'Utilisateur');
        $sheet1->setCellValue('G1', 'Article');    
        $sheet1->setCellValue('H1', 'Status');
        $sheet1->setCellValue('I1', 'ID Article');
        $row = 2;
        foreach ($ventes as $vente) {
            $sheet1->setCellValue('A' . $row, $vente->id);
            $sheet1->setCellValue('B' . $row, $vente->created_at);
            $sheet1->setCellValue('C' . $row, $vente->prix_unitaire);
            $sheet1->setCellValue('D' . $row, $vente->quantite);
            $sheet1->setCellValue('E' . $row, $vente->prix_unitaire * $vente->quantite);
            $sheet1->setCellValue('F' . $row, $vente->user->name);
            $sheet1->setCellValue('G' . $row, $vente->article->description);
            $sheet1->setCellValue('H' . $row, $vente->status);
            $sheet1->setCellValue('I' . $row, $vente->article_id);
            $row++;
        }

        // Onglet 2 : Articles
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Articles');
        $sheet2->setCellValue('A1', 'ID');
        $sheet2->setCellValue('B1', 'Description');
        $sheet2->setCellValue('C1', 'Date Création');
        $sheet2->setCellValue('D1', 'Localisation');
        $sheet2->setCellValue('E1', 'Créateur');
        $sheet2->setCellValue('F1', 'Quantité');
        $sheet2->setCellValue('G1', 'Prix Vente');
        $sheet2->setCellValue('H1', 'Prix Client');
        $sheet2->setCellValue('I1', 'Prix Solde');
        $sheet2->setCellValue('J1', 'Status');
        $sheet2->setCellValue('K1', 'Est payé');
        $row = 2;
        foreach ($articles as $article) {
            $sheet2->setCellValue('A' . $row, $article->id);
            $sheet2->setCellValue('B' . $row, $article->description);
            $sheet2->setCellValue('C' . $row, $article->created_at);
            $sheet2->setCellValue('D' . $row, $article->localisation);
            $sheet2->setCellValue('E' . $row, $article->user->name);
            $sheet2->setCellValue('F' . $row, $article->quantite);
            $sheet2->setCellValue('G' . $row, $article->prixVente);
            $sheet2->setCellValue('H' . $row, $article->prixClient);
            $sheet2->setCellValue('I' . $row, $article->prixSolde);
            $sheet2->setCellValue('J' . $row, $article->status);
            $sheet2->setCellValue('K' . $row, $article->isPaid ? 'Oui' : 'Non');
            $row++;
        }

        // Onglet 3 : Fournisseurs
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Fournisseurs');
        $sheet3->setCellValue('A1', 'ID');
        $sheet3->setCellValue('B1', 'Nom');
        $sheet3->setCellValue('C1', 'Date Création');
        $sheet3->setCellValue('D1', 'rue');
        $sheet3->setCellValue('E1', 'ville');
        $sheet3->setCellValue('F1', 'code postal');
        $sheet3->setCellValue('G1', 'pays');
        $sheet3->setCellValue('H1', 'email');
        $sheet3->setCellValue('I1', 'mobile');
        $sheet3->setCellValue('J1', 'telephone');
        $sheet3->setCellValue('K1', 'numPro');
        $row = 2;
        foreach ($fournisseurs as $fournisseur) {
            $sheet3->setCellValue('A' . $row, $fournisseur->id);
            $sheet3->setCellValue('B' . $row, $fournisseur->nom . ' ' . $fournisseur->prenom);
            $sheet3->setCellValue('C' . $row, $fournisseur->created_at);
            $sheet3->setCellValue('D' . $row, $fournisseur->rue);
            $sheet3->setCellValue('E' . $row, $fournisseur->ville);
            $sheet3->setCellValue('F' . $row, $fournisseur->npa);
            $sheet3->setCellValue('G' . $row, $fournisseur->pays);
            $sheet3->setCellValue('H' . $row, $fournisseur->email);
            $sheet3->setCellValue('I' . $row, $fournisseur->mobile);
            $sheet3->setCellValue('J' . $row, $fournisseur->telephone);
            $sheet3->setCellValue('K' . $row, $fournisseur->numProf);
            $row++;
        }

        // Onglet 4 : Articles avec changement de statut
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Articles Status');
        $sheet4->setCellValue('A1', 'ID');
        $sheet4->setCellValue('B1', 'Description');
        $sheet4->setCellValue('C1', 'Date Création');
        $sheet4->setCellValue('D1', 'Localisation');
        $sheet4->setCellValue('E1', 'Créateur');
        $sheet4->setCellValue('F1', 'Quantité');
        $sheet4->setCellValue('G1', 'Prix Vente');
        $sheet4->setCellValue('H1', 'Prix Client');
        $sheet4->setCellValue('I1', 'Prix Solde');
        $sheet4->setCellValue('J1', 'Status');
        $sheet4->setCellValue('K1', 'Date Status');
        $row = 2;
        foreach ($articlesStatus as $article) {
            $sheet4->setCellValue('A' . $row, $article->id);
            $sheet4->setCellValue('B' . $row, $article->description);
            $sheet4->setCellValue('C' . $row, $article->created_at);
            $sheet4->setCellValue('D' . $row, $article->localisation);
            $sheet4->setCellValue('E' . $row, $article->user->name);
            $sheet4->setCellValue('F' . $row, $article->quantite);
            $sheet4->setCellValue('G' . $row, $article->prixVente);
            $sheet4->setCellValue('H' . $row, $article->prixClient);
            $sheet4->setCellValue('I' . $row, $article->prixSolde);
            $sheet4->setCellValue('J' . $row, $article->status);
            $sheet4->setCellValue('K' . $row, $article->dateStatus);
            $row++;
        }

        // Onglet 5 : Frais Société
        $sheet5 = $spreadsheet->createSheet();
        $sheet5->setTitle('Frais Société');
        $sheet5->setCellValue('A1', 'ID');
        $sheet5->setCellValue('B1', 'Description');
        $sheet5->setCellValue('C1', 'Prix');
        $row = 2;
        foreach ($fraisSocietes as $frais) {
            $sheet5->setCellValue('A' . $row, $frais->id);
            $sheet5->setCellValue('B' . $row, $frais->description);
            $sheet5->setCellValue('C' . $row, $frais->prix);
            $row++;
        }

        // Téléchargement du fichier
        $fileName = 'Export_Articles_' . date('Y-m-d_H-i-s') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        
        exit();
    }
}
