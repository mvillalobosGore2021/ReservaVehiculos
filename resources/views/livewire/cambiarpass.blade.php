<div>
    <form>
        @csrf
        <div class="row justify-content-center mx-2 mx-md-5">
            <!-- <div class="col-12 col-md-8 px-1 d-none" id="msjCambioPass">
                <div class="alert alert-success border border-success" role="alert">
                    <span class="fs-4 pe-2">
                        <i class="bi bi-info-circle-fill"></i></span>
                    <span class="fs-6 fst-italic pt-1">
                        Su contraseña se ha modificado exitosamente, debe iniciar sesión con su nueva contraseña.
                    </span>
                </div>
            </div> -->

            <!-- style="visibility:hidden;" -->
            
             <div class="col-12 col-md-6 px-md-5 pt-1 pt-md-4">
                <div class="card shadow">
                    <div class="card-header text-center fs-5 fw-bold py-2">
                        Cambiar Contraseña
                    </div>
                    <div class="card-body">
                        <div class="row ps-3">
                            <div class="col-12 px-2 px-md-3 pt-2">
                                <label>Contraseña Actual</label>
                                <div class="input-group">
                                    <span class="input-group-text px-2 py-0">
                                        <i class="bi bi-key" style="font-size:1.6rem;"></i>
                                    </span>
                                    <input type="password" name="current_password" wire:model.debounce.250ms="current_password" class="form-control" id="current_password" placeholder="Contraseña">
                                    <span class="input-group-text bg-white px-2 py-0" style="cursor:pointer;">
                                        <i class="bi bi-eye-fill" id="togglePasswordCurrent" onclick="togglePassword('#current_password', '#togglePasswordCurrent')" style="font-size:1.2rem;"></i>
                                    </span>
                                </div>
                            </div>
                            @error('current_password')
                            <div class="col-12">
                                <span class="colorerror">{{ $message }}</span>
                            </div>
                            @enderror

                            <div class="col-12 px-2 px-md-3 pt-2">
                                <label>Nueva Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text px-2 py-0">
                                        <i class="bi bi-key" style="font-size:1.6rem;"></i>
                                    </span>
                                    <input type="password" name="password" wire:model.debounce.250ms="password" class="form-control" id="password" placeholder="Nueva Contraseña">
                                    <span class="input-group-text bg-white px-2 py-0" style="cursor:pointer;">
                                        <i class="bi bi-eye-fill" id="togglePasswordNva" onclick="togglePassword('#password', '#togglePasswordNva')" style="font-size:1.2rem;"></i>
                                    </span>
                                </div>
                            </div>
                            @error('password')
                            <div class="col-12">
                                <span class="colorerror">{{ $message }}</span>
                            </div>
                            @enderror
                            <div class="col-12 px-2 px-md-3 pt-2">
                                <label>Confirmar Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text px-2 py-0">
                                        <i class="bi bi-key" style="font-size:1.6rem;"></i>
                                    </span>
                                    <input type="password" name="password_confirmation" wire:model.debounce.250ms="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirmar Contraseña">
                                    <span class="input-group-text bg-white px-2 py-0" style="cursor:pointer;">
                                        <i class="bi bi-eye-fill" id="togglePasswordConfirm" onclick="togglePassword('#password_confirmation', '#togglePasswordConfirm')" style="font-size:1.2rem;"></i>
                                    </span>
                                </div>
                            </div>
                            @error('password_confirmation')
                            <div class="col-12 pb-1">
                                <span class="colorerror">{{ $message }}</span>
                            </div>
                            @enderror
                            <div class="col-12 text-end pt-3 pt-md-4 pe-3">
                                <button id="btnGuardar" class="btn btn-dark py-1" type="button" wire:click="cambiarPass" wire:loading.attr="disabled" wire:target="cambiarPass">
                                    <span wire:loading.class="spinner-border spinner-border-sm" wire:target="cambiarPass" role="status" aria-hidden="true"></span>
                                    Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-8 pt-3" style="visibility:hidden;" id="spinnerRedirect">
               <div>
                  <div class="spinner-grow text-primary" style="width: 1.2rem; height: 1.2rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <div class="spinner-grow text-secondary" style="width: 1.2rem; height: 1.2rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <div class="spinner-grow text-success" style="width: 1.2rem; height: 1.2rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <div class="fst-italic text-success fw-bold d-inline" style="font-size:15px;">
                  Redirigiendo a la página de inicio...Debe iniciar sesión con su nueva contraseña.
                  </div>
                </div>
            </div>
            <!-- <div class="col-12 pt-4 pt-md-5" id="spinnerRedirectEnc"> -->
            <!-- visibility:hidden para el pt -->
            <!-- <div class="row" id="spinnerRedirectBody"> -->
            <!-- d-none para que el elemento no ocupe espacio -->
            <!-- <div class="col-12">
                        <div class="loader">Loading...</div>
                    </div>
                    <div class="col-12 col-md-8 px-1" id="msjCambioPass">
                        <div class="alert alert-success border border-success" role="alert">
                            <span class="fs-4 pe-2">
                                <div class="loader">Loading...</div>
                            </span>
                            <span class="fs-6 fst-italic pt-1">
                                Su contraseña se ha modificado exitosamente, debe iniciar sesión con su nueva contraseña.
                            </span>
                        </div>
                    </div>
                    <div class="col-12">
                        <center><span class="fst-italic">Debe iniciar sesión con su nueva contraseña. Redirigiendo a la página de inicio...</span></center>
                    </div> 
                </div>
            </div> -->

        </div>



        @if (session()->has('flgGuardar'))
        <script>
            var element = document.getElementById("spinnerRedirect");
            element.style.visibility = "visible";
            // var element1 = document.getElementById("spinnerRedirectEnc");
            // var element2 = document.getElementById("spinnerRedirectBody");
            // var element3 = document.getElementById("msjCambioPass");
            // element1.style.visibility = "visible";
            // element2.classList.remove("d-none");
            // element3.classList.remove("d-none");            
            setTimeout(redirectLogin, 5000);
        </script>
        @endif
    </form>

    @if (session()->has('exceptionMessage'))
    <div class="row justify-content-center mt-4 mx-2 mx-md-5">
        <div class="col-12">
            <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:">
                    <use xlink:href="#exclamation-triangle-fill" />
                </svg>
                <div>
                    {{ session('exceptionMessage') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        function togglePassword(idInput, idIcon) {
            const inputPass = document.querySelector(idInput);
            const iconPass = document.querySelector(idIcon);

            const type = inputPass.getAttribute('type') === 'password' ? 'text' : 'password';
            inputPass.setAttribute('type', type);
            // toggle the eye slash icon
            iconPass.classList.toggle('bi-eye-slash-fill');
        }

        function redirectLogin() {
            window.location = "/login";
        }
    </script>
</div>