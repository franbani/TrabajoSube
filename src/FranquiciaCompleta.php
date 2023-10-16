<?php

namespace TrabajoSube;

class FranquiciaCompleta extends Tarjeta{
    public $tipo = "completa";

    public $saldo;

    public function __construct($sald = 0, $id = 1){ // $saldo e id son inherentes a cada tarjeta
        $this->saldo = $sald;
        $this->id = $id;
    }

}