<?php

namespace TrabajoSube;

class Colectivo{
    private $saldoMin = -211.84;
    public $costePasaje = 120;
    public $timerNuevoPago = 300; // Tiempo en segundos que se tardará en poder volver a pagar pasajes luego de ya haber pagado uno
    public $verif; // mas adelante verif sera true si se está en fyh habiles para franquicia de boleto, y false en el caso contrario.

    public function __construct($linea = "102N"){ // Por defecto los colectivos normales se inicializan con la linea 102 Negra 
        $this->linea = $linea;
        $this->verif = $this->verificarFyhHabil();
    }

    // acreditarSaldoPend toma un objeto de tipo Tarjeta y se encarga de, luego de un pago, acreditar todo el saldo que se pueda
    // del saldo que quedó pendiente de alguna carga
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

        // Se verifica que haya transcurrido suficiente tiempo desde el anterior pago
        if (time() - $tarjeta->fyhUltPago >= $this->timerNuevoPago){
            
            // Casos de tiempo transcurrido correctamente:

            // Caso de Franquicia Completa
            if($tarjeta->tipo == "completa" && $tarjeta->viajesHoy < $tarjeta->viajesDiarios && $this->verif){
                $tarjeta->fyhUltPago = time();
                $tarjeta->viajesHoy += 1;
                return $boleto->generarBoleto($tarjeta);
            }
            
            // Caso de Franquicia Parcial
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
            
            // Caso de Franquicia Completa / Parcial no habilitadas o tarjeta comun (en esta ultima se analiza posibilidad de descuentos)
            else{

                // Si no hay descuento o la tarjeta es de Franq Completa o Parcial, $multiplicador = 1
                $multiplicador = $tarjeta->calculoMultiplicador($tarjeta);

                if (($tarjeta->saldo - ($this->costePasaje * $multiplicador)) >= $this->saldoMin){
                    $tarjeta->saldo -= ($this->costePasaje * $multiplicador);
                    $tarjeta->fyhUltPago = time();
                    
                    // Si la tarjeta es comun, se incrementan en 1 los viajes del mes actual
                    if ($tarjeta->tipo == "comun"){
                        $tarjeta->viajesEsteMes += 1;
                    }
                    
                    // Se acredita todo el saldo pendiente que se pueda, se aumenta viajesHoy para las franquicias y se genera el boleto
                    $this->acreditarSaldoPend($tarjeta);
                    if($tarjeta->tipo != "comun"){
                        $tarjeta->viajesHoy += 1;
                    }
                    return $boleto->generarBoleto($tarjeta);
                }
                // Caso de saldo insuficiente
                else{
                    return $boleto->saldoIns();
                }
            }
        }
        // Caso de timer aun no finalizado
        else{
            return "Ya has pagado un pasaje recientemente";
        }

    }

}