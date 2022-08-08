<div>
  <div class="card m-2 mb-4 m-md-3 mb-md-5">
    <div class="card-header py-3 text-center h5">
      Solicitudes de Reserva
    </div>

    <div class="card-body">
      <div class="table-responsive-sm mx-4">
        <table class="table">
          <thead>
            <tr class="text-center">
              <th scope="col">
              <label>Fecha Solicitud</label>
                <div class="input-group input-group-sm"> 
                  <span class="input-group-text">
                    <i class="bi bi-alarm"></i>
                  </span>
                  <input type="date" id="fechaSolSearch" wire:model="fechaSolSearch" class="form-control">
                </div>
              </th>
              <th scope="col">Hora Inicio</th>
              <th scope="col">Hora Fin</th>
              <th scope="col">Fecha Confirmación</th>
              <th scope="col" style="width:165px;">
                  <label>Estado Reserva</label>
                  <div class="input-group input-group-sm"> 
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
              </th>
              <th scope="col" class="text-center">Motivo</th>
              <th scope="col">Acción</th>
            </tr>            
          </thead>
          <tbody>
            @if(!empty($reservasTotales) && count($reservasTotales) > 0)
            @foreach($reservasTotales as $item)
            <tr class="text-center">
              <td>{{ \Carbon\Carbon::parse($item->fechaSolicitud)->format('d/m/Y')}}</td>
              <td>{{ \Carbon\Carbon::parse($item->horaInicio)->format('H:i')}}</td>
              <td>{{ \Carbon\Carbon::parse($item->horaFin)->format('H:i')}}</td>
              <td>{{ \Carbon\Carbon::parse($item->fechaConfirmacion)->format('d/m/Y')}}</td>
              <td>{{$item->descripcionEstado}}</td>
              <td class="text-center"><i class="bi bi-eye-fill fs-4" data-tippy-content="{{$item->motivo}}"></i></td>
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
              <td colspan="6">
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