<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservavehiculo extends Model
{
    use HasFactory;

    protected $primaryKey = 'idReserva';

    protected $fillable = ['idUser', 'motivo', 'prioridad', 'flgUsoVehiculoPersonal', 'fechaSolicitud', 'fechaConfirmacion', 'codEstado', 'horaInicio', 'horaFin'];
}
