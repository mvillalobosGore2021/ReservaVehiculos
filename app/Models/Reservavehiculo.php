<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservavehiculo extends Model   
{
    use HasFactory; 

    protected $primaryKey = 'idReserva';

    protected $fillable = ['idUser', 'motivo', 'codComuna', 'prioridad', 'flgUsoVehiculoPersonal', 
    'fechaSolicitud', 'fechaConfirmacion', 'codEstado', 'codVehiculo', 'horaInicio',  
    'horaFin', 'idUserCreacion', 'idUserModificacion', 'codDivision', 'cantPasajeros',
    'rutConductor', 'motivoAnulacion'];
} 
