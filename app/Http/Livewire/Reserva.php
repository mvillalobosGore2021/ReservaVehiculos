<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservavehiculo;

class Reserva extends Component
{
    public $horaInicio, $horaFin, $firstDayMonth, $lastDayMonth, $cantDaysMonth, 
    $monthNow, $monthNowStr, $nextMontStr, $yearNow, $yearNextMont, $flgBisiesto, 
    $fechaModal, $flgNextMonth, $monthSel, $yearSel, $openModal, $flgUsoVehiculoPersonal,
    $motivo, $reservasFechaSel, $userName, $idUser;

    protected  $arrMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    public function mount() {
        $this->openModal = true;
        $this->getCalendarMonth(1);
        $this->flgUsoVehiculoPersonal = false;
        $user = Auth::user();
        $this->userName = $user->name;
        $this->idUser = $user->id;
    }

    public function render() 
    {
        return view('livewire.reserva');
    }

    public function getCalendarMonth($flgNextMonth) {
        $this->flgNextMonth = $flgNextMonth;
        $fechaActual = Carbon::now();
        // $fechaActual = Carbon::parse("01.12.2022");

        $this->monthNow = $fechaActual->month;
        $this->yearNow = $fechaActual->year;
        
        $this->monthNowStr = $this->arrMeses[$this->monthNow-1];
        if ($this->monthNow == 12) { //Si es diciembre el proximo mes se pasa a Enero (array de 0 a 11)
            $this->nextMontStr = $this->arrMeses[0];
            $this->yearNextMont = $this->yearNow+1;        
        } else {
            $this->nextMontStr = $this->arrMeses[$this->monthNow]; 
            $this->yearNextMont = $this->yearNow;
        }       

        $fechaMonthActive = $fechaActual;
        if ($flgNextMonth == 1) {
            $fechaMonthActive = $fechaActual->addMonth();
        }

        $this->monthSel = $fechaMonthActive->month;
        $this->yearSel = $fechaMonthActive->year;

        $this->firstDayMonth = $fechaMonthActive->firstOfMonth()->dayOfWeek;
        if ($this->firstDayMonth == 0) { //Si el dia es Domingo 
            $this->firstDayMonth = 7;
        } 

        $this->lastDayMonth = $fechaMonthActive->lastOfMonth()->dayOfWeek;
        if ($this->lastDayMonth == 0) { //Si el dia es Domingo
            $this->lastDayMonth = 7;
        }

        $this->cantDaysMonth = $fechaMonthActive->daysInMonth;
        //$this->flgBisiesto = Carbon::parse("01.02".$this->yearNow)->daysInMonth == 28; 

        //dd($this->firstDayMonth, $this->lastDayMonth, $this->cantDaysMonth);
        
    }


    public function setFechaModal($fechaSel) {
    
       $this->fechaModal = Carbon::parse($fechaSel)->format('d/m/Y');

       $this->reservasFechaSel = Reservavehiculo::join('users', 'users.id', '=', 'reservavehiculos.idUser')
        ->whereRaw("DATE_FORMAT(fechaSolicitud, '%d/%m/%Y') = ".$this->fechaModal) 
        ->get(['reservavehiculos.*', 'users.name']);

        $this->dispatchBrowserEvent('showModal');
       
    }

    public function solicitarReserva($fechaSel) {
        try {

           // 'idUser', 'motivo', 'prioridad', 'flgUsoVehiculoPersonal', 'fechaSolicitud', 'fechaConfirmacion', 'codEstado'

            $prioridad = 0; //Calcular del listado de reserva por orden de llegada, dar la posibilidad de cambiar la prioridad al Adm
            $this->fechaModal = Carbon::parse($fechaSel)->format('d/m/Y');

            $reserva = Reservavehiculo::updateOrCreate(
                ['idReserva' => $this->idReserva], 
                [
                    'idUser' => $this->idUser,
                    'prioridad' => $prioridad,
                    'flgUsoVehiculoPersonal' => $this->flgUsoVehiculoPersonal, 
                    'fechaSolicitud' => $this->fechaModal,
                    'horaInicio' => $this->horaInicio,
                    'horaFin' => $this->horaFin,
                    'codEstado' => 1, //Crear tabla con los estados: Pendiente, Confirmada
                    //'fechaConfirmacion' => $this->correoRepLegal, fecha de confirmación se guarda cuando el administrador confirma la reserva
                ]
            );


        } catch (exception $e) {
            session()->flash('exceptionMessage', $e->getMessage());
            $this->currentStep = 1;
        }
        
     }


}
