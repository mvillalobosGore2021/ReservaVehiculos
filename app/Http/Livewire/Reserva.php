<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Carbon;

class Reserva extends Component
{
    public $horaInicio, $horaFin, $firstDayMonth, $lastDayMonth, $cantDaysMonth, $monthNow, $yearNow, $flgBisiesto,
    $fechaModal;

    public function mount() {
        $fechaActual = Carbon::now(); 

        $this->monthNow = $fechaActual->month;
        $this->yearNow = $fechaActual->year;

        $this->firstDayMonth = $fechaActual->firstOfMonth()->dayOfWeek;
        if ($this->firstDayMonth == 0) { //Si el dia es Domingo 
            $this->firstDayMonth = 7;
        } 

        $this->lastDayMonth = $fechaActual->lastOfMonth()->dayOfWeek;
        if ($this->lastDayMonth == 0) { //Si el dia es Domingo
            $this->lastDayMonth = 7;
        }

        $this->cantDaysMonth = $fechaActual->daysInMonth;
        //$this->flgBisiesto = Carbon::parse("01.02".$this->yearNow)->daysInMonth == 28; 
    }

    public function render() 
    {
        return view('livewire.reserva');
    }

    public function setFechaInicio() {
        dd("Pase por aqui");
    }
}
