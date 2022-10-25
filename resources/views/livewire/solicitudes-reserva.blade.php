<div>
  @if ($flgAdmin != true)
  <center>
    <div class="alert alert-warning border border-warning d-flex justify-content-center" role="alert">
      <span class="fs-4 pe-2 pe-md-3">
        <i class="bi bi-info-circle-fill"></i>
      </span>
      <span class="fs-6 fst-italic pt-1">
        No Autorizado
      </span>
    </div>
  </center>
  @else
  <div class="card m-2 mb-4 m-md-3 mb-md-5">
    <div class="card-header py-3 text-center h3">
      Gestionar Solicitudes de Reserva
    </div>
    <div class="card-body">
      <div class="card mx-2 mt-3 mb-3 shadow">
        <div class="card-header">
          <div class="row py-md-1 justify-content-center">
            <div class="col-12 text-center h4 py-2">Parámetros de Búsqueda</div>
            <div class="col-12 pb-2 col-md-1 pb-md-0 text-nowrap me-md-4 text-center">
              <button type="button" data-tippy-content="Ver solicitudes ingresadas hoy" class="btn btn-primary btn-sm" style="width:135px;" wire:click="setSolicitudesHoySearch" wire:loading.attr="disabled" wire:target="setSolicitudesHoySearch, mostrarTodo">
                <span wire:loading.remove wire:target="setSolicitudesHoySearch"><i class="bi bi-calendar-check"></i> </span>
                <span wire:loading.class="spinner-border spinner-border-sm" wire:target="setSolicitudesHoySearch" role="status" aria-hidden="true"></span>
                Solicitudes Hoy
              </button>
            </div>
            <div class="col-12 pb-2 col-md-1 pb-md-0 text-nowrap ms-md-4 me-md-2 text-center">
              <button type="button" data-tippy-content="Ver reservas solicitadas para el día de hoy" class="btn btn-primary btn-sm ms-md-2" style="width:135px;" wire:click="setReservasHoySearch" wire:loading.attr="disabled" wire:target="setReservasHoySearch, mostrarTodo">
                <span wire:loading.remove wire:target="setReservasHoySearch"><i class="bi bi-calendar-check"></i></span>
                <span wire:loading.class="spinner-border spinner-border-sm" wire:target="setReservasHoySearch" role="status" aria-hidden="true"></span>
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
              <label>Nombre Funcionario(a)</label>
              <div class="input-group pb-1">
                <span class="input-group-text"> 
                  <i class="bi bi-person"></i>
                </span>
                <input type="text" class="form-control" maxlength="100" id="nameSearch" wire:model.debounce.250ms="nameSearch" placeholder="Nombre del funcionario(a) que desea buscar" data-tippy-content="Ingrese el nombre del funcionario(a) que desea buscar">
                <span class="input-group-text bg-white" id="borrarNameSearch{{rand(0, 100)}}" style="cursor:pointer;" data-tippy-content="Borrar" wire:click="$set('nameSearch', '')">
                  <i class="bi bi-x-circle"></i>
                </span>
              </div>
              @if (!empty($nomFuncSearchMsj))
                 <span>
                   <a href="JavaScript:moveScrollById('#listadoSolReservas')" class="link-primary" style="font-size:15px;font-style:italic;" data-tippy-content="Click paraVer resultado">{{$nomFuncSearchMsj}}</a>
                 </span>
              @endif
            </div>
            <div class="col-12 col-md-3">
              <label>Estado Reserva</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-list-ul"></i>
                </span>
                <select wire:model="codEstadoSearch" class="form-select" data-tippy-content="Seleccione el estado de las reservas que desea buscar">
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

            <div class="col-12 col-md-10 mt-3">
              <div class="card" id="rangoFecReserva">
                <div class="card-header">
                  Busqueda por Rango de Fecha
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-12 col-md-4">
                      <div class="row">
                        <div class="col-12">
                          <label>Fecha Desde</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="bi bi-calendar4"></i>
                            </span>
                            <input type="date" wire:model.defer="fechaInicioReserva" class="form-control" autocomplete="off">
                            <span class="input-group-text bg-white" id="fechaInicioReservaDel" style="cursor:pointer;" data-tippy-content="Borrar" wire:click="$set('fechaInicioReserva', '')">
                              <i class="bi bi-x-circle"></i>
                            </span>
                          </div>
                        </div>
                        @error('fechaInicioReserva')
                        <div class="col-12  pt-1">
                          <span class="colorerror">{{ $message }}</span>
                        </div>
                        @enderror
                      </div>
                    </div>
                    <div class="col-12 col-md-4">
                      <div class="row">
                        <div class="col-12">
                          <label>Fecha Hasta</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="bi bi-calendar4"></i>
                            </span>
                            <input type="date" wire:model.defer="fechaFinReserva" class="form-control" autocomplete="off">
                            <span class="input-group-text bg-white" id="fechaFinReservaDel" style="cursor:pointer;" data-tippy-content="Borrar" wire:click="$set('fechaFinReserva', '')">
                              <i class="bi bi-x-circle"></i>
                            </span>
                          </div>
                        </div>
                        @error('fechaFinReserva')
                        <div class="col-12  pt-1">
                          <span class="colorerror">{{ $message }}</span>
                        </div>
                        @enderror
                      </div>
                    </div>

                    <div class="col-12 col-md-4 pt-3 pt-md-4 text-center text-md-start"> 
                        <button id="btnBuscar" class="btn btn-primary" type="button" wire:click="buscarReservas" wire:loading.attr="disabled" wire:target="buscarReservas">
                          <span wire:loading.class="spinner-border spinner-border-sm" wire:target="buscarReservas" role="status" aria-hidden="true"></span>
                          <span wire:loading.remove wire:target="buscarReservas"><i class="bi bi-search"></i></span>
                          Buscar
                        </button>
                    </div>
                  </div>
                </div>
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
            @if(!empty($reservasTotales) && count($reservasTotales) > 0)
            <tr>
              <th scope="col" colspan="7" class="ps-4 text-primary py-4"> 
                <center>
                  <b>
                  @if ($flgSolicitudesHoy == 1)
                       Para el día de hoy {{$cantReservasSearch > 1 ? 'se han realizado':'se ha realizado'}} <span class="text-dark">{{$cantReservasSearch}}</span> {{$cantReservasSearch > 1 ? 'Reservas':'Reserva'}}</span> 
                  @else
                    @if (!empty($nameSearch) || !empty($codEstadoSearch) || !empty($fechaInicioReserva) || !empty($fechaFinReserva))
                        Para los parámetros de búsqueda {{$cantReservasSearch > 1 ? 'se encontraron':'se encontró'}} <span class="text-dark">{{$cantReservasSearch}}</span><span class="text-success"> {{$cantReservasSearch > 1 ? 'Reservas':'Reserva'}}</span>           
                    @else
                        {{$cantReservasSearch > 1 ? 'Se encontraron':'Se encontró'}} <span class="text-dark">{{$cantReservasSearch}}</span><span class="text-success"> {{$cantReservasSearch > 1 ? 'Reservas vigentes':'Reserva vigente'}}</span>  
                    @endif    
                  @endif               
                    
                  @if ($cantReservasSearch > 1)
                       desde el <span style="background-color:#FFD42F;color:black;padding-left:4px;padding-right:4px;">{{\Carbon\Carbon::parse($fecInicioResult)->format('d/m/Y')}}</span> 
                       hasta el <span style="background-color:#FFD42F;color:black;padding-left:4px;padding-right:4px;">{{\Carbon\Carbon::parse($fecFinResult)->format('d/m/Y')}}</span> 
                  @endif

                  @if ($codEstadoSearch > 0) en estado <span style="background-color:{{$colorEstadoSearch}};color:black;padding-left:4px;padding-right:4px;">{{$descripEstadoSearch}}</span> @endif
                  </b>
                </center>
              </th>
            </tr>
            @endif
            <tr>
              <th scope="col" class="ps-4">Funcionario(a)</th>
              <th scope="col" class="text-start">Fecha Creación</th>
              <th scope="col" class="text-start">Fecha Reserva</th>
              <!-- <th scope="col" class="text-center">Hora Inicio</th>
              <th scope="col" class="text-center">Hora Fin</th> -->
              <th scope="col" class="text-start">Estado</th>
              <th scope="col" class="text-start">Destino</th>
              <th scope="col" class="text-start">Vehículo</th>
              <th scope="col" class="text-start">Motivo</th>
              <!-- <th scope="col" style="width:170px;">Acción</th> -->
            </tr>
          </thead>
          <tbody>
            @if(!empty($reservasTotales) && count($reservasTotales) > 0)
            @foreach($reservasTotales as $item)
            <tr style="height:55px;cursor:pointer;" id="td{{$loop->index}}{{rand(0, 100)}}" wire:click="reservaSel('{{$item->idReserva}}', '1')" data-tippy-content="Click para editar">
              <td nowrap class="ps-4">{{ $item->name}}</td>
              <td class="text-start" nowrap>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i')}}</td>
              <td class="text-start"><span style="background-color:#FFD42F;color:black;padding-left:4px;padding-right:4px;"><b>{{ \Carbon\Carbon::parse($item->fechaSolicitud)->format('d/m/Y')}}</b></span></td>
              <!-- <td class="text-center">{{ \Carbon\Carbon::parse($item->horaInicio)->format('H:i')}}</td>
              <td class="text-center">{{ \Carbon\Carbon::parse($item->horaFin)->format('H:i')}}</td> -->
              <!-- <td class="text-center">
                @if(!empty($item->fechaConfirmacion))
                   {{ \Carbon\Carbon::parse($item->fechaConfirmacion)->format('d/m/Y')}}
                @endif
              </td> -->
              <td class="text-start" nowrap><span style="background-color:{{$item->codColor}};color:white;padding-left:4px;padding-right:4px;">{{$item->descripcionEstado}}</span></td>
              <td class="text-start" nowrap>{{$item->nombreComuna}}</td>
              <td class="text-start" nowrap>{{$item->codVehiculo > 0 ? $item->descripcionVehiculo: 'No Asignado'}}</td>
              <td class="glosaTable pe-4">
                <!-- <i class="bi bi-eye-fill size-icon" id="id{{$loop->index.rand()}}" data-tippy-content="{{$item->motivo}}"></i> -->
                {{$item->motivo}}
              </td>

            </tr>
            @endforeach
            <tr id="td{{rand(101, 120)}}">
              <td colspan="7">   
                <center style="font-size:16px;font-style: italic;" class="text-primary"> 
                  <b>Página {{$reservasTotales->currentPage()}} de {{$reservasTotales->lastPage()}}: </b>Desplegando <b>{{count($reservasTotales)}} de {{$cantReservasSearch}}</b> {{count($reservasTotales) > 1 ? 'reservas':'reserva'}} 
                   
                  @if ($cantReservasSearch > 1)
                    desde el <span style="background-color:#FFD42F;color:black;padding-left:4px;padding-right:4px;"><b>{{!empty($reservasTotales) ? \Carbon\Carbon::parse($reservasTotales[0]->fechaSolicitud)->format('d/m/Y'):''}}</b></span> 
                    hasta el <b><span style="background-color:#FFD42F;color:black;padding-left:4px;padding-right:4px;">{{!empty($reservasTotales) ? \Carbon\Carbon::parse($reservasTotales[(count($reservasTotales)-1)]->fechaSolicitud)->format('d/m/Y'):''}}</span></b> 
                  @endif

                  @if ($codEstadoSearch > 0) en estado <span style="background-color:{{$colorEstadoSearch}};color:black;padding-left:4px;padding-right:4px;"><b>{{$descripEstadoSearch}}</b></span> @endif
                </center> 
              </td>
            </tr>
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
              <button type="button" id="btnIconClose" class="btn-close" onclick="ocultarModal()" wire:loading.attr="disabled" wire:target="guardarReservaSel"></button>
            </div>
            <div class="modal-body" id="modalBody">
              <!-- <input type="text" id="myInput" class="form-control"> -->
              @php($flgError = false)
              <div class="row">
                <div class="col-12 col-md-6 ps-4">
                  <div class="row mb-2">
                    <div class="col-12">
                      <div class="row">
                        <div class="col-12" id="inputFunc">
                          <label>Funcionario(a)</label>
                          @if ($flgNuevaReserva == true)
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="bi bi-person"></i>
                            </span>
                            <select wire:model="idUserSel" wire:loading.attr="disabled" wire:target="guardarReservaSel" class="form-select">
                              <option value="">Sel.Funcionario(a)</option>
                              @if (!empty( $userList))
                              @foreach($userList as $item)
                              <option value="{{$item->id}}">{{$item->name}}</option>
                              @endforeach
                              @endif
                            </select>
                          </div>
                          @else
                          <input type="hidden" wire:model="idUserSel">
                          <span class="text-success">{{$nameSel}}</span>
                          @endif
                        </div>
                        @error('idUserSel')
                        <div class="col-12" id="idUserSelError">
                          @if($flgError == false)
                          <script>
                            movScrollModalById('#inputFunc');
                          </script>
                          @php($flgError = true)
                          @endif
                          <span class="colorerror">{{ $message }}</span>
                        </div>
                        @enderror
                      </div>

                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-12 col-md-5" id="fechaSolicitudId">
                      <input type="hidden" wire:model="idReservaSel">
                      <div class="row">
                        <div class="col-12">
                          <label>Fecha Reserva</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="bi bi-calendar4"></i>
                            </span>
                            <input type="date" wire:model.debounce.500ms="fechaSolicitudSel" wire:loading.attr="disabled" wire:target="guardarReservaSel" class="date-ini form-control" autocomplete="off">
                          </div>
                        </div>
                        @error('fechaSolicitudSel')
                        <div class="col-12">
                          @if($flgError == false)
                          <script>
                            movScrollModalById('#fechaSolicitudId');
                          </script>
                          @php($flgError = true)
                          @endif
                          <span class="colorerror">{{ $message }}</span>
                        </div>
                        @enderror
                      </div>
                    </div>
                    <div class="col-12 col-md-6 mt-2 mt-md-0">
                      <div class="row">
                        <div class="col-12" id="codEstadoId">
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
                          @if($flgError == false)
                          <script>
                            movScrollModalById('#codEstadoId');
                          </script>
                          @php($flgError = true)
                          @endif
                          <span class="colorerror">{{ $message }}</span>
                        </div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12 pb-2 col-md-5 mt-md-0">
                      <div class="row">
                        <div class="col-12" id="horaInicioId">
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
                          @if($flgError == false)
                          <script>
                            movScrollModalById('#horaInicioId');
                          </script>
                          @php($flgError = true)
                          @endif
                          <span class="colorerror">{{ $message }}</span>
                        </div>
                        @enderror
                      </div>
                    </div>
                    <div class="col-12 col-md-6">
                      <div class="row">
                        <div class="col-12" id="horaFinId">
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
                          @if($flgError == false)
                          <script>
                            movScrollModalById('#horaFinId');
                          </script>
                          @php($flgError = true)
                          @endif
                          <span class="colorerror">{{ $message }}</span>
                        </div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="row pb-2">
                    <div class="col-12" id="codVehiculoId">
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
                      @if($flgError == false)
                      <!-- <script>
                            movScrollModalById('#codVehiculoId');
                          </script> -->
                      @php($flgError = true)
                      @endif
                      <span class="colorerror">{{ $message }}</span>
                    </div>
                    @enderror
                  </div>

                  <div class="row">
                    <div class="col-12 pb-2 col-md-6 mt-md-0">
                      <div class="row">
                        <div class="col-12" id="cantPasajerosId">
                          <label data-tippy-content="Cantidad de pasajeros.">Cant.Pasajeros</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="bi bi-people"></i>
                            </span>
                            <input type="text" id="cantPasajeros" @if($codEstadoSel==3) readonly @endif onkeydown="return onlyNumberKey(event, this);" maxlength="2" wire:model.debounce.500ms="cantPasajerosSel" wire:loading.attr="disabled" wire:target="guardarReservaSel" class="form-control" placeholder="Cantidad" data-tippy-content="Indique el n&uacute;mero de pasajeros." autocomplete="off">
                          </div>
                        </div>
                        @error('cantPasajerosSel')
                        <div class="col-12 pb-1">
                          @if($flgError == false)
                          <script>
                            movScrollModalById('#cantPasajerosId');
                          </script>
                          @php($flgError = true)
                          @endif
                          <span class="colorerror">{{ $message }}</span>
                        </div>
                        @enderror
                      </div>
                    </div>
                    <div class="col-12 col-md-6">
                      <div class="row">
                        <div class="col-12" id="codComunaId">
                          <label>Comuna destino</label>
                          <div class="input-group">
                            <span class="input-group-text">
                              <i class="bi bi-signpost-2"></i>
                            </span>
                            <select id="codComuna" wire:model="codComunaSel" @if($codEstadoSel==3) readonly @endif wire:loading.attr="disabled" wire:target="guardarReservaSel" class="form-select">
                              <option value="">Sel. Comuna destino</option>
                              @foreach($comunasCmb as $itemComuna)
                              <option value="{{$itemComuna->codComuna}}">{{$itemComuna->nombreComuna}}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        @error('codComunaSel')
                        <div class="col-12 pb-1">
                          @if($flgError == false)
                          <script>
                            movScrollModalById('#codComunaId');
                          </script>
                          @php($flgError = true)
                          @endif
                          <span class="colorerror">{{ $message }}</span>
                        </div>
                        @enderror
                      </div>
                    </div>
                  </div>

                  <div class="row pt-2 pt-md-0 pb-2" id="divisionId">
                    <div class="col-12">
                      <label>División</label>
                      <div class="input-group">
                        <span class="input-group-text">
                          <i class="bi bi-list-ul"></i>
                        </span>
                        <select id="codDivision" wire:model="codDivisionSel" @if($codEstadoSel==3) readonly @endif wire:loading.attr="disabled" wire:target="guardarReservaSel" class="form-select">
                          <option value="">Sel.División</option>
                          @foreach($divisionesCmb as $itemDivision)
                          <option value="{{$itemDivision->codDivision}}">{{$itemDivision->nombreDivision}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    @error('codDivisionSel')
                    <div class="col-12">
                      @if($flgError == false)
                      <script>
                        movScrollModalById('#divisionId');
                      </script>
                      @php($flgError = true)
                      @endif
                      <span class="colorerror">{{$message}}</span>
                    </div>
                    @enderror
                  </div>

                  <div class="row pt-3 pt-md-0 pb-3">
                    <div class="col-12" id="motivoId">
                      <label>Motivo del viaje</label>
                      <textarea wire:model.debounce.250ms="motivoSel" onclick="movScrollModalById('#usoVehiculoHead')" wire:loading.attr="disabled" wire:target="guardarReservaSel" placeholder="Motivo de la reserva (Máximo 500 caracteres)" class="form-control" maxlength="500" rows="3"></textarea>
                    </div>
                    @error('motivoSel')
                    <div class="col-12">
                      @if($flgError == false)
                      <script>
                        movScrollModalById('#motivoId');
                      </script>
                      @php($flgError = true)
                      @endif
                      <span class="colorerror">{{$message}}</span>
                    </div>
                    @enderror
                  </div>
                  <!-- <div class="row">
                    <div class="col-12" id="usoVehiculoHead">
                      <div class="form-check form-switch" data-tippy-content="Proponer uso de vehiculo personal con devolución del costo por gastos de combustible y peajes.">
                        <label class="form-check-label text-secondary" style="font-style:italic;" for="flgUsoVehiculoPersonal">
                          Usar Vehiculo Personal con Devolución de Combustible y Peajes.
                        </label>
                        <input wire:model.debounce.500ms="flgUsoVehiculoPersSel" class="form-check-input" wire:loading.attr="disabled" wire:target="guardarReservaSel" type="checkbox" id="flgUsoVehiculoPersonal">
                      </div>
                    </div>
                  </div> -->
                </div>

                <div class="col-12 col-md-6 pt-3 pt-md-1">
                  <div class="table-responsive mx-2">
                    <table class="table @if(!empty($reservasFechaSel) && count($reservasFechaSel) > 0 && $flgNuevaReserva == false) table-hover @endif ">
                      <!-- table-bordered -->
                      <thead>
                        <tr>
                          <th scope="col" colspan="7" class="text-start text-success pb-3">
                            <span data-tippy-content="Reservas realizadas por otros funcionarios para el día: {{ \Carbon\Carbon::parse($fechaSolicitudSel)->format('d/m/Y')}}">
                              Reservas realizadas para el día <span style="background-color:#FFD42F;color:black;padding-left:4px;padding-right:4px;">{{ \Carbon\Carbon::parse($fechaSolicitudSel)->format('d/m/Y')}}</span>
                            </span>
                          </th>
                        </tr>
                        <tr>
                          <th scope="col" class="text-start" nowrap>Fecha Creación</th>
                          <th scope="col" class="text-start">Nombre</th>
                          <th scope="col" class="text-start" nowrap>Fecha Reserva</th>
                          <th scope="col" class="text-start">Estado</th>
                          <th scope="col" class="text-start">Vehículo</th>
                          <th scope="col" class="text-start">Destino</th>
                          <th scope="col" class="text-start" nowrap>Hora Inicio-Fin</th>
                        </tr>
                      </thead>
                      <tbody>
                        @if(!empty($reservasFechaSel) && count($reservasFechaSel) > 0)
                        @foreach($reservasFechaSel as $index => $item)
                        <tr id="fila{{$index}}" @if ($flgNuevaReserva==false) style="cursor:pointer;" wire:click="reservaSel('{{$item->idReserva}}', '0')" @endif>
                          <td class="text-start" nowrap>{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y H:i')}}</td>
                          <td class="text-start" nowrap>{{$item['name']}}</td>
                          <td class="text-center" nowrap><span style="background-color:#FFD42F;padding-left:4px;padding-right:4px;">{{ \Carbon\Carbon::parse($item['fechaSolicitud'])->format('d/m/Y')}}</span></td>
                          <td class="text-start" nowrap><span style="background-color:{{$item['codColor']}};padding-left:4px;padding-right:4px;">{{$item['descripcionEstado']}}</span></td>
                          <td class="text-start" nowrap>{{ !empty($item['codVehiculo']) ? $item->descripcionVehiculo: 'No Asignado'}}</td>
                          <td class="text-start" nowrap>{{$item['nombreComuna']}}</td>
                          <td class="text-center" nowrap>{{ \Carbon\Carbon::parse($item['horaInicio'])->format('H:i')}} - {{ \Carbon\Carbon::parse($item['horaFin'])->format('H:i')}}</td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                          <td colspan="7">
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
                  <div class="row mt-3">
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

    window.addEventListener('moveScrollModalById', event => {
      const modalBody = document.getElementById("modalBody");



      const element = document.querySelector(event.detail.id);
      const topPos = element.getBoundingClientRect().top;

      modalBody.scrollTo({
        top: topPos - 109,
        behavior: 'smooth'
      });
    });

    function onlyNumberKey(evt, obj) {
      var ASCIICode = (evt.which) ? evt.which : evt.keyCode;
      var flgAsciiNumberOK = false;

      if (ASCIICode == 8 /*Borrar <-*/ || ASCIICode == 46 /*Supr*/ || ASCIICode == 37 /*Atras*/ || ASCIICode == 39 /*Adelante*/ || ASCIICode == 9 /*Tab*/ ) {
        return true;
      }

      if (obj.value.length >= obj.maxLength) {
        return false;
      }

      if ((ASCIICode > 47 && ASCIICode < 58) || (ASCIICode > 95 && ASCIICode < 106)) {
        return true;
      } else {
        return false;
      }
    }

    // let el = document.querySelector('.el');
    // let height = el.scrollHeight;
    // el.style.setProperty('--max-height', height + 'px');
  </script>
  @endif
</div>