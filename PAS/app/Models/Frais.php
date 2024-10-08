<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frais extends Model
{
    use HasFactory;

    protected $fillable = [
        'description', 
        'prix', 
        'article_id'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
