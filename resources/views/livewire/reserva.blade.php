<div>
  <form>
    <x-menureserva />
    
<!-- Modal -->

<div wire:ignore.self class="modal fade pt-5" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"  data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable pt-1">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ingrese los Datos de Su Reserva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> 
      </div>
      <div class="modal-body">
         <!-- <input type="text" id="myInput" class="form-control"> -->
         {{$fechaModal}}
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
            {{$fechaModal}}
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
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>


    <div class="table-responsive mx-5">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th scope="col" colspan="7" class="text-center">Julio</th>
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

            @if ($flgPrintDay == 1 && ($countDay < ($cantDaysMonth+1))) 
              <td class="bgcolorday thDaysofweek" wire:click="$set('fechaModal', '{{$countDay}}')" data-bs-toggle="modal" data-bs-target="#exampleModal">
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
  const myModal = document.getElementById('exampleModal')
    // const myInput = document.getElementById('myInput')
    myModal.addEventListener('shown.bs.modal', () => {
      // myInput.focus()

    })
  </script>

</div>