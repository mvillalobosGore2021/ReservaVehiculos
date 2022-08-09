<div>
  <form>   
    <!-- Modal -->
    <div wire:ignore.self class="modal fade pt-0" id="modalReserva" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-xl modal-dialog-scrollable pt-1">
        <div class="modal-content">
          <div class="modal-header bg-light">
            <h5 class="modal-title ps-3 text-primary" id="modalReservaLabel">Ingrese los Datos de Su Reserva   {{$userName}}</h5>
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
                          <input type="time" wire:loading.attr="disabled" wire:target="solicitarReserva" class="time-ini form-control" wire:model.debounce.500ms="horaInicio" placeholder="Inicio" autocomplete="off">
                        </div>
                      </div>
                      @error('horaInicio')
                      <div class="col-12 pb-1">
                        <span class="colorerror">{{ $message }}</span>
                      </div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <div class="row">
                      <div class="col-12">
                        <label data-tippy-content="Hora estimada de regreso">Hora Fin Reserva</label>
                        <div class="input-group">
                          <span class="input-group-text">
                            <i class="bi bi-alarm"></i>
                          </span>
                          <input type="time" wire:loading.attr="disabled" wire:target="solicitarReserva" class="time-fin form-control" wire:model.debounce.500ms="horaFin" placeholder="Termino" autocomplete="off">
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
                <div class="row pt-3 pt-md-0 pb-3">
                  <div class="col-12">
                    <textarea wire:model.debounce.500ms="motivo" wire:loading.attr="disabled" wire:target="solicitarReserva" placeholder="Motivo de la reserva (Máximo 500 caracteres)" class="form-control" maxlength="500" rows="6"></textarea>
                  </div>
                  @error('motivo')
                  <div class="col-12">
                    <span class="colorerror">{{$message}}</span>
                  </div>
                  @enderror
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="form-check form-switch" data-tippy-content="Proponer uso de vehiculo personal con devolución del costo por gastos de combustible y peajes.">
                      <label class="form-check-label text-secondary" style="font-style:italic;" for="flgUsoVehiculoPersonal">
                         Usar Vehiculo Personal con Devolución de Combustible y Peajes.
                      </label>
                      <input wire:model.debounce.500ms="flgUsoVehiculoPersonal" class="form-check-input" wire:loading.attr="disabled" wire:target="solicitarReserva" type="checkbox" id="flgUsoVehiculoPersonal">
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12 col-md-6 px-3 pt-3 pt-md-1">
                <div class="table-responsive-sm mx-4">
                  <table class="table">
                    <!-- table-bordered -->
                    <thead>
                      <tr>
                        <th scope="col" colspan="4" class="text-center text-success pb-3">
                          Reservas realizadas para el día {{$fechaModal}}
                          <input type="hidden" wire:model="fechaModal"> 
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
                      @foreach($reservasFechaSel as $index => $item)
                      <tr>
                        <td>{{$item['name']}}</td>
                        <td>
                        {{ \Carbon\Carbon::parse($item['horaInicio'])->format('H:i')}}
                      </td>
                        <td>
                        {{ \Carbon\Carbon::parse($item['horaFin'])->format('H:i')}}
                        </td>
                        <td>{{$item['descripcionEstado']}}</td> 
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
            <button type="button" class="btn btn-danger" onclick="ocultarModal();"  wire:loading.attr="disabled" wire:target="solicitarReserva">
            Cerrar <i class="bi bi-x-circle pt-1"></i>
           </button>
            <button type="button" class="btn btn-primary" wire:click="solicitarReserva()"  wire:loading.attr="disabled" wire:target="solicitarReserva">
               {{$idReserva > 0 ? 'Modificar Reserva':'Solicitar Reserva'}}
               <span wire:loading.remove wire:target="solicitarReserva"><i class="bi bi-send pt-1"></i></span>
               <span wire:loading.class="spinner-border spinner-border-sm" wire:target="solicitarReserva" role="status" aria-hidden="true"></span>
            </button>
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

          @for($i=1; $i < ($cantDaysMonth + $firstDayMonth + $lastDayMonth); $i++) 
          @php($countDayWeek++) 
            @if ($countDayWeek==1) 
              <tr>
            @endif

            @if ($i == $firstDayMonth)
               @php($flgPrintDay = 1)
            @endif

            @if ($flgPrintDay == 1 && ($countDay < ($cantDaysMonth+1)) ) 
              <td class="thDaysofweek @if($countDay > $dayNow-1) bgcolorday @else text-secondary bg-light @endif" @if($countDay > $dayNow-1) wire:click="setFechaModal('{{$countDay}}.{{$monthSel}}.{{$yearSel}}')" @endif>
                  <span class="pt-1 d-block">
                     {{$countDay}}
                  </span>
                  <span class="d-block pt-3 fst-italic text-secondary text-center" style="font-size:14px;">
                  @php($fechaKeyArr = \Carbon\Carbon::parse($yearSel."-".$monthSel."-".$countDay)->format('Y-m-d'))
                  @if (!empty($arrCantReservasCount[$fechaKeyArr]))                    
                      {{$arrCantReservasCount[$fechaKeyArr]}} {{$arrCantReservasCount[$fechaKeyArr] > 1 ? 'Reservas':'Reserva'}}          
                  @else
                    &nbsp;&nbsp;&nbsp;  
                  @endif
                  </span>                
              </td>
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

    window.addEventListener('swal:information', event => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'center',
            showConfirmButton: false,
            timer: 5500,
            timerProgressBar: false,
            showCloseButton: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        Toast.fire({
            icon: event.detail.icon,
            title: '',
            html: event.detail.mensaje,
        })
    });


    const container = document.getElementById("modalReserva");
    const modal = new bootstrap.Modal(container);

    window.addEventListener('showModal', event => {
      modal.show();
    });

    window.addEventListener('closeModal', event => {
      modal.hide();
    });

    function ocultarModal() {
      //myModal2.show();
      modal.hide();
    }
  </script>

</div>