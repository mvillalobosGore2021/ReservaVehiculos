<?php

namespace App\Classes;

use App\Models\Reservavehiculo;
use App\Models\Estado;
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

        if (!empty($reservasFechaUser)) {
            $objInput->idReserva = $reservasFechaUser['idReserva'];
            $objInput->horaInicio = Carbon::parse($reservasFechaUser['horaInicio'])->format('H:i');
            $objInput->horaFin = Carbon::parse($reservasFechaUser['horaFin'])->format('H:i');
            $objInput->codEstado = $reservasFechaUser['codEstado'];
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
        $objInput->reset(['idReserva', 'codEstado', 'descripcionEstado', 'horaInicio', 'horaFin', 'motivo', 'codDivision', 'codComuna', 'cantPasajeros', 'flgUsoVehiculoPersonal']);
        $objInput->resetValidation(['horaInicio', 'horaFin', 'motivo', 'codDivision', 'codComuna', 'cantPasajeros']);
        $objInput->resetErrorBag(['horaInicio', 'horaFin', 'motivo', 'codDivision', 'codComuna', 'cantPasajeros']);
    }

    public function confirmAnularReserva($objInput)
    {
        $objInput->dispatchBrowserEvent('swal:confirm', [
            'type' => 'warning',
            'title' => 'Anulación de Reserva',
            'text' => '¿Está seguro(a) que desea Anular su reserva?',
        ]);
        $objInput->dispatchBrowserEvent('iniTooltips');
    }

    public function anularReserva($objInput)
    {
        $msjException = "";
        try {
            DB::beginTransaction();

            Reservavehiculo::where("idReserva",  $objInput->idReserva)->update(["codEstado" => 3]); //Estado 3 = Anular

            $reservaVehiculo = Reservavehiculo::where("idReserva",  $objInput->idReserva)->first();
            //Envío de correo
            //  $mailData = [
            //     'asunto' => "Anulación de Reserva de Vehículo - Gobierno Regional del Bio Bio",
            //     'titulo' => "Su reserva ha sido anulada",
            //     'funcionario' => $objInput->userName,
            //     'fechaReserva' => $objInput->fechaModal, 
            //     'fechaAnulacion' => Carbon::now()->format('d/m/Y'), 
            //     'horaInicio' => $objInput->horaInicio,           
            //     'horaFin' => $objInput->horaFin,
            //     'descripcionEstado' => Estado::where('codEstado', '=',  $reservaVehiculo->codEstado)->first(),
            //     // 'usaVehiculoPersonal' => $objInput->flgUsoVehiculoPersonal == 0?'No':'Si',
            // ];

            //Envío de correo
            $mailData = [
                'asunto' => "Notificación: Anulación de Reserva de Vehículo",
                'resumen' => "se ha <span style='background-color:#EF3B2D;color:white;'>Anulado</span> su reserva solicitada para el día",
                'funcionario' => $objInput->userName,
                'sexo' => $objInput->sexo,
                'fechaCreacion' =>  Carbon::parse($reservaVehiculo->created_at)->format('d/m/Y H:i'),
                'fechaReserva' => Carbon::parse($reservaVehiculo->fechaSolicitud)->format('d/m/Y'),
                'horaInicio' => $reservaVehiculo->horaInicio,
                'horaFin' => $reservaVehiculo->horaFin,
                'descripcionEstado' => "Anulada",
                'codEstado' => $reservaVehiculo->codEstado,
                // 'usaVehiculoPersonal' => $objInput->flgUsoVehiculoPersonal == 0?'No':'Si',
                'motivo' => $reservaVehiculo->motivo,
            ];

            try {
                //Mail al postulante 
                Mail::to($objInput->correoUser)->send(new CorreoNotificacion($mailData));
            } catch (exception $e) {
                $msjException = 'Se ha producido un error al intentar enviar el correo de notificación a : <span class="fs-6 text-success" style="font-weight:500;">' . $objInput->correoUser . '</span>';
                throw $e;
            }

            $userAdmin = User::where('flgAdmin', '=', 1)->get();

            // $mailData['titulo'] =  $objInput->idReserva > 0 ? "Modificación de Reserva de Vehículo" :"Solicitud de Reserva de Vehiculo";
            // $mailData['asunto'] = ($objInput->idReserva > 0 ? "Modificación de Reserva de Vehículo de " :"Solicitud de Reserva de Vehiculo de ").$objInput->userName;
            $mailData['resumen'] = ($objInput->userName == "F" ? "la funcionaria" : "el funcionario") . " <b>" . $mailData['funcionario'] . "</b> ha <span style='background-color:#EF3B2D;color:white;'>Anulado</span> su reserva solicitada para el día";

            $emailAdmin = "";
            try {
                foreach ($userAdmin as $item) {
                    $emailAdmin = $item->email;
                    $mailData['nomAdmin'] = $item->name;

                    Mail::to($item->email)->send(new CorreoNotificacion($mailData));
                }
            } catch (exception $e) {
                $msjException = 'Se ha producido un error al intentar enviar el correo de notificación a :  <span class="fs-6 text-success" style="font-weight:500;">' . $emailAdmin . '</span>';
                throw $e;
            }

            $objInput->dispatchBrowserEvent('swal:information', [
                'icon' => '', //'info',
                'mensaje' => '<i class="bi bi-send-check-fill text-success fs-4"></i><span class="ps-2 fs-6 text-primary" style="font-weight:430;">Su reserva ha sido anulada y notificada con exito.</span>',
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
        // dd($objInput->horaInicio, $objInput->horaFin); 

        $flgError = false;
        try {
            $objInput->validate($objInput->getArrRules());
        } catch (exception $e) {
            $flgError = true;
        }

        if ($flgError == true) {
            $objInput->dispatchBrowserEvent('swal:information', [
                'icon' => 'error', //'info',
                'title' => '<span class="fs-6 text-primary" style="font-weight:430;">Algunos campos contienen Errores, por favor revíselos y corríjalos.</span>',
                //'mensaje' => '<span class="ps-2 fs-6 text-primary" style="font-weight:430;">Algunos campos contienen Errores, por favor reviselos y corrijalos.</span>',
                'timer' => '5000',
            ]);

            $objInput->validate($this->getArrRules()); //Para que se generen nuevamente los msjs           
        }

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
                    'fechaSolicitud' => Carbon::createFromFormat('d/m/Y', $objInput->fechaModal)->format('Y-m-d'), // Carbon::parse($objInput->fechaModal)->format('Y/m/d'),
                    'horaInicio' => $objInput->horaInicio,
                    'horaFin' => $objInput->horaFin,
                    'codComuna' => $objInput->codComuna,
                    'codDivision' => $objInput->codDivision,
                    'cantPasajeros' => $objInput->cantPasajeros,
                    'motivo' => $objInput->motivo,
                    // 'codEstado' => 1, //El valor por defecto es 1=No Confirmada cuando se graba por primera vez (ver migration)  
                    //'fechaConfirmacion' => $objInput->correoRepLegal, fecha de confirmación se guarda cuando el administrador confirma la reserva
                ]
            );

            $descripcionEstado = $reservaVehiculo->codEstado > 0 ? (Estado::where('codEstado', '=',  $reservaVehiculo->codEstado)->first())->descripcionEstado : "No Confirmada";

            //Envío de correo
            $mailData = [
                'asunto' => $objInput->idReserva > 0 ? "Notificación: Modificación de Reserva de Vehículo" : "Notificación: Solicitud de Reserva de Vehículo",
                'resumen' => $objInput->idReserva > 0 ? "se ha <span style='background-color:#EF3B2D;color:white;'>Modificado</span> su reserva solicitada para el día" : "se ha <span style='background-color:#EF3B2D;color:white;'>Ingresado</span> una nueva solicitud de reserva a su nombre para el día",
                'funcionario' => $objInput->userName,
                'sexo' => $objInput->sexo,
                'fechaCreacion' =>  Carbon::parse($reservaVehiculo->created_at)->format('d/m/Y H:i'),
                'fechaReserva' => $objInput->fechaModal,
                'horaInicio' => $objInput->horaInicio,
                'horaFin' => $objInput->horaFin,
                'descripcionEstado' => $descripcionEstado,
                'codEstado' => $reservaVehiculo->codEstado > 0 ? $reservaVehiculo->codEstado : 1/*No Confirmada*/,
                // 'usaVehiculoPersonal' => $objInput->flgUsoVehiculoPersonal == 0?'No':'Si',
                'motivo' => $objInput->motivo,
            ];

            try {
                //Mail al postulante 
                Mail::to($objInput->correoUser)->send(new CorreoNotificacion($mailData));
            } catch (exception $e) {
                $msjException = 'Se ha producido un error al intentar enviar el correo de notificación a : <span class="fs-6 text-success" style="font-weight:500;">' . $objInput->correoUser . '</span>';
                throw $e;
            }

            $userAdmin = User::where('flgAdmin', '=', 1)->get();

            // $mailData['titulo'] =  $objInput->idReserva > 0 ? "Modificación de Reserva de Vehículo" :"Solicitud de Reserva de Vehiculo";
            // $mailData['asunto'] = ($objInput->idReserva > 0 ? "Modificación de Reserva de Vehículo de " :"Solicitud de Reserva de Vehiculo de ").$objInput->userName;
            $mailData['resumen'] = ($objInput->userName == "F" ? "la funcionaria" : "el funcionario") . " <b>" . $mailData['funcionario'] . "</b>" . ($objInput->idReserva > 0 ? " ha <span style='background-color:#EF3B2D;color:white;'>Modificado</span> su reserva solicitada para el día" : " ha <span style='background-color:#EF3B2D;color:white;'>Ingresado</span> una nueva solicitud de reserva para el día");

            $emailAdmin = "";
            try {
                foreach ($userAdmin as $item) {
                    $emailAdmin = $item->email;
                    $mailData['nomAdmin'] = $item->name;

                    Mail::to($item->email)->send(new CorreoNotificacion($mailData));
                }
            } catch (exception $e) {
                $msjException = 'Se ha producido un error al intentar enviar el correo de notificación a :  <span class="fs-6 text-success" style="font-weight:500;">' . $emailAdmin . '</span>';
                throw $e;
            }

            DB::commit();

            $this->getReservas($objInput);

            $mensaje = $objInput->idReserva > 0 ? 'Su solicitud de reserva ha sido modificada y enviada.' : 'Su solicitud de reserva ha sido ingresada y enviada.';

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

    public function getArrRules()
    {
        return [
            'horaInicio' => ['required', 'date_format:H:i', new HoraValidator()],
            'horaFin' => ['required', 'date_format:H:i', new HoraValidator()],
            'codDivision' => 'required|gt:0',
            'codComuna' => 'required|gt:0',
            'cantPasajeros' => 'required|gt:0|integer|digits_between:1,2',
            'motivo' => 'required|max:500',
        ];
    }
}
