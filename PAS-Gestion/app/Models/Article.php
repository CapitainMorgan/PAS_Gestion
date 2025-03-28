<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Fournisseur;
use App\Models\Vente;
use App\Models\Frais;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'description', 
        'taille',
        'quantite',
        'localisation', 
        'prixVente', 
        'prixClient', 
        'prixSolde',
        'status',
        'dateDepot', 
        'dateEcheance',
        'dateStatus',
        'statusMail',
        'color',
        'isPaid',
        'fournisseur_id',
        'fournisseur_id_transit',
        'utilisateur_id',
        'vente_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id');
    }

    public function frais()
    {
        return $this->hasMany(Frais::class);
    }
}


