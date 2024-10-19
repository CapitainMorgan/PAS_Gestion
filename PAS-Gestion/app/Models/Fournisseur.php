<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Article;
use App\Models\Depot;

class Fournisseur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 
        'prenom', 
        'rue', 
        'ville', 
        'npa', 
        'pays', 
        'dateCreation', 
        'mobile', 
        'numProf', 
        'remarque', 
        'email', 
        'telephone'
    ];

    public function articles()
    {
        return $this->hasManyThrough(
            Article::class,
            Depot::class,
            'fournisseur_id',
            'depot_id',
            'id',
            'id'
        );
    }
}
