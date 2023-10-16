<?php

namespace TrabajoSube;

class Boleto{

    public function fecha(){
        return date("d/m/Y",time());
    }

    public function conocerTipo($tarj){
        return $tarj->tipo;
    }

    public function conocerLinea($cole){
        return $cole->linea;
    }

    public function conocerAbonado($cole,$tarj){
        if ($tarj->tipo == "completa"){
            return 0;
        }
        else if ($tarj->tipo == "parcial"){
            return $cole->costePasaje / 2;
        }
        else if ($tarj->tipo == "comun"){
            return $cole->costePasaje;
        }
    }

    public function conocerID($tarj){
        return $tarj->id;
    }

    public function generarBoleto($tarjeta){ // Caso en el cual se permite pagar el pasaje
        if($tarjeta->tipo == "completa" && $tarjeta->viajesHoy < 3){
            $texto = "Descuento completo aplicado";
        }
        else if ($tarjeta->saldo > 0){
            $texto = "Pago exitoso. Saldo restante: $" . $tarjeta->saldo;
        }
        else {
            $texto = "Pago exitoso. La tarjeta adeuda $" . -$tarjeta->saldo;
        }
        return $texto;
    }

    public function saldoIns(){ // Caso en el cual no se permita el pago por saldo insuficiente
        echo 'Saldo insuficiente';
        return FALSE;
    }

}
