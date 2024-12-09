<?php
namespace App\Exports;

use App\Models\Article;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArticlesExport implements FromView
{
    protected $startDate;
    protected $endDate;

    public function __construct(Request $request)
    {
        $this->startDate = $request['start_date'];
        $this->endDate = $request['end_date'];
    }

    public function view(): View
    {
        $articles = Article::with('vente')
            ->whereBetween('dateStatus', [$this->startDate, $this->endDate])
            ->get();

        return view('exports.articles', [
            'articles' => $articles
        ]);
    }
}
