<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAmbulanciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ambulancias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('patente',6)->unique();
            $table->integer('tipo_id')->unsigned();
            $table->integer('capacidad');
            $table->integer('enUso')->default(0);
            $table->string('ubicacion');
            $table->integer('estadoA_id')->default('1')->unsigned();
            $table->integer('establecimiento_id')->unsigned();

            $table->foreign('tipo_id')->references('id')->on('tipo_ambulancias');
            $table->foreign('estadoA_id')->references('id')->on('estado_ambulancias');
            $table->foreign('establecimiento_id')->references('id')->on('establecimientos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ambulancias');
    }
}
