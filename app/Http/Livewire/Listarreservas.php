<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Classes\ReservaServices;
use App\Models\Reservavehiculo;
use App\Models\Comuna;
use App\Models\Division;
use App\Models\Estado;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Validation\Validator;

class Listarreservas extends Component
{
    use WithPagination;
 
    protected $paginationTheme = 'bootstrap'; 
  
    public $userName, $fechaSolicitud, $idUser, $fechaDesde, $fechaHasta, $idReserva, $horaInicio, $horaFin, $codEstado, 
    $codEstadoOrig, $descripcionEstado, $motivo, $flgUsoVehiculoPersonal, $fechaModal, $codComuna, $codDivision, $cantPasajeros, $comunasCmb, $divisionesCmb, 
    $correoUser, $arrCantReservasCount, $codColor, $sexo, $flgNuevaReserva, $fechaInicioReserva, 
    $fechaFinReserva, $cantReservasSearch, $flgSolicitudesHoy, $flgReservasHoy, $codEstadoSearch;

    protected $listeners = ['anularReserva'];

    public function mount() {
        $user = Auth::user();
        $this->userName = $user->name;
        $this->sexo = $user->sexo;
        $this->idUser = $user->id;
        $this->correoUser = $user->email;
        $this->comunasCmb = Comuna::orderBy('nombreComuna', 'asc')->get();
        $this->divisionesCmb = Division::orderBy('nombreDivision', 'asc')->get();
        $this->flgNuevaReserva = false;
       // $this->consultarRerservasUser();
    }
    
    public function render() {   
        $fechaInicio = Carbon::now()->format('Y-m-d');//Carbon::now()->format('Y-m-01');
        $fechaFin = Carbon::now()->addMonthsNoOverflow(3); //Se muestran las reservas en un rango de 3 meses
        $fechaFin = $fechaFin->format('Y-m-' . $fechaFin->daysInMonth);

        //Busqueda por rango de fecha      
        if ($this->flgSolicitudesHoy == true || $this->flgReservasHoy == true) {//Logica asociada al Botón Reservas Hoy
            $fechaInicio = Carbon::now()->format('Y-m-d'); 
            $fechaFin = Carbon::now()->format('Y-m-d');  
         }
         else
           if (!empty($this->fechaInicioReserva) && !empty($this->fechaFinReserva)) {
               $fechaInicio = $this->fechaInicioReserva;
               $fechaFin = $this->fechaFinReserva; 
            }

         if ($this->flgSolicitudesHoy == true) {
            $sqlFechaSearch = "DATE_FORMAT(reservavehiculos.created_at, '%Y-%m-%d') >= DATE_FORMAT('" . $fechaInicio . "', '%Y-%m-%d') and DATE_FORMAT(reservavehiculos.created_at, '%Y-%m-%d') <= DATE_FORMAT('" . $fechaFin."', '%Y-%m-%d')"; 
         } else { 
            $sqlFechaSearch = "reservavehiculos.fechaSolicitud >= '" . $fechaInicio . "' and reservavehiculos.fechaSolicitud <= '" . $fechaFin."'"; 
         }        

        $reservasUsuario = Reservavehiculo::where('reservavehiculos.idUser', '=', $this->idUser) 
            ->join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado') 
            ->leftJoin('comunas', 'comunas.codComuna', '=', 'reservavehiculos.codComuna') 
            ->leftJoin('vehiculos', 'vehiculos.codVehiculo', '=', 'reservavehiculos.codVehiculo') 
            ->select('reservavehiculos.*', 'estados.descripcionEstado', 'estados.codColor', 'comunas.nombreComuna', 'vehiculos.descripcionVehiculo') 
            ->whereRaw($sqlFechaSearch)
            ->where('reservavehiculos.codEstado', 'like', '%' . $this->codEstadoSearch . '%')
            ->orderBy('fechaSolicitud', 'asc');

            // dd($reservasUsuario->toSql());
            
            $reservasUsuarioCollect = $reservasUsuario->get(); 

           //Se obtiene la fecha menor y mayor del resultado de la busqueda 
            $this->cantReservasSearch = "";
            if (count($reservasUsuarioCollect) > 0) {
                $this->fecInicioResult = $reservasUsuarioCollect[0]->fechaSolicitud;
                $this->fecFinResult = $reservasUsuarioCollect[(count($reservasUsuarioCollect)-1)]->fechaSolicitud;
                $this->cantReservasSearch = count($reservasUsuarioCollect);                
            }

        $reservasUsuario = $reservasUsuario->paginate(5);

        $this->dispatchBrowserEvent('iniTooltips');

        $estadosCmbSearch = Estado::all();

      //Si es una reserva Nueva la tabla del listado de solicitudes del Modal no se filtra por el usuario seleccionado 
        $sqlRawStr =  $this->flgNuevaReserva == true ? " 0 = 0 ":" idUser != ". ($this->idUser > 0 ? $this->idUser:0);
      
        //Lista de reservas realizadas el mismo dia de la reserva seleccionada 
        $reservasFechaSel = collect(Reservavehiculo::join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado') 
            ->join('comunas', 'reservavehiculos.codComuna', '=', 'comunas.codComuna', 'left outer') 
            ->join('users', 'users.id', '=', 'reservavehiculos.idUser')
            ->join('vehiculos', 'reservavehiculos.codVehiculo', '=', 'vehiculos.codVehiculo', 'left outer')
            ->where('fechaSolicitud', '=', $this->fechaSolicitud)
            ->where('idUser', '!=', $this->idUser)  //Reservas de otros funcionarios
            ->WhereRaw($sqlRawStr) 
            ->get(['reservavehiculos.*', 'users.id', 'users.name', 'estados.descripcionEstado', 'estados.codColor', 'reservavehiculos.codVehiculo', 'vehiculos.descripcionVehiculo', 'comunas.nombreComuna']));

        return view('livewire.listarreservas', compact('reservasUsuario', 'reservasFechaSel', 'estadosCmbSearch'));
    }

    public function setFechaModal($fechaSel)  {      
      //Se elimina validacion de busqueda por fechas, para que no valide en el modal
       $this->resetValidation(['fechaInicioReserva', 'fechaFinReserva']); 
       $this->resetErrorBag(['fechaInicioReserva', 'fechaFinReserva']);

        $reservaService = new ReservaServices();
        $reservaService->setFechaModal($fechaSel, $this); 
    }

    public function confirmAnularReserva() {
        $reservaService = new ReservaServices();
        $reservaService->confirmAnularReserva($this);
    }

    public function anularReserva() {
     //Se elimina validacion de busqueda por fechas, para que no valide en el modal
       $this->resetValidation(['fechaInicioReserva', 'fechaFinReserva']);
       $this->resetErrorBag(['fechaInicioReserva', 'fechaFinReserva']);

        $reservaService = new ReservaServices(); 
        $reservaService->anularReserva($this);
    }

    public function solicitarReserva() {
     //Se elimina validacion de busqueda por fechas, para que no valide esos campos en el modal
       $this->resetValidation(['fechaInicioReserva', 'fechaFinReserva']);
       $this->resetErrorBag(['fechaInicioReserva', 'fechaFinReserva']);
       $reservaService = new ReservaServices();

       $this->withValidator(function (Validator $validator) {
        $validator->after(function ($validator) {
            $fieldsErrors = array_keys($validator->errors()->getMessages()); 

            if (count($fieldsErrors) > 0) {
              $this->dispatchBrowserEvent('movScrollModalById', ['id' => '#id'.$fieldsErrors[0]/*, 'arrInptError' => $fieldsErrors */]);//Mover Scroll al campo con el error
            }         
        });
    })->validate($reservaService->getArrRules($this));  
    
       $flgError = false; 
      //Se valida si ya existe una reserva para el funcionario en la fecha seleccionada  
      if ($this->flgNuevaReserva == true && $this->buscarReservaFuncionario() == true) {
        $flgError = true; 
        $this->resetValidation(['idUserSel', 'fechaSolicitud']);
        $this->resetErrorBag(['idUserSel', 'fechaSolicitud']);
        $this->addError('fechaSolicitud', 'Usted ya realizó una solicitud de reserva para el día ' . Carbon::createFromFormat('Y-m-d', $this->fechaSolicitud)->format('d-m-Y') . '.');

        $this->dispatchBrowserEvent('movScrollModalById', ['id' => '#idfechaSolicitud']); 
    }

    if ($flgError == false) {
        $reservaService->solicitarReserva($this);
    }
    }

    public function nuevaReserva() {        
        $this->resetValidation(['fechaInicioReserva', 'fechaFinReserva']);
        $this->resetErrorBag(['fechaInicioReserva', 'fechaFinReserva']);
        $reservaService = new ReservaServices(); 
        $reservaService->resetCamposModal($this);
        $this->flgNuevaReserva = true;
        $this->dispatchBrowserEvent('showModal');
    }

    public function buscarReservas() {
        $this->validate( 
            [
                'fechaInicioReserva' => 'required|date_format:Y-m-d',
                'fechaFinReserva' => 'required|date_format:Y-m-d|after_or_equal:fechaInicioReserva',
            ]  
        ); 
 
        $this->dispatchBrowserEvent('moveScroll', ['id' => '#listadoSolReservas']);
    }

    public function setReservasHoySearch() {
        $this->flgReservasHoy = true; 
        $this->flgSolicitudesHoy = false; 
        $this->dispatchBrowserEvent('moveScroll', ['id' => '#listadoSolReservas']);
        $this->reset(['codEstadoSearch', 'fechaInicioReserva', 'fechaFinReserva']);//Se limpian los demas filtros 
        $this->resetValidation(['fechaInicioReserva', 'fechaFinReserva']);
        $this->resetErrorBag(['fechaInicioReserva', 'fechaFinReserva']);
        $this->resetPage();  
    }

    public function setSolicitudesHoySearch() {
        //Buscar solicitudes ingresadas en la fecha actual
          $this->flgSolicitudesHoy = true;
          $this->flgReservasHoy = false;
          $this->dispatchBrowserEvent('moveScroll', ['id' => '#listadoSolReservas']);
          $this->reset(['codEstadoSearch', 'fechaInicioReserva', 'fechaFinReserva']);//Se limpian los demas filtros 
          $this->resetValidation(['fechaInicioReserva', 'fechaFinReserva']);
          $this->resetErrorBag(['fechaInicioReserva', 'fechaFinReserva']);
          $this->resetPage();  
    } 
 
    public function mostrarTodo() { 
        $this->reset(['codEstadoSearch', 'fechaInicioReserva', 'fechaFinReserva', 'flgSolicitudesHoy', 'flgReservasHoy']);
        $this->flgReservasHoy = false;
        $this->flgSolicitudesHoy = false;
        //$this->dispatchBrowserEvent('iniTooltips');
        $this->dispatchBrowserEvent('moveScroll', ['id' => '#listadoSolReservas']);
        $this->resetPage();
    }

    public function updated($field, $value)
    {
        // if ($field == 'flgUsoVehiculoPersSel') { //Campo opcional no se valida
        //     return true;
        // }

     if ($field == 'codEstadoSearch') {
        $this->descripEstadoSearch = ""; 
        $this->colorEstadoSearch =  ""; 
         if ($this->codEstadoSearch > 0) {
            $estado = Estado::where('codEstado', '=', $this->codEstadoSearch)->first();
            $this->descripEstadoSearch = $estado->descripcionEstado;
            $this->colorEstadoSearch =  $estado->codColor; 
         }

         $this->flgSolicitudesHoy = false; 
         $this->flgReservasHoy = false;

        $this->dispatchBrowserEvent('moveScroll', ['id' => '#listadoSolReservas']);        
     }

        if ($field == 'fechaInicioReserva' || $field == 'fechaFinReserva') {
            $this->flgSolicitudesHoy = false;
            $this->flgReservasHoy = false;
        }

        $this->resetPage();

        if ($field == 'horaInicio' || $field == 'horaFin') {
             $this->resetValidation(['horaInicio', 'horaFin']);
             $this->resetErrorBag(['horaInicio', 'horaFin']);
        }

        $this->resetValidation(['fechaInicioReserva', 'fechaFinReserva']); 
        $this->resetErrorBag(['fechaInicioReserva', 'fechaFinReserva']);

        $reservaService = new ReservaServices();
        $this->validateOnly($field, $reservaService->getArrRules($this));
        
       //Se valida si ya existe una reserva para el funcionario en la fecha seleccionada 
        if ($this->flgNuevaReserva == true) {
            if (($field == 'fechaSolicitud')) {
                if (!empty($this->fechaSolicitud)) {
                    if ($this->buscarReservaFuncionario() == true) {
                        $this->resetValidation(['fechaSolicitud']);
                        $this->resetErrorBag(['fechaSolicitud']);
                        $this->addError($field, 'Usted ya registra una solicitud de reserva para el día seleccionado: ' . Carbon::createFromFormat('Y-m-d', $this->fechaSolicitud)->format('d-m-Y') . '.');
                    }
                }
            }
        }    
    }

    public function buscarReservaFuncionario() {
        return count(Reservavehiculo::where('idUser', '=', $this->idUser)
            ->where('fechaSolicitud', '=', $this->fechaSolicitud)->get()) > 0;
    }
}
