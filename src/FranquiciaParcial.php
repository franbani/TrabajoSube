<?php

namespace TrabajoSube;

class FranquiciaParcial extends Tarjeta{
    public $tipo = "parcial"; 
    public $fyhUltPago = 0;
    public $viajesHoy = 0;

    public function actualizarUsos($tarjeta){
        if (date("d/m/Y",time()) != date("d/m/Y",$tarjeta->fyhUltPago)){
            $tarjeta->viajesHoy = 0;
        }
    }

}