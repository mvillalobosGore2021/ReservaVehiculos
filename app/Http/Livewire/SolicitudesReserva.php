<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Reservavehiculo;
use App\Models\Estado;
use App\Models\Vehiculo;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use App\Rules\HoraValidator;
use Illuminate\Support\Arr;
use Exception;

class SolicitudesReserva extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $fechaSolSearch, $codEstadoSearch, $nameSearch, $fechaHoySearch, $codVehiculoSel, $codEstadoSel;

    public $idReservaSel, $fechaSolicitudSel, $horaInicioSel, $horaFinSel, $descripEstadoSel, $flgUsoVehiculoPersSel,
        $motivoSel, $nameSel, $flgNuevaReserva, $userList, $idUserSel;

    public Collection $inputsTable;

    public $reservasFechaSelPaso;

    public function mount()
    {
       
        $this->userList = User::orderBy('name')->get();
        // $this->estadosCmb = Estado::where('codEstado', '>', 1)->get();
        // Ver por que se pierden los datos del combo estado y el de funcionarios no al crear una nueva reserva
    }

    public function render()
    {
        $fechaInicio = Carbon::now()->format('Y-m-01');
        $fechaNextMonth = Carbon::now()->addMonth();
        $fechaNextMonth = $fechaNextMonth->format('Y-m-' . $fechaNextMonth->daysInMonth);

        //Se obtienen las reservas para un rango de dos meses
        $reservasTotales = Reservavehiculo::join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado')
            ->join('users', 'users.id', '=', 'reservavehiculos.idUser')
            ->whereBetween('fechaSolicitud', [$fechaInicio, $fechaNextMonth])
            ->where('fechaSolicitud', 'like', '%' . $this->fechaHoySearch . '%')
            ->where('users.name', 'like', '%' . $this->nameSearch . '%')
            ->where('reservavehiculos.codEstado', 'like', '%' . $this->codEstadoSearch . '%')
            ->orderBy('fechaSolicitud', 'desc')
            ->paginate(4);

        // dd($reservasTotales->where('fechaSolicitud', 'like', '%2022-08-09%'));
        $estadosCmb = null;
        if ($this->flgNuevaReserva == true) {
            //Se obtienen todos los estados distintos a No Confirmado
            $estadosCmb = Estado::orderBy('codEstado')->where('codEstado', '>', 1)->get();
        } else {
            //Se obtienen todos los estados distintos a No Confirmado
            $estadosCmb = Estado::where('codEstado', '>', 1)
                ->where('codestado', '!=', $this->codEstadoSel)->get();
        }
        //dd($estadosCmb);

        $cmbVehiculos = Vehiculo::all();

        $estadosCmbSearch = Estado::all();

        $this->inputsTable = new Collection();
        foreach ($reservasTotales as $item) {
            $this->inputsTable->push([
                'idReserva' => $item->idReserva, 'codEstado' => ''
            ]);
        }

        //Lista de reservas realizadas el mismo dia de la reserva seleccionada
        $reservasFechaSel = collect(Reservavehiculo::join('users', 'users.id', '=', 'reservavehiculos.idUser')
            ->join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado')
            ->where('fechaSolicitud', '=', $this->fechaSolicitudSel)
            ->get(['reservavehiculos.*', 'users.id', 'users.name', 'estados.descripcionEstado']));

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
        $this->flgUsoVehiculoPersSel = $reservaSel->flgUsoVehiculoPersona;
        $this->motivoSel = $reservaSel->motivo;
        $this->nameSel = $reservaSel->name;
        $this->idUserSel = $reservaSel->idUser;
        $this->descripEstadoSel = $reservaSel->descripcionEstado;
        $this->codEstadoSel = $reservaSel->codEstado;
        $this->codVehiculoSel = $reservaSel->codVehiculo;

        // //Lista de reservas realizadas el mismo dia de la reserva seleccionada
        // $this->reservasFechaSelPaso = collect(Reservavehiculo::join('users', 'users.id', '=', 'reservavehiculos.idUser')
        //     ->join('estados', 'estados.codEstado', '=', 'reservavehiculos.codEstado')
        //     ->where('fechaSolicitud', '=', $reservaSel->fechaSolicitud)
        //     ->get(['reservavehiculos.*', 'users.id', 'users.name', 'estados.descripcionEstado']));

        if ($openModal == 1) {
            $this->dispatchBrowserEvent('showModal');
        }
    }

    public function setFechaHoySearch()
    {
        $this->fechaHoySearch = Carbon::now()->format('Y-m-d');
        $this->dispatchBrowserEvent('iniTooltips');
        // $this->dispatchBrowserEvent('moveScroll', ['id' => '#listadoSolReservas']);
        $this->resetPage();
    }

    public function mostrarTodo()
    {
        $this->reset(['codEstadoSearch', 'nameSearch', 'fechaHoySearch']);
        // $this->dispatchBrowserEvent('iniTooltips');
        $this->resetPage();
    }

    public function resetSearch($field)
    {
        $this->reset($field);
        // $this->dispatchBrowserEvent('iniTooltips');
        $this->resetPage();
    }

    public function updated($field)
    {
        if ($field == 'flgUsoVehiculoPersSel') { //Campo opcional no se valida
            return true;
        }

        $this->resetPage();

        $this->validateOnly($field, $this->getArrRules());
    }


    public function cambiarEstado($idEstado)
    {
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
        $this->reset(['idReservaSel', 'fechaSolicitudSel', 'horaInicioSel', 'horaFinSel', 'motivoSel', 'flgUsoVehiculoPersSel', 'descripEstadoSel', 'idUserSel', 'nameSel', 'codEstadoSel']);
        $this->resetValidation(['idReservaSel', 'fechaSolicitudSel', 'horaInicioSel', 'horaFinSel', 'motivoSel', 'flgUsoVehiculoPersSel', 'descripEstadoSel', 'idUserSel', 'nameSel', 'codEstadoSel']);
        $this->resetErrorBag(['idReservaSel', 'fechaSolicitudSel', 'horaInicioSel', 'horaFinSel', 'motivoSel', 'flgUsoVehiculoPersSel', 'descripEstadoSel', 'idUserSel', 'nameSel', 'codEstadoSel']);
    }

    public function guardarReservaSel()
    {
        $this->validate($this->getArrRules());


        try {
            // 'idUser', 'motivo', 'prioridad', 'flgUsoVehiculoPersonal', 'fechaSolicitud', 'fechaConfirmacion', 'codEstado'
            $prioridad = 0; //Calcular del listado de reserva por orden de llegada, dar la posibilidad de cambiar la prioridad al Adm

            //Validar que el usuario no tenga una reserva ingresada para el mismo dia cuando se ingresa una nueva

            $reservaVehiculo =  Reservavehiculo::updateOrCreate(
                ['idReserva' => $this->idReservaSel],
                [
                    'idUser' => $this->idUserSel,
                    'prioridad' => $prioridad,
                    'flgUsoVehiculoPersonal' => $this->flgUsoVehiculoPersSel,
                    'fechaSolicitud' => $this->fechaSolicitudSel, //Carbon::createFromFormat('d/m/Y', $this->fechaSolicitudSel)->format('Y-m-d'), 
                    'horaInicio' => $this->horaInicioSel,
                    'horaFin' => $this->horaFinSel,
                    'motivo' => $this->motivoSel,
                    'codEstado' => $this->codEstadoSel,
                    'codVehiculo' => $this->codVehiculoSel,
                    //'fechaConfirmacion' => $this->correoRepLegal, fecha de confirmación se guarda cuando el administrador confirma la reserva
                ]
            );

            //$this->getReservas();

            $mensaje = $this->idReservaSel > 0 ? 'Su solicitud de reserva ha sido modificada y enviada.' : 'Su solicitud de reserva ha sido ingresada y enviada.';

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
        $rulesReserva = [
            'horaInicioSel' => ['required', 'date_format:H:i', new HoraValidator()],
            'horaFinSel' => ['required', 'date_format:H:i', new HoraValidator()],
            'motivoSel' => 'required:max:500',
            'codEstadoSel' => 'required',
            'codVehiculoSel' => 'required',
        ];

        if ($this->flgNuevaReserva == true) {
            $rulesReserva = Arr::add($rulesReserva, 'idUserSel', 'required');
            $rulesReserva = Arr::add($rulesReserva, 'fechaSolicitudSel', 'required|date_format:Y-m-d|after:yesterday');
        }

        return $rulesReserva;
    }
}
