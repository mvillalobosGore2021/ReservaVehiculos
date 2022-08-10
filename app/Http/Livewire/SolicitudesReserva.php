<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Reservavehiculo;
use App\Models\Estado;
use App\Models\Vehiculo;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class SolicitudesReserva extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $fechaSolSearch, $codEstadoSearch, $nameSearch, $fechaHoySearch, $idVehiculo, $codEstadoSel;

    public $idReservaSel, $fechaSolicitudSel, $horaInicioSel, $horaFinSel, $descripEstadoSel, $flgUsoVehiculoPersSel, 
           $motivoSel, $nameSel, $cmbVehiculos, $estadosCmb;

    public Collection $inputsTable;

    public function mount() {
        $this->cmbVehiculos = Vehiculo::all();
    }

    public function render()
    {
        $fechaInicio = Carbon::now()->format('Y-m-01');
        $fechaNextMonth = Carbon::now()->addMonth();
        $fechaNextMonth = $fechaNextMonth->format('Y-m-' . $fechaNextMonth->daysInMonth);

         //Se obtienen las reservas para un rango de dos meses
         $reservasTotales = Reservavehiculo::join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado')
          ->join('users', 'users.id', '=', 'reservavehiculos.idUser')
          ->whereBetween('fechaSolicitud', [$fechaInicio, $fechaNextMonth])
          ->where('fechaSolicitud', 'like', '%' . $this->fechaHoySearch . '%')
          ->where('users.name', 'like', '%' . $this->nameSearch . '%')
          ->where('reservavehiculos.codEstado', 'like', '%' . $this->codEstadoSearch . '%')
          ->orderBy('fechaSolicitud', 'desc')
          ->paginate(4); 
          
         // dd($reservasTotales->where('fechaSolicitud', 'like', '%2022-08-09%'));

        //   $estadosCmb = Estado::where('codEstado', '>', 1)->get();
   
          $estadosCmbSearch = Estado::all();
        
          $this->inputsTable = new Collection();
          foreach ($reservasTotales as $item) {
            $this->inputsTable->push([
                'idReserva' => $item->idReserva, 'codEstado' => '']);
          }
          
        return view('livewire.solicitudes-reserva', compact(['reservasTotales', 'estadosCmbSearch']));
    }

    public function reservaSel($idReservaSel) {
        $reservaSel = Reservavehiculo::join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado')
          ->join('users', 'users.id', '=', 'reservavehiculos.idUser')
          ->where('idReserva', '=', $idReservaSel)->first();

        $this->idReservaSel = $reservaSel->idReserva;
        $this->fechaSolicitudSel = $reservaSel->fechaSolicitud;
        $this->horaInicioSel = $reservaSel->horaInicio;
        $this->horaFinSel = $reservaSel->horaFin;
        $this->flgUsoVehiculoPersSel = $reservaSel->flgUsoVehiculoPersona;
        $this->motivoSel = $reservaSel->motivo;
        $this->nameSel = $reservaSel->name;
        $this->descripEstadoSel = $reservaSel->descripcionEstado;

        $this->estadosCmb = Estado::where('codEstado', '>', 1)
        ->where('codestado', '!=', $reservaSel->codEstado)->get();       

        $this->dispatchBrowserEvent('showModal');
    }

    public function setFechaHoySearch() {
        $this->fechaHoySearch = Carbon::now()->format('Y-m-d');        
        $this->dispatchBrowserEvent('iniTooltips');
        $this->resetPage();
    }

    public function mostrarTodo() {
        $this->reset(['codEstadoSearch', 'nameSearch', 'fechaHoySearch']);        
        // $this->dispatchBrowserEvent('iniTooltips');
        $this->resetPage();
    }

    public function resetSearch($field) {
        $this->reset($field);
        // $this->dispatchBrowserEvent('iniTooltips');
        $this->resetPage();
    } 

    public function updated() {
        // $this->dispatchBrowserEvent('iniTooltips');
        $this->resetPage();
    }

    public function cambiarEstado($idEstado) {
         
    }
}
