<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depot extends Model
{
    use HasFactory;

    protected $fillable = [
        'fournisseur_id', 
        'dateDepot', 
        'dateEcheance'
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }
}
