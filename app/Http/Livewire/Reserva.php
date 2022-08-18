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
use App\Mail\CorreoNotificacion;
use Illuminate\Support\Facades\Mail;

class Reserva extends Component 
{
    public $horaInicio, $horaFin, $firstDayMonth, $lastDayMonth, $cantDaysMonth,
        $monthNow, $monthNowStr, $nextMontStr, $yearNow, $yearNextMont, $flgBisiesto,
        $fechaModal, $flgNextMonth, $monthSel, $yearSel, $openModal, $flgUsoVehiculoPersonal,
        $motivo, $userName, $idUser, $idReserva, $reservas, $reservasFechaSel1, $reservasFechaSel,
        $arrCantReservasCount, $dayNow, $diaActual, $mesSel, $agnoSel, $mesSelStr, $mesActual, $randId,
        $diasRestantesMesActual, $fechaActual, $diasMesesAnt, $correoUser;

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
        $this->correoUser = $user->email;
        $this->getCalendarMonth(Carbon::now()->month);
        $this->diasMesesAnt = 0;
    }

    public function calculoDespliegue60Dias() {
         //Inicio Calculo Despliege de 60 dias
         $this->fechaActual = Carbon::now();
       
         //dd($this->fechaActual->firstOfMonth()->dayOfWeek);                                          
        
          $this->arrMonthDisplay = Arr::add($this->arrMonthDisplay, $this->fechaActual->month, ['mes'=>$this->arrMeses[$this->fechaActual->month-1], 'agno' => $this->fechaActual->year, 'primerDiaSemana' => $this->fechaActual->firstOfMonth()->dayOfWeek == 0?7:$this->fechaActual->firstOfMonth()->dayOfWeek, 'ultimoDiaSemana' => $this->fechaActual->lastOfMonth()->dayOfWeek == 0?7:$this->fechaActual->lastOfMonth()->dayOfWeek, 'cantDiasMes' => $this->fechaActual->daysInMonth]);  
          
          $this->mesSel = $this->fechaActual->month;
          $this->mesActual = $this->fechaActual->month;
         
          $this->mesSelStr = $this->arrMeses[$this->fechaActual->month];
          $this->agnoSel = $this->fechaActual->year;
          $this->cantDaysMonth = $this->fechaActual->daysInMonth;
          $this->firstDayMonth = $this->fechaActual->firstOfMonth()->dayOfWeek; 
          $this->lastDayMonth = $this->fechaActual->lastOfMonth()->dayOfWeek;
  
          $fechaSiguiente = Carbon::now()->addMonth();
          $diasSiguienteMes = $fechaSiguiente->daysInMonth;
          $this->arrMonthDisplay = Arr::add($this->arrMonthDisplay, $fechaSiguiente->month, ['mes'=>$this->arrMeses[$fechaSiguiente->month-1], 'agno' => $fechaSiguiente->year, 'primerDiaSemana' => $fechaSiguiente->firstOfMonth()->dayOfWeek == 0?7:$fechaSiguiente->firstOfMonth()->dayOfWeek, 'ultimoDiaSemana' => $fechaSiguiente->lastOfMonth()->dayOfWeek == 0?7:$fechaSiguiente->lastOfMonth()->dayOfWeek,'cantDiasMes' => $fechaSiguiente->daysInMonth]);  
                  
          $this->diasRestantesMesActual = $this->fechaActual->daysInMonth - Carbon::now()->format('d') * 1 + 1;//Calculo de los dias restantes para que termine el mes, se le suma uno para incluir el dia actual
      
        //Si los dos meses no suman 60 dias se agrega otro mes
          if (( $this->diasRestantesMesActual+$diasSiguienteMes) < 60) {
              $fechaUltima = Carbon::now()->addMonths(2); 
              $this->arrMonthDisplay = Arr::add($this->arrMonthDisplay, $fechaUltima->month, ['mes'=>$this->arrMeses[$fechaUltima->month-1], 'agno' => $fechaUltima->year, 'primerDiaSemana' => $fechaUltima->firstOfMonth()->dayOfWeek == 0?7:$fechaUltima->firstOfMonth()->dayOfWeek, 'ultimoDiaSemana' => $fechaUltima->lastOfMonth()->dayOfWeek == 0?7:$fechaUltima->lastOfMonth()->dayOfWeek, 'cantDiasMes' => $fechaUltima->daysInMonth]);     
          }
        //Fin Calculo Despliege de 60 dias  
    }
    
    public function getCalendarMonth($mesSel)
    {
        $this->calculoDespliegue60Dias();
        $this->getReservas();

          $this->diasMesesAnt = 0;
        if ($this->arrMonthDisplay[$mesSel]['mes'] == last($this->arrMonthDisplay)['mes']) {
            if (count($this->arrMonthDisplay) == 2) {
                $this->diasMesesAnt = $this->diasRestantesMesActual;
            } else { //Sino el array contiene 3 meses 
                //Se suman los dias restantes del primer mes mas los del segundo
                $this->diasMesesAnt = $this->diasRestantesMesActual + $this->arrMonthDisplay[$mesSel-1]['cantDiasMes'];
            }
        } 

        //$diasMesesAnt 

        $itemMesSel = $this->arrMonthDisplay[$mesSel];
        $this->mesSelStr = $this->arrMeses[$mesSel-1];
        $this->mesSel = $mesSel;
        $this->agnoSel = $itemMesSel['agno'];
        $this->cantDaysMonth = $itemMesSel['cantDiasMes'];
        $this->firstDayMonth = $itemMesSel['primerDiaSemana']; 
        $this->lastDayMonth = $itemMesSel['ultimoDiaSemana'];
    }

    public function getReservas()
    {
    //Se obtienen las reservas para un rango de 60 dias a contar de la fecha actual 
      $this->dayNow = Carbon::now()->format('d')*1;
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

              //Envío de correo: no se hace rollback si se produce un error al realizar el envío
              $mailData = [
                'asunto' => ($this->idReserva > 0 ? "Modificación de Reserva de Vehículo" : "Reserva de Vehículo")." - Gobierno Regional del Bio Bio",
                'titulo' => $this->idReserva > 0 ? "Resumen modificación de Reserva" : "Resumen de su Reserva",
                'funcionario' => $this->userName,
                'fechaReserva' => $this->fechaModal,
                'horaInicio' => $this->horaInicio,
                'horaFin' => $this->horaFin,
                'usaVehiculoPersonal' => $this->flgUsoVehiculoPersonal == 0?'No':'Si',
                'motivo' => $this->motivo,
            ];

            try {
              //Mail al postulante 
                Mail::to($this->correoUser)->send(new CorreoNotificacion($mailData));
            } catch (exception $e) {
             
                session()->flash('exceptionMessage', $e->getMessage());

                dd($e->getMessage());
            }

            // $userAdmin = User::where('flgAdmin', '=', 1)->get();  

            // $mailData['titulo'] =  $this->idReserva > 0 ? "Modificación de Reserva de Vehículo" :"Solicitud de Reserva de Vehiculo";
            // $mailData['asunto'] = ($this->idReserva > 0 ? "Modificación de Reserva de Vehículo de " :"Solicitud de Reserva de Vehiculo de ").$this->userName;

            // try {
            //     foreach ($userAdmin as $item) { 
            //         Mail::to($item->email)->send(new CorreoNotificacion($mailData));
            //     }
            // } catch (exception $e) {
            //     session()->flash('exceptionMessage', $e->getMessage());
            // }

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
