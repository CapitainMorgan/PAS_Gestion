<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{

    public function addToCart(Request $request)
    {
        $cart = session()->get('cart', []);
        $cart[] = $request->input('article');
        session()->put('cart', $cart);

        return response()->json(['success' => true, 'cart' => $cart]);
    }

    public function getCart()
    {
        $cart = session()->get('cart', []);

        //reload all articles from the cart to get the updated article but keep the quantiteVente
        $cart = array_map(function ($article) {
            $article = Article::find($article['id']);
            $article->quantiteVente = 1;
            return $article;
        }, $cart);

        return response()->json(['cart' => $cart]);
    }

    public function clearCart()
    {
        session()->forget('cart');
        return response()->json(['success' => true]);
    }
}