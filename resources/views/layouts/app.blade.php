<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">


  <title>Reserva de Vehículos</title>

  <!-- Autor: Mario Villalobos P.
             Correo: mvillalobos@gorebiobio.cl             
             Fecha de creación: 09-08-2022
             Ultima modificiación:17-10-2022 MV
             Objetivo: Ingreso de solicitudes de reservas de vehiculos del Gobierno Regional
             Unidad: Informática -->

  <!-- Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

  <!-- Styles -->
  <!-- <link rel="stylesheet" href="{{ mix('css/app.css') }}"> -->

  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous"> -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/scale.css" />
  <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/scale-extreme.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
  <link rel="stylesheet" href="{{ asset('css/utils.css') }}">

  @livewireStyles
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>

  <!-- Scripts -->
  <!-- <script src="{{ mix('js/app.js') }}" defer></script> -->
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script defer src="https://unpkg.com/alpinejs@3.10.5/dist/cdn.min.js"></script>
  <script src="https://unpkg.com/@popperjs/core@2"></script>
  <script src="https://unpkg.com/tippy.js@6"></script>

  <!-- Ini DateRangePicker -->

  <link rel="stylesheet" type="text/css" media="all" href="{{ asset('css/daterangepicker.css') }}" />
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/moment.min.js"></script>
  <script type="text/javascript" src="{{ asset('js/daterangepicker.js') }}"></script>
  <!-- Fin DateRangePicker -->


</head>
<!-- class="font-sans antialiased" -->

<body style="margin-top:150px;background-color: #F5F8FF;font-family:Nunito;">
  <div class="container-md px-3 px-md-4" id="headPage">
    <!-- Page Content -->
    <main>
      <livewire:menureserva />
      {{$slot}}
    </main>


    @livewireScripts
  </div>
  <br>
  <div class="text-center mx-5 mb-4 text-secondary">
    <hr>
    <span class="d-block">© {{ \Carbon\Carbon::parse(Carbon\Carbon::now())->format('Y')}} Reserva de Vehículos - Desarrollo Interno Unidad de Informática.</span>
    <span class="d-block py-2">Gobierno Regional del Biobío - Avenida Prat 525 - Mesa Central 56-41-2405700 - Concepción, Región del Biobío, Chile.</span>
    <hr>
  </div>
  <br><br>
</body>
<script>
  // Ini DateRangePicker
  $('#demo').daterangepicker({
    "autoApply": true,
    "ranges": {
      "Today": [
        "2022-11-22T15:40:08.279Z",
        "2022-11-22T15:40:08.279Z"
      ],
      "Yesterday": [
        "2022-11-21T15:40:08.279Z",
        "2022-11-21T15:40:08.280Z"
      ],
      "Last 7 Days": [
        "2022-11-16T15:40:08.280Z",
        "2022-11-22T15:40:08.280Z"
      ],
      "Last 30 Days": [
        "2022-10-24T15:40:08.280Z",
        "2022-11-22T15:40:08.280Z"
      ],
      "This Month": [
        "2022-11-01T03:00:00.000Z",
        "2022-12-01T02:59:59.999Z"
      ],
      "Last Month": [
        "2022-10-01T03:00:00.000Z",
        "2022-11-01T02:59:59.999Z"
      ]
    },
    "alwaysShowCalendars": true,
    "startDate": "11/16/2022",
    "endDate": "11/22/2022"
  }, function(start, end, label) {
    console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
  });
  // Fin DateRangePicker

  initToolTips();

  var flgMediaQuerySmall = true;

  window.addEventListener('iniTooltips', event => {
    initToolTips();
  });

  function initToolTips() {
    tippy('[data-tippy-content]', {
      touch: false, //Habilita Toolstips para moviles
      animation: 'scale-extreme',
      placement: 'bottom',
      duration: 450, //Tiempo que se demora el despliegue
      delay: 500, //Tiempo que se demora en aparecer
      theme: 'tablereservas',
      // animation: 'rotate',
      // inertia: true,
    });

    tippy('.classTippy', {
      content(reference) {
        const id = reference.getAttribute('data-template');
        const template = document.getElementById(id);
        return template.innerHTML;
      },
      theme: 'tablereservas',
      allowHTML: true,
      touch: false, //Toolstips para moviles
      animation: 'scale-extreme',
      placement: 'bottom',
      duration: 450, //Tiempo que se demora el despliegue
      delay: 500, //Tiempo que se demora en aparecer
    });
  }

  window.addEventListener('moveScroll', event => {
    moveScrollById(event.detail.id);
  });

  function moveScrollById(id) {
    const menuReserva = document.getElementById("menureserva");

    const element = document.querySelector(id);
    var topPos = element.getBoundingClientRect().top - menuReserva.offsetTop + window.pageYOffset;

    if (flgMediaQuerySmall == true) { //Si la pantalla es menor a 700px se aumenta el offset a top
      topPos += 25;
    }

    window.scrollTo({
      top: topPos,
      behavior: 'smooth'
    });
  }

  window.addEventListener('moveScrollModal', event => {
    moveScrollModal();
  });

  function moveScrollModal() {
    const modalBody = document.getElementById("modalBody");
    //const topPos = modalBody.getBoundingClientRect().top;// - menuReserva.offsetTop + window.pageYOffset;

    modalBody.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  }

  window.addEventListener('movScrollModalById', event => {
    const modalBody = document.getElementById("modalBody");
    // alert(event.detail.id);
  
  
    const element = document.querySelector(event.detail.id);
    const topPos = element.getBoundingClientRect().top;

    modalBody.scrollTo({
      top: topPos - 109,
      behavior: 'smooth' 
    });
    setTimeout(function() { document.querySelector(event.detail.id+'Error').classList.add("shake-effect");}, 1000);
    document.querySelector(event.detail.id+'Error').classList.remove("shake-effect");
    
    //Rutina para mover todos los Errors
    // var arrInptError = (new String(event.detail.arrInptError)).split(',');
    // for (var i=0; i < arrInptError.length; i++) {
    //    var elementInptError = document.querySelector("#id"+arrInptError[i]+"Error");
    // setTimeout(function() { elementInptError.classList.add("shake-effect");}, 100);
    //    elementInptError.classList.add("shake-effect");
    //    setTimeout(function() {}, 5000);
    //    elementInptError.classList.remove("shake-effect");
    // }

  });



  window.addEventListener('moveScroll', event => {
    moveScrollById(event.detail.id); 
  });

  window.addEventListener('moveScrollModal', event => {
    moveScrollModal();
  });

  function shakeButton() {
    var btnBuscar = document.getElementById('btnBuscar');
    btnBuscar.classList.add("shake-effect"); 
  }

  function deleteClassShake() {
    var btnBuscar = document.getElementById('btnBuscar');
    btnBuscar.classList.remove("shake-effect");
  }

  window.addEventListener('swal:information', event => {
    const Toast = Swal.mixin({
      toast: true,
      position: 'center', //((event.detail.position != 'center') ? event.detail.position:'center'),
      showConfirmButton: false,
      timer: 5500,
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

  

  window.addEventListener('swal:confirmAnular', event => {
    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-primary m-2',
        cancelButton: 'btn btn-danger m-2'
      },
      buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
      title: event.detail.title,
      html: event.detail.text,
      icon: 'warning',
      showCancelButton: true,
      showCloseButton: true,
      confirmButtonText: 'Confirmar',
      cancelButtonText: 'Cancelar',
      reverseButtons: false
    }).then((result) => {
      if (result.isConfirmed) {
        window.livewire.emit('anularReserva');
      }
    })
  });


  document.addEventListener('livewire:load', () => {
    //Anular desde el perfil User
    window.livewire.on('anularReserva', () => {
      var element = document.getElementById("spinnerAnularReserva");
      var element2 = document.getElementById("anularIcon");
      element.classList.add("spinner-border");
      element.classList.add("spinner-border-sm");
      element2.classList.add("d-none");

      document.getElementById("btnCerrar").disabled = true;
      document.getElementById("btnIconClose").disabled = true;     

      if (document.getElementById("btnGuardar") != undefined) {
        document.getElementById("btnGuardar").disabled = true;
      }

      if (document.getElementById("btnAnularReserva") != undefined) {
          document.getElementById("btnAnularReserva").disabled = true;
      } 

      if (document.getElementById("fechaSolicitud") != undefined) {
          document.getElementById("fechaSolicitud").disabled = true; 
      } 

      document.getElementById("horaInicio").disabled = true;
      document.getElementById("horaFin").disabled = true;    
      document.getElementById("cantPasajeros").disabled = true;
      document.getElementById("codComuna").disabled = true;
      document.getElementById("codDivision").disabled = true;
      document.getElementById("motivo").disabled = true;
      
      // document.getElementById("flgUsoVehiculoPersonal").disabled = true;
    });
  });

  //Inicio MediaQuery
  function myFunction(x) {
    if (x.matches) { // If media query matches 
      flgMediaQuerySmall = true;
    } else {
      flgMediaQuerySmall = false;
    }
  }

  var x = window.matchMedia("(max-width: 700px)");
  myFunction(x); // Call listener function at run time
  x.addListener(myFunction); // Attach listener function on state changes
  //Fin MediaQuery
</script>

</html>