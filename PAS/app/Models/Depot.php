<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depot extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id', 
        'fournisseur_id', 
        'dateDepot', 
        'dateEcheance'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }
}
