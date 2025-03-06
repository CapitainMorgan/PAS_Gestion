<?php

namespace App\Http\Controllers;

use App\Models\Article; // Pour valider l'existence de l'article
use Illuminate\Http\Request;

class DepotController extends Controller
{

    public function create($id)
    {
        return inertia('Depot/Create', [
            'fournisseur_id' => $id,
        ]);
    }   

    public function addToDepot(Request $request)
    {
        $depot = session()->get('depot', []);

        // Récupérer les données de l'article et son fournisseur_id
        $article = $request->input('article');
        $fournisseurId = $request->input('fournisseur_id');

        // Vérifier si le fournisseur existe déjà dans le dépôt
        if (!isset($depot[$fournisseurId])) {
            $depot[$fournisseurId] = [];
        }

        // Ajouter l'article en début de liste pour ce fournisseur
        array_unshift($depot[$fournisseurId], $article);

        // Sauvegarder dans la session
        session()->put('depot', $depot);

        $articles = $depot[$fournisseurId] ?? [];

        return response()->json(['success' => true, 'depot' => $articles]);
    }

    public function getDepot(Request $request)
    {
        $depot = session()->get('depot', []);
        $fournisseurId = $request->input('fournisseur_id');
        $articles = isset($depot[$fournisseurId]) ? $depot[$fournisseurId] : [];

        return response()->json(['depot' => $articles]);
    }

    public function removeArticle(Request $request)
    {
        $depot = session()->get('depot', []);

        $description = $request->input('description');
        $fournisseurId = $request->input('fournisseur_id');

        if (isset($depot[$fournisseurId])) {
            // Rechercher l'index de l'article à supprimer dans le tableau du fournisseur
            $index = array_search($description, array_column($depot[$fournisseurId], 'description'));
        
            if ($index !== false) {
                // Supprimer l'article de la liste du fournisseur
                unset($depot[$fournisseurId][$index]);
        
                // Réindexer les articles du fournisseur pour éviter des clés manquantes
                $depot[$fournisseurId] = array_values($depot[$fournisseurId]);
        
                // Si plus aucun article pour ce fournisseur, on le supprime du dépôt
                if (empty($depot[$fournisseurId])) {
                    unset($depot[$fournisseurId]);
                }
            }
        }

        session()->put('depot', $depot);

        
        $articles = $depot[$fournisseurId] ?? [];

        return response()->json(['success' => true, 'depot' => $articles]);
    }

    public function clearDepot(Request $request)
    {
        $depot = session()->get('depot', []);
        $fournisseurId = $request->input('fournisseur_id');

        // Si un fournisseur_id est spécifié, supprimer uniquement ses articles
        if ($fournisseurId !== null) {
            unset($depot[$fournisseurId]);
        } else {
            // Si aucun fournisseur_id n'est donné, on supprime tout le dépôt
            $depot = [];
        }

        session()->put('depot', $depot);

        return response()->json(['success' => true]);
    }
}
