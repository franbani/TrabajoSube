<?php

namespace TrabajoSube;

class FranquiciaCompleta extends Tarjeta{
    public $tipo = "completa";
    public $viajesHoy = 0; // Cantidad de boletos comprados en un dia, para controlar cuando se usa el beneficio y cuando no. Se reinicia a 0 cuando se detecta un dia diferente en un pago.
    public $viajesDiarios = 2; // Especificar cantidad máxima de pasajes en los que se puede aplicar el beneficio por dia

    // actualizarUsosDiarios toma una tarjeta y analiza si el dia en que se realizo el ultimo pago es distinto al dia actual, y en dicho caso se reinicia el valor de viajesHoy a 0
    public function actualizarUsosDiarios($tarjeta){
        if (date("d/m/Y",time()) != date("d/m/Y",$tarjeta->fyhUltPago)){
            $tarjeta->viajesHoy = 0;
        }
    }

}