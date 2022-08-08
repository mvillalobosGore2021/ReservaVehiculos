<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Reservavehiculo;
use App\Models\Estado;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class SolicitudesReserva extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $fechaSolSearch, $codEstadoSearch;

    public function mount() {
      
    }

    public function render()
    {
        $fechaInicio = Carbon::now()->format('Y-m-01');
        $fechaNextMonth = Carbon::now()->addMonth();
        $fechaNextMonth = $fechaNextMonth->format('Y-m-' . $fechaNextMonth->daysInMonth);

         //Se obtienen las reservas para un rango de dos meses
         $reservasTotales = Reservavehiculo::join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado') 
          ->whereBetween('fechaSolicitud', [$fechaInicio, $fechaNextMonth])
          ->where('fechaSolicitud', 'like', '%' . $this->fechaSolSearch . '%')
          ->where('reservavehiculos.codEstado', 'like', '%' . $this->codEstadoSearch . '%')
          ->orderBy('fechaSolicitud', 'desc')
          ->paginate(10);

          $estadosCmb = Estado::where('codEstado', '>', 1)
          ->get();
   
          $estadosCmbSearch = Estado::all();

         // dd($reservasTotales);
        return view('livewire.solicitudes-reserva', compact(['reservasTotales', 'estadosCmb', 'estadosCmbSearch']));
    }

    public function cambiarEstado($idEstado) {
         
    }
}
