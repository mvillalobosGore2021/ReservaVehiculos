<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Reserva de Vehículos</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
   

</head>

<body style="background-color: #F5F8FF;font-family:Nunito;overflow-x:hidden;">
    {{ $slot }}
    
    <div class="text-center mx-5 mb-4 mt-5 text-secondary">
        <hr>
        <span class="d-block">© {{ \Carbon\Carbon::parse(Carbon\Carbon::now())->format('Y')}} Reserva de Vehículos - Desarrollo Interno Unidad de Informática.</span>
        <span class="d-block py-2">Gobierno Regional del Biobío - Avenida Prat 525 - Mesa Central 56-41-2405700 - Concepción, Región del Biobío, Chile.</span>
        <hr>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
</body>

</html>