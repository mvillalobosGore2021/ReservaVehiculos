<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Menureserva extends Component
{
    public  $flgAdmin, $userName;

    public function mount() {
        $user = Auth::user();
        $this->userName = $user->name;
        $this->flgAdmin = $user->flgAdmin;
    }
    public function render()
    {
        return view('livewire.menureserva');
    }
}
