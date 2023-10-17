<?php

namespace TrabajoSube;

class Boleto{

    // fecha devuelve el dia, mes, año, hora, minutos y segundos actuales de forma ordenada como un texto
    public function fecha(){
        return date("d/m/Y H:i:s",time());
    }

    // conocerTipo toma un objeto Tarjeta devuelve el tipo de la clase de tarjeta ingresada (comun, parcial, completa)
    public function conocerTipo($tarj){
        return $tarj->tipo;
    }

    // conocerLinea toma un objeto Colectivo devuelve la linea de colectivo del colectivo que se le ingrese (102N es el default para Colectivo y Expreso es el default para interurbano)
    public function conocerLinea($cole){
        return $cole->linea;
    }

    // conocerAbonado toma un objeto de clase Colectivo y uno de clase Tarjeta, analiza el tipo de la tarjeta y devuelve el coste que deberia tener el pasaje en el colectivo dado usando la tarjeta dada
    public function conocerAbonado($cole,$tarj){
        if ($tarj->tipo == "completa" && $tarj->viajesHoy < 2 && $cole->verif){
            return 0;
        }
        else if ($tarj->tipo == "parcial" && $tarj->viajesHoy < 4 && $cole->verif){
            return $cole->costePasaje / 2;
        }
        else if ($tarj->tipo == "comun"){
            if($tarj->viajesEsteMes >= 30 && $tarj->viajesEsteMes <= 80){
                return $cole->costePasaje * 0.80;
            }
            else if($tarj->viajesEsteMes >= 80){
                return $cole->costePasaje * 0.75;
            }
            else return $cole->costePasaje;
        }
        else return $cole->costePasaje;
    }
    
    // conocerID toma un objeto Tarjeta y devuelve su nro de ID
    public function conocerID($tarj){
        return $tarj->id;
    }

    // generarBoleto toma una tarjeta y devuelve un texto que será distinto dependiendo de si se aplica el boleto gratuito, si se paga normalmente o si la tarjeta queda con saldo negativo
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

    // saldoIns se llama en los casos en los que no se pueda generar el boleto porque la tarjeta ya utilizó su/s viaje/s plus
    public function saldoIns(){
        echo 'Saldo insuficiente';
        return FALSE;
    }

}
