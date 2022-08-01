<nav id="menureserva" class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow mb-5">
    <div class="container-fluid">
        <a class="navbar-brand ps-0 pe-0 ps-md-4" href="#">
            <img src="{{ asset('images/logo-menu-001.png') }}" alt="" width="170" height="80">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <!--active--> text-primary" aria-current="page" href="reserva">Reservar Vehiculo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <!--active--> text-primary" aria-current="page" href="#">Mis Reservas</a>
                </li>

                <!-- <li class="nav-item">
                            <a class="nav-link" href="#">Link</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Dropdown
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled">Disabled</a>
                        </li> -->
            </ul>            
            <form method="POST" action="{{ route('logout') }}" class="pe-1 pe-md-5 pb-4 pb-md-1">
                @csrf
                <a href="{{ route('logout') }}" class="btn btn-outline-primary pt-1" role="button" onclick="event.preventDefault();
                                    this.closest('form').submit();">
                    Cerrar Sesi&oacute;n
                </a>
            </form>
        </div>
    </div>
</nav>