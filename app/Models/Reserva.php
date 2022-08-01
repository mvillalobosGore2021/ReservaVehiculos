<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = ['idUser', 'motivo', 'prioridad', 'flgUsoVehiculoPersonal', 'fechaSolicitud', 'fechaConfirmacion', 'codEstado'];
}
