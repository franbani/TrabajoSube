<?php

namespace TrabajoSube;

class Colectivo{

    private $saldoMin = -211.84;
    private $costePasaje = 120;
    
    public function pagarCon($tarjeta){
        $boleto = new Boleto();
        if (($tarjeta->saldo - $this->costePasaje) >= $this->saldoMin){
            $tarjeta->saldo = $tarjeta->saldo - $this->costePasaje;
            return $boleto->generarBoleto($tarjeta);
        }
        else{
            return $boleto->saldoIns();
        }
    }

}
