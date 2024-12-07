<?php

namespace App\Http\Controllers;

use App\Models\Frais;
use Illuminate\Http\Request;

class FraisController extends Controller
{
    // GET: Récupérer tous les frais
    public function index()
    {
        $frais = Frais::all();
        return response()->json($frais);
    }

    // POST: Créer un nouveau frais
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string|max:255',
            'prix' => 'nullable|numeric',
            'article_id' => 'required|exists:articles,id',
        ]);

        $frais = Frais::create($request->all());

        return response()->json($frais, 201);
    }

    // GET: Récupérer un frais spécifique
    public function show($id)
    {
        $frais = Frais::find($id);

        if (!$frais) {
            return response()->json(['error' => 'Frais not found'], 404);
        }

        return response()->json($frais);
    }

    // PUT/PATCH: Mettre à jour un frais
    public function update(Request $request, $id)
    {
        $frais = Frais::find($id);

        if (!$frais) {
            return response()->json(['error' => 'Frais not found'], 404);
        }

        $frais->update($request->all());

        return response()->json($frais);
    }

    // DELETE: Supprimer un frais
    public function destroy($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $frais = Frais::find($id);

        $id = $frais->article_id;

        if (!$frais) {
            return response()->json(['error' => 'Frais not found'], 404);
        }

        $frais->delete();

        return redirect()->route('article.show', $id)->with('message', 'Frais deleted successfully');
    }
}
