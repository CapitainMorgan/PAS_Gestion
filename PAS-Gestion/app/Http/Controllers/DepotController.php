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

        array_unshift($depot, $request->input('article'));

        session()->put('depot', $depot);

        return response()->json(['success' => true, 'depot' => $depot]);
    }

    public function getDepot()
    {
        $depot = session()->get('depot', []);

        return response()->json(['depot' => $depot]);
    }

    public function removeArticle(Request $request)
    {
        $depot = session()->get('depot', []);

        $description = $request->input('description');
        $index = array_search($description, array_column($depot, 'description'));        

        if ($index !== false) {
            unset($depot[$index]);
            $depot = array_values($depot);
        }

        session()->put('depot', $depot);

        return response()->json(['success' => true, 'depot' => $depot]);
    }

    public function clearDepot()
    {
        session()->forget('depot');

        return response()->json(['success' => true]);
    }
}
