<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Rules\Password;
use exception;

class Cambiarpass extends Component
{

    public $current_password, $password, $password_confirmation;

  
    public function render()
    {
        return view('livewire.cambiarpass'); 
    }

    public function cambiarPass()
    {  
               $this->validate($this->getArrRules());

               try {
                $user = Auth::user();
                
                if (! Hash::check($this->current_password, $user->password)) {
                    $this->addError('current_password', 'Contraseña incorrecta'); 
                } else {
                
                        $user = User::whereId($user->id)->update(['password' => Hash::make($this->password)]);
                       
                        $this->reset(['current_password', 'password', 'password_confirmation']);
                         $this->resetValidation(['current_password', 'password', 'password_confirmation']);
                         $this->resetErrorBag(['current_password', 'password', 'password_confirmation']);
                         
                         $this->dispatchBrowserEvent('swal:information', [
                            'icon' => '',//info 
                            'mensaje' => '<i class="bi bi-info-circle text-info" style="font-size:2rem;"></i><span class="ps-2 fs-6 text-primary" style="font-weight:430;">Su contraseña ha sido modificada</span>',
                        ]); 

                        return redirect('/login'); 
                }
            } catch (exception $e) {
                session()->flash('exceptionMessage', $e->getMessage());
            }
    }

  
//   public function updated($field, $value) {
//     $this->validateOnly($field, $this->getArrRules());    
//   }

    public function getArrRules() {
        return [
            'current_password' => ['required', 'string'],
        'password' => ['required', 'string', new Password, 'confirmed'],
        ];
    }
}
