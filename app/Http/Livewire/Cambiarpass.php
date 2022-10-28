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
        // dd("Pase por render ", $this->current_password, $this->password, $this->password_confirmation);
        return view('livewire.cambiarpass');
    }

    public function cambiarPass()
    {

      
        //Ver por que no envia mensaje a la vista

        // try {
        //    $this->resetValidation(['current_password', 'password', 'password_confirmation']);
        //    $this->resetErrorBag(['current_password', 'password', 'password_confirmation']);
           $this->validate($this->getArrRules());
        // } catch(exception $e) {
        //     dd("Validate ".$e->getMessage());
        // }

        // dd("Pase por cambiarPass ", $this->current_password, $this->password, $this->password_confirmation);

        // try {           

            // dd(($this->password != $this->password_confirmation));

            //Se comprueba que la nueva pass y la confirm sean iguales
            // if ($this->password != $this->password_confirmation) {
            //     $this->addError('password_confirmation', 'La nueva contraseña y la de confirmación son distintas.');
            //     dd("La nueva contraseña y la de confirmación son distintas");
            // } else {
                $user = Auth::user();
                
                if (! Hash::check($this->current_password, $user->password)) {
                    dd("Contraseña incorrecta");
                    $this->addError('current_password', 'Contraseña incorrecta'); 
                } else {
                    try {
                        $user = User::whereId($user->id)->update(['password' => Hash::make($this->password)]);
                        dd("Password Cambiada con exito");
                    } catch (exception $e) {
                        dd("Error al modificar la Password " . $e->getMessage());
                    }
                }
            // }
        // } catch (exception $e) {
        //     dd($e->getMessage());
        // }
    }

    public function getArrRules() {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', new Password, 'confirmed'],
        ];
    }
}
