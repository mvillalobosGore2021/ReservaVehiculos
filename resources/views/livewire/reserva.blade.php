<div>
  <form>
  @csrf
  <!-- <div class="donut"></div> Spinner -->
    <div class="pt-0 pt-md-1"></div>
    <div class="card shadow mt-2 mt-md-3" id="headReservas">
      <div class="card-header py-3 h3 text-center">
        Calendario de Reservas de Vehículos
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
      
      <div class="alert alert-info border border-info mb-4 mx-2 mx-md-4 shadow" role="alert"> 
      <h4 class="alert-heading text-center fw-bold fs-5">Calendario de reservas</h4>
      <hr>
          <p class="fs-6 fst-italic pt-1 mx-3" style="text-align:justify;text-indent: 30px;">
          <span class="fw-bold fs-4 text-white" style="background-color:#17a2b8;border:2px solid;border-radius:5px;padding-left:4px;padding-right:8px;">
          E</span>n el presente calendario usted podrá ingresar nuevas solicitudes de reservas haciendo click en el día en el cuál desea 
          reservar. El calendario se encuentra habilitado en un rango de 60 días. 
          </p>
        </div>  

        <div class="table-responsive-sm mx-2 mx-md-4 my-4">
          <table class="table table-bordered" >
            <thead>
              <tr>
                <th scope="col" colspan="7">
                  <div class="row">
                    <div class="col-12 col-md-5 ps-md-0" id="headCalendar">
                      <div class="input-group py-3 justify-content-center">
                        @foreach($arrMonthDisplay as $mesIndex => $item)
                          <button wire:click="getCalendarMonth('{{$item['mesNumber']}}_{{$item['agno']}}', 1)" class="btn {{$mesSel == $mesIndex ? 'btn-primary':'btn-outline-primary'}}" type="button">{{$item['mes']}}</button>
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

                @if ($flgPrintDay == 1 && ($countDay < ($cantDaysMonth+1)) ) @php($flgCallModal=0) @if((($mesActual==$mesSel && $countDay> $dayNow-1) || $mesSel != $mesActual) && ($countDay + $diasMesesAnt) < 61) @php($flgCallModal=1) @endif @php($fechaKeyArr=\Carbon\Carbon::parse($agnoSel."-".$mesSel."-".$countDay)->format('Y-m-d')) 
                    <td id="dayTD{{rand(0,1000)}}" class="thDaysofweek @if (!empty($arrCantReservasCount[$fechaKeyArr])) classTippy  @endif {{$flgCallModal == 1 ? 'bgcolorday':'text-secondary bg-light'}}" @if($flgCallModal==1) wire:click="setFechaModal('{{$countDay}}-{{$mesSel}}-{{$agnoSel}}')" @endif  @if($flgCallModal==1) @if (!empty($arrCantReservasCount[$fechaKeyArr])) data-template="td{{\Carbon\Carbon::parse($arrCantReservasCount[$fechaKeyArr]['fechaSolicitud'])->format('Ymd')}}" @else data-tippy-content="Haga Click sobre el recuadro para ingresar una solicitud de reserva el día: {{$countDay}}-{{$mesSel}}-{{$agnoSel}}." @endif @else data-tippy-content="Día no habilitado" @endif>
                      <span class="pt-1 d-block">
                        {{$countDay}}
                      </span>
                      <span class="d-block pt-3 fst-italic text-secondary text-center" style="font-size:14px;">

                        @if (!empty($arrCantReservasCount[$fechaKeyArr]))
                        {{$arrCantReservasCount[$fechaKeyArr]['cantReservas']}} {{$arrCantReservasCount[$fechaKeyArr]['cantReservas'] > 1 ? 'Reservas':'Reserva'}}
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

<!-- Modal --> 
<div wire:ignore.self class="modal fade pt-0" id="modalReserva" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-scrollable pt-1">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title ps-3 text-primary" id="modalReservaLabel">@if ($idReserva < 1) Ingrese los @endif Datos de Su Reserva</h5>
            <button type="button" id="btnIconClose" class="btn-close" onclick="ocultarModal()" wire:loading.attr="disabled" wire:target="solicitarReserva,anularReserva"></button>
      </div>
      <div class="modal-body" id="modalBody">
        <!-- <input type="text" id="myInput" class="form-control"> -->
        <div class="row">
          <div class="col-12 col-md-6 ps-4">
            <div class="row pb-md-1 text-success"> 

            @if($codEstado==3) 
              <div class="col-12" >
              <div class="alert alert-info border border-info pb-0" role="alert">               
                  <p class="fst-italic" style="font-size:0.99rem;text-align:justify;text-indent: 10px;">
                  <span class="fw-bold text-white" style="font-size:1.2rem;background-color:#17a2b8;border:2px solid;border-radius:5px;padding-left:4px;padding-right:8px;">
                  S</span>u reserva se encuentra <span style="color:#EF3B2D;"><b>{{$descripcionEstado}}</b></span>. Si desea modificar el estado  de su reserva lo debe solicitar a <b>Darwin Figueroa</b> dfigueroa@gorebiobio.cl o a <b>Alvaro Olate</b> aolate@gorebiobio.cl.
              </p>
             </div>
            </div>
            @endif

              <div class="col-12 pb-md-1" id="funcionarioId">
                <span class="text-primary"><b>{{$sexo == "F" ? "Funcionaria":"Funcionario"}}:</b></span> {{$userName}}
              </div>
              <div class="col-12 col-md-6 py-2 py-md-0" id="idfechaReserva">
                <span id="idfechaReservaError">
                <span class="text-primary"><b>Fecha Reserva:</b></span> 
                {{\Carbon\Carbon::parse($fechaSolicitud)->format('d/m/Y')}}
                </span>
              </div>
              <div class="col-12 col-md-6 pb-2 pb-md-0" id="estadoId">
                <span class="text-primary"><b>Estado:</b></span> {{$descripcionEstado}}
              </div>
            </div>
            <div class="row">
              <div class="col-12 pb-2 col-md-6 mt-md-0">
                <div class="row">
                  <div class="col-12" id="idhoraInicio"> 
                    <label data-tippy-content="Hora estimada de inicio.">Hora Inicio Reserva</label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="bi bi-alarm"></i>
                      </span>
                      <input type="time" id="horaInicio" wire:model.debounce.500ms="horaInicio" @if($codEstado==3) disabled @endif data-tippy-content="Hora estimada de salida" wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva" class="time-ini form-control" placeholder="Inicio" autocomplete="off">
                    </div>
                  </div>
                  @error('horaInicio')
                  <div class="col-12 pb-1" id="idhoraInicioError">
                    <span class="colorerror">{{ $message }}</span>
                  </div>
                  @enderror
                </div>
              </div>
              <div class="col-12 pb-2 col-md-6" id="idhoraFin">
                <div class="row">
                  <div class="col-12">
                    <label>Hora Fin Reserva</label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="bi bi-alarm"></i>
                      </span>
                      <input type="time" id="horaFin" wire:model.debounce.500ms="horaFin"  @if($codEstado==3) disabled @endif data-tippy-content="Hora estimada de regreso" wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva" class="time-fin form-control" placeholder="Termino" autocomplete="off">
                    </div>
                  </div>
                  @error('horaFin')
                  <div class="col-12 pb-1" id="idhoraFinError">
                    <span class="colorerror">{{ $message }}</span>
                  </div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12 pb-2 col-md-6 mt-md-0">
                <div class="row">
                  <div class="col-12" id="idcantPasajeros">
                    <label data-tippy-content="Cantidad de pasajeros.">Cant.Pasajeros</label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="bi bi-people"></i>
                      </span>
                      <input type="text" id="cantPasajeros" wire:model.debounce.500ms="cantPasajeros" @if($codEstado==3) disabled @endif onkeydown="return onlyNumberKey(event, this);" maxlength="2" wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva" class="form-control" placeholder="Cantidad" data-tippy-content="Indique el n&uacute;mero de pasajeros." autocomplete="off">
                    </div>
                  </div>
                  @error('cantPasajeros')
                  <div class="col-12 pb-1" id="idcantPasajerosError">
                    <span class="colorerror">{{ $message }}</span>
                  </div>
                  @enderror
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="row">
                  <div class="col-12" id="idcodComuna">
                    <label>Comuna destino</label>
                    <div class="input-group">
                      <span class="input-group-text">
                        <i class="bi bi-signpost-2"></i>
                      </span>
                      <select id="codComuna" wire:model="codComuna" @if($codEstado==3) disabled @endif wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva" class="form-select">
                        <option value="">Sel. Comuna destino</option>
                        @foreach($comunasCmb as $itemComuna)
                        <option value="{{$itemComuna->codComuna}}">{{$itemComuna->nombreComuna}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  @error('codComuna')
                  <div class="col-12 pb-1" id="idcodComunaError">
                    <span class="colorerror">{{ $message }}</span>
                  </div>
                  @enderror
                </div>
              </div>
            </div>
            <div class="row pt-2 pt-md-0 pb-2" id="idcodDivision"> 
              <div class="col-12">
                <label>División</label>
                <div class="input-group">
                  <span class="input-group-text"> 
                    <i class="bi bi-list-ul"></i>
                  </span>
                  <select id="codDivision" wire:model="codDivision" @if($codEstado==3) disabled @endif wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva" class="form-select">
                    <option value="">Sel.División</option>
                    @foreach($divisionesCmb as $itemDivision)
                    <option value="{{$itemDivision->codDivision}}">{{$itemDivision->nombreDivision}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              @error('codDivision')
              <div class="col-12" id="idcodDivisionError">
                <span class="colorerror">{{$message}}</span>
              </div>
              @enderror
            </div>
            <div class="row pt-md-0 pb-3">
              <div class="col-12" id="idmotivo">
                <label>Motivo del viaje</label>
                <textarea id="motivo" wire:model.debounce.500ms="motivo" @if($codEstado==3) disabled @endif wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva" onclick="movScrollModalById('#motivoId')" placeholder="Motivo/justificación del viaje (Máximo 500 caracteres)" class="form-control" maxlength="500" rows="4"></textarea>
              </div>
              @error('motivo')
              <div class="col-12" id="idmotivoError">
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
            <div class="table-responsive mx-4">
              <table class="table">
                <!-- table-bordered -->
                <thead>
                  <tr>
                    <th scope="col" colspan="8" class="text-start text-success pb-3">
                      <span data-tippy-content="Reservas realizadas por otros funcionarios para el día: {{$fechaSolicitud}}">
                      <span class="text-success">Reservas realizadas por otros funcionarios para el día:</span>
                      <span style="background-color:#FFD42F;color:black;padding-left:4px;padding-right:4px;">
                          {{\Carbon\Carbon::parse($fechaSolicitud)->format('d/m/Y')}}
                        </span>
                      </span>
                      <input type="hidden" wire:model="fechaSolicitud"> 
                    </th>
                  </tr>
                  <tr>
                        <th scope="col" class="text-start" nowrap>Fecha Creación</th>
                        <th scope="col" class="text-start">Nombre</th>
                        <th scope="col" class="text-start" nowrap>Fecha Reserva</th>
                        <th scope="col" class="text-start">Estado</th>
                        <th scope="col" class="text-start">Vehículo</th>
                        <th scope="col" class="text-start">Destino</th>                            
                        <th scope="col" class="text-start" nowrap>Hora Inicio-Fin</th>            
                      </tr>
                    </thead>
                    <tbody>
                      @if(!empty($reservasFechaSel) && count($reservasFechaSel) > 0)
                      @foreach($reservasFechaSel as $index => $item)
                      <tr>
                        <td class="text-start" nowrap>{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y H:i')}}</td>
                        <td class="text-start" nowrap>{{$item['name']}}</td>
                        <td class="text-center" nowrap><span style="background-color:#FFD42F;padding-left:4px;padding-right:4px;">{{ \Carbon\Carbon::parse($item['fechaSolicitud'])->format('d/m/Y')}}</span></td>                       
                        <td class="text-start" nowrap><span style="background-color:{{$item['codColor']}};padding-left:4px;padding-right:4px;">{{$item['descripcionEstado']}}</span></td>
                        <td class="text-start" nowrap>{{$item['codVehiculo'] > 0 ? $item['descripcionVehiculo']: 'No Asignado'}}</td> 
                        <td class="text-start" nowrap>{{$item['nombreComuna']}}</td>
                        <td class="text-center" nowrap>{{ \Carbon\Carbon::parse($item['horaInicio'])->format('H:i')}} - {{ \Carbon\Carbon::parse($item['horaFin'])->format('H:i')}}</td>
                      </tr>
                      @endforeach
                      @else
                      <tr>
                        <td colspan="8">
                          <div class="alert alert-info border border-info d-flex justify-content-center my-3 mx-2 my-md-4" role="alert">
                            <span class="fs-4 pe-2 pe-md-3">
                              <i class="bi bi-info-circle-fill"></i>
                            </span>
                            <span class="fs-6 fst-italic pt-1">
                               No existen reservas realizadas por otros funcionarios para el día seleccionado.
                            </span>
                          </div>
                        </td>
                      </tr>
                      @endif
                    </tbody>
              </table>
            </div>

            @if (session()->has('exceptionMessage'))
            <div class="row pt-3 mx-3">
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
        <button type="button" id="btnGuardar" @if($codEstado==3) disabled @endif class="btn btn-primary" style="width:175px;" wire:click="solicitarReserva()" wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva">
          {{$idReserva > 0 ? 'Modificar Reserva':'Solicitar Reserva'}}
          <span wire:loading.remove wire:target="solicitarReserva, anularReserva"><i class="bi bi-send pt-1"></i></span>
          <span wire:loading.class="spinner-border spinner-border-sm" wire:target="solicitarReserva, anularReserva" role="status" aria-hidden="true"></span>
        </button>
        @if($codEstadoOrig != 3 && $idReserva > 0) 
        <button type="button" class="btn btn-danger" id="btnAnularReserva" style="width:175px;" wire:click="confirmAnularReserva" wire:loading.attr="disabled" wire:target="solicitarReserva, anularReserva, confirmAnularReserva">
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

<!-- Inicio Tooltip Reservas realizadas para el día seleccionado  -->
@if (!empty($arrCantReservasCount)) 
<div style="display:none">
  @foreach($arrCantReservasCount as $index => $itemReserva)
  <div id="td{{\Carbon\Carbon::parse($itemReserva['fechaSolicitud'])->format('Ymd')}}">
    <table class="table">
      <tr>
        <td colspan="3" class="fst-italic">
          <span class="text-primary">Haga Click sobre el recuadro para ingresar una solicitud de reserva para el día: <span class="text-success">{{\Carbon\Carbon::parse($itemReserva['fechaSolicitud'])->format('d-m-Y')}}</span>.</span>
          <span style="display:block;padding-top: 10px;">Solicitudes realizadas para el día: <span class="fw-bolder">{{\Carbon\Carbon::parse($itemReserva['fechaSolicitud'])->format('d-m-Y')}}</span>.</span>
        </td>
      </tr>
      <tr>
        <th nowrap>Funcionario(a)</th>
        <th nowrap>Destino</th>
        <th nowrap>Estado</th>
      </tr>

      @foreach($itemReserva['reservasFechaItem'] as $itemReservasFecha)
      <tr>
        <td nowrap>{{$itemReservasFecha['name']}}</td>
        <td nowrap>{{$itemReservasFecha['nombreComuna']}}</td>
        <td nowrap>{{$itemReservasFecha['descripcionEstado']}}</td>
      </tr>
      @endforeach
    </table>
  </div>
  @endforeach
</div>
@endif
<!-- Fin Tooltip Reservas realizadas para el día seleccionado -->
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