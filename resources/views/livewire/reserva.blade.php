<div>
  <form>
    <div class="card shadow mt-4" id="headReservas">
      <div class="card-header py-3 h3 text-center">
        Reserva de Vehiculos
        <span class="d-block fst-italic text-secondary pt-1" style="font-size:15px;">
          Fecha Actual: <i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::parse(now())->format('d/m/Y')}}
        </span>
      </div>
      <div class="card-body" id="card{{$randId}}">
        <input wire:model="mesSelStr" type="hidden">
        <input wire:model="mesSel" type="hidden">
        <input wire:model="agnoSel" type="hidden">
        <input wire:model="cantDaysMonth" type="hidden">
        <input wire:model="firstDayMonth" type="hidden">
        <input wire:model="lastDayMonth" type="hidden">

        <div class="alert alert-info border border-info d-flex justify-content-center mx-4 shadow" role="alert">
          <span class="fs-4 pe-2 pe-md-3">
            <i class="bi bi-info-circle-fill"></i></span>
          <span class="fs-6 fst-italic pt-1">
            Haga click sobre el día en el cuál desea realizar su reserva. El calendario se encuentran habilitado dentro de un rango de 60 días.
          </span>
        </div>
        <div class="table-responsive-sm mx-4 my-4">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th scope="col" colspan="7">
                  <div class="row">
                    <div class="col-12 col-md-5 ps-md-0">
                      <div class="input-group py-3 justify-content-center">
                        @foreach($arrMonthDisplay as $mesIndex => $item)
                        <button wire:click="getCalendarMonth({{$mesIndex}})" class="btn {{$mesSel == $mesIndex ? 'btn-primary':'btn-outline-primary'}}" type="button">{{$item['mes']}}</button>
                        @endforeach
                        <!-- <button wire:click="getCalendarMonth(0)" class="btn {{$flgNextMonth == 0 ? 'btn-primary':'btn-outline-primary'}}" type="button">{{$monthNowStr}}</button>
                        <button wire:click="getCalendarMonth(1)" class="btn {{$flgNextMonth == 1 ? 'btn-primary':'btn-outline-primary'}}" type="button">{{$nextMontStr}}</button> -->
                      </div>
                    </div>
                    <div class="col-12 col-md-3 py-3 text-center ps-md-0">
                      <span class="h4">
                        {{$mesSelStr}} {{$agnoSel}}
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
              @php($flgCallModal = 0)

              <!-- (7 - $lastDayMonth) Se calculan los dias restantes para que termine la semana -->

              @for($i=1; $i < ($cantDaysMonth + $firstDayMonth + (7 - $lastDayMonth)); $i++) @php($countDayWeek++) @if ($countDayWeek==1) <tr id="fila{{rand(0,1000)}}">
                @endif

                @if ($i == $firstDayMonth)
                @php($flgPrintDay = 1)
                @endif

                @if ($flgPrintDay == 1 && ($countDay < ($cantDaysMonth+1)) ) @php($flgCallModal=0) @if((($mesActual==$mesSel && $countDay> $dayNow-1) || $mesSel != $mesActual) && ($countDay + $diasMesesAnt) < 61) @php($flgCallModal=1) @endif <td id="dayTD{{rand(0,1000)}}" class="thDaysofweek {{$flgCallModal == 1 ? 'bgcolorday':'text-secondary bg-light'}}" @if($flgCallModal==1) wire:click="setFechaModal('{{$countDay}}-{{$mesSel}}-{{$agnoSel}}')" data-tippy-content="Click para solicitar reserva el día {{$countDay}} de {{$mesSelStr}}" @endif>
                    <span class="pt-1 d-block">
                      {{$countDay}}
                    </span>
                    <span class="d-block pt-3 fst-italic text-secondary text-center" style="font-size:14px;">
                      @php($fechaKeyArr = \Carbon\Carbon::parse($agnoSel."-".$mesSel."-".$countDay)->format('Y-m-d'))
                      @if (!empty($arrCantReservasCount[$fechaKeyArr]))
                      {{$arrCantReservasCount[$fechaKeyArr]}} {{$arrCantReservasCount[$fechaKeyArr] > 1 ? 'Reservas':'Reserva'}}
                      @else
                      &nbsp;&nbsp;&nbsp;
                      @endif
                    </span>
        </div>
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
        @endfor
        </tbody>
        </table>
      </div>
    </div>
</div>

<!-- Modal -->
<div wire:ignore.self class="modal fade pt-0" id="modalReserva" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-scrollable pt-1">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title ps-3 text-primary" id="modalReservaLabel">@if ($idReserva < 1) Ingrese los @endif Datos de Su Reserva</h5>
            <button type="button" id="btnIconClose" class="btn-close" onclick="ocultarModal()" wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva"></button>
      </div>
      <div class="modal-body" id="modalBody">
        <!-- <input type="text" id="myInput" class="form-control"> -->
        @php($flgError = false)
        <div class="row">
          <div class="col-12 col-md-6 ps-4">
            <div class="row pb-md-1 text-success">
              <div class="col-12 pb-md-1" id="funcionarioId">
                <span class="text-primary">Funcionario:</span> {{$userName}}
              </div>
              <div class="col-12 col-md-6 py-2 py-md-0">
                <span class="text-primary">Dia Reserva:</span> {{$fechaModal}}
              </div>
              <div class="col-12 col-md-6 pb-2 pb-md-0" id="estadoId">
                <span class="text-primary">Estado:</span> {{$descripcionEstado}}
              </div>
            </div>
            <div class="row">
              <div class="col-12 pb-2 col-md-6 mt-md-0">
                <div class="row">
                  <div class="col-12" id="horaInicioId">
                    <label data-tippy-content="Hora estimada de inicio.">Hora Inicio Reserva</label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="bi bi-alarm"></i>
                      </span>
                      <input type="time" id="horaInicio" @if($codEstado==3) readonly @endif data-tippy-content="Hora estimada de salida" wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva" class="time-ini form-control" wire:model.debounce.500ms="horaInicio" placeholder="Inicio" autocomplete="off">
                    </div>
                  </div>
                  @error('horaInicio') 
                  <div class="col-12 pb-1">                 
                    @if($flgError == false)
                      <script>movScrollModalById('#horaInicioId');</script>
                      @php($flgError = true)
                    @endif
                    <span class="colorerror">{{ $message }}</span>
                  </div>
                  @enderror
                </div>
              </div>
              <div class="col-12 pb-2 col-md-6" id="horaFinId">
                <div class="row">
                  <div class="col-12">
                    <label>Hora Fin Reserva</label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="bi bi-alarm"></i>
                      </span>
                      <input type="time" id="horaFin" @if($codEstado==3) readonly @endif data-tippy-content="Hora estimada de regreso" wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva" class="time-fin form-control" wire:model.debounce.500ms="horaFin" placeholder="Termino" autocomplete="off">
                    </div>
                  </div>
                  @error('horaFin') 
                  <div class="col-12 pb-1">
                    @if($flgError == false)
                      <script>movScrollModalById('#horaFinId');</script>
                      @php($flgError = true)
                    @endif
                    <span class="colorerror">{{ $message }}</span>
                  </div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12 pb-2 col-md-6 mt-md-0">
                <div class="row">
                  <div class="col-12" id="cantPasajerosId">
                    <label data-tippy-content="Cantidad de pasajeros.">Cant.Pasajeros</label>
                    <div class="input-group">
                      <span class="input-group-text" id="cantPasajeros">
                        <i class="bi bi-people"></i>
                      </span>
                      <input type="text" id="cantPasajeros" @if($codEstado==3) readonly @endif onkeydown="return onlyNumberKey(event, this);" maxlength="2" wire:model.debounce.500ms="cantPasajeros" wire:loading.attr="disabled" wire:target="solicitarReserva" class="form-control" placeholder="Cantidad" data-tippy-content="Indique el n&uacute;mero de pasajeros." autocomplete="off">
                    </div>
                  </div>
                  @error('cantPasajeros')
                  <div class="col-12 pb-1">
                    @if($flgError == false)                  
                      <script>movScrollModalById('#cantPasajerosId');</script>
                      @php($flgError = true)
                    @endif
                    <span class="colorerror">{{ $message }}</span>
                  </div>
                  @enderror
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="row">
                  <div class="col-12" id="codComunaId">
                    <label>Comuna destino</label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="bi bi-signpost-2"></i>
                      </span>
                      <select id="codComuna" wire:model="codComuna" @if($codEstado==3) readonly @endif wire:loading.attr="disabled" wire:target="solicitarReserva" class="form-select">
                        <option value="">Sel. Comuna destino</option>
                        @foreach($comunasCmb as $itemComuna)
                          <option value="{{$itemComuna->codComuna}}">{{$itemComuna->nombreComuna}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  @error('codComuna')
                  <div class="col-12 pb-1">
                    @if($flgError == false)
                      <script>movScrollModalById('#codComunaId');</script>
                      @php($flgError = true)
                    @endif
                    <span class="colorerror">{{ $message }}</span>
                  </div>
                  @enderror
                </div>
              </div>
            </div> 
            <div class="row pt-2 pt-md-0 pb-2" id="divisionId">
              <div class="col-12"> 
                <label>División</label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-signpost-2"></i>
                  </span>
                  <select id="codDivision" wire:model="codDivision" @if($codEstado==3) readonly @endif wire:loading.attr="disabled" wire:target="solicitarReserva" class="form-select">
                    <option value="">Sel.División</option>
                    @foreach($divisionesCmb as $itemDivision)
                      <option value="{{$itemDivision->codDivision}}">{{$itemDivision->nombreDivision}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              @error('codDivision')
              <div class="col-12">
                @if($flgError == false)
                  <script>movScrollModalById('#divisionId');</script>
                  @php($flgError = true)
                @endif                
                <span class="colorerror">{{$message}}</span>
              </div>
              @enderror
            </div>
            <div class="row pt-md-0 pb-3"> 
              <div class="col-12" id="motivoId">
              <label>Motivo del viaje</label>
                <textarea id="motivo" @if($codEstado==3) readonly @endif wire:model.debounce.500ms="motivo" wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva" onclick="movScrollModalById('#motivoId')" placeholder="Motivo/justificación del viaje (Máximo 500 caracteres)" class="form-control" maxlength="500" rows="4"></textarea>
              </div> 
              @error('motivo')
              <div class="col-12">
              @if($flgError == false)
                  <script>movScrollModalById('#motivoId');</script>
                  @php($flgError = true)
                @endif  
                <span class="colorerror">{{$message}}</span> 
              </div>
              @enderror
            </div>
            <!-- <div class="row">
              <div class="col-12">
                <div class="form-check form-switch" data-tippy-content="Proponer uso de vehiculo personal con devolución del costo por gastos de combustible y peajes.">
                  <label class="form-check-label text-secondary" style="font-style:italic;" for="flgUsoVehiculoPersonal">
                    Usar Vehiculo Personal con Devolución de Combustible y Peajes.
                  </label>
                  <input id="flgUsoVehiculoPersonal" @if($codEstado==3) disabled @endif wire:model.debounce.500ms="flgUsoVehiculoPersonal" class="form-check-input" wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva" type="checkbox">
                </div>
              </div>
            </div> -->
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
        <button type="button" id="btnCerrar" class="btn btn-danger" style="width:175px;" onclick="ocultarModal();" wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva">
          Cerrar <i class="bi bi-x-lg"></i>
        </button>
        <button type="button" id="btnSolicitarReserva" @if($codEstado==3) disabled @endif class="btn btn-primary" style="width:175px;" wire:click="solicitarReserva()" wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva">
          {{$idReserva > 0 ? 'Modificar Reserva':'Solicitar Reserva'}}
          <span wire:loading.remove wire:target="solicitarReserva"><i class="bi bi-send pt-1"></i></span>
          <span wire:loading.class="spinner-border spinner-border-sm" wire:target="solicitarReserva" role="status" aria-hidden="true"></span>
        </button>
        @if($idReserva > 0)
        <button type="button" class="btn btn-danger" @if($codEstado==3) disabled @endif id="btnAnularReserva" style="width:175px;" wire:click="confirmAnularReserva" wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva, confirmAnularReserva">
          Anular Reserva
          <span id="anularIcon"><i class="bi bi-x-circle"></i></i></span>
          <span id="spinnerAnularReserva"></span>
        </button>
        @endif

      </div>
    </div>
  </div>
</div>
<!-- Fin Modal -->
</form>


<script>
  // const myModal = document.getElementById('modalReserva')
  // // const myInput = document.getElementById('myInput')
  // myModal.addEventListener('shown.bs.modal', () => {
  //   // myInput.focus() 
  // })

  function onlyNumberKey(evt, obj) {
    var ASCIICode = (evt.which) ? evt.which : evt.keyCode;
    var flgAsciiNumberOK = false;

    if (ASCIICode == 8 /*Borrar <-*/ || ASCIICode == 46 /*Supr*/ || ASCIICode == 37 /*Atras*/ || ASCIICode == 39 /*Adelante*/ || ASCIICode == 9 /*Tab*/ ) {
      return true;
    }

    if (obj.value.length >= obj.maxLength) {
      return false;
    }

    if ((ASCIICode > 47 && ASCIICode < 58) || (ASCIICode > 95 && ASCIICode < 106)) {
      return true;
    } else {
      return false;
    }
  }

  window.addEventListener('swal:information', event => {
    const Toast = Swal.mixin({
      toast: true,
      position: 'center',
      showConfirmButton: false,
      timer: 6000,
      timerProgressBar: false,
      showCloseButton: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    })

    Toast.fire({
      icon: event.detail.icon,
      title: event.detail.title,
      html: event.detail.mensaje,
    })
  });

  window.addEventListener('swal:confirm', event => {
    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-primary m-2',
        cancelButton: 'btn btn-danger m-2'
      },
      buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
      title: event.detail.title,
      html: event.detail.text,
      icon: 'warning',
      showCancelButton: true,
      showCloseButton: true,
      confirmButtonText: 'Confirmar',
      cancelButtonText: 'Cancelar',
      reverseButtons: false
    }).then((result) => {
      if (result.isConfirmed) {
        window.livewire.emit('anularReserva');
      }
    })
  });

  document.addEventListener('livewire:load', () => {
    window.livewire.on('anularReserva', () => {
      var element = document.getElementById("spinnerAnularReserva");
      var element2 = document.getElementById("anularIcon");
      element.classList.add("spinner-border");
      element.classList.add("spinner-border-sm");
      element2.classList.add("d-none");
      document.getElementById("btnCerrar").disabled = true;
      document.getElementById("btnIconClose").disabled = true;
      document.getElementById("btnSolicitarReserva").disabled = true;
      document.getElementById("btnAnularReserva").disabled = true;
      document.getElementById("horaInicio").disabled = true;
      document.getElementById("horaFin").disabled = true;
      document.getElementById("motivo").disabled = true;
      document.getElementById("flgUsoVehiculoPersonal").disabled = true;
    });
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