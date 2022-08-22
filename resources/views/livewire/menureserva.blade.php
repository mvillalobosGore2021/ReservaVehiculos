<div id="menureserva">
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow mb-5 ps-2">
    <div class="container-md">
            <!-- <img src="{{ asset('images/logo-menu-001.png') }}" alt="" width="170" height="80"> -->
        <x-logogoremenu/> 

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse ps-md-4" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item pt-2">   
                    <!-- <a class="nav-link  @if(request()->routeIs('reserva')) link-primary @else link-secondary @endif" aria-current="page" href="{{ route('reserva') }}">
                        Reservar Vehiculo
                    </a> -->
                    <a class="hover-underline-animation @if(request()->routeIs('reserva')) link-primary @else link-secondary @endif" aria-current="page" href="{{ route('reserva') }}">
                        Reservar Vehiculo
                    </a>
                </li>
                <li class="nav-item pt-2 px-md-2">
                    <a class="hover-underline-animation @if(request()->routeIs('listarreservas')) link-primary @else link-secondary @endif" aria-current="page" href="{{ route('listarreservas') }}">
                        Mis Reservas                        
                    </a>
                </li>      
                @if ($flgAdmin  == 1)
                <li class="nav-item pt-2 px-md-2"> 
                <a class="hover-underline-animation @if(request()->routeIs('solicitudesreserva')) link-primary @else link-secondary @endif" aria-current="page" href="{{ route('solicitudesreserva') }}">
                        Solicitudes de Reservas
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
                <span class="d-block fst-italic text-secondary pb-2" style="font-size:15px;">
                  <i class="bi bi-person-circle"></i> {{$userName}}
                </span>
                <a href="{{ route('logout') }}" class="btn btn-outline-primary ms-2" role="button" onclick="event.preventDefault();
                    this.closest('form').submit();">
                    Cerrar Sesi&oacute;n
                </a>                
            </form>
            </div>
        </div>
    </div>
</nav>
</div>
