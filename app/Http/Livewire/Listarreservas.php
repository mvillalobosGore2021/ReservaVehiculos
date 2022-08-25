<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Reservavehiculo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;


class Listarreservas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $userName, $idUser, $fechaDesde, $fechaHasta;//, $reservasUsuario;

    public function mount() {
        $user = Auth::user();
        $this->userName = $user->name;
        $this->idUser = $user->id;
       // $this->consultarRerservasUser();
    }
    
    public function render() 
    {   //Se obtienen las reservas para un rango de tres meses
         $reservasUsuario = Reservavehiculo::join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado') 
          ->select('reservavehiculos.*', 'estados.descripcionEstado')
          ->where('idUser', '=', $this->idUser)
          ->whereBetween('fechaSolicitud', [Carbon::now()->format('Y-m-d'), Carbon::now()->addMonths(2)->format('Y-m-d')])
          ->orderBy('fechaSolicitud', 'desc')
          ->paginate(10);

          $this->fechaDesde = Carbon::now()->format('d/m/Y');
          $this->fechaHasta = Carbon::now()->addMonths(2)->format('d/m/Y');

        return view('livewire.listarreservas', compact('reservasUsuario'));
    }

    // public function consultarRerservasUser() {
    //     $fechaInicio = Carbon::now()->format('Y-m-01');
    //     $fechaNextMonth = Carbon::now()->addMonth();
    //     $fechaNextMonth = $fechaNextMonth->format('Y-m-' . $fechaNextMonth->daysInMonth);

    //      //Se obtienen las reservas para un rango de dos meses
    //      $this->reservasUsuario = Reservavehiculo::where('idUser', '=', $this->idUser)
    //       ->whereBetween('fechaSolicitud', [$fechaInicio, $fechaNextMonth])
    //       ->paginate(10);
    // }

}
