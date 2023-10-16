<?php

namespace TrabajoSube;

class Colectivo{
    private $saldoMin = -211.84;
    public $costePasaje = 120;
    public $timerNuevoPago = 300;


    public function __construct($linea = "102N"){
        $this->linea = $linea;
    }

    private function acreditarSaldoPend($tarj){
        if($tarj->saldoPendiente > 0){
            if($tarj->saldo + $tarj->saldoPendiente > $tarj->saldoMax){
                $acreditando = $tarj->saldoMax - $tarj->saldo;
                $tarj->saldoPendiente -= $acreditando;
            }
            else{
                $acreditando = $tarj->saldoPendiente;
                $tarj->saldoPendiente = 0;
            }
            $tarj->saldo += $acreditando;
        }
    }


    // pagarCon toma un objeto de clase Tarjeta, descuenta el precio del pasaje al saldo, crea un objeto de clase Boleto y retorna el resultado de generarBoleto o de saldoIns
    public function pagarCon($tarjeta){

        $boleto = new Boleto();

        $tarjeta->actualizarUsos($tarjeta);

        if (time() - $tarjeta->fyhUltPago >= $this->timerNuevoPago){
            $tarjeta->fyhUltPago = time();
            if($tarjeta->tipo == "completa" && $tarjeta->viajesHoy < 2){
                $tarjeta->viajesHoy += 1;
                return $boleto->generarBoleto($tarjeta);
            }
            
            else if ($tarjeta->tipo == "parcial" && $tarjeta->viajesHoy < 4){
                if (($tarjeta->saldo - ($this->costePasaje / 2 )) >= $this->saldoMin){
                    $tarjeta->viajesHoy += 1;
                    $tarjeta->saldo -= ($this->costePasaje / 2);
                    $this->acreditarSaldoPend($tarjeta);
                    return $boleto->generarBoleto($tarjeta);
                }
                else {
                    return $boleto->saldoIns();
                }
            }
    
            else{
                if (($tarjeta->saldo - $this->costePasaje) >= $this->saldoMin){
                    $tarjeta->saldo -= $this->costePasaje;
                    $this->acreditarSaldoPend($tarjeta);
                    $tarjeta->viajesHoy += 1;
                    return $boleto->generarBoleto($tarjeta);
                }
                else{
                    return $boleto->saldoIns();
                }
            }
        }
        else{
            return "Ya has pagado un pasaje recientemente";
        }

    }

}