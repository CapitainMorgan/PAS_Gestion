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

        array_unshift($cart, $request->input('article'));

        session()->put('cart', $cart);

        return response()->json(['success' => true, 'cart' => $cart]);
    }

    public function updateArticle(Request $request)
    {
        $cart = session()->get('cart', []);

        $articles = $request->input('articles');
        foreach ($articles as $article) {
            $index = array_search($article['id'], array_column($cart, 'id'));

            if ($index !== false) {
                $cart[$index]['quantiteVente'] = $article['quantiteVente'];
                $cart[$index]['prixSolde'] = $article['prixSolde'];
            }
        }

        session()->put('cart', $cart);

        return response()->json(['success' => true, 'cart' => $cart]);
    }

    public function getCart()
    {
        $cart = session()->get('cart', []);

        //reload all articles from the cart to get the updated article but keep the quantiteVente
        $cart = array_map(function ($article) {
            $quantiteVente = $article['quantiteVente'];
            $prixSolde = $article['prixSolde'];
            $article = Article::find($article['id']);
            $article->quantiteVente = $quantiteVente;
            $article->prixSolde = $prixSolde;
            return $article;
        }, $cart);

        return response()->json(['cart' => $cart]);
    }

    public function removeArticle(Request $request)
    {
        $cart = session()->get('cart', []);

        $index = array_search($request->input('id'), array_column($cart, 'id'));

        if ($index !== false) {
            array_splice($cart, $index, 1);
        }

        session()->put('cart', $cart);

        return response()->json(['success' => true, 'cart' => $cart]);
    }

    public function exportCart()
    {
        $articles = session()->get('cart', []);


         // Création du fichier Excel
         $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Description');
        $sheet->setCellValue('B1', 'Taille');
        $sheet->setCellValue('C1', 'Quantité');
        $sheet->setCellValue('D1', 'PrixVente');
        $sheet->setCellValue('E1', 'PrixSolde');
        $sheet->setCellValue('F1', 'PrixClient');
        $sheet->setCellValue('G1', 'Localisation');
        $sheet->setCellValue('H1', 'Status');
        $sheet->setCellValue('J1', 'Créer le');
        $sheet->setCellValue('K1', 'Modifier le');

        $row = 2;

        foreach ($articles as $article) {
            $sheet->setCellValue('A' . $row, $article['description']);
            $sheet->setCellValue('B' . $row, $article['taille']);
            $sheet->setCellValue('C' . $row, $article['quantiteVente']);
            $sheet->setCellValue('D' . $row, $article['prixVente']);
            $sheet->setCellValue('E' . $row, $article['prixSolde']);
            $sheet->setCellValue('F' . $row, $article['prixClient']);
            $sheet->setCellValue('G' . $row, $article['localisation']);
            $sheet->setCellValue('H' . $row, $article['status']);
            $sheet->setCellValue('J' . $row, $article['created_at']);
            $sheet->setCellValue('K' . $row, $article['updated_at']);

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

    public function clearCart()
    {
        session()->forget('cart');
        return response()->json(['success' => true]);
    }
}