<div>
<table class="table">
  <thead>
    <tr>
        <td colspan="4" class="text-center h5">
            Listado de Reservas 
        </td>
    </tr>
    <tr class="text-center">
      <th scope="col">Fecha Solicitud</th>
      <th scope="col">Hora Inicio</th>
      <th scope="col">Hora Fin</th>
      <th scope="col">Fecha Confirmación</th>
    </tr>
  </thead>
  <tbody>
    @foreach($reservasUsuario as $item)
    <tr class="text-center">
      <td>{{ \Carbon\Carbon::parse($item->fechaSolicitud)->format('d/m/Y')}}</td>
      <td>{{ \Carbon\Carbon::parse($item->horaInicio)->format('H:i')}}</td>
      <td>{{ \Carbon\Carbon::parse($item->horaFin)->format('H:i')}}</td>
      <td>{{ \Carbon\Carbon::parse($item->fechaConfirmacion)->format('d/m/Y')}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
</div>   
