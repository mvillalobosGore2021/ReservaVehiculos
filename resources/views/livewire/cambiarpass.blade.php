<div>
    <form>
        @csrf
        <div class="row justify-content-center mt-5 mx-2 mx-md-5">
            <div class="col-12 col-md-6 px-md-5 mt-3">
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

        </div>
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
    </script>
</div>