<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentesTable extends Migration
{
    public function up()
    {
        Schema::create('ventes', function (Blueprint $table) {
            $table->foreignId('utilisateur_id')->constrained('users');
            $table->id();
            $table->decimal('quantite', 10, 2);
            $table->foreignId('article_id')->constrained('articles');
            $table->decimal('prix_unitaire', 10, 2);
            $table->string('status', 50);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ventes');
    }
}
