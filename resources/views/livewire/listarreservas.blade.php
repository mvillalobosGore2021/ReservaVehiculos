<div>
  <form>
  

      <div class="card shadow mt-4" id="headReservas">
      <div class="card-header py-3 h3 text-center">
         Mis Reservas 
    </div>
<div class="card-body">
<div class="alert alert-info border border-info d-flex justify-content-center shadow mx-3 mx-md-4 my-3" role="alert">
                        <span class="fs-4 pe-2 pe-md-3">
                          <i class="bi bi-info-circle-fill"></i></span>
                        <span class="fs-6 fst-italic pt-1">
                           Listado de sus solicitudes de reserva en un rango de tres meses.
                        </span>
      </div>
    <div class="card p-0 mx-3 mt-4 mb-5 mx-md-4 shadow">    
      <div class="table-responsive-sm">
        <table class="table m-0">
          <thead class="table-light">
            <tr class="text-center fs-5" >
              <th scope="col" colspan="6" class="py-3"><span class="text-primary">Listado de Reservas de</span> <span class="text-success">{{$userName}}</span></th> 
            </tr>
            <tr class="text-center">
              <th scope="col" class="ps-4">Fecha Solicitud</th>
              <th scope="col">Hora Inicio</th>
              <th scope="col">Hora Fin</th>
              <th scope="col">Fecha Confirmación</th>
              <th scope="col">Estado Reserva</th>    
              <th scope="col" class="pe-4" style="text-align: left;">Motivo</th>   
            </tr>
          </thead>
          <tbody>
          @if(!empty($reservasUsuario) && count($reservasUsuario) > 0)
            @foreach($reservasUsuario as $item)
            <tr class="text-center">
              <td class="ps-4">{{ \Carbon\Carbon::parse($item->fechaSolicitud)->format('d/m/Y')}}</td>
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
              <td class="glosaTable pe-4">{{$item->motivo}}</td>
            </tr>
            @endforeach
            @else
            <tr>
              <td colspan="6">
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
      <div class="row mt-3">
        <div class="col-7 offset-2 col-md-5 offset-md-5 ">
          {{ $reservasUsuario->links()}}
        </div>
      </div>
    </div>
    </div>
  </div>
  </form>
</div>