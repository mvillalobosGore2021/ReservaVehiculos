<!DOCTYPE html>
<html>

<head>
  <title>Gobierno Regional del Bio Bio</title>
</head>

<body>
  <table>
    <tr style="width:150px;">
      <td rowspan="14" width="35"></td>
    </tr>
    <tr>
      <td>
        <img src="https://sitio.gorebiobio.cl/wp-content/themes/html5blank/img/auxi/logo-header-200x300.png" alt="" height="160" width="120">
    </td>
    <td></td>
    </tr>
    <tr>
      <td colspan="2" height="10">
        <h3>{{ $mailData['titulo'] }}</h3>
      </td>
    </tr>
    <tr>
      <td colspan="2" height="15">
        <hr>
      </td>
    </tr>
    <tr>
      <td><b>Funcionario:</b></td>
      <td>{{ $mailData['funcionario'] }}</td>
    </tr>
    <tr>
      <td><b>Fecha Reserva:</b></td>
      <td>{{ $mailData['fechaReserva'] }}</td>
    </tr>
    <tr>
      <td><b>Hora Inicio:</b></td>
      <td>{{ $mailData['horaInicio'] }}</td>
    </tr>
    <tr>
      <td><b>Hora Fin:</b></td>
      <td>{{ $mailData['horaFin'] }}</td>
    </tr>
    <tr>
      <td><b>Uso de Vehiculo Personal:</b></td>
      <td>{{ $mailData['usaVehiculoPersonal'] }}</td>
    </tr>
    <tr>
      <td><b>Motivo:</b></td>
      <td>{{ $mailData['motivo'] }}</td>
    </tr>
    <tr>
      <td colspan="2">
        <hr>
      </td>
    </tr>
  </table>
</body>

</html>