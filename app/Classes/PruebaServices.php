<?php

namespace App\Classes;

use App\Models\User;


class PruebaServices {
    public function saludo($obj) {
        // return User::all();
        $obj->pruebaStr = "Saludo desde el service";
    }
}