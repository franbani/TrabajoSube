<?php

namespace TrabajoSube;

class Colectivo{
    private $saldoMin = -211.84;
    public $costePasaje = 120;


    public function __construct($linea = "102N"){
        $this->linea = $linea;
    }


    // pagarCon toma un objeto de clase Tarjeta, descuenta el precio del pasaje al saldo, crea un objeto de clase Boleto y retorna el resultado de generarBoleto o de saldoIns
    public function pagarCon($tarjeta){
        $boleto = new Boleto();

        if($tarjeta->tipo == "completa"){
            return $boleto->generarBoleto($tarjeta);
        }

        else if($tarjeta->tipo == "parcial"){
            if (($tarjeta->saldo - ($this->costePasaje / 2 )) >= $this->saldoMin){
                $tarjeta->saldo = $tarjeta->saldo - ($this->costePasaje / 2);
                return $boleto->generarBoleto($tarjeta);
            }
            else{
                return $boleto->saldoIns();
            }
        }

        else{
            if (($tarjeta->saldo - $this->costePasaje) >= $this->saldoMin){
                $tarjeta->saldo = $tarjeta->saldo - $this->costePasaje;
                return $boleto->generarBoleto($tarjeta);
            }
            else{
                return $boleto->saldoIns();
            }
        }

    }

}