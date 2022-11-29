<?php

namespace App\Http\Livewire;
use Livewire\Component;
use Illuminate\Support\Carbon;
use App\Classes\ReservaServices;
use Illuminate\Support\Facades\Auth;
use App\Models\Comuna;
use App\Models\Division;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

 
class Reserva extends Component 
{
    public $horaInicio, $horaFin, $flgNuevaReserva, $firstDayMonth, $lastDayMonth, $cantDaysMonth, $fechaSolicitud,
        $monthNow, $monthNowStr, $nextMontStr, $yearNow, $yearNextMont, $flgBisiesto, 
        $fechaModal, $flgNextMonth, $monthSel, $yearSel, $openModal, $flgUsoVehiculoPersonal,
        $motivo, $userName, $idUser, $idReserva, $reservas, $reservasFechaSel,
        $arrCantReservasCount, $dayNow, $diaActual, $mesSel, $agnoSel, $mesSelStr, $mesActual, $randId,
        $diasRestantesMesActual, $fechaActual, $fechaSiguiente, $fechaUltima, $diasMesesAnt, $correoUser, $codEstado, $codEstadoOrig, $descripcionEstado, 
        $codComuna, $codDivision, $cantPasajeros, $comunasCmb, $divisionesCmb, $arrMonthDisplay, $codColor, $sexo; 

    protected $reservaService;

    protected  $arrMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    protected $listeners = ['anularReserva'];

    public function mount()
    {
        $this->openModal = true; 
        $this->randId = rand(0, 100); 
        $this->flgUsoVehiculoPersonal = false;
        $user = Auth::user();
        $this->userName = $user->name;
        $this->sexo = $user->sexo;
        $this->idUser = $user->id;
        $this->correoUser = $user->email;
        $this->getCalendarMonth(Carbon::now()->month."_".Carbon::now()->year, 0); 
        $this->diasMesesAnt = 0;
        $this->comunasCmb = Comuna::orderBy('nombreComuna', 'asc')->get();
        $this->divisionesCmb = Division::orderBy('nombreDivision', 'asc')->get();
        $this->reservaService = new ReservaServices();
        // dd($this->comunasCmb, $this->divisionesCmb);        
        // dd(SomeExampleClass::someFunction()); 
        //$pruebaService = new PruebaServices();
        //dd($pruebaService->saludo());
        $this->dispatchBrowserEvent('iniTooltips');
      
    }

    public function render()
    {
        return view('livewire.reserva', compact(['reservasFechaColl' => $this->reservasFechaSel]));
    }

    public function calculoDespliegue60Dias() {
         //Inicio Calculo Despliege de 60 dias
         $this->fechaActual = Carbon::now();
       
         //dd($this->fechaActual->firstOfMonth()->dayOfWeek);                                          
        
          $this->arrMonthDisplay = Arr::add($this->arrMonthDisplay, $this->fechaActual->month.'_'.$this->fechaActual->year, ['mesNumber'=>$this->fechaActual->month, 'mes'=>$this->arrMeses[$this->fechaActual->month-1], 'agno' => $this->fechaActual->year, 'primerDiaSemana' => $this->fechaActual->firstOfMonth()->dayOfWeek == 0?7:$this->fechaActual->firstOfMonth()->dayOfWeek, 'ultimoDiaSemana' => $this->fechaActual->lastOfMonth()->dayOfWeek == 0?7:$this->fechaActual->lastOfMonth()->dayOfWeek, 'cantDiasMes' => $this->fechaActual->daysInMonth]);  
          $this->mesSel = $this->fechaActual->month;
          $this->mesActual = $this->fechaActual->month;
         
          $this->mesSelStr = $this->arrMeses[$this->fechaActual->month];
          $this->agnoSel = $this->fechaActual->year;
          $this->cantDaysMonth = $this->fechaActual->daysInMonth;
          $this->firstDayMonth = $this->fechaActual->firstOfMonth()->dayOfWeek; 
          $this->lastDayMonth = $this->fechaActual->lastOfMonth()->dayOfWeek;
  
          $this->fechaSiguiente = Carbon::now()->addMonthsNoOverflow(); //addMonthsNoOverflow para que cuando finalice el mes no agregue dos meses 
          $diasSiguienteMes = $this->fechaSiguiente->daysInMonth; 
          $this->arrMonthDisplay = Arr::add($this->arrMonthDisplay, $this->fechaSiguiente->month.'_'.$this->fechaSiguiente->year, ['mesNumber'=>$this->fechaSiguiente->month, 'mes'=>$this->arrMeses[$this->fechaSiguiente->month-1], 'agno' => $this->fechaSiguiente->year, 'primerDiaSemana' => $this->fechaSiguiente->firstOfMonth()->dayOfWeek == 0?7:$this->fechaSiguiente->firstOfMonth()->dayOfWeek, 'ultimoDiaSemana' => $this->fechaSiguiente->lastOfMonth()->dayOfWeek == 0?7:$this->fechaSiguiente->lastOfMonth()->dayOfWeek,'cantDiasMes' => $this->fechaSiguiente->daysInMonth]);  
        
          $this->diasRestantesMesActual = $this->fechaActual->daysInMonth - Carbon::now()->format('d') * 1 + 1;//Cálculo de los dias restantes para que termine el mes, se le suma uno para incluir el dia actual
         //Si los dos meses no suman 60 dias se agrega otro mes
          if (( $this->diasRestantesMesActual+$diasSiguienteMes) < 60) {
              $this->fechaUltima = Carbon::now()->addMonthsNoOverflow(2); 
              $this->arrMonthDisplay = Arr::add($this->arrMonthDisplay, $this->fechaUltima->month.'_'.$this->fechaUltima->year, ['mesNumber'=>$this->fechaUltima->month, 'mes'=>$this->arrMeses[$this->fechaUltima->month-1], 'agno' => $this->fechaUltima->year, 'primerDiaSemana' => $this->fechaUltima->firstOfMonth()->dayOfWeek == 0?7:$this->fechaUltima->firstOfMonth()->dayOfWeek, 'ultimoDiaSemana' => $this->fechaUltima->lastOfMonth()->dayOfWeek == 0?7:$this->fechaUltima->lastOfMonth()->dayOfWeek, 'cantDiasMes' => $this->fechaUltima->daysInMonth]);     
          }
        //Fin Calculo Despliege de 60 dias  
    }
    
    public function getCalendarMonth($mesSel, $flgMoveScroll)
    {      
        $this->calculoDespliegue60Dias();
        $reservaService = new ReservaServices();
        $reservaService->getReservas($this);

          $this->diasMesesAnt = 0;
        if ($this->arrMonthDisplay[$mesSel]['mes'] == last($this->arrMonthDisplay)['mes']) {
            if (count($this->arrMonthDisplay) == 2) {
                $this->diasMesesAnt = $this->diasRestantesMesActual; 
            } else { //Sino el array contiene 3 meses 
                //Se suman los dias restantes del primer mes mas los del segundo     
                $this->diasMesesAnt = $this->diasRestantesMesActual + $this->arrMonthDisplay[$this->fechaSiguiente->month.'_'.$this->fechaSiguiente->year]['cantDiasMes'];
            }
        }

        //$diasMesesAnt 

        $itemMesSel = $this->arrMonthDisplay[$mesSel];
        $this->mesSelStr = $this->arrMeses[(explode('_',  $mesSel)[0])*1-1]; //Se extrae solo el mes (la clave esta compuesta por mes año), la posicion es el mes-1 
        $this->mesSel = (explode('_',  $mesSel)[0])*1; //Se extrae solo el mes (la clave esta compuesta por mes año) //$mesSel;
        $this->agnoSel = $itemMesSel['agno']; 
        $this->cantDaysMonth = $itemMesSel['cantDiasMes'];
        $this->firstDayMonth = $itemMesSel['primerDiaSemana']; 
        $this->lastDayMonth = $itemMesSel['ultimoDiaSemana']; 
        
        $this->dispatchBrowserEvent('iniTooltips'); 

        if ($flgMoveScroll == 1) {
            $this->dispatchBrowserEvent('moveScroll', ['id' => '#headCalendar']);
        }
    } 
    
    // public function getReservasFechaSel($fechaSel) {
    //     //Despliegue de reservas Tooltip onmouseover dia 
    //     $this->reservasFechaSel = collect(Reservavehiculo::join('users', 'users.id', '=', 'reservavehiculos.idUser')
    //         ->join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado')
    //         // ->whereRaw("DATE_FORMAT(fechaSolicitud, '%d/%m/%Y') = " . $this->fechaModal)
    //         ->where('fechaSolicitud', '=', Carbon::createFromFormat('d/m/Y', Carbon::parse($fechaSel)->format('d/m/Y'))->format('Y-m-d'))
    //         ->get(['reservavehiculos.*', 'users.id', 'users.name', 'estados.descripcionEstado']));        
    // }

    public function setFechaModal($fechaSel) {
       $reservaService = new ReservaServices();
       $reservaService->setFechaModal($fechaSel, $this); 
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

        $reservaService = new ReservaServices();

        $this->validateOnly($field, $reservaService->getArrRules($this)); 
    }


    // public function buscarReservaFuncionario()
    // {
    //     return count(Reservavehiculo::where('idUser', '=', $this->idUser) 
    //         ->where('fechaSolicitud', '=', Carbon::createFromFormat('d/m/Y', $this->fechaModal)->format('Y-m-d'))->get()) > 0;
    // }


    public function confirmAnularReserva() {
        $reservaService = new ReservaServices();
        $reservaService->confirmAnularReserva($this);
    }

    public function anularReserva() {
        $reservaService = new ReservaServices(); 
        $reservaService->anularReserva($this);
    }

    public function solicitarReserva()  {
        $reservaService = new ReservaServices();

        $this->withValidator(function (Validator $validator) {
            $validator->after(function ($validator) {
                $fieldsErrors = array_keys($validator->errors()->getMessages()); 

                if (count($fieldsErrors) > 0) {                   
                    $this->dispatchBrowserEvent('movScrollModalById', ['id' => '#id'.$fieldsErrors[0]]);//Mover Scroll al campo con el error
                }         
            });
        })->validate($reservaService->getArrRules($this));  
        
           $flgError = false; 
          //Se valida si ya existe una reserva para el funcionario en la fecha seleccionada  
          if ($this->flgNuevaReserva == true && $this->buscarReservaFuncionario() == true) {
            $flgError = true; 
            // $this->resetValidation(['idUserSel', 'fechaSolicitud']);
            // $this->resetErrorBag(['idUserSel', 'fechaSolicitud']);
            // $this->addError('idUserSel', 'Usted ya realizó una solicitud de reserva para el día ' . Carbon::createFromFormat('Y-m-d', $this->fechaSolicitud)->format('d-m-Y') . '.');

            $this->dispatchBrowserEvent('swal:information', [
                'icon' => 'error', //'info', 
                'title' => '<span class="fs-6 text-primary" style="font-weight:430;">Usted ya registra una solicitud de reserva para el día </span><span class="fs-6 text-success" style="font-weight:430;">' . Carbon::createFromFormat('Y-m-d', $this->fechaSolicitud)->format('d-m-Y') . '.</span>',
                //'mensaje' => '<span class="ps-2 fs-6 text-primary" style="font-weight:430;">Algunos campos contienen Errores, por favor reviselos y corrijalos.</span>',
                'timer' => '5000',
            ]);

            $this->dispatchBrowserEvent('movScrollModalById', ['id' => '#idfechaReserva']);
        }

        if ($flgError == false) {
            $reservaService->solicitarReserva($this);
        }
        
    }
}
