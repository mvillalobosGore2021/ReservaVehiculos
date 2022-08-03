<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservavehiculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservavehiculos', function (Blueprint $table) {
            $table->increments('idReserva');
            $table->integer('idUser');
            $table->string('motivo', 500);
            $table->boolean('flgUsoVehiculoPersonal')->nullable();
            $table->date('fechaSolicitud');
            $table->time('horaInicio');
            $table->time('horaFin');
            $table->dateTime('fechaConfirmacion')->nullable();
            $table->integer('codEstado');          
            $table->integer('prioridad');   
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
        Schema::dropIfExists('reservavehiculos');
    }
}
