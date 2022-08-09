<div>
  <div class="card m-2 mb-4 m-md-3 mb-md-5">
    <div class="card-header py-3 text-center h5">
      Solicitudes de Reserva
    </div>

    <div class="card-body">
      <div class="card mx-2 mt-3 mb-3">
        <div class="card-header">
          <div class="row">
            <div class="col-12 text-center py-2">Parámetros de Búsqueda 
              <button type="button" class="btn btn-danger btn-sm ms-5" wire:click="setFechaHoySearch">
              <i class="bi bi-calendar-check"></i> Solicitudes Hoy 
              </button>
              <button type="button" class="btn btn-danger btn-sm ms-2" wire:click="mostrarTodo">
                <i class="bi bi-eye"></i> Mostrar Todo</button>
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
              </div>
            </div>
            <!-- <div class="col-12 col-md-3 pb-3">
              <label>Fecha Inicio Solicitud</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-calendar4"></i>
                </span>
                <input type="date" id="fechaSolSearch" wire:model="fechaSolSearch" class="form-control">
              </div>
            </div>
            <div class="col-12 col-md-3">
              <label>Fecha Fin Solicitud</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-calendar4"></i>
                </span>
                <input type="date" id="fechaSolSearch" wire:model="fechaSolSearch" class="form-control">
              </div>
            </div> -->
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
            <div class="col-12 mt-3">
              @if(!empty($fechaHoySearch)) 
            <button type="button" id="tipoBusqueda1" class="btn btn-dark btn-sm rounded-pill p-1" style="cursor:context-menu;">
              Solicitudes Hoy <div class="d-inline" wire:click="resetSearch('fechaHoySearch')"><i class="bi bi-x-circle" style="cursor:pointer;"></i></div>
            </button>
            @endif
            <!-- @if(!empty($nameSearch)) 
            <button type="button" id="tipoBusqueda1" class="btn btn-dark btn-sm rounded-pill p-1" style="cursor:context-menu;">
              Nombre Funcionario <div class="d-inline" wire:click="resetSearch('nameSearch')"><i class="bi bi-x-circle" style="cursor:pointer;"></i></div>
            </button>
            @endif -->
            </div>
          </div>
         </div>
      </div>
      <div class="table-responsive card mx-2 mt-4">
        <table class="table">
          <thead class="table-light">
            <tr class="text-center">
            <th scope="col">
                Funcionario
              </th>
              <th scope="col">
                Fecha Solicitud
              </th>
              <th scope="col">Hora Inicio</th>
              <th scope="col">Hora Fin</th>
              <th scope="col">Fecha Confirmación</th>
              <th scope="col">
                Estado Reserva
              </th>
              <th scope="col" class="text-center">Motivo</th>
              <th scope="col">Acción</th>
            </tr>
          </thead>
          <tbody>
            @if(!empty($reservasTotales) && count($reservasTotales) > 0)
            @foreach($reservasTotales as $item)
            <tr class="text-center" style="height:55px;">
              <td nowrap>{{ $item->name}}</td>
              <td>{{ \Carbon\Carbon::parse($item->fechaSolicitud)->format('d/m/Y')}}</td>
              <td>{{ \Carbon\Carbon::parse($item->horaInicio)->format('H:i')}}</td>
              <td>{{ \Carbon\Carbon::parse($item->horaFin)->format('H:i')}}</td>
              <td>{{ \Carbon\Carbon::parse($item->fechaConfirmacion)->format('d/m/Y')}}</td>
              <td nowrap>{{$item->descripcionEstado}}</td>
              <td class="text-center">
                   <i class="bi bi-eye-fill size-icon" data-tippy-content="{{$item->motivo}}"></i></td>
              <td>

                <div class="input-group">
                  <select id="codEstado" name="codEstado" wire:model="codEstado" class="form-select form-select-sm">
                    <option value="0">Sel.Estado</option>
                    @if (!empty( $estadosCmb))
                    @foreach($estadosCmb as $item)
                    <!-- No mostrar el estado actual   -->
                    <option value="{{$item->codEstado}}">{{$item->descripAccionEstado}}</option>
                    @endforeach
                    @endif
                  </select>
                  <button type="button" style="width:120px;" class="btn btn-sm btn-success" wire:click="cambiarEstado('{{$item['idReserva']}}')" wire:loading.attr="disabled" wire:target="cambiarEstado">
                    Guardar
                    <!-- <span wire:loading.remove wire:target="cambiarEstado"><i class="bi bi-check-circle"></i></span> 
               <span wire:loading.class="spinner-border spinner-border-sm" wire:target="cambiarEstado" role="status" aria-hidden="true"></span> -->
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
            @else
            <tr>
              <td colspan="8">
                <div class="alert alert-info border border-info d-flex justify-content-center my-3 mx-2 my-md-4" role="alert">
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
      <div class="row mt-3">
        <div class="col-7 offset-2 col-md-5 offset-md-5 ">
          {{ $reservasTotales->links()}}
        </div>
      </div>


    </div>
  </div>
</div>