<?php

namespace TrabajoSube;

class Interurbano extends Colectivo{
    private $saldoMin = -211.84;
    public $costePasaje = 184;

    public function __construct($linea = "Expreso"){
        $this->linea = $linea;
    }

}