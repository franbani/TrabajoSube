<?php

namespace TrabajoSube;

class Interurbano extends Colectivo{
    public $costePasaje = 184;

    // Por defecto los colectivos interurbanos son Expresos
    public function __construct($linea = "Expreso"){
        $this->linea = $linea;
        $this->verif = $this->verificarFyhHabil();
    }

}