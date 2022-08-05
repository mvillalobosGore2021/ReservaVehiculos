<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Reservavehiculo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;


class Listarreservas extends Component
{
    public $userName, $idUser, $reservasUsuario;

    public function mount() {
        $user = Auth::user();
        $this->userName = $user->name;
        $this->idUser = $user->id;
        $this->consultarRerservasUser();
    }
    public function render()
    {   
        return view('livewire.listarreservas');
    }

    public function consultarRerservasUser() {
        $fechaInicio = Carbon::now()->format('Y-m-01');
        $fechaNextMonth = Carbon::now()->addMonth();
        $fechaNextMonth = $fechaNextMonth->format('Y-m-' . $fechaNextMonth->daysInMonth);

         //Se obtienen las reservas para un rango de dos meses
         $this->reservasUsuario = Reservavehiculo::where('idUser', '=', $this->idUser)
          ->whereBetween('fechaSolicitud', [$fechaInicio, $fechaNextMonth])
          ->get();
    }

}
