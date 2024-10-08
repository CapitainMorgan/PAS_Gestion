<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepotsTable extends Migration
{
    public function up()
    {
        Schema::create('depots', function (Blueprint $table) {
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->onDelete('cascade');
            $table->id();
            $table->date('dateDepot')->nullable();
            $table->date('dateEcheance')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('depots');
    }
}
