<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 
        'prénom', 
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
}
