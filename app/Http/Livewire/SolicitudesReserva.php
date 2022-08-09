<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Reservavehiculo;
use App\Models\Estado;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class SolicitudesReserva extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $fechaSolSearch, $codEstadoSearch, $nameSearch, $fechaHoySearch;
    protected $flgSearch;

    public Collection $inputsTable;

    public function mount() {
        
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

          $estadosCmb = Estado::where('codEstado', '>', 1)->get();
   
          $estadosCmbSearch = Estado::all();
        
          $this->inputsTable = new Collection();
          foreach ($reservasTotales as $item) {
            $this->inputsTable->push([
                'idReserva' => $item->idReserva, 'codEstado' => '']);
          }
          
        return view('livewire.solicitudes-reserva', compact(['reservasTotales', 'estadosCmb', 'estadosCmbSearch']));
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
