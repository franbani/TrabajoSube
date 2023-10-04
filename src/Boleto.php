<?php

namespace TrabajoSube;

class Boleto{

    public function generarBoleto($tarjeta){ // Caso en el cual se permite pagar el pasaje
        $texto = "Pago exitoso. Saldo restante: " . $tarjeta->saldo;
        return $texto;
    }

    public function saldoIns(){ // Caso en el cual no se permita el pago por saldo insuficiente
        echo 'Saldo insuficiente';
        return FALSE;
    }

}
