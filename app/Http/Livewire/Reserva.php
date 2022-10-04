<?php

namespace App\Http\Livewire;
use App\Classes\PruebaServices;
use Livewire\Component;
use Illuminate\Support\Carbon;
use App\Classes\ReservaServices;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservavehiculo;
use App\Models\User;
use App\Models\Comuna;
use App\Models\Division;
use App\Tools\SomeExampleClass;
use App\Rules\HoraValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use App\Mail\CorreoNotificacion;
use App\Mail\CorreoAnulacion;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class Reserva extends Component 
{
    public $horaInicio, $horaFin, $firstDayMonth, $lastDayMonth, $cantDaysMonth,
        $monthNow, $monthNowStr, $nextMontStr, $yearNow, $yearNextMont, $flgBisiesto,
        $fechaModal, $flgNextMonth, $monthSel, $yearSel, $openModal, $flgUsoVehiculoPersonal,
        $motivo, $userName, $idUser, $idReserva, $reservas, $reservasFechaSel1, $reservasFechaSel,
        $arrCantReservasCount, $dayNow, $diaActual, $mesSel, $agnoSel, $mesSelStr, $mesActual, $randId,
        $diasRestantesMesActual, $fechaActual, $diasMesesAnt, $correoUser, $codEstado, $descripcionEstado, 
        $codComuna, $codDivision, $cantPasajeros, $comunasCmb, $divisionesCmb, $arrMonthDisplay; 

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
        $this->idUser = $user->id;
        $this->correoUser = $user->email;
        $this->getCalendarMonth(Carbon::now()->month);
        $this->diasMesesAnt = 0;
        $this->comunasCmb = Comuna::all();
        $this->divisionesCmb = Division::all();
        $this->reservaService = new ReservaServices();
        // dd($this->comunasCmb, $this->divisionesCmb);        
        // dd(SomeExampleClass::someFunction());
        //$pruebaService = new PruebaServices();
        //dd($pruebaService->saludo());
    }

    public function render()
    {
        return view('livewire.reserva', compact(['reservasFechaColl' => $this->reservasFechaSel]));
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
  
          $fechaSiguiente = Carbon::now()->addMonthsNoOverflow(); //addMonthsNoOverflow para que cuando finalice el mes no agregue dos meses 
          $diasSiguienteMes = $fechaSiguiente->daysInMonth;
          $this->arrMonthDisplay = Arr::add($this->arrMonthDisplay, $fechaSiguiente->month, ['mes'=>$this->arrMeses[$fechaSiguiente->month-1], 'agno' => $fechaSiguiente->year, 'primerDiaSemana' => $fechaSiguiente->firstOfMonth()->dayOfWeek == 0?7:$fechaSiguiente->firstOfMonth()->dayOfWeek, 'ultimoDiaSemana' => $fechaSiguiente->lastOfMonth()->dayOfWeek == 0?7:$fechaSiguiente->lastOfMonth()->dayOfWeek,'cantDiasMes' => $fechaSiguiente->daysInMonth]);  
        
          $this->diasRestantesMesActual = $this->fechaActual->daysInMonth - Carbon::now()->format('d') * 1 + 1;//Calculo de los dias restantes para que termine el mes, se le suma uno para incluir el dia actual
         //Si los dos meses no suman 60 dias se agrega otro mes
          if (( $this->diasRestantesMesActual+$diasSiguienteMes) < 60) {
              $fechaUltima = Carbon::now()->addMonthsNoOverflow(2);
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

      //Tabla Hash (key=fechaSolicitud) con las reservas realizadas
        foreach ($reservas as $item) {
           $this->arrCantReservasCount = Arr::add($this->arrCantReservasCount, $item['fechaSolicitud'], $item['cantReservas']);
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

        $this->validateOnly($field, $this->getArrRules());
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
        $reservaService->solicitarReserva($this);
    }

    public function getArrRules()
    {
        return [
            'horaInicio' => ['required', 'date_format:H:i', new HoraValidator()],
            'horaFin' => ['required', 'date_format:H:i', new HoraValidator()],
            'codDivision' => 'required',
            'codComuna' => 'required',
            'cantPasajeros' => 'required:gt:0',
            'motivo' => 'required:max:500',
        ];
    }
}
