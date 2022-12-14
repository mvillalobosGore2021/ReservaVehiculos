<?php

namespace App\Classes;

use App\Models\Reservavehiculo;
use App\Models\Estado;
use App\Models\Comuna;
use App\Models\Vehiculo;
use App\Models\Conductor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;
use App\Models\User;
use App\Rules\HoraValidator;
use Illuminate\Support\Arr;
use App\Mail\CorreoNotificacion;
use App\Mail\CorreoAnulacion;
use Illuminate\Support\Collection;

class ReservaServices
{

    public function getReservas($objInput)
    {
        //Se obtienen las reservas para un rango de 60 dias a contar de la fecha actual 
        $objInput->dayNow = Carbon::now()->format('d') * 1;
        $reservas = Reservavehiculo::groupBy('fechaSolicitud')
            ->selectRaw('count(*) as cantReservas, fechaSolicitud')
            ->whereBetween('fechaSolicitud', [Carbon::now()->format('Y-m-d'), Carbon::now()->addDays(60)->format('Y-m-d')])
            ->get();

        //Tabla Hash (key=fechaSolicitud) con las reservas realizadas
        $objInput->arrCantReservasCount = new Collection();
        foreach ($reservas as $item) {
            $reservasFechaItem = collect(Reservavehiculo::join('users', 'users.id', '=', 'reservavehiculos.idUser')
                ->join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado')
                ->join('comunas', 'reservavehiculos.codComuna', '=', 'comunas.codComuna', 'left outer')
                ->where('fechaSolicitud', '=', Carbon::createFromFormat('d/m/Y', Carbon::parse($item['fechaSolicitud'])->format('d/m/Y'))->format('Y-m-d'))
                ->get(['reservavehiculos.*', 'users.id', 'users.name', 'estados.descripcionEstado', 'comunas.nombreComuna']));

            // $objInput->arrCantReservasCount =  Arr::add($objInput->arrCantReservasCount, $item['fechaSolicitud'], $item['cantReservas']);

            $objInput->arrCantReservasCount->add(['fechaSolicitud' =>  $item['fechaSolicitud'], 'cantReservas' => $item['cantReservas'], 'reservasFechaItem' => $reservasFechaItem]);
        }

        $objInput->arrCantReservasCount = $objInput->arrCantReservasCount->keyBy('fechaSolicitud');
    }

    public function setFechaModal($fechaSel, $objInput)
    {
        $objInput->fechaModal = Carbon::parse($fechaSel)->format('d/m/Y');
        $objInput->flgNuevaReserva = false; //Modo modificacion  

        $objInput->reservasFechaSel = collect(Reservavehiculo::join('users', 'users.id', '=', 'reservavehiculos.idUser')
            ->join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado')
            ->join('comunas', 'reservavehiculos.codComuna', '=', 'comunas.codComuna', 'left outer')
            ->leftJoin('vehiculos', 'vehiculos.codVehiculo', '=', 'reservavehiculos.codVehiculo')
            // ->whereRaw("DATE_FORMAT(fechaSolicitud, '%d/%m/%Y') = " . $objInput->fechaModal) 
            ->where('fechaSolicitud', '=', Carbon::createFromFormat('d/m/Y', Carbon::parse($fechaSel)->format('d/m/Y'))->format('Y-m-d'))
            ->get(['reservavehiculos.*', 'users.id', 'users.name', 'estados.descripcionEstado', 'comunas.nombreComuna', 'estados.codColor', 'vehiculos.descripcionVehiculo']));

        //Si existe una reserva del usuario conectado para el dia seleccionado, si asignan los datos para su edicion
        $reservasFechaUser = $objInput->reservasFechaSel->where('idUser', '=', $objInput->idUser)->first();

        $this->resetCamposModal($objInput); 

        $objInput->fechaSolicitud = Carbon::createFromFormat('d/m/Y', Carbon::parse($fechaSel)->format('d/m/Y'))->format('Y-m-d'); 

        if (!empty($reservasFechaUser)) { 
            $objInput->idReserva = $reservasFechaUser['idReserva']; 
            // $objInput->fechaSolicitud = $reservasFechaUser['fechaSolicitud']; 
            $objInput->horaInicio = Carbon::parse($reservasFechaUser['horaInicio'])->format('H:i');
            $objInput->horaFin = Carbon::parse($reservasFechaUser['horaFin'])->format('H:i');
            $objInput->codEstado = $reservasFechaUser['codEstado'];
            $objInput->codEstadoOrig = $reservasFechaUser['codEstado'];
            $objInput->codColor = $reservasFechaUser['codColor'];
            $objInput->descripcionEstado = $reservasFechaUser['descripcionEstado'];
            $objInput->codComuna = $reservasFechaUser['codComuna'];
            $objInput->codDivision = $reservasFechaUser['codDivision'];
            $objInput->cantPasajeros = $reservasFechaUser['cantPasajeros'];
            $objInput->motivo = $reservasFechaUser['motivo'];
            $objInput->flgUsoVehiculoPersonal = $reservasFechaUser['flgUsoVehiculoPersonal'];
        }

        //Listado de reservas realizadas por otros funcionarios para el el dia seleccionado
        $objInput->reservasFechaSel = $objInput->reservasFechaSel->where('idUser', '!=', $objInput->idUser);

        $objInput->dispatchBrowserEvent('showModal');
        $objInput->dispatchBrowserEvent('iniTooltips');
    }

    public function resetCamposModal($objInput)  {
        $objInput->reset(['idReserva', 'codEstado', 'descripcionEstado', 'fechaSolicitud', 'horaInicio', 'horaFin', 'motivo', 'codDivision', 'codComuna', 'cantPasajeros', 'flgUsoVehiculoPersonal']);
        $objInput->resetValidation(['idReserva', 'codEstado', 'codEstadoOrig', 'descripcionEstado', 'fechaSolicitud', 'horaInicio', 'horaFin', 'motivo', 'codDivision', 'codComuna', 'cantPasajeros']);
        $objInput->resetErrorBag(['idReserva', 'codEstado', 'codEstadoOrig', 'descripcionEstado', 'fechaSolicitud', 'horaInicio', 'horaFin', 'motivo', 'codDivision', 'codComuna', 'cantPasajeros']);
    }

    public function confirmAnularReserva($objInput)
    {
        $objInput->dispatchBrowserEvent('swal:confirmAnular', [
            'type' => 'warning',
            'title' => 'Anulaci??n de Reserva',
            'text' => '??Est?? seguro(a) que desea Anular su reserva?',
        ]);
        $objInput->dispatchBrowserEvent('iniTooltips');
    }

    public function anularReserva($objInput) {
        $msjException = "";
        try {
            DB::beginTransaction();

            Reservavehiculo::where("idReserva",  $objInput->idReserva)->update(["codEstado" => 3]); //Estado 3 = Anular

            $reservaVehiculo = Reservavehiculo::join('comunas', 'comunas.codComuna', '=', 'reservavehiculos.codComuna')
              ->leftjoin('vehiculos', 'vehiculos.codVehiculo', '=', 'reservavehiculos.codVehiculo')
              ->leftjoin('conductors', 'conductors.rutConductor', '=', 'reservavehiculos.rutConductor')
              ->where("idReserva",  $objInput->idReserva)->first();

            //Env??o de correo
            $mailData = [
                'asunto' => "Notificaci??n: Anulaci??n de Reserva de Veh??culo",
                'resumen' => "se ha <span style='background-color:#EF3B2D;color:white;'>Anulado</span> su reserva solicitada para el d??a",
                'funcionario' => $objInput->userName,
                'sexo' => $objInput->sexo,
                'fechaCreacion' =>  Carbon::parse($reservaVehiculo->created_at)->format('d/m/Y H:i'),
                'fechaReserva' => Carbon::parse($reservaVehiculo->fechaSolicitud)->format('d/m/Y'),
                'horaInicio' => $reservaVehiculo->horaInicio,
                'horaFin' => $reservaVehiculo->horaFin,
                'descripcionEstado' => "Anulada",
                'codEstado' => $reservaVehiculo->codEstado,
                'flgConductor' => false,
                'motivoAnulacion' => null,  
                'descripcionVehiculo' => $reservaVehiculo->descripcionVehiculo,
                'nombreComuna' => $reservaVehiculo->nombreComuna,
                // 'usaVehiculoPersonal' => $objInput->flgUsoVehiculoPersonal == 0?'No':'Si',
                'motivo' => $reservaVehiculo->motivo,
            ];

            try {
                //Mail al postulante 
                Mail::to($objInput->correoUser)->send(new CorreoNotificacion($mailData));
            } catch (exception $e) {
                $msjException = 'Se ha producido un error al intentar enviar el correo de notificaci??n a : <span class="fs-6 text-success" style="font-weight:500;">' . $objInput->correoUser . '</span>';
                throw $e;
            } 

            $userAdmin = User::where('flgAdmin', '=', 1)->get();

            $mailData['resumen'] = ($objInput->sexo == "F" ? "la funcionaria" : "el funcionario") . " <b>" . $mailData['funcionario'] . "</b> ha <span style='background-color:#EF3B2D;color:white;'>Anulado</span> su reserva solicitada para el d??a";


            $emailAdmin = "";
            try {
                foreach ($userAdmin as $item) {
                    $emailAdmin = $item->email;
                    $mailData['nomAdmin'] = $item->name;
                    $mailData['sexoAdmin'] = $item->sexo;

                    Mail::to($item->email)->send(new CorreoNotificacion($mailData));
                }
            } catch (exception $e) {
                $msjException = 'Se ha producido un error al intentar enviar el correo de notificaci??n a :  <span class="fs-6 text-success" style="font-weight:500;">' . $emailAdmin . '</span>';
                throw $e;
            }

            //Si la reserva tenia asignado un conductor y el estado anterior era Confirmado, se le notifica que la reserva ha sido Anulada 
             if (!empty($reservaVehiculo->rutConductor) && $objInput->codEstadoOrig == 2) {
                $mailData['descripcionVehiculo'] =  $reservaVehiculo->descripcionVehiculo;
                $mailData['nombreComuna'] = $reservaVehiculo->nombreComuna;
                $mailData['nombreConductor'] = $reservaVehiculo->nombreConductor;
                $mailData['asunto'] = "Notificaci??n: Se ha anulado una reserva de veh??culo en la cual usted estaba asignado como conductor."; 
                $mailData['resumen'] = "Le informamos que <b>" . $objInput->userName . "</b> ha <span style='background-color:#EF3B2D;color:white;'>Anulado</span> una reserva de veh??culo para el d??a ".$mailData['fechaReserva'].". En la cual usted estaba asignado como conductor.";
                $mailData['flgConductor'] = true;

                Mail::to($reservaVehiculo->mail)->send(new CorreoNotificacion($mailData));
            }


            $objInput->dispatchBrowserEvent('swal:information', [
                'icon' => '', //'info',
                'mensaje' => '<i class="bi bi-send-check-fill text-success fs-4"></i><span class="ps-2 fs-6 text-primary" style="font-weight:430;">Su reserva ha sido anulada y notificada.</span>',
            ]);

            $objInput->dispatchBrowserEvent('closeModal');

            DB::commit();
        } catch (exception $e) {
            DB::rollBack();
            $objInput->dispatchBrowserEvent('swal:information', [
                'icon' => 'error', //'info',
                'title' => '<span class="fs-6 text-primary" style="font-weight:430;">No fue posible anular su reserva. ' . $msjException . '</span>',
            ]);

            session()->flash('exceptionMessage', $e->getMessage());
        }
        $objInput->dispatchBrowserEvent('iniTooltips');
    }

    public function solicitarReserva($objInput)
    {    
        $msjException = "";
        try {
            DB::beginTransaction();
            // 'idUser', 'motivo', 'prioridad', 'flgUsoVehiculoPersonal', 'fechaSolicitud', 'fechaConfirmacion', 'codEstado'
            $prioridad = 0; //Calcular del listado de reserva por orden de llegada, dar la posibilidad de cambiar la prioridad al Adm

            $reservaVehiculo =  Reservavehiculo::updateOrCreate(
                ['idReserva' => $objInput->idReserva], 
                [
                    'idUser' => $objInput->idUser,
                    'prioridad' => $prioridad,
                    //'flgUsoVehiculoPersonal' => $objInput->flgUsoVehiculoPersonal, //Por ahora no se va a utilizar este campo
                    'fechaSolicitud' => $objInput->fechaSolicitud, //Carbon::createFromFormat('d/m/Y', $objInput->fechaSolicitud)->format('Y-m-d'), // Carbon::parse($objInput->fechaModal)->format('Y/m/d'),
                    'horaInicio' => $objInput->horaInicio,
                    'horaFin' => $objInput->horaFin,
                    'codComuna' => $objInput->codComuna,
                    'codDivision' => $objInput->codDivision,
                    'cantPasajeros' => $objInput->cantPasajeros,
                    'motivo' => $objInput->motivo,
                    // 'codEstado' => 1, //El valor por defecto es 1=No Confirmada cuando se graba por primera vez (ver migration)  
                    //'fechaConfirmacion' => $objInput->correoRepLegal, fecha de confirmaci??n se guarda cuando el administrador confirma la reserva
                ]
            );

            $descripcionEstado = "";
            $descripcionVehiculo = "";
            $nombreConductor = "";            
        if ($reservaVehiculo->codEstado == 2) {/*Confirmada*/
            $reservaVehiculo = Reservavehiculo::leftjoin('vehiculos', 'vehiculos.codVehiculo', '=', 'reservavehiculos.codVehiculo')
            ->leftjoin('comunas', 'comunas.codComuna', '=', 'reservavehiculos.codComuna')
            ->leftjoin('conductors', 'conductors.rutConductor', '=', 'reservavehiculos.rutConductor')
            ->where('idReserva', '=', $objInput->idReserva)->first();
         
            $descripcionVehiculo = $reservaVehiculo->descripcionVehiculo;
            $nombreConductor = $reservaVehiculo->nombreConductor;   
            $nombreComuna = $reservaVehiculo->nombreComuna;
        }

        $descripcionEstado = $reservaVehiculo->codEstado > 0 ? (Estado::where('codEstado', '=',  $reservaVehiculo->codEstado)->first())->descripcionEstado : "No Confirmada";
        $nombreComuna = (Comuna::where('codComuna', '=',  $reservaVehiculo->codComuna)->first())->nombreComuna;

         //Env??o de correo
            $mailData = [
                'asunto' => $objInput->idReserva > 0 ? "Notificaci??n: Modificaci??n de Reserva de Veh??culo" : "Notificaci??n: Solicitud de Reserva de Veh??culo",
                'resumen' => $objInput->idReserva > 0 ? "se ha <span style='background-color:#EF3B2D;color:white;'>Modificado</span> su reserva solicitada para el d??a" : "se ha <span style='background-color:#EF3B2D;color:white;'>Ingresado</span> una nueva solicitud de reserva a su nombre para el d??a",
                'funcionario' => $objInput->userName,
                'sexo' => $objInput->sexo,
                'fechaCreacion' =>  Carbon::parse($reservaVehiculo->created_at)->format('d/m/Y H:i'),
                'fechaReserva' => Carbon::parse($objInput->fechaSolicitud)->format('d/m/Y'), 
                'horaInicio' => $objInput->horaInicio,
                'horaFin' => $objInput->horaFin,
                'flgConductor' => false,
                'motivoAnulacion' => '',  
                'descripcionEstado' => $descripcionEstado,
                'descripcionVehiculo' => $descripcionVehiculo, 
                'nombreConductor' => $nombreConductor,
                'nombreComuna' => $nombreComuna,
                'codEstado' => $reservaVehiculo->codEstado > 0 ? $reservaVehiculo->codEstado : 1/*No Confirmada*/,
                // 'usaVehiculoPersonal' => $objInput->flgUsoVehiculoPersonal == 0?'No':'Si',
                'motivo' => $objInput->motivo,
            ];

            try {
                //Mail al postulante 
                Mail::to($objInput->correoUser)->send(new CorreoNotificacion($mailData)); 
            } catch (exception $e) {
                $msjException = 'Se ha producido un error al intentar enviar el correo de notificaci??n a : <span class="fs-6 text-success" style="font-weight:500;">' . $objInput->correoUser . '</span>';
                throw $e;
            }

            $userAdmin = User::where('flgAdmin', '=', 1)->get();

            // $mailData['titulo'] =  $objInput->idReserva > 0 ? "Modificaci??n de Reserva de Veh??culo" :"Solicitud de Reserva de Vehiculo";
            // $mailData['asunto'] = ($objInput->idReserva > 0 ? "Modificaci??n de Reserva de Veh??culo de " :"Solicitud de Reserva de Vehiculo de ").$objInput->userName;
            $mailData['resumen'] = ($objInput->sexo == "F" ? "la funcionaria" : "el funcionario") . " <b>" . $mailData['funcionario'] . "</b>" . ($objInput->idReserva > 0 ? " ha <span style='background-color:#EF3B2D;color:white;'>Modificado</span> su reserva solicitada para el d??a" : " ha <span style='background-color:#EF3B2D;color:white;'>Ingresado</span> una nueva solicitud de reserva para el d??a");

            $emailAdmin = "";
            try {
                foreach ($userAdmin as $item) {
                    $emailAdmin = $item->email;
                    $mailData['nomAdmin'] = $item->name;
                    $mailData['sexoAdmin'] = $item->sexo;
                    Mail::to($item->email)->send(new CorreoNotificacion($mailData));
                }
            } catch (exception $e) {
                $msjException = 'Se ha producido un error al intentar enviar el correo de notificaci??n a :  <span class="fs-6 text-success" style="font-weight:500;">' . $emailAdmin . '</span>';
                throw $e;
            }

            DB::commit();

            $this->getReservas($objInput);

            $mensaje = $objInput->idReserva > 0 ? 'Su solicitud de reserva ha sido modificada y notificada a los administradores del sistema.' : 'Su solicitud de reserva ha sido ingresada y notificada a los administradores del sistema.';

            $objInput->dispatchBrowserEvent('swal:information', [
                'icon' => '', //'info',
                'mensaje' => '<i class="bi bi-send-check-fill text-success fs-4"></i><span class="ps-2 fs-6 text-primary" style="font-weight:430;">' . $mensaje . '</span>',
            ]);

            $objInput->dispatchBrowserEvent('closeModal');
        } catch (exception $e) {
            DB::rollBack();
            if (strlen($msjException) > 0) {
                $objInput->dispatchBrowserEvent('swal:information', [
                    'icon' => 'error', //'info',
                    'title' => '<span class="fs-6 text-primary" style="font-weight:430;">No fue posible procesar su solicitud. ' . $msjException . '</span>',
                ]);
            }
            session()->flash('exceptionMessage', $e->getMessage());
        }
        $objInput->dispatchBrowserEvent('iniTooltips');
       
    }

    public function getArrRules($objInput) 
    { 
        
        $rules = [
            // 'horaInicio' => ['required', 'date_format:H:i', new HoraValidator()],            
            'horaInicio' => 'required|date_format:H:i',
            // 'horaFin' => ['required', 'date_format:H:i', new HoraValidator()],            
            'horaFin' => 'required|date_format:H:i|after:horaInicio',
            'cantPasajeros' => 'required|gt:0|integer|digits_between:1,2',
            'codComuna' => 'required|gt:0', 
            'codDivision' => 'required|gt:0',
            'motivo' => 'required|max:500',
        ];
        
        if ($objInput->flgNuevaReserva == true) {
            // $rules = Arr::add($rules, 'fechaSolicitud',  'required|date_format:Y-m-d|after:yesterday');
            $rulesPaso = ['fechaSolicitud' => 'required|date_format:Y-m-d|after:yesterday'];
            $rules = array_merge($rulesPaso, $rules); 
        }
        
       return $rules;
    }
}
