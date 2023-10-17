<?php

namespace TrabajoSube;

class FranquiciaParcial extends Tarjeta{
    public $tipo = "parcial"; 
    public $fyhUltPago = 0;
    public $viajesHoy = 0;

    // actualizarUsosDiarios toma una tarjeta y analiza si el dia en que se realizo el ultimo pago es distinto al dia actual, y en dicho caso se reinicia el valor de viajesHoy a 0
    public function actualizarUsosDiarios($tarjeta){
        if (date("d/m/Y",time()) != date("d/m/Y",$tarjeta->fyhUltPago)){
            $tarjeta->viajesHoy = 0;
        }
    }

}