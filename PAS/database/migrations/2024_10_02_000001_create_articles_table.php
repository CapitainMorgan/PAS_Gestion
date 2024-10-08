<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('description', 255);
            $table->float('taille')->nullable();
            $table->decimal('quantite', 10, 2)->nullable();
            $table->decimal('prixVente', 10, 2)->nullable();
            $table->decimal('prixClient', 10, 2)->nullable();
            $table->decimal('prixSolde', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
