<?php

namespace TrabajoSube;

class Colectivo{
    private $saldoMin = -211.84;
    public $costePasaje = 120;
    public $timerNuevoPago = 300;
    public $verif; // mas adelante verif sera true si se está en fyh habiles para franquicia de boleto, y false en el caso contrario.

    public function __construct($linea = "102N"){ // por defecto los colectivos normales se inicializan con la linea 102 Negra 
        $this->linea = $linea;
        $this->verif = $this->verificarFyhHabil();
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

    // verificarFyhHabil analiza si en la fecha y hora actuales es posible hacer uso del beneficio de alguna franquicia, y en ese caso devuelve true, y sino false 
    public function verificarFyhHabil(){
        $dia = date("N",time());
        $hora = date("H",time());
        if ($dia >= 1 && $dia <= 5 && $hora >= 6 && $hora <= 22){
            return true;
        }
        else return false;
    }


    // pagarCon toma un objeto de clase Tarjeta, descuenta el precio del pasaje al saldo, crea un objeto de clase Boleto y retorna el resultado de generarBoleto o de saldoIns
    public function pagarCon($tarjeta){

        $boleto = new Boleto();

        $tarjeta->actualizarUsosDiarios($tarjeta);

        if (time() - $tarjeta->fyhUltPago >= $this->timerNuevoPago){


            if($tarjeta->tipo == "completa" && $tarjeta->viajesHoy < $tarjeta->viajesDiarios && $this->verif){
                $tarjeta->fyhUltPago = time();
                $tarjeta->viajesHoy += 1;
                return $boleto->generarBoleto($tarjeta);
            }
            
            else if ($tarjeta->tipo == "parcial" && $tarjeta->viajesHoy < $tarjeta->viajesDiarios && $this->verif){
                if (($tarjeta->saldo - ($this->costePasaje / 2 )) >= $this->saldoMin){
                    $tarjeta->fyhUltPago = time();
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
                $multiplicador = $tarjeta->calculoMultiplicador($tarjeta);

                if (($tarjeta->saldo - ($this->costePasaje * $multiplicador)) >= $this->saldoMin){
                    $tarjeta->saldo -= ($this->costePasaje * $multiplicador);
                    $tarjeta->fyhUltPago = time();
                    if ($tarjeta->tipo == "comun"){
                        $tarjeta->viajesEsteMes += 1;
                    }
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