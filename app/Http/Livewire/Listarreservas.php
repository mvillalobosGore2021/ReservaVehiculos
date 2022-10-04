<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Classes\ReservaServices;
use App\Models\Reservavehiculo;
use App\Models\Comuna;
use App\Models\Division;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Listarreservas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $userName, $idUser, $fechaDesde, $fechaHasta, $idReserva, $horaInicio, $horaFin, $codEstado, $descripcionEstado,
    $motivo, $flgUsoVehiculoPersonal, $fechaModal, $codComuna, $codDivision, $cantPasajeros, $comunasCmb, $divisionesCmb;

    public function mount() {
        $user = Auth::user();
        $this->userName = $user->name;
        $this->idUser = $user->id;
        $this->comunasCmb = Comuna::all();
        $this->divisionesCmb = Division::all();
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

    public function callSetFechaModal($fechaSel) {
         $reservaService = new ReservaServices();
        //dd($pruebaService->saludo());
        $reservaService->setFechaModal($fechaSel, $this);
    }

    public function setFechaModal($fechaSel)  {
        $this->fechaModal = Carbon::parse($fechaSel)->format('d/m/Y');
        
        $this->reservasFechaSel = collect(Reservavehiculo::join('users', 'users.id', '=', 'reservavehiculos.idUser')
            ->join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado')
            // ->whereRaw("DATE_FORMAT(fechaSolicitud, '%d/%m/%Y') = " . $this->fechaModal)
            ->where('fechaSolicitud', '=', Carbon::createFromFormat('d/m/Y', Carbon::parse($fechaSel)->format('d/m/Y'))->format('Y-m-d'))
            ->get(['reservavehiculos.*', 'users.id', 'users.name', 'estados.descripcionEstado']));

        //Si existe una reserva del usuario conectado para el dia seleccionado, si asignan los datos para su edicion
        $reservasFechaUser = $this->reservasFechaSel->where('idUser', '=', $this->idUser)->first();

        $this->reset(['idReserva', 'codEstado', 'descripcionEstado', 'horaInicio', 'horaFin', 'motivo', 'codDivision', 'codComuna', 'cantPasajeros', 'flgUsoVehiculoPersonal']);
        $this->resetValidation(['horaInicio', 'horaFin', 'motivo', 'codDivision', 'codComuna', 'cantPasajeros']);
        $this->resetErrorBag(['horaInicio', 'horaFin', 'motivo', 'codDivision', 'codComuna', 'cantPasajeros']);

        if (!empty($reservasFechaUser)) {
            $this->idReserva = $reservasFechaUser['idReserva'];
            $this->horaInicio = Carbon::parse($reservasFechaUser['horaInicio'])->format('H:i');
            $this->horaFin = Carbon::parse($reservasFechaUser['horaFin'])->format('H:i');
            $this->codEstado = $reservasFechaUser['codEstado'];  
            $this->descripcionEstado = $reservasFechaUser['descripcionEstado']; 
            $this->motivo = $reservasFechaUser['motivo'];
            $this->flgUsoVehiculoPersonal = $reservasFechaUser['flgUsoVehiculoPersonal'];
        }       

        $this->dispatchBrowserEvent('showModal');
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
