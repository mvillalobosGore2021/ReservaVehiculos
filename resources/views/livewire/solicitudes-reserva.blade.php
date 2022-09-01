<div>
  <div class="card m-2 mb-4 m-md-3 mb-md-5">
    <div class="card-header py-3 text-center h3">
      Solicitudes de Reserva
    </div>
    <div class="card-body">
      <div class="card mx-2 mt-3 mb-3 shadow">
        <div class="card-header">
          <div class="row py-md-1 justify-content-center">
            <div class="col-12 text-center h4 py-2">Parámetros de Búsqueda</div>
            <div class="col-12 pb-2 col-md-1 pb-md-0 text-nowrap me-md-4 text-center">
              <button type="button" data-tippy-content="Ver solicitudes realizadas hoy" class="btn btn-primary btn-sm" style="width:135px;" wire:click="setFechaHoySearch(1)" wire:loading.attr="disabled" wire:target="setFechaHoySearch, mostrarTodo">
                <span wire:loading.remove wire:target="setFechaHoySearch(1)"><i class="bi bi-calendar-check"></i> </span>
                <span wire:loading.class="spinner-border spinner-border-sm" wire:target="setFechaHoySearch(1)" role="status" aria-hidden="true"></span>
                Solicitudes Hoy
              </button>
            </div>
            <div class="col-12 pb-2 col-md-1 pb-md-0 text-nowrap ms-md-4 me-md-2 text-center">
              <button type="button" data-tippy-content="Ver reservas solicitadas para el día de hoy" class="btn btn-primary btn-sm ms-md-2" style="width:135px;" wire:click="setFechaHoySearch(2)" wire:loading.attr="disabled" wire:target="setFechaHoySearch, mostrarTodo">
                <span wire:loading.remove wire:target="setFechaHoySearch(2)"><i class="bi bi-calendar-check"></i></span>
                <span wire:loading.class="spinner-border spinner-border-sm" wire:target="setFechaHoySearch(2)" role="status" aria-hidden="true"></span>
                Reservas Hoy
              </button>
            </div>
            <div class="col-12 pb-2 col-md-1 pb-md-0 text-nowrap ms-md-5 text-center">
              <button type="button" class="btn btn-primary btn-sm ms-md-2" style="width:135px;" wire:click="mostrarTodo" wire:loading.attr="disabled" wire:target="setFechaHoySearch, mostrarTodo">
                <span wire:loading.remove wire:target="mostrarTodo"><i class="bi bi-eye"></i></span>
                <span wire:loading.class="spinner-border spinner-border-sm" wire:target="mostrarTodo" role="status" aria-hidden="true"></span>
                Mostrar Todo
              </button>
            </div>
            <div class="col-12 pb-2 col-md-1 pb-md-2 text-nowrap ms-md-5 text-center">
              <button type="button" class="btn btn-danger btn-sm ms-md-3" style="width:135px;" wire:click="nuevaReserva" wire:loading.attr="disabled" wire:target="setFechaHoySearch, mostrarTodo">
                <i class="bi bi-plus-circle"></i> Nueva Reserva
              </button>
            </div>
          </div>

        </div>
        <div class="card-body">
          <div class="row ms-1">
            <div class="col-12 col-md-7">
              <label>Nombre Funcionario</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-person"></i>
                </span>
                <input type="text" class="form-control" wire:model.debounce.250ms="nameSearch">
                <span class="input-group-text bg-white" id="borrarNameSearch" style="cursor:pointer;" data-tippy-content="Borrar" wire:click="$set('nameSearch', '')">
                      <i class="bi bi-x-circle"></i>
               </span>
              </div>
            </div>
            <div class="col-12 col-md-3">
              <label>Estado Reserva</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-list-ul"></i>
                </span>
                <select wire:model="codEstadoSearch" class="form-select">
                  <option value="">Todos</option>
                  @if (!empty( $estadosCmbSearch))
                  @foreach($estadosCmbSearch as $item)
                  <!-- No mostrar el estado actual   -->
                  <option value="{{$item->codEstado}}">{{$item->descripcionEstado}}</option>
                  @endforeach
                  @endif
                </select>
              </div>
            </div>

            <div class="col-12 col-md-3 pt-3">
              <div class="row">
                <div class="col-12">
                  <label>{{$flgFechaSearch == 1 ? 'Fecha Solicitud':'Fecha Reserva'}}</label>
                  <div class="input-group">
                    <span class="input-group-text"> 
                      <i class="bi bi-calendar4"></i>
                    </span>
                    <input type="date" wire:model.debounce.500ms="fechaSearch" class="form-control" autocomplete="off">
                    <span class="input-group-text bg-white" id="borrarFechaSearch" style="cursor:pointer;" data-tippy-content="Borrar" wire:click="$set('fechaSearch', '')">
                      <i class="bi bi-x-circle"></i>
                    </span>
                  </div>
                </div>
                <div class="col-12  pt-1">
                  <div class="form-check form-switch" data-tippy-content="Active la casilla si desea buscar por la fecha cuando se realizó la solicitud.">
                    <input class="form-check-input" type="checkbox" wire:model.debounce.500ms="flgFechaSearch">
                    <label class="form-check-label" for="flexSwitchCheckDefault">Fecha Solicitud</label> 
                  </div>
                </div>
                @error('fechaSearch')
                <div class="col-12  pt-1">
                  <span class="colorerror">{{ $message }}</span>
                </div>
                @enderror
              </div>
            </div>

            <div class="col-12 mt-3">
              <div class="row text-center text-md-start">
                <div class="col-12 col-md-5" id="resetSearch">
                  @if(!empty($fechaSearch) && ($flgSearchHoy == 1 || $flgSearchHoy == 2))
                  <button type="button" class="btn btn-dark btn-sm rounded-pill p-1" style="cursor:context-menu;">
                    {{$flgSearchHoy == 1 ? 'Solicitudes Realizadas Hoy':'Reservas Para Hoy'}}
                    <div class="d-inline" wire:click="resetSearch" data-tippy-content="Eliminar Filtro"><i class="bi bi-x-circle" style="cursor:pointer;"></i></div>
                  </button>
                  @endif
                </div>
                <div class="col-12 col-md-3 pt-3 pt-md-0">
                  <div style="height:33px;">
                    <div wire:loading wire:target="setFechaHoySearch,mostrarTodo">
                      <div class="spinner-grow text-primary" style="width: 1.2rem; height: 1.2rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                      <div class="spinner-grow text-secondary" style="width: 1.2rem; height: 1.2rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                      <div class="spinner-grow text-success" style="width: 1.2rem; height: 1.2rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                      <div class="fst-italic d-inline" style="font-size:15px;">
                        Cargando...
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="table-responsive card mx-2 mt-4 shadow" id="listadoSolReservas">
        <table class="table @if(!empty($reservasTotales) && count($reservasTotales) > 0) table-hover @endif ">
          <thead class="table-light">
            <tr>
              <th scope="col" class="ps-4">Funcionario</th>
              <th scope="col" class="text-center">Día Reserva</th>
              <th scope="col" class="text-center">Hora Inicio</th>
              <th scope="col" class="text-center">Hora Fin</th>
              <th scope="col" class="text-start">Fecha Creación</th>
              <th scope="col" class="text-center">Estado Reserva</th>
              <th scope="col" class="text-left">Motivo</th>
              <!-- <th scope="col" style="width:170px;">Acción</th> -->
            </tr>
          </thead>
          <tbody>
            @if(!empty($reservasTotales) && count($reservasTotales) > 0)
            @foreach($reservasTotales as $item)
            <tr style="height:55px;cursor:pointer;" id="td{{$loop->index}}" wire:click="reservaSel('{{$item->idReserva}}', '1')" data-tippy-content="Click para editar">
              <td nowrap class="ps-4">{{ $item->name}}</td>
              <td class="text-center">{{ \Carbon\Carbon::parse($item->fechaSolicitud)->format('d/m/Y')}}</td>
              <td class="text-center">{{ \Carbon\Carbon::parse($item->horaInicio)->format('H:i')}}</td>
              <td class="text-center">{{ \Carbon\Carbon::parse($item->horaFin)->format('H:i')}}</td>
              <!-- <td class="text-center">
                @if(!empty($item->fechaConfirmacion))
                   {{ \Carbon\Carbon::parse($item->fechaConfirmacion)->format('d/m/Y')}}
                @endif
              </td> -->
              <td class="text-start" nowrap>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i')}}</td>
              <td class="text-center" nowrap>{{$item->descripcionEstado}}</td>
              <td class="glosaTable pe-4">
                <!-- <i class="bi bi-eye-fill size-icon" id="id{{$loop->index.rand()}}" data-tippy-content="{{$item->motivo}}"></i> -->
                {{$item->motivo}}
              </td>

            </tr>
            @endforeach
            @else
            <tr>
              <td colspan="7">
                <div class="alert alert-success border border-success d-flex justify-content-center my-3 mx-3 mx-md-5 my-md-4" role="alert">
                  <span class="fs-4 pe-2 pe-md-3">
                    <i class="bi bi-info-circle-fill"></i></span>
                  <span class="fs-6 fst-italic pt-1">
                    No existen reservas para mostrar
                  </span>
                </div>
              </td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
      <div class="row mt-3 ">
        <div class="col-7 offset-0 col-md-5 offset-md-4 mb-4 pt-2">
          {{ $reservasTotales->links()}}
        </div>
      </div>

      <!-- Inicio Modal -->
      <div wire:ignore.self class="modal fade pt-0" id="modalReserva" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-scrollable pt-1">
          <div class="modal-content">
            <div class="modal-header bg-light">
              <h5 class="modal-title ps-3 text-primary" id="modalReservaLabel">
                @if ($flgNuevaReserva == true) Nueva Reserva @else Datos Reserva @endif
              </h5>
              <div style="margin:auto;" wire:loading wire:target="reservaSel">
                <div class="spinner-grow text-primary" style="width: 1.1rem; height: 1.1rem;" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <div class="spinner-grow text-secondary" style="width: 1.1rem; height: 1.1rem;" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <div class="spinner-grow text-success" style="width: 1.1rem; height: 1.1rem;" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <div class="fst-italic d-inline" style="font-size:15px;">
                  Cargando...
                </div>
              </div>

              <button type="button" class="btn-close" onclick="ocultarModal()"></button>
            </div>

            <div class="modal-body" id="modalBody">
              <!-- <input type="text" id="myInput" class="form-control"> -->
              <div class="row">
                <div class="col-12 col-md-6 ps-4">
                  <div class="row mb-2">
                    <div class="col-12">
                      <div class="row">
                        <div class="col-12" id="inputFunc">
                          <label>Funcionario(a)</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="bi bi-person"></i>
                            </span>
                            <select wire:model="idUserSel" wire:loading.attr="disabled" wire:target="guardarReservaSel" class="form-select">
                              <option value="">Sel.Funcionario</option>
                              @if (!empty( $userList))
                              @foreach($userList as $item)
                              <option value="{{$item->id}}">{{$item->name}}</option>
                              @endforeach
                              @endif
                            </select>
                          </div>
                        </div>
                        @error('idUserSel')
                        <div class="col-12" id="idUserSelError">
                          <span class="colorerror">{{ $message }}</span>
                        </div>
                        @enderror
                      </div>

                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-12 col-md-5">
                      <input type="hidden" wire:model="idReservaSel">
                      <div class="row">
                        <div class="col-12">
                          <label>Dia a Reservar</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="bi bi-calendar4"></i>
                            </span>
                            <input type="date" wire:model.debounce.500ms="fechaSolicitudSel" wire:loading.attr="disabled" wire:target="guardarReservaSel" class="date-ini form-control" autocomplete="off">
                          </div>
                        </div>
                        @error('fechaSolicitudSel')
                        <div class="col-12">
                          <span class="colorerror">{{ $message }}</span>
                        </div>
                        @enderror
                      </div>
                    </div>
                    <div class="col-12 col-md-6 mt-2 mt-md-0">
                      <div class="row">
                        <div class="col-12">
                          <label>Estado Reserva</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="bi bi-list-ul"></i>
                            </span>
                            <select wire:model="codEstadoSel" wire:loading.attr="disabled" wire:target="guardarReservaSel" class="form-select">
                              <option value="">Sel.Estado</option>
                              @if (!empty( $estadosCmb))
                              @foreach($estadosCmb as $item)
                              <!-- No mostrar el estado actual   -->
                              <option value="{{$item->codEstado}}">{{$item->descripcionEstado}}</option>
                              @endforeach
                              @endif
                            </select>
                          </div>
                        </div>
                        @error('codEstadoSel')
                        <div class="col-12">
                          <span class="colorerror">{{ $message }}</span>
                        </div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12 pb-2 col-md-5 mt-md-0">
                      <div class="row">
                        <div class="col-12">
                          <label>Hora Inicio Reserva</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="bi bi-alarm"></i>
                            </span>
                            <input type="time" wire:loading.attr="disabled" wire:target="guardarReservaSel" class="time-ini form-control" wire:model.debounce.250ms="horaInicioSel" placeholder="Inicio" autocomplete="off">
                          </div>
                        </div>
                        @error('horaInicioSel')
                        <div class="col-12 pb-1">
                          <span class="colorerror">{{ $message }}</span>
                        </div>
                        @enderror
                      </div>
                    </div>
                    <div class="col-12 col-md-6">
                      <div class="row">
                        <div class="col-12">
                          <label data-tippy-content="Hora estimada de regreso">Hora Fin Reserva</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="bi bi-alarm"></i>
                            </span>
                            <input type="time" wire:loading.attr="disabled" wire:target="guardarReservaSel" class="time-fin form-control" wire:model.debounce.250ms="horaFinSel" placeholder="Termino" autocomplete="off">
                          </div>
                        </div>
                        @error('horaFinSel')
                        <div class="col-12 pb-1">
                          <span class="colorerror">{{ $message }}</span>
                        </div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-12">
                      <label>Vehículo Asignado</label>
                      <div class="input-group">
                        <span class="input-group-text">
                          <i class="bi bi-list-ul"></i>
                        </span>
                        <select wire:model="codVehiculoSel" wire:loading.attr="disabled" wire:target="guardarReservaSel" class="form-select">
                          <option value="">Sel.Vehículo</option>
                          @if (!empty( $cmbVehiculos))
                          @foreach($cmbVehiculos as $item)
                          <option value="{{$item->codVehiculo}}">{{$item->descripcionVehiculo}}</option>
                          @endforeach
                          @endif
                        </select>
                      </div>
                    </div>
                    @error('codVehiculoSel')
                    <div class="col-12 pb-1">
                      <span class="colorerror">{{ $message }}</span>
                    </div>
                    @enderror
                  </div>
                  <div class="row pt-3 pt-md-0 pb-3">
                    <div class="col-12">
                      <textarea wire:model.debounce.250ms="motivoSel" onclick="movScrollModalById('#usoVehiculoHead')" wire:loading.attr="disabled" wire:target="guardarReservaSel" placeholder="Motivo de la reserva (Máximo 500 caracteres)" class="form-control" maxlength="500" rows="3"></textarea>
                    </div>
                    @error('motivoSel')
                    <div class="col-12">
                      <span class="colorerror">{{$message}}</span>
                    </div>
                    @enderror
                  </div>
                  <div class="row">
                    <div class="col-12" id="usoVehiculoHead">
                      <div class="form-check form-switch" data-tippy-content="Proponer uso de vehiculo personal con devolución del costo por gastos de combustible y peajes.">
                        <label class="form-check-label text-secondary" style="font-style:italic;" for="flgUsoVehiculoPersonal">
                          Usar Vehiculo Personal con Devolución de Combustible y Peajes.
                        </label>
                        <input wire:model.debounce.500ms="flgUsoVehiculoPersSel" class="form-check-input" wire:loading.attr="disabled" wire:target="guardarReservaSel" type="checkbox" id="flgUsoVehiculoPersonal">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-6 pt-3 pt-md-1">
                  <div class="table-responsive mx-2">
                    <table class="table @if(!empty($reservasFechaSel) && count($reservasFechaSel) > 0 && $flgNuevaReserva == false) table-hover @endif ">
                      <!-- table-bordered -->
                      <thead>
                        <tr>
                          <th scope="col" colspan="4" class="text-center text-success pb-3">
                            Reservas para el día {{ \Carbon\Carbon::parse($fechaSolicitudSel)->format('d/m/Y')}}
                          </th>
                        </tr>
                        <tr>
                          <th scope="col">Nombre</th>
                          <th scope="col" nowrap>Hora Inicio</th>
                          <th scope="col" nowrap>Hora Fin</th>
                          <th scope="col">Estado</th>
                          <th scope="col" nowrap>Vehículo Asignado</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($reservasFechaSel) && count($reservasFechaSel) > 0)
                        @foreach($reservasFechaSel as $index => $item)
                        <tr id="fila{{$index}}" @if ($flgNuevaReserva==false) style="cursor:pointer;" wire:click="reservaSel('{{$item->idReserva}}', '0')" @endif>
                          <td nowrap>{{$item['name']}}</td>
                          <td align="center">
                            {{ \Carbon\Carbon::parse($item['horaInicio'])->format('H:i')}}
                          </td>
                          <td align="center">
                            {{ \Carbon\Carbon::parse($item['horaFin'])->format('H:i')}}
                          </td>
                          <td nowrap>{{$item['descripcionEstado']}}</td>
                          <td nowrap>{{$item['descripcionVehiculo']}}</td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                          <td colspan="4">
                            <div class="alert alert-info border border-info d-flex justify-content-center my-3 mx-2 my-md-4" role="alert">
                              <span class="fs-4 pe-2 pe-md-3">
                                <i class="bi bi-info-circle-fill"></i></span>
                              <span class="fs-6 fst-italic pt-1">
                                No existen reservas para el día seleccionado
                              </span>
                            </div>
                          </td>
                        </tr>
                        @endif
                      </tbody>
                    </table>
                  </div>

                  @if (session()->has('exceptionMessage'))
                  <div class="row">
                    <div class="col-12">
                      <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:">
                          <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        <div>
                          {{ session('exceptionMessage') }}
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                      </div>
                    </div>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="modal-footer bg-light pe-5">
              <button type="button" class="btn btn-danger" onclick="ocultarModal();" wire:loading.attr="disabled" wire:target="guardarReservaSel">
                Cerrar <i class="bi bi-x-circle pt-1"></i>
              </button>
              <button type="button" class="btn btn-primary" wire:click="guardarReservaSel" wire:loading.attr="disabled" wire:target="guardarReservaSel">
                Guardar
                <span wire:loading.remove wire:target="guardarReservaSel"><i class="bi bi-send pt-1"></i></span>
                <span wire:loading.class="spinner-border spinner-border-sm" wire:target="guardarReservaSel" role="status" aria-hidden="true"></span>
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- Fin Modal -->


    </div>
  </div>
  <script>
    // const myModal = document.getElementById('modalReserva')
    // // const myInput = document.getElementById('myInput')
    // myModal.addEventListener('shown.bs.modal', () => {
    //   // myInput.focus() 
    // })

    window.addEventListener('swal:information', event => {
      const Toast = Swal.mixin({
        toast: true,
        position: 'center',
        showConfirmButton: false,
        timer: 5500,
        timerProgressBar: false,
        showCloseButton: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })

      Toast.fire({
        icon: event.detail.icon,
        title: event.detail.title,
        html: event.detail.mensaje,
      })
    });


    const container = document.getElementById("modalReserva");
    const modal = new bootstrap.Modal(container);

    window.addEventListener('showModal', event => {
      modal.show();
    });

    window.addEventListener('closeModal', event => {
      modal.hide();
    });

    function mostrarModal() {
      //myModal2.show();
      modal.show();
    }

    function ocultarModal() {
      //myModal2.show();
      modal.hide();
    }


    let el = document.querySelector('.el');
    let height = el.scrollHeight;
    el.style.setProperty('--max-height', height + 'px');
  </script>
</div>