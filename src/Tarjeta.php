<?php

namespace TrabajoSube;

class Tarjeta{

    public $tipo = "comun";
    public $saldoPendiente = 0;

    public function __construct($sald = 0, $id = 1){ // $saldo e id son inherentes a cada tarjeta
        $this->saldo = $sald;
        $this->id = $id;
    }

    public $saldoMax = 6600;

    public function cargaTarjeta($tarjeta, $carga){ // Toma un objeto de clase Tarjeta y un entero, y si la carga no excede el saldoMax y el valor de carga es valido, se acredita la carga y se retorna el nuevo saldo

        if(($tarjeta->saldo + $carga) > $this->saldoMax){
            $this->saldoPendiente = $tarjeta->saldo + $carga - $this ->saldoMax;
            $tarjeta->saldo = 6600;
            $texto = "Te pasaste del saldo maximo ($6600). Se cargará la tarjeta hasta este saldo y el excedente se acreditará a medida que se use la tarjeta.";
            return $texto;
        }
        else if(!(($carga >= 150 and $carga <= 500 and ($carga % 50) == 0) or ($carga >= 600 and $carga <= 1500 and ($carga % 100) == 0) or ($carga >= 2000 and $carga <= 4000 and ($carga % 500) == 0))){
            $texto = 'Valor de carga invalido. Los valores validos son: 150, 200, 250, 300, 350, 400, 450, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 2000, 2500, 3000, 3500 o 4000';
            return $texto;
        }
        else if (($tarjeta->saldo + $carga) <= $this->saldoMax){
            $tarjeta->saldo = $tarjeta->saldo + $carga;
            $texto = 'Se han cargado $' . $carga . '. Saldo final: $' . $tarjeta->saldo;
            return $texto;
        }
    }

    public function actualizarUsos($tarjeta){

    }

}