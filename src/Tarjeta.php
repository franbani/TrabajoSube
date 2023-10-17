<?php

namespace TrabajoSube;

class Tarjeta{

    public $tipo = "comun";
    public $saldoPendiente = 0; // El saldo remanente luego de una carga de tarjeta que exceda el limite de saldo posible
    public $fyhUltPago = 0; // Se sobreescribe cada vez que se realiza un pago nuevo
    public $viajesEsteMes = 0; // Se reinicia cada vez que se detecta un cambio de mes
    public  $multiplicador1 = 0.80; // Especificar multiplicador para descuento n° 1 (tarjetas normales)
    public $multiplicador2 = 0.75; // Especificar multiplicador para descuento n° 2 (tarjetas normales)
    public $multiplicador1APartirDe = 30; // El descuento n° 1 se comenzará a aplicar luego de esta cantidad de viajes al mes
    public  $multiplicador2APartirDe = 80; // Y el n° 2 a esta cantidad
    public $saldoMax = 6600;

    public function __construct($sald = 0, $id = 1){ // Saldo e id son inherentes a cada tarjeta
        $this->saldo = $sald;
        $this->id = $id;
    }

    // Toma un objeto de clase Tarjeta y un entero, y si la carga no excede el saldoMax y el valor de carga es valido,
    // se acredita la carga y se retorna el nuevo saldo
    public function cargaTarjeta($tarjeta, $carga){

        // Caso en el que la carga exceda al saldo maximo
        if(($tarjeta->saldo + $carga) > $this->saldoMax){
            $this->saldoPendiente = $tarjeta->saldo + $carga - $this ->saldoMax;
            $tarjeta->saldo = $this->saldoMax;
            $texto = "Te pasaste del saldo maximo ($" . $this->saldoMax . "). Se cargará la tarjeta hasta este saldo y el excedente se acreditará a medida que se use la tarjeta.";
            return $texto;
        }
        // Caso en el que el valor de carga no sea de los enunciados en la consigna
        else if(!(($carga >= 150 && $carga <= 500 && ($carga % 50) == 0) || ($carga >= 600 && $carga <= 1500 && ($carga % 100) == 0) || ($carga >= 2000 && $carga <= 4000 && ($carga % 500) == 0))){
            $texto = 'Valor de carga invalido. Los valores validos son: 150, 200, 250, 300, 350, 400, 450, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 2000, 2500, 3000, 3500 o 4000';
            return $texto;
        }
        // Caso en el que se puede cargar la tarjeta correctamente
        else if (($tarjeta->saldo + $carga) <= $this->saldoMax){
            $tarjeta->saldo = $tarjeta->saldo + $carga;
            $texto = 'Se han cargado $' . $carga . '. Saldo final: $' . $tarjeta->saldo;
            return $texto;
        }
    }

    // Se declara actualizarUsosDiarios para poder utilizarla en las clases heredaderas de Tarjeta: FranquiciaParcial y FranquiciaCompleta 
    public function actualizarUsosDiarios($tarjeta){

    }

    // actualizarUsoMensual toma una tarjeta y si el día actual no es del 1 al 30, o si el mes es distinto al mes del ultimo pago realizado, se resetea la propiedad de
    // viajesEsteMes de la tarjeta introducida devolviendola a 0, para que se reinicie el recuento de pasajes pagados en este mes
    public function actualizarUsoMensual($tarjeta){
        if (date("d",$tarjeta->fyhUltPago) > 30 || date("m",$tarjeta->fyhUltPago) != date("m",time())){
            $tarjeta->viajesEsteMes = 0;
        }
    }

    // calculoMultiplicador toma un objeto de clase tarjeta, que debe ser de tipo común, y analiza cuantos boletos pagó en el mes.
    // En base a esta cantidad devuelve un distinto multiplicador para el coste del pasaje de colectivo, para los descuentos
    public function calculoMultiplicador($tarjeta){
        $multiplicador = 1;
        if($tarjeta->tipo = "comun"){
            $tarjeta->actualizarUsoMensual($tarjeta);
            if($tarjeta->viajesEsteMes >= $this->multiplicador1APartirDe && $tarjeta->viajesEsteMes <= $this->multiplicador2APartirDe){
                $multiplicador = $this->multiplicador1;
            }
            if($tarjeta->viajesEsteMes >= $this->multiplicador2APartirDe){
                $multiplicador = $this->multiplicador2;
            }
        }
        return $multiplicador;
    }

}