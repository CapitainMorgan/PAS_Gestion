<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Depot;
use App\Models\Fournisseur;
use App\Models\Vente;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'description', 
        'taille',
        'quantite',
        'localisation', 
        'prixVente', 
        'prixClient', 
        'prixSolde',
        'status',
        'depot_id',
        'vente_id',
    ];

    public function depot()
    {
        return $this->belongsTo(Depot::class);
    }

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function fournisseur()
    {
        return $this->hasManyThrough(
            Fournisseur::class, 
            Depot::class, 
            'id', 
            'id', 
            'depot_id', 
            'fournisseur_id'
        );
    }
}


