<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEsperaAmbulanciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('espera_ambulancias', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('hora_ambulancia_requerida');
            $table->string('motivo');
            $table->enum('estado',['activo', 'desactivada']);
            $table->integer('paciente_id')->unsigned();

            $table->foreign('paciente_id')->references('id')->on('pacientes');

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
        Schema::dropIfExists('espera_ambulancias');
    }
}
