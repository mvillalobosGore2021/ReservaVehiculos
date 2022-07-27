<div>
  <form>
    <x-menureserva />




    <div class="table-responsive mx-5">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th scope="col" colspan="7" class="text-center">Julio</th>
          </tr>
          <tr>
            <th scope="col">Lun</th>
            <th scope="col">Mar</th>
            <th scope="col">Mie</th>
            <th scope="col">Jue</th>
            <th scope="col">Vie</th>
            <th scope="col">Sab</th>
            <th scope="col">Dom</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>2</td>
            <td>3</td>
            <td>4</td>
            <td>5</td>
            <td>6</td>
            <td>7</td>
          </tr>
          <tr>
            <td>1</td>
            <td>2</td>
            <td>3</td>
            <td>4</td>
            <td>5</td>
            <td>6</td>
            <td>7</td>
          </tr>
          <tr>
            <td>1</td>
            <td>2</td>
            <td>3</td>
            <td>4</td>
            <td>5</td>
            <td>6</td>
            <td>7</td>
          </tr>
          <tr>
            <td>1</td>
            <td>2</td>
            <td>3</td>
            <td>4</td>
            <td>5</td>
            <td>6</td>
            <td>7</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="row ms-0 ms-md-4 mt-md-4">
      <div class="col-12  mt-3 col-md-3 mt-md-0">
        <div class="row">
          <div class="col-12">
            <label>Fecha Inicio Reserva {{$fechaInicio}}</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-calendar4"></i>
              </span>
              <input type="date" wire:model.debounce.500ms="fechaInicio" wire:loading.attr="disabled" wire:target="thirdStepSubmit,back" class="date-ini form-control" autocomplete="off">
            </div>
          </div>
          @error('fechaInicio')
          <div class="col-12">
            <span class="colorerror">{{ $message }}</span>
          </div>
          @enderror
        </div>
      </div>
      <div class="col-12 mt-3 col-md-3 mt-md-0">
        <div class="row">
          <div class="col-12">
            <label>Fecha Fin Reserva</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-calendar4"></i>
              </span>
              <input type="date" wire:model.debounce.500ms="fechaFin" wire:loading.attr="disabled" wire:target="thirdStepSubmit,back" class="date-fin form-control" autocomplete="off">
            </div>
          </div>
          @error('fechaFin')
          <div class="col-12">
            <span class="colorerror">{{ $message }}</span>
          </div>
          @enderror
        </div>
      </div>
    </div>

    <div class="row ms-0 ms-md-4 mt-md-4">
      <div class="col-12  mt-3 col-md-3 mt-md-0">
        <div class="row">
          <div class="col-12">
            <label>Hora Inicio Reserva</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-alarm"></i>
              </span>
              <input type="time" wire:loading.attr="disabled" wire:target="thirdStepSubmit,back" class="time-ini form-control" wire:model.debounce.500ms="horaInicio" placeholder="Inicio" autocomplete="off">
            </div>
          </div>
          @error('horaInicio')
          <div class="col-12">
            <span class="colorerror">{{ $message }}</span>
          </div>
          @enderror
        </div>
      </div>
      <div class="col-12 mt-3 col-md-3 mt-md-0">
        <div class="row">
          <div class="col-12">
            <label>Hora Fin Reserva</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-alarm"></i>
              </span>
              <input type="time" wire:loading.attr="disabled" wire:target="thirdStepSubmit,back" class="time-fin form-control" wire:model.debounce.500ms="horaFin" placeholder="Termino" autocomplete="off">
            </div>
          </div>
          @error('horaFin')
          <div class="col-12">
            <span class="colorerror">{{ $message }}</span>
          </div>
          @enderror
        </div>
      </div>
    </div>




  </form>

</div>