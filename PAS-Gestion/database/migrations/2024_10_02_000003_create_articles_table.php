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
            $table->string('taille', 10)->nullable();
            $table->decimal('quantite', 10, 2)->nullable();
            $table->decimal('prixVente', 10, 2)->nullable();
            $table->string('localisation', 255)->nullable();
            $table->decimal('prixClient', 10, 2)->nullable();
            $table->decimal('prixSolde', 10, 2)->nullable();
            $table->string('status', 255);   
            $table->date('dateDepot');
            $table->date('dateEcheance');
            $table->date('dateStatus');            
            $table->string('color', 7)->nullable();
            $table->boolean('isPaid')->default(false);
            $table->integer('statusMail')->default(0);
            $table->foreignId('utilisateur_id')->constrained('users');
            $table->foreignId('fournisseur_id')->constrained('fournisseurs');
            $table->foreignId('fournisseur_id_transit')->constrained('fournisseurs')->nullable();
            $table->foreignId('vente_id')->constrained('ventes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
