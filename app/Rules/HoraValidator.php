<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;

class HoraValidator implements Rule, DataAwareRule, ValidatorAwareRule
{
    private $data;

    private $validator;

    public $mensaje;

    public function __construct()
    {
        $this->mensaje = [];
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function setValidator($validator)
    {
        $this->validator = $validator;

        return $this;
    }
    
    public function passes($attribute, $value)
    {
        $attributeSwitch = $attribute == 'horaInicioSel'?'horaFinSel':'horaInicioSel';//Se obtienen el nombre del atributo contrario para verificar si tiene errores para no realizar la comparación
      
        if (empty(data_get($this->data, $attributeSwitch)) /*$this->validator->errors()->has($attributeSwitch) || */ ) { //Si hora inicio o fin tiene errores no se continua con la validación 
            return true;
        }

        $horaInicio = data_get($this->data, 'horaInicioSel');
        $horaFin = data_get($this->data, 'horaFinSel');
        
        if ($horaInicio > $horaFin) {
            $this->mensaje = $attribute == 'horaInicioSel'?'Hora inicio es mayor a hora fin.':'Hora fin es menor a hora inicio.';
           return false;
        } 

        if ($horaInicio == $horaFin) {
            $this->mensaje = 'Hora de inicio y fin son iguales.';
           return false;
        }         
            
       return true;
    }

    
    public function message()
    {
        return $this->mensaje;
    }
}
