<div id="menureserva">
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow mb-5 ps-2">
        <div class="container-md">
            <!-- <img src="{{ asset('images/logo-menu-001.png') }}" alt="" width="170" height="80"> -->
            <x-logogoremenu />

            <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span> 
        </button> -->

            <button class="navbar-toggler hamburger hamburger--spin" type="button" onclick="toggleBurgerMenu()" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <b class="text-secondary pe-2">Menú</b>  <span class="hamburger-box">
                     <span class="hamburger-inner"></span>
                </span>
            </button>

            <div class="collapse navbar-collapse ps-md-4" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li><hr></li>
                    <li class="nav-item pt-3 pt-md-2 ps-2 ps-md-0">
                        <!-- <a class="nav-link  @if(request()->routeIs('reserva')) link-primary @else link-secondary @endif" aria-current="page" href="{{ route('reserva') }}">
                        Reservar Vehiculo
                    </a> -->
                        <a class="@if(request()->routeIs('reserva')) link-primary custom-underline @else link-secondary hover-underline-animation @endif" aria-current="page" href="{{ route('reserva') }}">
                            Calendario de Reservas
                        </a>
                    </li>                   
                    <li class="nav-item pt-2 ps-2 ps-md-3">
                        <a class=" my-0  @if(request()->routeIs('listarreservas')) link-primary custom-underline @else link-secondary hover-underline-animation @endif" aria-current="page" href="{{ route('listarreservas') }}">
                            Consultar Mis Reservas
                        </a>
                    </li>
                    @if ($flgAdmin == 1) 
                    <li class="nav-item pt-2 ps-2 ps-md-3">
                        <a class=" @if(request()->routeIs('solicitudesreserva')) link-primary custom-underline @else link-secondary hover-underline-animation @endif" aria-current="page" href="{{ route('solicitudesreserva') }}">
                            Gestionar Reservas
                        </a>
                    </li>
                    @endif
                    
                    <!-- <li class="nav-item">
                    <button type="button" class="btn btn-link">Btn Link</button>
                </li>             -->
                </ul>
                <div class="d-flex justify-content-center pe-1 pe-md-5 mb-4 mb-md-2 pt-2">

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <span class="d-block fst-italic text-secondary ps-2 pb-2" style="font-size:0.95rem;">
                            <i class="bi bi-person-circle"></i> {{$userName}}
                        </span>

                        <a href="{{ route('logout') }}" class="btn btn-outline-primary btn-sm d-block p-1" style="font-size:0.97rem;" role="button" onclick="event.preventDefault();
                    this.closest('form').submit();">
                            <!-- <span class="d-inline fw-bold" style="font-size:1.13rem;"><i class="bi bi-door-open"></i></span> <span class="d-inline">Cerrar Sesi&oacute;n</span> -->
                            <span class="d-inline" style="font-size:1.12rem;font-weight:700;"><i class="bi bi-power"></i></span> <span class="d-inline" style="font-size:0.96rem;">Cerrar Sesi&oacute;n</span>
                        </a>
                        <div class="d-block pt-2 ps-2 pb-1" style="font-style:italic;">
                            <a href="{{ route('cambiarpass') }}" class="d-block" style="font-size:0.95rem;">Cambiar Contraseña</a>

                        </div>
                    </form>

                </div>
            </div>
        </div>
        
    </nav>
    
    <script>
        function toggleBurgerMenu() {
            const menuBurger = document.querySelector(".hamburger");
            menuBurger.classList.toggle('is-active');
        }
    </script>
</div>