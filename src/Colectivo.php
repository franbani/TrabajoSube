<?php

namespace TrabajoSube;

class Colectivo{

    private $saldoMin = -211.84;
    private $costePasaje = 120;
    
    public function pagarCon($tarjeta){ // pagarCon toma un objeto de clase Tarjeta, descuenta el precio del pasaje al saldo, crea un objeto de clase Boleto y retorna el resultado de generarBoleto o de saldoIns 
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
