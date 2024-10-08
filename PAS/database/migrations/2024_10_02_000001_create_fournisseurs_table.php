<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFournisseursTable extends Migration
{
    public function up()
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 50);
            $table->string('prénom', 50);
            $table->string('rue', 50)->nullable();
            $table->string('ville', 50)->nullable();
            $table->string('npa', 10)->nullable();
            $table->string('pays', 50)->nullable();
            $table->date('dateCreation')->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('numProf', 20)->nullable();
            $table->string('remarque', 255)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('telephone', 15)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fournisseurs');
    }
}
