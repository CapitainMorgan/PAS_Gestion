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
        
        $articles = $fournisseur->articles->where('dateDepot', '==', $date_depot)->sortBy('id');

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
            $query->orderBy('dateStatus', 'desc')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc'); 
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


        $fournisseur->articles = $articles;        

        $articles_transit = Article::where('fournisseur_id_transit', $fournisseur->id)
            ->where('status', 'En transit')
            ->orderBy('created_at', 'desc')
            ->get();

        $articles_transit->map(function ($article) {
            $article->vente_total = Vente::where('article_id', $article->id)
                ->where('status', 'En transit')
                ->select(DB::raw('SUM(prix_unitaire * quantite) as total'))
                ->value('total');
            if ($article->vente_total == null) {
                $article->vente_total = 0;
            }
            return $article;
        });

        $articles_transit->map(function ($article) {
            $article->frais = Frais::where('article_id', $article->id)->sum('prix');
            return $article;
        });

        $fournisseur->articles_transit = $articles_transit;

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

    public function exportAllFournisseurs(Request $request)
    {
        $fournisseurs = Fournisseur::all();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        
        $sheet->setTitle('Fournisseurs');
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Prénom');        
        $sheet->setCellValue('D1', 'Date Création');
        $sheet->setCellValue('E1', 'rue');
        $sheet->setCellValue('F1', 'ville');
        $sheet->setCellValue('G1', 'code postal');
        $sheet->setCellValue('H1', 'pays');
        $sheet->setCellValue('I1', 'email');
        $sheet->setCellValue('J1', 'mobile');
        $sheet->setCellValue('K1', 'telephone');
        $sheet->setCellValue('L1', 'numPro');
        $sheet->setCellValue('M1', 'Remarque');

        $i = 2;
        foreach ($fournisseurs as $fournisseur) {
            $sheet->setCellValue('A' . $i, $fournisseur->id);
            $sheet->setCellValue('B' . $i, $fournisseur->nom);
            $sheet->setCellValue('C' . $i, $fournisseur->prenom);
            $sheet->setCellValue('D' . $i, $fournisseur->created_at);
            $sheet->setCellValue('E' . $i, $fournisseur->rue);
            $sheet->setCellValue('F' . $i, $fournisseur->ville);
            $sheet->setCellValue('G' . $i, $fournisseur->code_postal);
            $sheet->setCellValue('H' . $i, $fournisseur->pays);
            $sheet->setCellValue('I' . $i, $fournisseur->email);
            $sheet->setCellValue('J' . $i, $fournisseur->mobile);
            $sheet->setCellValue('K' . $i, $fournisseur->telephone);
            $sheet->setCellValue('L' . $i, $fournisseur->numPro);
            $sheet->setCellValue('M' . $i, $fournisseur->remarque);
            $i++;
        }

        
        $fileName = 'Export_Fournisseurs_' . date('Y-m-d_H-i-s') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
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
