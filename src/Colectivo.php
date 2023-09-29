<?php

namespace TrabajoSube;

class Colectivo{

    private $saldoMin = 0;
    private $costePasaje = 120;
    
    public function pagarCon($tarjeta){
        $boleto = new Boleto();
        if (($tarjeta->saldo - $this->costePasaje) >= 0){
            $tarjeta->saldo = $tarjeta->saldo - $this->costePasaje;
            return $tarjeta->saldo;
        }
        else{
            $boleto->saldoIns();
        }
    }

}
