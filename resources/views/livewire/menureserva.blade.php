<div>
<nav id="menureserva" class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow mb-5">
    <div class="container-md">
            <!-- <img src="{{ asset('images/logo-menu-001.png') }}" alt="" width="170" height="80"> -->
        <x-logogoremenu/> 

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse ps-md-4" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link link-nav @if(request()->routeIs('reserva')) link-primary @else link-secondary @endif" aria-current="page" href="{{ route('reserva') }}">
                        Reservar Vehiculo
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('listarreservas')) link-primary @else link-secondary @endif" aria-current="page" href="{{ route('listarreservas') }}">
                        Mis Reservas                        
                    </a>
                </li>      
                @if ($flgAdmin  == 1)
                <li class="nav-item">
                    <a class="nav-link link-secondary" aria-current="page" href="#">
                        Solicitudes de Reservas
                    </a>
                </li>
                @endif
                <!-- <li class="nav-item">
                    <button type="button" class="btn btn-link">Btn Link</button>
                </li>             -->
            </ul>     
              <form method="POST" action="{{ route('logout') }}" class="pe-1 pe-md-5 pb-4 pb-md-1">
                @csrf
                <span class="d-block pt-3 fst-italic text-secondary text-center pb-2" style="font-size:15px;">Bienvenido {{$userName}}</span>
                <a href="{{ route('logout') }}" class="btn btn-outline-primary ms-4 mb-3" role="button" onclick="event.preventDefault();
                    this.closest('form').submit();">
                    Cerrar Sesi&oacute;n
                </a>
                
            </form>

        </div>
    </div>
</nav>
</div>
