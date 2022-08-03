<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservavehiculo;
use App\Models\User;
use App\Rules\HoraValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class Reserva extends Component
{
    public $horaInicio, $horaFin, $firstDayMonth, $lastDayMonth, $cantDaysMonth,
        $monthNow, $monthNowStr, $nextMontStr, $yearNow, $yearNextMont, $flgBisiesto,
        $fechaModal, $flgNextMonth, $monthSel, $yearSel, $openModal, $flgUsoVehiculoPersonal,
        $motivo, $userName, $idUser, $idReserva, $reservas, $reservasFechaSel1, $reservasFechaSel,
        $arrCantReservasCount;

    protected  $arrMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    public function mount()
    {
        $this->openModal = true;
        $this->getCalendarMonth(1);
        $this->flgUsoVehiculoPersonal = false;
        $user = Auth::user();
        $this->userName = $user->name;
        $this->idUser = $user->id;
        $this->getReservas();
    }

    public function getReservas() {
        // $this->reservasFechaSel = collect(Reservavehiculo::join('users', 'users.id', '=', 'reservavehiculos.idUser')
        // // ->whereRaw("DATE_FORMAT(fechaSolicitud, '%d/%m/%Y') = " . $this->fechaModal)
        // ->where ('fechaSolicitud', '=', Carbon::createFromFormat('d/m/Y', Carbon::parse($fechaSel)->format('d/m/Y'))->format('Y-m-d'))
        //  ->get(['reservavehiculos.*','users.id', 'users.name']));

        
        $fechaActual = Carbon::now();
        $fechaActualNextMonth = Carbon::now()->addMonth();

         $reservas = collect(Reservavehiculo::all()->groupBy('fechaSolicitud'));//->count('fechaSolicitud'); 

         
         $countPaso = "";
         foreach ($reservas as $item) {
            //$countPaso .= " ".$item[0]['fechaSolicitud'];
            $this->arrCantReservasCount = Arr::add($this->arrCantReservasCount, $item[0]['fechaSolicitud'], $item->count()); 
        }
                
       // dd($this->arrCantReservasCount);


    }

    public function render()
    {
        return view('livewire.reserva', compact(['reservasFechaColl' => $this->reservasFechaSel]));
    }

    public function getCalendarMonth($flgNextMonth)
    {
        $this->flgNextMonth = $flgNextMonth;
        $fechaActual = Carbon::now();
        // $fechaActual = Carbon::parse("01.12.2022");

        $this->monthNow = $fechaActual->month;
        $this->yearNow = $fechaActual->year;

        $this->monthNowStr = $this->arrMeses[$this->monthNow - 1];
        if ($this->monthNow == 12) { //Si es diciembre el proximo mes se pasa a Enero (array de 0 a 11)
            $this->nextMontStr = $this->arrMeses[0];
            $this->yearNextMont = $this->yearNow + 1;
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


    public function setFechaModal($fechaSel)
    {
        $this->fechaModal = Carbon::parse($fechaSel)->format('d/m/Y');

      
        $this->reservasFechaSel = collect(Reservavehiculo::join('users', 'users.id', '=', 'reservavehiculos.idUser')
            // ->whereRaw("DATE_FORMAT(fechaSolicitud, '%d/%m/%Y') = " . $this->fechaModal)
            ->where ('fechaSolicitud', '=', Carbon::createFromFormat('d/m/Y', Carbon::parse($fechaSel)->format('d/m/Y'))->format('Y-m-d'))
             ->get(['reservavehiculos.*','users.id', 'users.name']));

             //Si existe una reserva del usuario conectado para el dia seleccionado, si asignan los datos para su edicion
             $reservasFechaUser = $this->reservasFechaSel->where('idUser', '=', $this->idUser)->first();

             $this->reset(['idReserva', 'horaInicio', 'horaFin', 'motivo', 'flgUsoVehiculoPersonal']);
             $this->resetValidation(['horaInicio', 'horaFin', 'motivo']);
             $this->resetErrorBag(['horaInicio', 'horaFin', 'motivo']);

             if (!empty($reservasFechaUser)) {
                $this->idReserva = $reservasFechaUser['idReserva'];
                $this->horaInicio = Carbon::parse($reservasFechaUser['horaInicio'])->format('H:i');                
                $this->horaFin = Carbon::parse($reservasFechaUser['horaFin'])->format('H:i');
                $this->motivo = $reservasFechaUser['motivo'];
                $this->flgUsoVehiculoPersonal = $reservasFechaUser['flgUsoVehiculoPersonal'];
             }            

        $this->dispatchBrowserEvent('showModal');       
    }

    public function updated($field) {
        if ($field == 'flgUsoVehiculoPersonal') {//Campo opcional no se valida
            return true;
        }
       //dd($this->horaInicio, $this->horaFin);

       // dd($this->reservasFechaSel);
        //if ($field == 'horaInicio' || 'horaFin') {
        //    $this->resetValidation($field);
        //    $this->resetErrorBag($field);
        //}

        $this->validateOnly($field, $this->getArrRules());
    }

    public function solicitarReserva()
    {
       // dd($this->horaInicio, $this->horaFin);
    $this->validate($this->getArrRules());

        try {
            // 'idUser', 'motivo', 'prioridad', 'flgUsoVehiculoPersonal', 'fechaSolicitud', 'fechaConfirmacion', 'codEstado'
            $prioridad = 0; //Calcular del listado de reserva por orden de llegada, dar la posibilidad de cambiar la prioridad al Adm

           $reservaVehiculo =  Reservavehiculo::updateOrCreate(
                ['idReserva' => $this->idReserva],
                [
                    'idUser' => $this->idUser,
                    'prioridad' => $prioridad,
                    'flgUsoVehiculoPersonal' => $this->flgUsoVehiculoPersonal,
                    'fechaSolicitud' => Carbon::createFromFormat('d/m/Y', $this->fechaModal)->format('Y-m-d'),// Carbon::parse($this->fechaModal)->format('Y/m/d'),
                    'horaInicio' => $this->horaInicio,
                    'horaFin' => $this->horaFin,
                    'motivo' => $this->motivo,
                    'codEstado' => 1, //Crear tabla con los estados: Pendiente, Confirmada
                    //'fechaConfirmacion' => $this->correoRepLegal, fecha de confirmación se guarda cuando el administrador confirma la reserva
                ]
            );


            $mensaje = $this->idReserva > 0 ? 'Su solicitud de reserva ha sido modificada y enviada.':'Su solicitud de reserva ha sido ingresada y enviada.';
            
            $this->dispatchBrowserEvent('swal:information', [
                'icon' => '',//'info',
                'mensaje' => '<i class="bi bi-send-check-fill text-success fs-4"></i><span class="ps-2 fs-6 text-primary" style="font-weight:430;">'.$mensaje.'</div>',
            ]);

            $this->dispatchBrowserEvent('closeModal');

        } catch (exception $e) {
            session()->flash('exceptionMessage', $e->getMessage());
        }
    }

    public function getArrRules()
    {
        return [
            'horaInicio' => ['required', 'date_format:H:i', new HoraValidator()],
            'horaFin' => ['required', 'date_format:H:i', new HoraValidator()],
            'motivo' => 'required:max:500',
        ];
    }
}
