<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Article;

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
        'mobile', 
        'numProf', 
        'remarque', 
        'email', 
        'telephone'
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
