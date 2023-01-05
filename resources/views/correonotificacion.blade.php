<!DOCTYPE html>
<html>

<head>
  <title>Gobierno Regional del Bio Bio</title>

  <style>
#table2 {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 600px;
}

#table2 td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

hr {
  color:#D8DCE2;
}



</style>
  
</head>

<body>  
<center>
  <table width="640">
   <tr>
      <td colspan="2">
        <hr>
      </td>
    </tr>
    <tr>      
      <td colspan="2" style="margin:0px;padding:0px;">
        <img alt="" height="160" width="650" src="{{ public_path('images/encabezadocorreo.png')}}">

        <!-- <img alt="" height="160" width="650" src="{{ asset('images/encabezadocorreo.png') }}"> -->
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <hr>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="padding-top:10px;padding-bottom:11px;color:#3980BB;font-size:20px;font-weight:550;font-family: arial, sans-serif;text-align:justify;">
        {{ $mailData['asunto'] }}
      </td>
    </tr>
    <tr>
    <td colspan="2" style="padding-bottom:10px;padding-left:15px;padding-right:16px;text-align:justify;font-size:15px;">
    @if ($mailData['flgConductor'] == true)
       Estimado <b>{{$mailData['nombreConductor']}}</b><br><br>
       {!! htmlspecialchars_decode(nl2br($mailData['resumen'])) !!}
    @else
      {{ $mailData['sexo'] == 'F' ? 'Estimada': 'Estimado' }} <b>{{ !empty($mailData['nomAdmin']) ? $mailData['nomAdmin'] : $mailData['funcionario'] }}</b>: <br><br>
       Le informamos que {!! htmlspecialchars_decode(nl2br($mailData['resumen'])) !!} <b>{{ $mailData['fechaReserva'] }}</b>.
       
       @if ($mailData['codEstado'] == 1 && empty($mailData['nomAdmin']))
         <br><br><span style="background-color:#EF3B2D;color:white;">Importante!: </span><span style="background-color:#3980BB;color:white;">Su solicitud de reserva aún no se encuentra confirmada, se le notificará a su correo cuando su reserva cambie de estado.</span> 
       @endif

       @if ($mailData['codEstado'] == 3 && !empty($mailData['motivoAnulacion']))
         <br><br><span style="background-color:#EF3B2D;color:white;"><b>Motivo de la Anulación:</b></span> <span style="background-color:#3980BB;color:white;">{{ $mailData['motivoAnulacion'] }}</span>
       @endif       
    @endif
       <br><br>
       Saludos cordiales.
      </td>
    </tr>

    <tr>
      <td colspan="2" height="15">
        <hr>
      </td>
    </tr>
</table>
    </center>
    <br>
    <center>     
  <table id="table2">
  <tr>
      <td style="color:#3980BB;text-align:center;background-color: #F8F9FA;" height="40" colspan="2"> 
         <b>Resumen de la reserva</b>
      </td>
    </tr>
    <tr> 
      <td style="color:#282D33;width:185px;"><b>{{ $mailData['sexo'] == 'F' ? 'Funcionaria': 'Funcionario' }}:</b></td> 
      <td style="color:#746873">{{ $mailData['funcionario'] }}</td>
    </tr>
    <tr>
      <td><b>Fecha Creación:</b></td>
      <td style="color:#746873">{{ $mailData['fechaCreacion'] }}</td>
    </tr>
    <tr>
      <td><b>Fecha Reserva:</b></td>
      <td style="color:#746873">{{ $mailData['fechaReserva'] }}</td>
    </tr>
    <tr>
      <td><b>Hora Inicio:</b></td>
      <td style="color:#746873">{{ $mailData['horaInicio'] }}</td>
    </tr>
    <tr>
      <td><b>Hora Fin:</b></td>
      <td style="color:#746873">{{ $mailData['horaFin'] }}</td>
    </tr>   
    <tr>
      <td><b>Estado:</b></td>
      <td style="color:#746873">{{ $mailData['descripcionEstado'] }}</td>
    </tr> 
    @if ($mailData['codEstado'] == 2) 
    <tr>
      <td><b>Vehículo:</b></td>
      <td style="color:#746873">{{$mailData['descripcionVehiculo']}}</td>
    </tr> 
    <tr>
      <td><b>Conductor:</b></td>
      <td style="color:#746873">{{$mailData['nombreConductor']}}</td>
    </tr>    
    @endif    
    <tr>
      <td><b>Destino:</b></td>
      <td style="color:#746873">{{$mailData['nombreComuna']}}</td>
    </tr> 
    <tr>
      <td><b>Motivo:</b></td>
      <td style="color:#746873;text-align:justify;">{{ $mailData['motivo'] }}</td>
    </tr>    
</table>
</center>
    <center>
  <table width="640">
    <tr>
    <td colspan="2" style="padding-top:50px;">
        <img alt="" height="160" width="650" src="{{ public_path('images/footer_gore.jpg')}}">
    </td>
    </tr>
    <tr>
      <td colspan="2" style="font-size:15px;">
        <hr> 
        <br>
        <center style="padding-top: 35px;padding-bottom: 10px;color:#746873;">
           © {{ \Carbon\Carbon::parse(Carbon\Carbon::now())->format('Y')}} Reserva de Vehículos - Desarrollo Interno Unidad de Informática.
        </center>
        <center style="padding-bottom: 35px;color:#746873;">Gobierno Regional del Biobío - Avenida Prat 525 - Mesa Central 56-41-2405700</center>
        <center style="color:#746873;">Concepción, Región del Biobío, Chile.</center>
        <br>
        <hr>
      </td>
    </tr>
  </table>
</center>
<br><br><br><br><br><br>
</body>
</html>