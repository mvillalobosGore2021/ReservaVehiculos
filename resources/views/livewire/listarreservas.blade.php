<div>
  <form>
  @csrf
  <div class="pt-0 pt-md-1"></div>
    <div class="card mt-2 mb-4 mt-md-3 mb-md-5">
      <div class="card-header py-3 text-center h3">
        Consultar Mis Reservas
        <span class="d-block fst-italic text-secondary pt-1" style="font-size:15px;">
          Fecha Actual: <i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::parse(now())->format('d/m/Y')}}
        </span>
      </div>
      <div class="card-body mt-2 mx-2 mx-md-3">
      <div class="alert alert-info border border-info mb-4 shadow" role="alert">
      <h4 class="alert-heading text-center fw-bold fs-5">Consultar Mis reservas</h4>
      <hr>
          <p class="fs-6 fst-italic pt-1 mx-2 mx-md-3" style="text-align:justify;text-indent: 30px;">
          <span class="fw-bold fs-4 text-white" style="background-color:#17a2b8;border:2px solid;border-radius:5px;padding-left:4px;padding-right:8px;">E</span>n la opción <b>Consultar Mis Reservas</b> usted podrá ingresar <b>Nuevas Reservas</b>, buscar sus reservas ya ingresadas y editarlas. 
            Al presionar el botón <b>Solicitudes Hoy</b> se desplegarán 
            sus reservas ingresadas el día de hoy, al presionar <b>Reservas Hoy</b> se desplegarán sus solicitudes de reservas ingresadas para el día de hoy. <b>Mostrar Todo</b> desplegará
            todas sus solicitudes de reservas a partir de la fecha actual hasta un rango de tres meses.
          </p>
        </div>
        <div class="card shadow">
          <div class="card-header">
            <div class="row py-md-2 justify-content-center">
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
            <!-- Inicio Parametros de busqueda -->
            <div class="row ms-1">
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
                              <input type="date" wire:model.defer="fechaFinReserva" onfocusout="shakeButton()" class="form-control" autocomplete="off">
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
                        <button id="btnBuscar" class="btn btn-primary" type="button" onclick="deleteClassShake()" wire:click="buscarReservas" wire:loading.attr="disabled" wire:target="buscarReservas">
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
            <!-- Fin Parametros de busqueda -->
          </div>
        </div>

<!-- Inicio tabla solicitudes reservas -->
<div class="table-responsive card mx-1 mt-4 shadow" id="listadoSolReservas">
        <table class="table @if(!empty($reservasUsuario) && count($reservasUsuario) > 0) table-hover @endif ">
          <thead > 
            @if(!empty($reservasUsuario) && count($reservasUsuario) > 0)             
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
                        {{$cantReservasSearch > 1 ? 'Se encontraron':'Se encontró'}} <span class="text-dark">{{$cantReservasSearch}}</span><span class="text-success"> {{$cantReservasSearch > 1 ? 'Reservas vigentes':'Reserva vigente'}}</span> a su nombre
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
              <th scope="col" class="ps-4">Fecha Creación</th>
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
            @if(!empty($reservasUsuario) && count($reservasUsuario) > 0)
            @foreach($reservasUsuario as $item)
            <tr style="cursor:pointer;" wire:click="setFechaModal('{{ \Carbon\Carbon::parse($item['fechaSolicitud'])->format('d-m-Y')}}')" data-tippy-content="Click para ver reserva">
              <td class="ps-4" nowrap>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i')}}</td>
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
          </tbody>
            <tfoot>
            <tr id="td{{rand(101, 120)}}">
              <td colspan="7" style="height: 3.3rem;">  
                <center style="font-size:16px;font-style: italic;" class="text-primary pt-1"> 
                  <b>Página {{$reservasUsuario->currentPage()}} de {{$reservasUsuario->lastPage()}}: </b>Desplegando <b>{{count($reservasUsuario)}} @if($cantReservasSearch > 1) de {{$cantReservasSearch}} @endif</b> {{count($reservasUsuario) > 1 ? 'reservas':'reserva'}} 
                   
                  @if ($cantReservasSearch > 1)
                    desde el <span style="background-color:#FFD42F;color:black;padding-left:4px;padding-right:4px;"><b>{{!empty($reservasUsuario) ? \Carbon\Carbon::parse($reservasUsuario[0]->fechaSolicitud)->format('d/m/Y'):''}}</b></span> 
                    hasta el <b><span style="background-color:#FFD42F;color:black;padding-left:4px;padding-right:4px;">{{!empty($reservasUsuario) ? \Carbon\Carbon::parse($reservasUsuario[(count($reservasUsuario)-1)]->fechaSolicitud)->format('d/m/Y'):''}}</span></b> 
                  @endif

                  @if ($codEstadoSearch > 0) en estado <span style="background-color:{{$colorEstadoSearch}};color:black;padding-left:4px;padding-right:4px;"><b>{{$descripEstadoSearch}}</b></span> @endif
                </center> 
              </td>
            </tr>
            </tfoot>
            @else
            <tfoot>
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
            </tfoot>
            @endif
        </table>
      </div>
      <div class="row mt-3 ">
        <div class="col-7 offset-0 col-md-5 offset-md-4 mb-4 pt-2">
          {{ $reservasUsuario->links()}}
        </div>
      </div>
<!-- Fin Solicitudes Reservas -->
      </div>
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade pt-0" id="modalReserva" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-xl modal-dialog-scrollable pt-1">
        <div class="modal-content">
          <div class="modal-header bg-light">
            <h5 class="modal-title ps-3 text-primary" id="modalReservaLabel">
              @if ($flgNuevaReserva == true) Nueva Reserva @else Datos de su Reserva @endif
            </h5>
              <button type="button" id="btnIconClose" class="btn-close" onclick="ocultarModal()" wire:loading.attr="disabled" wire:target="solicitarReserva,anularReserva"></button>
          </div>
          <div class="modal-body" id="modalBody">
            <!-- <input type="text" id="myInput" class="form-control"> -->
            <div class="row">           
              <div class="col-12 col-md-6 ps-4">
                <div class="row pb-md-1 text-success">
                  
                @if($codEstado==3) 
              <div class="col-12" >
              <div class="alert alert-info border border-info pb-0" role="alert">               
                  <p class="fst-italic" style="font-size:0.99rem;text-align:justify;text-indent: 10px;">
                  <span class="fw-bold text-white" style="font-size:1.2rem;background-color:#17a2b8;border:2px solid;border-radius:5px;padding-left:4px;padding-right:8px;">
                  S</span>u reserva se encuentra <span style="color:#EF3B2D;"><b>{{$descripcionEstado}}</b></span>. Si desea modificar el estado  de su reserva lo debe solicitar a <b>Darwin Figueroa</b> dfigueroa@gorebiobio.cl o a <b>Alvaro Olate</b> aolate@gorebiobio.cl.
              </p>
             </div>
            </div>
            @endif
                  <div class="col-12 col-md-6 pb-md-1" id="funcionarioId">
                    <div class="row">
                      <div class="col-12">
                        <span class="text-dark">
                          {{$sexo == "F" ? "Funcionaria":"Funcionario"}}
                        </span>
                      </div>
                      <div class="col-12">
                        <b>{{$userName}}</b>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 col-md-6 py-2 py-md-0">
                    @if ($flgNuevaReserva == true)
                    <!-- Modo Insercion -->
                    <div class="row"> 
                      <div class="col-12" id="idfechaSolicitud">
                        <label class="text-dark">Fecha Reserva</label>
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="bi bi-calendar4"></i>
                          </span>
                          <input type="date" id="fechaSolicitud" wire:model.debounce.500ms="fechaSolicitud" wire:loading.attr="disabled" wire:target="solicitarReserva,anularReserva" class="date-ini form-control" autocomplete="off">
                        </div>
                      </div>
                      @error('fechaSolicitud')
                      <div class="col-12" id="idfechaSolicitudError">
                        <span class="colorerror">{{ $message }}</span>
                      </div>
                      @enderror
                    </div>
                    @else
                    <!-- Modo modificacion -->

                    <div class="row">
                      <div class="col-12">
                        <span class="text-dark">
                           Fecha Reserva
                        </span>
                      </div>
                      <div class="col-12">
                        <span id="funcionarioLbl">
                          <b>
                             {{ \Carbon\Carbon::parse($fechaSolicitud)->format('d/m/Y')}}
                          </b>
                      </span>
                      </div>
                    </div>
                        <input type="hidden" wire:model="fechaSolicitud">                        
                    @endif
                  </div>
                  <!-- <div class="col-12 col-md-6 pb-2 pb-md-0" id="estadoId">
                    <span class="text-primary">Estado:</span><span style="background-color:{{$codColor}};color:white;padding-left:4px;padding-right:4px;">{{$descripcionEstado}}</span>
                  </div> -->
                </div>


                <div class="row">
                  <div class="col-12 pb-2 col-md-6 mt-md-0">
                    <div class="row">
                      <div class="col-12" id="idhoraInicio">
                        <label data-tippy-content="Hora estimada de inicio.">Hora Inicio Reserva</label>
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="bi bi-alarm"></i>
                          </span>
                          <input type="time" id="horaInicio" wire:model.debounce.500ms="horaInicio" @if($codEstado==3) disabled @endif data-tippy-content="Hora estimada de salida" wire:loading.attr="disabled" wire:target="solicitarReserva,anularReserva" class="time-ini form-control" placeholder="Inicio" autocomplete="off">
                        </div>
                      </div>
                      @error('horaInicio')
                      <div class="col-12 pb-1" id="idhoraInicioError">
                         <span class="colorerror">{{ $message }}</span>
                      </div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-12 pb-2 col-md-6">
                    <div class="row">
                      <div class="col-12" id="idhoraFin">
                        <label>Hora Fin Reserva</label>
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="bi bi-alarm"></i>
                          </span>
                          <input type="time" id="horaFin" wire:model.debounce.500ms="horaFin" @if($codEstado==3) disabled @endif data-tippy-content="Hora estimada de regreso" wire:loading.attr="disabled" wire:target="solicitarReserva,anularReserva" class="time-fin form-control" placeholder="Termino" autocomplete="off">
                        </div>
                      </div>
                      @error('horaFin')
                      <div class="col-12 pb-1" id="idhoraFinError">
                        <span class="colorerror">{{ $message }}</span>
                      </div>
                      @enderror
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-12 pb-2 col-md-6 mt-md-0">
                    <div class="row">
                      <div class="col-12" id="idcantPasajeros">
                        <label data-tippy-content="Cantidad de pasajeros.">Cant.Pasajeros</label>
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="bi bi-people"></i>
                          </span>
                          <input type="text" id="cantPasajeros" wire:model.debounce.500ms="cantPasajeros" @if($codEstado==3) disabled @endif onkeydown="return onlyNumberKey(event, this);" maxlength="2" wire:loading.attr="disabled" wire:target="solicitarReserva,anularReserva" class="form-control" placeholder="Cantidad" data-tippy-content="Indique el n&uacute;mero de pasajeros." autocomplete="off">
                        </div>
                      </div>
                      @error('cantPasajeros')
                      <div class="col-12 pb-1" id="idcantPasajerosError">
                        <span class="colorerror">{{ $message }}</span>
                      </div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <div class="row">
                      <div class="col-12" id="idcodComuna">
                        <label>Comuna destino</label>
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="bi bi-signpost-2"></i>
                          </span>
                          <select id="codComuna" wire:model="codComuna" @if($codEstado==3) disabled @endif wire:loading.attr="disabled" wire:target="solicitarReserva,anularReserva" class="form-select">
                            <option value="">Sel. Comuna destino</option>
                            @foreach($comunasCmb as $itemComuna)
                            <option value="{{$itemComuna->codComuna}}">{{$itemComuna->nombreComuna}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      @error('codComuna')
                      <div class="col-12 pb-1" id="idcodComunaError">
                         <span class="colorerror">{{ $message }}</span>
                      </div>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="row pt-2 pt-md-0 pb-2">
                  <div class="col-12" id="idcodDivision">
                    <label>División</label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="bi bi-list-ul"></i>
                      </span>
                      <select id="codDivision" wire:model="codDivision" @if($codEstado==3) disabled @endif wire:loading.attr="disabled" wire:target="solicitarReserva,anularReserva" class="form-select">
                        <option value="">Sel.División</option>
                        @foreach($divisionesCmb as $itemDivision)
                        <option value="{{$itemDivision->codDivision}}">{{$itemDivision->nombreDivision}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  @error('codDivision')
                  <div class="col-12" id="idcodDivisionError">
                    <span class="colorerror">{{$message}}</span>
                  </div>
                  @enderror
                </div>
                <div class="row pt-md-0 pb-3">
                  <div class="col-12" id="idmotivo">
                    <label>Motivo del viaje</label>
                    <textarea id="motivo" wire:model.debounce.500ms="motivo" @if($codEstado==3) disabled @endif wire:loading.attr="disabled" wire:target="solicitarReserva,anularReserva" placeholder="Motivo/justificación del viaje (Máximo 500 caracteres)" class="form-control" maxlength="500" rows="4"></textarea>
                  </div>
                  @error('motivo')
                  <div class="col-12" id="idmotivoError">
                    <span class="colorerror">{{$message}}</span>
                  </div>
                  @enderror
                </div>
                <!-- <div class="row">
              <div class="col-12">
                <div class="form-check form-switch" data-tippy-content="Proponer uso de vehiculo personal con devolución del costo por gastos de combustible y peajes.">
                  <label class="form-check-label text-secondary" style="font-style:italic;" for="flgUsoVehiculoPersonal">
                    Usar Vehiculo Personal con Devolución de Combustible y Peajes.
                  </label>
                  <input id="flgUsoVehiculoPersonal" @if($codEstado==3) disabled @endif wire:model.debounce.500ms="flgUsoVehiculoPersonal" class="form-check-input" wire:loading.attr="disabled" wire:target="solicitarReserva,anularReserva" type="checkbox">
                </div>
              </div>
            </div> -->
              </div>

              <div class="col-12 col-md-6 px-3 pt-3 pt-md-1">
                <div class="table-responsive mx-4">
                  <table class="table">
                    <!-- table-bordered -->
                    <thead>
                      <tr>
                        <th scope="col" colspan="8" class="text-start text-success pb-3">
                          @if (!empty($fechaSolicitud))
                          <span data-tippy-content="Reservas realizadas por otros funcionarios para el día:  {{ \Carbon\Carbon::parse($fechaSolicitud)->format('d/m/Y')}}">
                            <span class="text-success">Reservas realizadas por otros funcionarios para el día:</span>
                            <span style="background-color:#FFD42F;color:black;padding-left:4px;padding-right:4px;"> {{ \Carbon\Carbon::parse($fechaSolicitud)->format('d/m/Y')}}</span>
                          </span>
                          @endif
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
                      <tr>
                        <td class="text-start" nowrap>{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y H:i')}}</td>
                        <td class="text-start" nowrap>{{$item['name']}}</td>
                        <td class="text-center" nowrap><span style="background-color:#FFD42F;padding-left:4px;padding-right:4px;">{{ \Carbon\Carbon::parse($item['fechaSolicitud'])->format('d/m/Y')}}</span></td>
                        <td class="text-start" nowrap><span style="background-color:{{$item['codColor']}};padding-left:4px;padding-right:4px;">{{$item['descripcionEstado']}}</span></td>
                        <td class="text-start" nowrap>{{$item['codVehiculo'] > 0 ? $item['descripcionVehiculo']: 'No Asignado'}}</td>
                        <td class="text-start" nowrap>{{$item['nombreComuna']}}</td>
                        <td class="text-center" nowrap>{{ \Carbon\Carbon::parse($item['horaInicio'])->format('H:i')}} - {{ \Carbon\Carbon::parse($item['horaFin'])->format('H:i')}}</td>
                      </tr>
                      @endforeach
                      @else
                      <tr>
                        <td colspan="8">
                          <div class="alert alert-info border border-info d-flex justify-content-center my-3 mx-2 my-md-4" role="alert">
                            <span class="fs-4 pe-2 pe-md-3">
                              <i class="bi bi-info-circle-fill"></i>
                            </span>
                            <span class="fs-6 fst-italic pt-1">
                              No existen reservas realizadas por otros funcionarios para el día seleccionado.
                            </span>
                          </div>
                        </td>
                      </tr>
                      @endif
                    </tbody>
                  </table>
                </div>

                @if (session()->has('exceptionMessage'))
                <div class="row pt-3 mx-3">
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
            <button type="button" id="btnCerrar" class="btn btn-danger" style="width:175px;" onclick="ocultarModal();" wire:loading.attr="disabled" wire:target="solicitarReserva,anularReserva">
              Cerrar <i class="bi bi-x-lg"></i>
            </button>
            <button type="button" id="btnGuardar" @if($codEstado==3) disabled @endif class="btn btn-primary" style="width:175px;" wire:click="solicitarReserva()" wire:loading.attr="disabled" wire:target="solicitarReserva,anularReserva">
              {{$idReserva > 0 ? 'Modificar Reserva':'Solicitar Reserva'}}
              <span wire:loading.remove wire:target="solicitarReserva,anularReserva"><i class="bi bi-send pt-1"></i></span>
              <span wire:loading.class="spinner-border spinner-border-sm" wire:target="solicitarReserva,anularReserva" role="status" aria-hidden="true"></span>
            </button>
            @if($codEstadoOrig != 3 && $idReserva > 0)  
            <button type="button" class="btn btn-danger" id="btnAnularReserva" style="width:175px;" wire:click="confirmAnularReserva" wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva, confirmAnularReserva">
              Anular Reserva
              <span id="anularIcon"><i class="bi bi-x-circle"></i></i></span>
              <span id="spinnerAnularReserva"></span> 
            </button>
            @endif
          </div>
        </div>
      </div>
    </div>
    <!-- Fin Modal -->

  </form>
  <script>
    // const myModal = document.getElementById('modalReserva')
    // // const myInput = document.getElementById('myInput')
    // myModal.addEventListener('shown.bs.modal', () => {
    //   // myInput.focus() 
    // })

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

    window.addEventListener('swal:information', event => {
      const Toast = Swal.mixin({
        toast: true,
        position: 'center',
        showConfirmButton: false,
        timer: 6000,
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

    function ocultarModal() {
      //myModal2.show();
      modal.hide();
    }

    
    document.addEventListener('livewire:load', () => {
      deleteClassShake();
    });
  </script>
</div>