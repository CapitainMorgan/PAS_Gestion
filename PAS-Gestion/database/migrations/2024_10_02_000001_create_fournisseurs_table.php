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
            $table->string('prenom', 50);
            $table->string('rue', 50);
            $table->string('ville', 50);
            $table->string('npa', 10);
            $table->string('pays', 50);
            $table->string('mobile', 15)->nullabe();
            $table->string('numProf', 20)->nullabe();
            $table->string('remarque', 255)->nullable();
            $table->string('email', 50);
            $table->string('telephone', 15);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fournisseurs');
    }
}
