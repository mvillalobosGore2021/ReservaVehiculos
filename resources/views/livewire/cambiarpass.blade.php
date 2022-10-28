<div> 
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
                                    <input type="password" name="current_password" wire:model="current_password" class="form-control" id="current_password" placeholder="Contraseña">
                                    <span class="input-group-text bg-white px-2 py-0" style="cursor:pointer;">
                                        <i class="bi bi-eye-fill" id="togglePasswordCurrent" onclick="togglePassword('#current_password', '#togglePasswordCurrent')" style="font-size:1.2rem;"></i>
                                    </span>
                                </div>
                            </div>
                            @error('current_password')
                            <div class="col-12 pb-1">
                                <span class="colorerror">{{ $message }}</span>
                            </div>
                            @enderror

                            <div class="col-12 px-2 px-md-3 pt-2">
                                <label>Nueva Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text px-2 py-0">
                                        <i class="bi bi-key" style="font-size:1.6rem;"></i>
                                    </span>
                                    <input type="password" name="password" wire:model="password" class="form-control" id="password" placeholder="Nueva Contraseña">
                                    <span class="input-group-text bg-white px-2 py-0" style="cursor:pointer;">
                                        <i class="bi bi-eye-fill" id="togglePasswordNva" onclick="togglePassword('#password', '#togglePasswordNva')" style="font-size:1.2rem;"></i>
                                    </span>
                                </div>
                            </div>
                            @error('password')
                            <div class="col-12 pb-1">
                                <span class="colorerror">{{ $message }}</span>
                            </div>
                            @enderror
                            <div class="col-12 px-2 px-md-3 pt-2">
                                <label>Confirmar Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text px-2 py-0">
                                        <i class="bi bi-key" style="font-size:1.6rem;"></i>
                                    </span>
                                    <input type="password" name="password_confirmation" wire:model="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirmar Contraseña">
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
                                <button wire:click="cambiarPass" class="btn btn-dark py-1">Guardar</button> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

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