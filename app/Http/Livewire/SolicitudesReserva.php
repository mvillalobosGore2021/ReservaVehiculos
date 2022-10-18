<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Reservavehiculo;
use App\Models\Estado;
use App\Models\Vehiculo;
use App\Models\User;
use App\Models\Comuna;
use App\Models\Division;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use App\Rules\HoraValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\CorreoNotificacion;
use App\Mail\CorreoAnulacion;
use Exception;

class SolicitudesReserva extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $fechaSolSearch, $codEstadoSearch, $nameSearch, $codVehiculoSel, $codEstadoSel;

    public $idReservaSel, $fechaSolicitudSel, $horaInicioSel, $horaFinSel, $descripEstadoSel, $flgUsoVehiculoPersSel,
        $motivoSel, $nameSel, $sexoUserSel, $flgNuevaReserva, $userList, $idUserSel, $emailSel, $usernameLog, $idUserAdmin, $flgSearchHoy,
        $fechaSearch, $flgFechaSearch, $flgValidateConfirmar, $funcionarioValidate, $descripVehiculoValidate,
        $codComunaSel, $codDivisionSel, $cantPasajerosSel, $comunasCmb, $divisionesCmb, $sexoUserLog;

    public Collection $inputsTable;

    public $reservasFechaSelPaso;

    public function mount()
    {
        $this->userList = User::orderBy('name')->get();
        // $this->estadosCmb = Estado::where('codEstado', '>', 1)->get();
        // Ver por que se pierden los datos del combo estado y el de funcionarios no al crear una nueva reserva
        $user = Auth::user();
        $this->idUserAdmin = $user->id;
        $this->usernameLog = $user->name; 
        $this->sexoUserLog = $user->sexo;

        $this->fechaSearch = "";
        $this->flgValidateConfirmar = false;
        $this->comunasCmb = Comuna::orderBy('nombreComuna', 'asc')->get(); 
        $this->divisionesCmb = Division::orderBy('nombreDivision', 'asc')->get();
    }

    public function render()
    {
        $fechaInicio = Carbon::now()->format('Y-m-01');
        $fechaFin = Carbon::now()->addMonthsNoOverflow(3); //Se muestran las reservas en un rando de 3 meses
        $fechaFin = $fechaFin->format('Y-m-' . $fechaFin->daysInMonth);

        $sqlFechaSearch = "";
        if ($this->flgFechaSearch == 1) {
            $sqlFechaSearch = "reservavehiculos.created_at like '%" . $this->fechaSearch . "%'";
        } else {
            $sqlFechaSearch = "reservavehiculos.fechaSolicitud like '%" . $this->fechaSearch . "%'";
        }

        //Se obtienen las reservas para un rango de dos meses
        $reservasTotales = Reservavehiculo::join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado')
            ->join('users', 'users.id', '=', 'reservavehiculos.idUser') 
            ->leftJoin('comunas', 'comunas.codComuna', '=', 'reservavehiculos.codComuna') 
            ->leftJoin('vehiculos', 'vehiculos.codVehiculo', '=', 'reservavehiculos.codVehiculo') 
            ->select('reservavehiculos.*', 'users.id', 'users.name', 'estados.descripcionEstado', 'estados.codColor', 'comunas.nombreComuna', 'vehiculos.descripcionVehiculo') 
            ->whereBetween('fechaSolicitud', [$fechaInicio, $fechaFin])
            // ->where('reservavehiculos.created_at', 'like', '%' . $this->fechaHoySearch . '%') 
            ->whereRaw($sqlFechaSearch)
            ->where('users.name', 'like', '%' . $this->nameSearch . '%')
            ->where('reservavehiculos.codEstado', 'like', '%' . $this->codEstadoSearch . '%')
            ->orderBy('fechaSolicitud', 'asc') 
            ->paginate(5);


            $estadosCmb = null;

     if ($this->flgNuevaReserva == true) {
        $estadosCmb = Estado::where('codEstado', '=', 2/*Confirmado*/)  
                       ->orderBy('codEstado')->get();//Las reservas nuevas solamente se ingresan en estado confirmado
     } else {
        $estadosCmb = Estado::where('codEstado', '!=', 1/*No Confirmado*/) 
                       ->orderBy('codEstado')->get();//El administrador no ingresa solicitudes No Confirmadas
     }

        $cmbVehiculos = Vehiculo::all();

        $estadosCmbSearch = Estado::all();

        $this->inputsTable = new Collection();
        foreach ($reservasTotales as $item) {
            $this->inputsTable->push([
                'idReserva' => $item->idReserva, 'codEstado' => ''
            ]);
        }

        $this->dispatchBrowserEvent('iniTooltips');
      
        //Si es una reserva Nueva la tabla del listado de solicitudes del Modal no se filtra por el usuario seleccionado 
        $sqlRawStr =  $this->flgNuevaReserva == true ? " 0 = 0 ":" idUser != ". ($this->idUserSel > 0 ? $this->idUserSel:0);       

        //Lista de reservas realizadas el mismo dia de la reserva seleccionada
        $reservasFechaSel = collect(Reservavehiculo::join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado') 
            ->join('comunas', 'reservavehiculos.codComuna', '=', 'comunas.codComuna', 'left outer') 
            ->join('users', 'users.id', '=', 'reservavehiculos.idUser')
            ->join('vehiculos', 'reservavehiculos.codVehiculo', '=', 'vehiculos.codVehiculo', 'left outer')
            ->where('fechaSolicitud', '=', $this->fechaSolicitudSel) 
            // ->where('idUser', '!=', $this->idUserSel)
            ->WhereRaw($sqlRawStr)
            ->get(['reservavehiculos.*', 'users.id', 'users.name', 'estados.descripcionEstado', 'estados.codColor', 'reservavehiculos.codVehiculo', 'vehiculos.descripcionVehiculo', 'comunas.nombreComuna']));

        return view('livewire.solicitudes-reserva', compact(['reservasTotales', 'reservasFechaSel', 'estadosCmbSearch', 'estadosCmb', 'cmbVehiculos']));
    }

    public function reservaSel($idReservaSel, $openModal)
    {
        $this->flgNuevaReserva = false;
        // dd($idReservaSel, $openModal);
        $reservaSel = Reservavehiculo::join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado')
            ->join('users', 'users.id', '=', 'reservavehiculos.idUser')
            ->where('idReserva', '=', $idReservaSel)->first();

        //dd($reservaSel);
        $this->flgNuevaReserva = false;
        $this->idReservaSel = $reservaSel->idReserva;
        $this->fechaSolicitudSel = $reservaSel->fechaSolicitud;
        $this->horaInicioSel = Carbon::createFromFormat('H:i:s', $reservaSel->horaInicio)->format('H:i');
        $this->horaFinSel = Carbon::createFromFormat('H:i:s', $reservaSel->horaFin)->format('H:i');
        // $this->flgUsoVehiculoPersSel = $reservaSel->flgUsoVehiculoPersona;
        $this->motivoSel = $reservaSel->motivo;
        $this->nameSel = $reservaSel->name;
        $this->emailSel = $reservaSel->email;
        $this->sexoSel = $reservaSel->sexo;
        $this->idUserSel = $reservaSel->idUser; 
        $this->descripEstadoSel = $reservaSel->descripcionEstado;
        $this->codEstadoSel = $reservaSel->codEstado;
        $this->codVehiculoSel = $reservaSel->codVehiculo;
        $this->codComunaSel = $reservaSel->codComuna;
        $this->codDivisionSel = $reservaSel->codDivision;
        $this->cantPasajerosSel = $reservaSel->cantPasajeros;        

        // //Lista de reservas realizadas el mismo dia de la reserva seleccionada
        // $this->reservasFechaSelPaso = collect(Reservavehiculo::join('users', 'users.id', '=', 'reservavehiculos.idUser')
        //     ->join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado')
        //     ->where('fechaSolicitud', '=', $reservaSel->fechaSolicitud)
        //     ->get(['reservavehiculos.*', 'users.id', 'users.name', 'estados.descripcionEstado']));

        //Para diferenciar cuando se abre desde la <table> del modal o de la <table> de la página principal
        if ($openModal == 1) {
            $this->dispatchBrowserEvent('showModal');
        }

        $this->resetValidation(['fechaSolicitudSel', 'motivoSel', 'idUserSel', 'nameSel', 'codEstadoSel', 'codVehiculoSel', 'horaInicioSel', 'horaFinSel', 'codComunaSel', 'codDivisionSel', 'cantPasajerosSel']);
        $this->resetErrorBag(['fechaSolicitudSel', 'motivoSel', 'idUserSel', 'nameSel', 'codEstadoSel', 'codVehiculoSel', 'horaInicioSel', 'horaFinSel', 'codComunaSel', 'codDivisionSel', 'cantPasajerosSel']);
    }

    public function setFechaHoySearch($flgSearchHoy)
    {
        $this->flgSearchHoy = $flgSearchHoy; 
        $this->flgFechaSearch = $flgSearchHoy == 1; //Si es true se activa el Switch de busqueda por fecha de solicitud
        $this->fechaSearch = Carbon::now()->format('Y-m-d');
        $this->dispatchBrowserEvent('moveScroll', ['id' => '#listadoSolReservas']);
        $this->reset(['codEstadoSearch', 'nameSearch']);//Se limpian los demas filtros 
        $this->resetPage(); 
    }

    public function mostrarTodo()
    {
        $this->reset(['codEstadoSearch', 'nameSearch', 'fechaSearch']);
        //$this->dispatchBrowserEvent('iniTooltips');
        $this->dispatchBrowserEvent('moveScroll', ['id' => '#listadoSolReservas']);
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->reset('fechaSearch');
        // $this->dispatchBrowserEvent('iniTooltips'); 
        $this->resetPage();
    }

    public function updated($field, $value)
    {
        // if ($field == 'flgUsoVehiculoPersSel') { //Campo opcional no se valida
        //     return true;
        // }

        if ($field == 'flgFechaSearch') {
            if ($this->fechaSearch ==  Carbon::now()->format('Y-m-d')) {
                $this->flgSearchHoy = $this->flgFechaSearch == 1 ? 1 : 2;
            }
        }

        if ($field == 'fechaSearch') {
            //dd($this->fechaSearch, Carbon::now()->format('Y-m-d'));
            if ($this->fechaSearch ==  Carbon::now()->format('Y-m-d')) {
                $this->flgSearchHoy = $this->flgFechaSearch == 1 ? 1 : 2;
            } else {
                $this->flgSearchHoy = 0;
            }
        }

        $this->resetPage();

        if ($field == 'horaInicioSel' || $field == 'horaFinSel') {
             $this->resetValidation(['horaInicioSel', 'horaFinSel']);
             $this->resetErrorBag(['horaInicioSel', 'horaFinSel']);
        }

        $this->validateOnly($field, $this->getArrRules());

        //Se valida si ya existe una reserva para el funcionario en la fecha seleccionada 
        if ($this->flgNuevaReserva == true) {
            if (($field == 'idUserSel' || $field == 'fechaSolicitudSel')) {
                if ($this->idUserSel > 0 && !empty($this->fechaSolicitudSel)) {
                    if ($this->buscarReservaFuncionario() == true) {
                        $this->resetValidation(['idUserSel', 'fechaSolicitudSel']);
                        $this->resetErrorBag(['idUserSel', 'fechaSolicitudSel']);
                        $this->addError($field, 'El funcionario(a) ya realizó una solicitud de reserva para el día ' . Carbon::createFromFormat('Y-m-d', $this->fechaSolicitudSel)->format('d-m-Y') . '.');
                    }
                }
            }
        }
      
        if ($field == 'codEstadoSel' || $field == 'codVehiculoSel' || $field == 'fechaSolicitudSel') {
            // dd("Pasé por Acato ".$field, ($field == 'codEstadoSel' || $field == 'codVehiculoSel' || 'fechaSolicitudSel'));
            // dd($this->flgValidateConfirmar);
            // if ($this->flgValidateConfirmar == true) {
            //     $this->flgValidateConfirmar = false; 
            //     $this->resetValidation(['codEstadoSel', 'codVehiculoSel', 'fechaSolicitudSel']);
            //     $this->resetErrorBag(['codEstadoSel', 'codVehiculoSel', 'fechaSolicitudSel']);
            // }  

            $this->resetValidation(['codVehiculoSel']);
            $this->resetErrorBag(['codVehiculoSel']);   

            //dd(!empty($this->validateEstadoConfirmar()), $this->validateEstadoConfirmar(), $this->funcionarioValidate);
            if (!empty($this->validateEstadoConfirmar())) {
                $this->flgValidateConfirmar = true; 
                $this->addError('codVehiculoSel', 'El vehículo ' . $this->descripVehiculoValidate . ' ya se encuentra asignado a ' . $this->funcionarioValidate . ' en una reserva confirmada para el día ' . Carbon::createFromFormat('Y-m-d', $this->fechaSolicitudSel)->format('d-m-Y') . '.');
                $this->dispatchBrowserEvent('moveScrollModalById', ['id' => '#codVehiculoId']);            
            }
        }        
    }

    public function nuevaReserva()
    {
        $this->resetCamposModal();
        $this->flgNuevaReserva = true;
        //Se obtienen todos los estados distintos a No Confirmado
        //$this->estadosCmb = Estado::where('codEstado', '>', 1)->get();
        $this->dispatchBrowserEvent('showModal');
    }

    public function resetCamposModal()
    {
        $this->reset(['idReservaSel', 'codVehiculoSel', 'fechaSolicitudSel', 'horaInicioSel', 'horaFinSel', 'motivoSel', 'flgUsoVehiculoPersSel', 'descripEstadoSel', 'idUserSel', 'nameSel', 'codEstadoSel', 'codDivisionSel', 'codComunaSel', 'cantPasajerosSel']);
        $this->resetValidation(['idReservaSel', 'codVehiculoSel', 'fechaSolicitudSel', 'horaInicioSel', 'horaFinSel', 'motivoSel', 'flgUsoVehiculoPersSel', 'descripEstadoSel', 'idUserSel', 'nameSel', 'codEstadoSel', 'codDivisionSel', 'codComunaSel', 'cantPasajerosSel']);
        $this->resetErrorBag(['idReservaSel', 'codVehiculoSel', 'fechaSolicitudSel', 'horaInicioSel', 'horaFinSel', 'motivoSel', 'flgUsoVehiculoPersSel', 'descripEstadoSel', 'idUserSel', 'nameSel', 'codEstadoSel', 'codDivisionSel', 'codComunaSel', 'cantPasajerosSel']);
    }

    protected function validateEstadoConfirmar()
    {
        $countReg = 0;
        $reservaVehiculo = null;

        if (!empty($this->fechaSolicitudSel) && !empty($this->codEstadoSel) && $this->codEstadoSel == 2 /*Confirmada*/ && !empty($this->codVehiculoSel)) {
            //Se verifica si el vehiculo ya se encuentra asignado en una reserva confirmada en la fecha seleccionada
            if ($this->flgNuevaReserva == true) {
                $reservaVehiculo = Reservavehiculo::where("fechaSolicitud", "=", $this->fechaSolicitudSel)
                    ->join("users", "users.id", "=", "reservavehiculos.idUser")
                    ->where("codEstado", "=", 2) //Estado 2 = Confirmada
                    // ->whereNotNull("codVehiculo")
                    ->whereRaw("codVehiculo = " . $this->codVehiculoSel . " and codVehiculo IS NOT NULL")->first();
            } else {
                //Si es una modificación se buscan las reservas distintas al usuario seleccionado 
                $reservaVehiculo = Reservavehiculo::where("fechaSolicitud", "=", $this->fechaSolicitudSel)
                    ->join("users", 'users.id', "=", "reservavehiculos.idUser")
                    ->where("codEstado", "=", 2) //Estado 2 = Confirmada
                    ->where("idUser", "!=", $this->idUserSel)
                    // ->whereNotNull("codVehiculo")
                    ->whereRaw("codVehiculo = " . $this->codVehiculoSel . " and codVehiculo IS NOT NULL")->first();
            }

            // $this->funcionarioValidate = !empty($reservaVehiculo) ? $reservaVehiculo->name:""; 

            // dd($reservaVehiculo->toSql());
            $this->funcionarioValidate = "";
            $this->descripVehiculoValidate = "";

            if (!empty($reservaVehiculo)) {
                $this->funcionarioValidate = $reservaVehiculo->name;
                $this->descripVehiculoValidate = Vehiculo::where('codVehiculo', '=', $reservaVehiculo->codVehiculo)->first()->descripcionVehiculo;
            }
            // dd($reservaVehiculo, $reservaVehiculo->toSql());

            // dd($this->idReservaSel > 0 ? "idReserva != ".$this->idReservaSel : $this->idReservaSel." IS NULL"); 
            //  dd($this->idReservaSel, $this->fechaSolicitudSel, $this->codVehiculoSel, $reservaVehiculo->toSql());

            //$countReg = count($reservaVehiculo);
        }
        // return $countReg;
        return $reservaVehiculo;
    }

    public function guardarReservaSel()
    {
        $msjException = ""; 

        if ($this->codEstadoSel == 3 && $this->flgNuevaReserva == false/*Modo modificacion*/) { //Estado 3 = Anular, no se validan los datos
            try {
                DB::beginTransaction();

                Reservavehiculo::where("idReserva",  $this->idReservaSel)
                    ->update(["codEstado" => $this->codEstadoSel, "idUserModificacion" => $this->idUserAdmin]);

                $reservaVehiculo = Reservavehiculo::where("idReserva",  $this->idReservaSel)->first();      
                
                $user = User::where("id", "=",  $this->idUserSel)->first();
                $this->nameSel = $user->name;
                $this->sexoUserSel = $user->sexo;

                //Envío de correo  
                $mailData = [
                    'asunto' => "Notificación: Anulación de Reserva de Vehículo",
                    'resumen' => "<b>" . $this->usernameLog . "</b> ha <span style='background-color:#EF3B2D;color:white;'>Anulado</span> su reserva solicitada para el día",
                    'funcionario' => $this->nameSel,
                    'sexo' => $this->sexoUserSel,
                    'fechaCreacion' =>  Carbon::parse($reservaVehiculo->created_at)->format('d/m/Y H:i'),
                    'fechaReserva' => Carbon::createFromFormat('Y-m-d', $reservaVehiculo->fechaSolicitud)->format('d/m/Y'),
                    'horaInicio' => $reservaVehiculo->horaInicio,
                    'horaFin' => $reservaVehiculo->horaFin,
                    'descripcionEstado' => "Anulada",
                    'codEstado' => $reservaVehiculo->codEstado,
                    // 'usaVehiculoPersonal' => $objInput->flgUsoVehiculoPersonal == 0?'No':'Si',
                    'motivo' => $reservaVehiculo->motivo,
                ];

                try {
                    //Mail al postulante 
                    Mail::to($this->emailSel)->send(new CorreoNotificacion($mailData));
                } catch (exception $e) {
                    $msjException = 'Se ha producido un error al intentar enviar el correo de notificación a : <span class="fs-6 text-success" style="font-weight:500;">' . $this->emailSel . '</span>';
                    throw $e;
                }

                $userAdmin = User::where('flgAdmin', '=', 1)->get();

                // $mailData['titulo'] =  "Anulación de Reserva de Vehículo - Gobierno Regional del Bio Bio"; 
                // $mailData['asunto'] = "Se Ha anulado la reserva de " . $this->nameSel;   

                $emailAdmin = "";
                try {
                    foreach ($userAdmin as $item) {
                        $emailAdmin = $item->email;
                        $mailData['nomAdmin'] = $item->name;
                        $mailData['resumen'] = ($this->sexoUserLog == "F" ? "la funcionaria":"él funcionario")."<b>" . $this->usernameLog . "</b> ha <span style='background-color:#EF3B2D;color:white;'>Anulado</span> la reserva de <b>" . $this->nameSel . "</b> solicitada para el día";

                        if ($item->id == $this->idUserAdmin) {
                            $mailData['resumen'] = "se ha <span style='background-color:#EF3B2D;color:white;'>Anulado</span> la reserva de <b>" . $mailData['funcionario'] . "</b> solicitada para el día";
                        }

                        Mail::to($item->email)->send(new CorreoNotificacion($mailData));
                    }
                } catch (exception $e) {
                    $msjException = 'Se ha producido un error al intentar enviar el correo de notificación a :  <span class="fs-6 text-success" style="font-weight:500;">' . $emailAdmin . '</span>';
                    throw $e;
                }

                $this->dispatchBrowserEvent('swal:information', [
                    'icon' => '', //'info',
                    'mensaje' => '<i class="bi bi-send-check-fill text-success fs-4"></i><span class="ps-2 fs-6 text-primary" style="font-weight:430;">La reserva de ' . $this->nameSel . ' ha sido anulada y notificada con éxito.</span>',
                ]);


                $this->dispatchBrowserEvent('closeModal');

                DB::commit();
            } catch (exception $e) {
                DB::rollBack();
                $this->dispatchBrowserEvent('swal:information', [
                    'icon' => 'error', //'info',
                    'title' => '<span class="fs-6 text-primary" style="font-weight:430;">No fue posible anular la reserva. ' . $msjException . '</span>',
                ]);
                session()->flash('exceptionMessage', $e->getMessage());
            }
        } else {
            $flgError = false;
            try { 
                $this->validate($this->getArrRules());                 
            } catch (exception $e) {
                $flgError = true;
            } 

            // dd($flgError, $this->getArrRules());

            if ($flgError == true) {
                // dd("Entre aca");
                $this->dispatchBrowserEvent('swal:information', [
                    'icon' => 'error', //'info',
                    'title' => '<span class="fs-6 text-primary" style="font-weight:430;">Algunos campos contienen Errores, por favor revíselos y corríjalos.</span>',
                    //'mensaje' => '<span class="ps-2 fs-6 text-primary" style="font-weight:430;">Algunos campos contienen Errores, por favor reviselos y corrijalos.</span>',
                    'timer' => '5000',
                ]);

                $this->validate($this->getArrRules()); //Para que se generen nuevamente los msjs           
            }

            //Se valida si ya existe una reserva para el funcionario en la fecha seleccionada  
            if ($this->flgNuevaReserva == true && $this->buscarReservaFuncionario() == true) {
                $flgError = true; 
                $this->resetValidation(['idUserSel', 'fechaSolicitudSel']);
                $this->resetErrorBag(['idUserSel', 'fechaSolicitudSel']);
                $this->addError('idUserSel', 'El funcionario(a) ya realizó una solicitud de reserva para el día ' . Carbon::createFromFormat('Y-m-d', $this->fechaSolicitudSel)->format('d-m-Y') . '.');

                $user = User::where('id', '=', $this->idUserSel)->first();

                $this->dispatchBrowserEvent('swal:information', [
                    'icon' => 'error', //'info', 
                    'title' => '<span class="fs-6 text-success" style="font-weight:450;">' . $user->name . ' <span class="fs-6 text-primary" style="font-weight:430;">ya registra una solicitud de reserva para el día </span><span class="fs-6 text-success" style="font-weight:430;">' . Carbon::createFromFormat('Y-m-d', $this->fechaSolicitudSel)->format('d-m-Y') . '.</span>',
                    //'mensaje' => '<span class="ps-2 fs-6 text-primary" style="font-weight:430;">Algunos campos contienen Errores, por favor reviselos y corrijalos.</span>',
                    'timer' => '5000',
                ]);

                $this->dispatchBrowserEvent('moveScrollModal');
            } else
                //Validar que el vehiculo no este asignado en otra reserva confirmada para el dia seleccionado 
                if (!empty($this->validateEstadoConfirmar())) {
                    $flgError = true;
                    $this->addError('codVehiculoSel', 'El vehículo ' . $this->descripVehiculoValidate . ' ya se encuentra asignado a ' . $this->funcionarioValidate . ' en una reserva confirmada para el día ' . Carbon::createFromFormat('Y-m-d', $this->fechaSolicitudSel)->format('d-m-Y') . '.');

                    $this->dispatchBrowserEvent('swal:information', [
                        'icon' => 'error', //'info',
                        'title' => '<span class="fs-6 text-primary" style="font-weight:450;">El vehículo <span class="fs-6 text-success" style="font-weight:450;">' . $this->descripVehiculoValidate . '</span> <span class="fs-6 text-primary" style="font-weight:430;">ya se encuentra asignado a </span><span class="fs-6 text-success" style="font-weight:430;">' . $this->funcionarioValidate . '</span><span class="fs-6 text-primary" style="font-weight:430;"> en una reserva confirmada para el día </span><span class="fs-6 text-success" style="font-weight:430;">' . Carbon::createFromFormat('Y-m-d', $this->fechaSolicitudSel)->format('d-m-Y') . '</span>',
                        //'mensaje' => '<span class="ps-2 fs-6 text-primary" style="font-weight:430;">Algunos campos contienen Errores, por favor reviselos y corrijalos.</span>',
                        'timer' => '5000',
                    ]);
                }

            if ($flgError == false) {
                try {
                    DB::beginTransaction();
                    // 'idUser', 'motivo', 'prioridad', 'flgUsoVehiculoPersonal', 'fechaSolicitud', 'fechaConfirmacion', 'codEstado'
                    $prioridad = 0; //Calcular del listado de reserva por orden de llegada, dar la posibilidad de cambiar la prioridad al Adm

                    //Validar que el usuario no tenga una reserva ingresada para el mismo dia cuando se ingresa una nueva   

                    $this->nameSel = (User::where("id", "=",  $this->idUserSel)->first())->name;    

                    $camposReservaVehiculoArr =  [
                        'idUser' => $this->idUserSel,
                        'prioridad' => $prioridad,
                        // 'flgUsoVehiculoPersonal' => $this->flgUsoVehiculoPersSel, 
                        'fechaSolicitud' => $this->fechaSolicitudSel, //Carbon::createFromFormat('d/m/Y', $this->fechaSolicitudSel)->format('Y-m-d'), 
                        'horaInicio' => $this->horaInicioSel,
                        'horaFin' => $this->horaFinSel,
                        'motivo' => $this->motivoSel,
                        'codEstado' => $this->codEstadoSel,
                        'codVehiculo' => $this->codVehiculoSel,
                        'codDivisionSel' => $this->codDivisionSel,
                        'codComunaSel' => $this->codComunaSel,
                        'cantPasajerosSel' => $this->cantPasajerosSel,
                        //'fechaConfirmacion' => $this->correoRepLegal, fecha de confirmación se guarda cuando el administrador confirma la reserva
                    ];

                    //Se guarda el usuario que crea o modifica
                    if ($this->idReservaSel > 0) {
                        $camposReservaVehiculoArr = Arr::add($camposReservaVehiculoArr, 'idUserModificacion', $this->idUserAdmin);
                    } else {
                        $this->idReservaSel = 0; //Para que no sea null
                        $camposReservaVehiculoArr = Arr::add($camposReservaVehiculoArr, 'idUserCreacion', $this->idUserAdmin);
                    }

                    if ($this->codEstadoSel == 2) { //Si el estado es Confirmar se agrega la fecha de confirmacion 
                        $camposReservaVehiculoArr = Arr::add($camposReservaVehiculoArr, 'fechaConfirmacion', now());
                    } else {
                        $camposReservaVehiculoArr = Arr::add($camposReservaVehiculoArr, 'fechaConfirmacion', null);
                    }

                    //Se crea la nueva reserva o se modifica 
                    $reservaVehiculo = Reservavehiculo::updateOrCreate(
                        ['idReserva' => $this->idReservaSel],
                        $camposReservaVehiculoArr
                    );

                    $estado = Estado::where('codEstado', '=', $this->codEstadoSel)->first();
 
                    //Envío de correo  
                    $mailData = [
                        'asunto' => $this->idReservaSel > 0 ? "Notificación: Modificación de Reserva de Vehículo":"Notificación: Ingreso de Reserva de Vehículo",
                        'resumen' => $this->idReservaSel > 0 ? ("<b>" . $this->usernameLog . "</b> ha <span style='background-color:".$estado->codColor.";color:white;'>".$estado->descripAccionEstado."</span> su reserva solicitada para el día"):("<b>" . $this->usernameLog . "</b> ha <span style='background-color:#EF3B2D;color:white;'>Ingresado</span> una reserva en estado <span style='background-color:".$estado->codColor.";color:white;'>".$estado->descripcionEstado."</span> a su nombre para el día"),
                        'funcionario' => $this->nameSel,
                        'funcionario' => $this->sexoSel, 
                        'fechaCreacion' =>  Carbon::parse($reservaVehiculo->created_at)->format('d/m/Y H:i'),
                        'fechaReserva' => Carbon::createFromFormat('Y-m-d', $reservaVehiculo->fechaSolicitud)->format('d/m/Y'),
                        'horaInicio' => $this->horaInicioSel,
                        'horaFin' => $this->horaFinSel,
                        'descripcionEstado' => $estado->descripcionEstado,
                        'codEstado' => $this->codEstadoSel,
                        // 'usaVehiculoPersonal' => $objInput->flgUsoVehiculoPersonal == 0?'No':'Si',
                        'motivo' => $this->motivoSel,
                    ]; 

                    try { 
                        //Mail al postulante 
                        Mail::to($this->emailSel)->send(new CorreoNotificacion($mailData));
                    } catch (exception $e) {
                        $msjException = 'Se ha producido un error al intentar enviar el correo de notificación a : <span class="fs-6 text-success" style="font-weight:500;">' . $this->emailSel . '</span>';
                        throw $e;
                    } 

                    $userAdmin = User::where('flgAdmin', '=', 1)->get();
    
                    $emailAdmin = "";
                    try {
                        foreach ($userAdmin as $item) {
                            $emailAdmin = $item->email;
                            $mailData['nomAdmin'] = $item->name; 

                            $mailData['resumen'] = ($this->idReservaSel > 0 ? ("<b>" . $this->usernameLog . "</b> ha <span style='background-color:".$estado->codColor.";color:white;'>".$estado->descripAccionEstado."</span> la reserva de "):("<b>" . $this->usernameLog . "</b> ha <span style='background-color:#EF3B2D;color:white;'>Ingresado</span> una reserva en estado <span style='background-color:".$estado->codColor.";color:white;'>".$estado->descripcionEstado."</span> a nombre de <b>") . $this->nameSel. "</b> para el día");
    
                            if ($item->id == $this->idUserAdmin) { 
                                $mailData['resumen'] = ($this->idReservaSel > 0 ? ("se ha <span style='background-color:".$estado->codColor.";color:white;'>".$estado->descripAccionEstado."</span> la reserva de "):("se ha <span style='background-color:#EF3B2D;color:white;'>Ingresado</span> una reserva en estado <span style='background-color:".$estado->codColor.";color:white;'>".$estado->descripcionEstado."</span> a nombre de <b>") .$this->nameSel. "</b> para el día");
                            }
    
                            Mail::to($item->email)->send(new CorreoNotificacion($mailData));
                        }
                    } catch (exception $e) {
                        $msjException = 'Se ha producido un error al intentar enviar el correo de notificación a :  <span class="fs-6 text-success" style="font-weight:500;">' . $emailAdmin . '</span>';
                        throw $e;
                    }

                    DB::commit();

                    $mensaje = $this->idReservaSel > 0 ? 'La solicitud de reserva de ' . $this->nameSel . ' ha sido modificada.' : 'La solicitud de reserva de ' . $this->nameSel . ' ha sido ingresada y notificada.';

                    $this->dispatchBrowserEvent('swal:information', [
                        'icon' => '', //'info',
                        'mensaje' => '<i class="bi bi-send-check-fill text-success fs-4"></i><span class="ps-2 fs-6 text-primary" style="font-weight:430;">' . $mensaje . '</div>',
                    ]);

                    $this->dispatchBrowserEvent('closeModal');
                } catch (exception $e) { 
                    DB::rollBack();
                    if (strlen($msjException) > 0) {
                        $this->dispatchBrowserEvent('swal:information', [
                            'icon' => 'error', //'info',
                            'title' => '<span class="fs-6 text-primary" style="font-weight:430;">No fue posible procesar su solicitud. ' . $msjException . '</span>',
                        ]);
                    }
                    session()->flash('exceptionMessage', $e->getMessage());
                }
            }
        }
    }

    public function buscarReservaFuncionario()
    {
        return count(Reservavehiculo::where('idUser', '=', $this->idUserSel)
            ->where('fechaSolicitud', '=', $this->fechaSolicitudSel)->get()) > 0;
    }  

    public function getArrRules() 
    {
        $rulesReserva = [
            'idUserSel' => 'required|gt:0',
            'fechaSolicitudSel' => 'required|date_format:Y-m-d|after:yesterday',
            'codEstadoSel' => 'required|gt:1',/*Mayor a 1 para omitir el estado No Confirmado*/
            'horaInicioSel' => ['required', 'date_format:H:i', new HoraValidator()],
            'horaFinSel' => ['required', 'date_format:H:i', new HoraValidator()],
            'codDivisionSel' => 'required|gt:0',
            'codComunaSel' => 'required|gt:0',
            'cantPasajerosSel' => 'required|gt:0',
            'motivoSel' => 'required|max:500', 
            'codVehiculoSel' => 'required|gt:0',
        ]; 
     

        // if ($this->flgNuevaReserva == true) {
        //     $rulesReserva = Arr::add($rulesReserva, 'idUserSel', 'required');
        //     $rulesReserva = Arr::add($rulesReserva, 'fechaSolicitudSel', 'required|date_format:Y-m-d|after:yesterday');
        //     $rulesReserva = Arr::add($rulesReserva, 'codEstadoNvo', 'required');
        // }

        return $rulesReserva;
    }
}
