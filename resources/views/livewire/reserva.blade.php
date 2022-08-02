<div>
  <form>
    <x-menureserva />

    <!-- Modal -->
    <div wire:ignore.self class="modal fade pt-0" id="modalReserva" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-xl modal-dialog-scrollable pt-1">
        <div class="modal-content">
          <div class="modal-header bg-light">
            <h5 class="modal-title ps-3 text-primary" id="modalReservaLabel">Ingrese los Datos de Su Reserva</h5>
            <button type="button" class="btn-close" onclick="ocultarModal()"></button>
          </div>
          <div class="modal-body">
            <!-- <input type="text" id="myInput" class="form-control"> -->
            <div class="row">
              <div class="col-12 col-md-6 ps-4">
                <div class="row">
                  <div class="col-12 py-2 h5 text-success">
                    Dia Reserva: {{$fechaModal}}
                  </div>
                </div>
                <div class="row">
                  <div class="col-12 pb-3 col-md-6 mt-md-0">
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
                      <div class="col-12 pb-1">
                        <span class="colorerror">{{ $message }}</span>
                      </div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-12 mt-3 col-md-6 mt-md-0">
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
                      <div class="col-12 pb-1">
                        <span class="colorerror">{{ $message }}</span>
                      </div>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="row pb-3">
                  <div class="col-12">
                    <textarea wire:model.debounce.500ms="motivo" placeholder="Motivo de la reserva (Máximo 500 caracteres)" class="form-control" maxlength="500" rows="6"></textarea>
                  </div>
                  @error('motivo')
                  <div class="col-12">
                    <span class="colorerror">{{ $message }}</span>
                  </div>
                  @enderror
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-check form-switch" data-tippy-content="Proponer uso de vehiculo personal con devolución del costo por gasto de combustible">
                      <label class="form-check-label text-secondary" style="font-style:italic;" for="flgUsoVehiculoPersonal">
                        Usar Vehiculo Personal con Devolución de Combustible
                      </label>
                      <input wire:model.debounce.500ms="flgUsoVehiculoPersonal" class="form-check-input" wire:loading.attr="disabled" wire:target="firstStepSubmit" type="checkbox" id="flgUsoVehiculoPersonal">
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 col-md-6 px-3">
                <div class="table-responsive-sm mx-4">
                  <table class="table">
                    <!-- table-bordered -->
                    <thead>
                      <tr>
                        <th scope="col" colspan="4" class="text-center text-success">
                          Reservas realizadas para el día {{$fechaModal}}
                        </th>
                      </tr>
                      <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Hora Inicio</th>
                        <th scope="col">Hora Fin</th>
                        <th scope="col">Estado</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if(!empty($reservasFechaSel) && count($reservasFechaSel) > 0)
                      @foreach($reservasFechaSel as $item)
                      <tr>
                        <th scope="row">{{$item->name}}</th>
                        <td>{{$item->horaInicio}}</td>
                        <td>{{$item->horaFin}}</td>
                        <td>{{$item->codEstado}}</td>
                      </tr>
                      @endforeach
                      @else
                      <tr>
                        <td colspan="4">
                          <div class="alert alert-info border border-info d-flex justify-content-center my-3 mx-2 my-md-4" role="alert">
                            <span class="fs-4 pe-2 pe-md-3">
                              <i class="bi bi-info-circle-fill"></i></span>
                            <span class="fs-6 fst-italic pt-1">
                              No existen reservas para el día seleccionado
                            </span>
                          </div>
                        </td>
                      </tr>
                      @endif
                    </tbody>
                  </table>
                </div>

                @if (session()->has('exceptionMessage'))
                <div class="row">
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
                
              </div>
            </div>
          </div>
          <div class="modal-footer bg-light pe-5">
            <button type="button" class="btn btn-danger" onclick="ocultarModal();">Cerrar</button>
            <button type="button" class="btn btn-primary" wire:click="solicitarReserva()">Solicitar Reserva</button>

          </div>
        </div>
      </div>
    </div>

    <div class="table-responsive-sm mx-4">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th scope="col" colspan="7">
              <div class="row">
                <div class="col-12 col-md-5 ps-md-0">
                  <div class="input-group py-3 justify-content-center">
                    <button wire:click="getCalendarMonth(0)" class="btn {{$flgNextMonth == 0 ? 'btn-primary':'btn-outline-primary'}}" type="button">{{$monthNowStr}}</button>
                    <button wire:click="getCalendarMonth(1)" class="btn {{$flgNextMonth == 1 ? 'btn-primary':'btn-outline-primary'}}" type="button">{{$nextMontStr}}</button>
                  </div>
                </div>
                <div class="col-12 col-md-3 py-3 text-center ps-md-0">
                  <span class="h4">
                    @if($flgNextMonth == 0)
                    {{$monthNowStr}} {{$yearNow}}
                    @else
                    {{$nextMontStr}} {{$yearNextMont}}
                    @endif
                  </span>
                </div>
              </div>
            </th>
          </tr>
          <tr>
            <th scope="col" class="thDaysofweek">Lun</th>
            <th scope="col" class="thDaysofweek">Mar</th>
            <th scope="col" class="thDaysofweek">Mie</th>
            <th scope="col" class="thDaysofweek">Jue</th>
            <th scope="col" class="thDaysofweek">Vie</th>
            <th scope="col" class="thDaysofweek">Sab</th>
            <th scope="col" class="thDaysofweek">Dom</th>
          </tr>
        </thead>
        <tbody>
          @php($countDayWeek = 0)
          @php($countWeek = 0)
          @php($countDay = 1)
          @php($flgPrintDay = 0)

          @for($i=1; $i < ($cantDaysMonth + $firstDayMonth + $lastDayMonth); $i++) @php($countDayWeek++) @if ($countDayWeek==1) <tr>
            @endif

            @if ($i == $firstDayMonth)
            @php($flgPrintDay = 1)
            @endif

            @if ($flgPrintDay == 1 && ($countDay < ($cantDaysMonth+1))) <td class="bgcolorday thDaysofweek" wire:click="setFechaModal('{{$countDay}}.{{$monthSel}}.{{$yearSel}}')">
              {{$countDay}}</td>
              @php($countDay++)
              @else
              <td class="bg-light"></td>
              @endif

              @if ($countDayWeek == 7)
              @php($countDayWeek = 0)
              @php($countWeek++)
              </tr>
              @endif

              @if ($countWeek > 4)
              @php($i = ($cantDaysMonth + $firstDayMonth + $lastDayMonth))
              @endif

              @endfor
        </tbody>
      </table>
    </div>
  </form>


  <script>
    // const myModal = document.getElementById('modalReserva')
    // // const myInput = document.getElementById('myInput')
    // myModal.addEventListener('shown.bs.modal', () => {
    //   // myInput.focus() 
    // })


    const container = document.getElementById("modalReserva");
    const modal = new bootstrap.Modal(container);

    window.addEventListener('showModal', event => {
      modal.show();
    });

    function ocultarModal() {
      //myModal2.show();
      modal.hide();
    }
  </script>

</div>