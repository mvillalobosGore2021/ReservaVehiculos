<x-guest-layout>
    <div class="row justify-content-center mt-4 mx-2 mx-md-5">
        <div class="col-md-2 col-4 mt-3">
            <img src="{{ asset('images/logo-header-200x300.png') }}" width="130" height="180">
        </div>
    </div>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="row justify-content-center mt-4 mx-2 mx-md-5">
            <div class="col-12 col-md-6 px-md-5">
                <div class="card shadow">
                    <div class="card-header text-center fs-5 fw-bold py-2">
                        Ingreso a Sistema de Reservas de Vehículos
                        @if (session('status'))
                          {{ session('status') }}
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row ps-3">
                            <div class="col-12 px-2 px-md-3 pt-2">
                                <label>Correo Institucional</label>
                                <div class="input-group">
                                    <span class="input-group-text px-2 py-0">
                                        <i class="bi bi-envelope" style="font-size:1rem;"></i>
                                    </span>
                                    <input type="email" name="email" class="form-control" id="email" placeholder="Correo" required="required" autofocus="autofocus">
                                </div>
                            </div>

                            <div class="col-12 px-2 px-md-3 pt-2">
                                <label>Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text px-2 py-0">
                                        <i class="bi bi-key" style="font-size:1.6rem;"></i>
                                    </span>
                                    <input type="password" name="password" class="form-control" id="password" placeholder="Contraseña" required="required" autocomplete="current-password">
                                    <span class="input-group-text bg-white px-2 py-0" style="cursor:pointer;">
                                        <i class="bi bi-eye-fill" id="togglePassword" style="font-size:1.2rem;"></i>
                                    </span>
                                </div>
                                <div class="mt-2" style="color: #ff0000;font-size:1rem;">
                                    @if ($errors->any())
                                     Correo o Contraseña incorrecta
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 text-end pt-3 pt-md-4 pe-3">
                                <button type="submit" class="btn btn-dark py-1">Ingresar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function(e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('bi-eye-slash-fill');
        });
    </script>

</x-guest-layout>