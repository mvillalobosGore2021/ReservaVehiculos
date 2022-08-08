<div>
  <div class="card m-2 mb-4 m-md-3 mb-md-5">
    <div class="card-header py-3 text-center h5">
      Mis Reservas
    </div>

    <div class="card-body">
      <div class="table-responsive-sm mx-4">
        <table class="table">
          <thead>
            <tr class="text-center">
              <th scope="col">Fecha Solicitud</th>
              <th scope="col">Hora Inicio</th>
              <th scope="col">Hora Fin</th>
              <th scope="col">Fecha Confirmación</th>
              <th scope="col">Estado Reserva</th>    
              <th scope="col">Motivo</th>   
            </tr>
          </thead>
          <tbody>
          @if(!empty($reservasUsuario) && count($reservasUsuario) > 0)
            @foreach($reservasUsuario as $item)
            <tr class="text-center">
              <td>{{ \Carbon\Carbon::parse($item->fechaSolicitud)->format('d/m/Y')}}</td>
              <td>{{ \Carbon\Carbon::parse($item->horaInicio)->format('H:i')}}</td>
              <td>{{ \Carbon\Carbon::parse($item->horaFin)->format('H:i')}}</td>
              <td>
                @if(!empty($item->fechaConfirmacion))
                   {{ \Carbon\Carbon::parse($item->fechaConfirmacion)->format('d/m/Y')}}
                @else
                   &nbsp;
                @endif                
              </td>
              <td>{{$item->descripcionEstado}}</td>
              <td class="glosaTable">{{$item->motivo}}</td>
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
          {{ $reservasUsuario->links()}}
        </div>
      </div>
    </div>
  </div>
</div>