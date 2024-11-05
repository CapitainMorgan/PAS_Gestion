<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFraisSocietesTable extends Migration
{
    public function up()
    {
        Schema::create('frais_societes', function (Blueprint $table) {
            $table->id();
            $table->string('description', 255)->nullable();
            $table->decimal('prix', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('frais_societes');
    }
}
