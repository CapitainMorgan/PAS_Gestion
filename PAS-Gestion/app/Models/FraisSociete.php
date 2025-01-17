<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraisSociete extends Model
{
    use HasFactory;

    protected $fillable = [
        'description', 
        'prix',
        'date'
    ];
}
