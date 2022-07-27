<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Reserva extends Component
{
    public $fechaInicio, $fechaFin, $horaInicio, $horaFin;

    public function mount() {
    }

    public function render() 
    {
        return view('livewire.reserva');
    }

    public function setFechaInicio() {
        dd("Pase por aqui");
    }
}
