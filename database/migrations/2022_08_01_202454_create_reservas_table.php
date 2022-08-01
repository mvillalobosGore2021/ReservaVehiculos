<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->integer('idUser')->index();
            $table->string('motivo', 500);
            $table->boolean('flgUsoVehiculoPersonal')->nullable();
            $table->dateTime('fechaSolicitud');
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
        Schema::dropIfExists('reservas');
    }
}
