<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Reserva de Vehiculos</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
   

    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/scale-extreme.css" />
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/utils.css') }}">
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>   
</head>

<body class="font-sans antialiased" style="margin-top:170px;background-color: #F5F8FF;">
    <div class="container-md px-3 px-md-4" id="headPage">
        <!-- Page Content -->
        <main>
            <livewire:menureserva />         
            {{$slot}}       
        </main>

        @stack('modals')

        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script> -->

        @livewireScripts
    </div>
    <br><br><br><br><br><br>
    <div class="text-center mx-5 mb-4 text-secondary">
        <hr>
        <span class="d-block">© {{ \Carbon\Carbon::parse(Carbon\Carbon::now())->format('Y')}} Reserva de Vehículos - Desarrollo Interno Unidad de Informática.</span>
        <span class="d-block py-2">Gobierno Regional del Biobío - Avenida Prat 525 - Mesa Central 56-41-2405700 - Concepción, Región del Biobío, Chile.</span>
    <hr>
</div>
</body>
<script>

initToolTips();

window.addEventListener('iniTooltips', event => {
   initToolTips();
});

function initToolTips() {
   tippy('[data-tippy-content]', {
      touch: true, //Habilita Toolstips para moviles
      animation: 'scale-extreme',
      placement: 'bottom',
      duration: 450, //Tiempo que se demora el despliegue
      delay: 500, //Tiempo que se demora en aparecer
   });
}

// tippy('[data-tippy-content]', {
//     touch: true, //Habilita Toolstips para moviles
//     animation: 'scale-extreme',
//     placement: 'bottom',
//     duration: 450, //Tiempo que se demora el despliegue
//     delay: 500, //Tiempo que se demora en aparecer
// });


</script>

</html>