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
        $arrCantReservasCount, $dayNow, $diaActual, $mesSel, $agnoSel, $mesSelStr, $mesActual, $randId;

    public $arrMonthDisplay;

    protected  $arrMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    public function mount()
    {
        $this->openModal = true; 
        $this->randId = rand(0, 100); 
        $this->flgUsoVehiculoPersonal = false;
        $user = Auth::user();
        $this->userName = $user->name;
        $this->idUser = $user->id;
        $this->getReservas();

        //Inicio Calculo Despliege de 60 dias
        $fechaActual = Carbon::now();
       
       //dd($fechaActual->firstOfMonth()->dayOfWeek);                                          
      
        $this->arrMonthDisplay = Arr::add($this->arrMonthDisplay, $fechaActual->month, ['mes'=>$this->arrMeses[$fechaActual->month-1], 'agno' => $fechaActual->year, 'primerDiaSemana' => $fechaActual->firstOfMonth()->dayOfWeek == 0?7:$fechaActual->firstOfMonth()->dayOfWeek, 'ultimoDiaSemana' => $fechaActual->lastOfMonth()->dayOfWeek == 0?7:$fechaActual->lastOfMonth()->dayOfWeek, 'cantDiasMes' => $fechaActual->daysInMonth]);  
        
        $this->mesSel = $fechaActual->month;
        $this->mesActual = $fechaActual->month;
       
        $this->mesSelStr = $this->arrMeses[$fechaActual->month];
        $this->agnoSel = $fechaActual->year;
        $this->cantDaysMonth = $fechaActual->daysInMonth;
        $this->firstDayMonth = $fechaActual->firstOfMonth()->dayOfWeek; 
        $this->lastDayMonth = $fechaActual->lastOfMonth()->dayOfWeek;
        
        // dd("jkdbhwvd", $this->arrMonthDisplay,  $fechaActual->month);

        $fechaSiguiente = Carbon::now()->addMonth();
        $diasSiguienteMes = $fechaSiguiente->daysInMonth;
        $this->arrMonthDisplay = Arr::add($this->arrMonthDisplay, $fechaSiguiente->month, ['mes'=>$this->arrMeses[$fechaSiguiente->month-1], 'agno' => $fechaSiguiente->year, 'primerDiaSemana' => $fechaSiguiente->firstOfMonth()->dayOfWeek == 0?7:$fechaSiguiente->firstOfMonth()->dayOfWeek, 'ultimoDiaSemana' => $fechaSiguiente->lastOfMonth()->dayOfWeek == 0?7:$fechaSiguiente->lastOfMonth()->dayOfWeek,'cantDiasMes' => $fechaSiguiente->daysInMonth]);  
        
        
        $difDiasMes = $fechaActual->daysInMonth - $fechaSiguiente->month + 1;//Calculo de los dias restantes para que termine el mes, se le suma uno para incluir el dia actual
       
      //Si los dos meses no suman 60 dias se agrega otro mes
        if (($difDiasMes+$diasSiguienteMes) < 61) {
            $fechaUltima = Carbon::now()->addMonths(2);
            $this->arrMonthDisplay = Arr::add($this->arrMonthDisplay, $fechaUltima->month, ['mes'=>$this->arrMeses[$fechaUltima->month-1], 'agno' => $fechaUltima->year, 'primerDiaSemana' => $fechaUltima->firstOfMonth()->dayOfWeek == 0?7:$fechaUltima->firstOfMonth()->dayOfWeek, 'ultimoDiaSemana' => $fechaUltima->lastOfMonth()->dayOfWeek == 0?7:$fechaUltima->lastOfMonth()->dayOfWeek, 'cantDiasMes' => $fechaUltima->daysInMonth]);     
        }      
      //Fin Calculo Despliege de 60 dias    

      $this->getCalendarMonth($fechaActual->month);
    }
    
    public function getCalendarMonth($mesSel)
    {
        $itemMesSel = $this->arrMonthDisplay[$mesSel];
        $this->mesSelStr = $this->arrMeses[$mesSel-1];
        $this->mesSel = $mesSel;
        $this->agnoSel = $itemMesSel['agno'];
        $this->cantDaysMonth = $itemMesSel['cantDiasMes'];
        $this->firstDayMonth = $itemMesSel['primerDiaSemana']; 
        $this->lastDayMonth = $itemMesSel['ultimoDiaSemana'];



        // dd($this->mesSelStr,
        // $this->mesSel,
        // $this->agnoSel,
        // $this->cantDaysMonth,
        // $this->firstDayMonth,
        // $this->lastDayMonth);
    }


    public function getReservas()
    {
    //Se obtienen las reservas para un rango de 60 dias a contar de la fecha actual 
      $this->dayNow = Carbon::now()->day;
      $reservas = Reservavehiculo::groupBy('fechaSolicitud')
          ->selectRaw('count(*) as cantReservas, fechaSolicitud')
          ->whereBetween('fechaSolicitud', [Carbon::now()->format('Y-m-d'), Carbon::now()->addDays(60)->format('Y-m-d')])
          ->get();

      //Tabla Hash con las reservas realizadas
        foreach ($reservas as $item) {
           $this->arrCantReservasCount = Arr::add($this->arrCantReservasCount, $item['fechaSolicitud'], $item['cantReservas']);
        }
    }

    public function render()
    {
        return view('livewire.reserva', compact(['reservasFechaColl' => $this->reservasFechaSel]));
    }



    

    public function setFechaModal($fechaSel)
    {
        dd($fechaSel);
        $this->fechaModal = Carbon::parse($fechaSel)->format('d/m/Y');

        $this->reservasFechaSel = collect(Reservavehiculo::join('users', 'users.id', '=', 'reservavehiculos.idUser')
            ->join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado')
            // ->whereRaw("DATE_FORMAT(fechaSolicitud, '%d/%m/%Y') = " . $this->fechaModal)
            ->where('fechaSolicitud', '=', Carbon::createFromFormat('d/m/Y', Carbon::parse($fechaSel)->format('d/m/Y'))->format('Y-m-d'))
            ->get(['reservavehiculos.*', 'users.id', 'users.name', 'estados.descripcionEstado']));

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

    public function updated($field)
    {
        if ($field == 'flgUsoVehiculoPersonal') { //Campo opcional no se valida
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
                    'fechaSolicitud' => Carbon::createFromFormat('d/m/Y', $this->fechaModal)->format('Y-m-d'), // Carbon::parse($this->fechaModal)->format('Y/m/d'),
                    'horaInicio' => $this->horaInicio,
                    'horaFin' => $this->horaFin,
                    'motivo' => $this->motivo,
                    'codEstado' => 1, //Crear tabla con los estados: Pendiente, Confirmada
                    //'fechaConfirmacion' => $this->correoRepLegal, fecha de confirmación se guarda cuando el administrador confirma la reserva
                ]
            );

            $this->getReservas();

            $mensaje = $this->idReserva > 0 ? 'Su solicitud de reserva ha sido modificada y enviada.' : 'Su solicitud de reserva ha sido ingresada y enviada.';

            $this->dispatchBrowserEvent('swal:information', [
                'icon' => '', //'info',
                'mensaje' => '<i class="bi bi-send-check-fill text-success fs-4"></i><span class="ps-2 fs-6 text-primary" style="font-weight:430;">' . $mensaje . '</div>',
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
