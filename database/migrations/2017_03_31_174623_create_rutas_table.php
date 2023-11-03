<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRutasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rutas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hospital_origen');
            $table->dateTime('hora_salida');
            $table->integer('hospital_destino');
            $table->dateTime('hora_llegada_API');
            $table->integer('paciente_rut')->nullable();
            $table->string('paciente_dv')->nullable();
            $table->integer('ambulancia_id');

            $table->foreign('ambulancia_id')->references('id')->on('ambulancias');

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
        Schema::dropIfExists('rutas');
    }
}
