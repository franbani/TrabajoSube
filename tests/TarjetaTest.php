<?php 

namespace TrabajoSube;

use PHPUnit\Framework\TestCase;

class TarjetaTest extends TestCase{

    // Cargar $500 a una tarjeta con $0 de saldo
    public function testcargaTarjeta(){
        $saldoInicial = 0;
        $carga = 500;
        $saldoFinal = $saldoInicial + $carga;
        $tarj = new Tarjeta($saldoInicial);
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga), 'Se han cargado $' . $carga . '. Saldo final: $' . $saldoFinal);
    }

    // Comprobar que no se haga la carga si se ingresa un valor de carga no listado en la consigna
    public function testcargaTarjetaInval(){
        $tarj = new Tarjeta();
        $carga = 666;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga), "Valor de carga invalido. Los valores validos son: 150, 200, 250, 300, 350, 400, 450, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 2000, 2500, 3000, 3500 o 4000");
    }

    // Hacer una carga a una tarjeta con saldo negativo para comprobar que se descuente lo que se debe
    public function testcargaPlus(){
        $saldoInicial = -100;
        $carga = 200;
        $tarj = new Tarjeta($saldoInicial);
        $saldoFinal = $saldoInicial + $carga;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga), 'Se han cargado $' . $carga . '. Saldo final: $' . $saldoFinal);
    }

    // Comprobar que se pueda usar el pasaje plus
    public function testpagarConPlus(){
        $saldoInicial = 0;
        $tarj = new Tarjeta($saldoInicial);
        $cole = new Colectivo();
        $saldoFinal = $saldoInicial - $cole->costePasaje;
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. La tarjeta adeuda $" . -$saldoFinal);
    }

    public function test4MedioBoletos(){
        $cole = new Colectivo();
        $saldoInicial = 1000;
        $tarj = new FranquiciaParcial($saldoInicial);
        
        $cole->verif = true; // Para que se pueda testear el beneficio sin estar en fecha u horario habil
        
        // Test de los 4 medios boletos diarios
        $cole->timerNuevoPago = 0; // Para poder pagar varios pasajes uno atras del otro
        $saldoFinal = $saldoInicial - ($cole->costePasaje / 2);
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . $saldoFinal);
        $saldoFinal -= $cole->costePasaje / 2;
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . $saldoFinal);
        $saldoFinal -= $cole->costePasaje / 2;
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . $saldoFinal);
        $saldoFinal -= $cole->costePasaje / 2;
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . $saldoFinal);
        $saldoFinal -= $cole->costePasaje;
        $this->assertEquals($tarj->viajesHoy, 4);
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . $saldoFinal);
    }

    // Comprobar cuando se exceda el saldo maximo en una carga, se cargue hasta este saldo maximo, y comprobar que se acredite la carga pendiente al pagar un pasaje
    public function testcargaTarjetaMax(){
        $saldoInicial = 6400;
        $tarj = new Tarjeta($saldoInicial);
        $cole = new Colectivo();
        $carga = 400;
        $this->assertEquals($tarj->cargaTarjeta($tarj, $carga), "Te pasaste del saldo maximo ($" . $tarj->saldoMax . "). Se cargará la tarjeta hasta este saldo y el excedente se acreditará a medida que se use la tarjeta.");
        if(($tarj->saldoMax - $saldoInicial) <= $carga){
            $acr = $carga - ($tarj->saldoMax - $saldoInicial);
        }
        else $acr = $carga;
        $this->assertEquals($tarj->saldoPendiente, $acr);

        $cole->timerNuevoPago = 0; // Para poder pagar varios pasajes uno atras del otro
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . 6600);
        $this->assertEquals($tarj->saldoPendiente, 80);
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . 6560);
        $this->assertEquals($tarj->saldoPendiente, 0);
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . 6440);
    }

    // Comprobar que dependiendo de cuantos viajes se hayan hecho, se aplicaran los distintos descuentos
    public function testcomprobarDescuento(){
        $saldoInicial = 1000;
        $tarj = new Tarjeta($saldoInicial);
        $cole = new Colectivo();
        $cole->timerNuevoPago = 0; // Para poder pagar varios pasajes uno atras del otro
        
        $saldoFinal = $saldoInicial - $cole->costePasaje;
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . $saldoFinal); // tarifa normal
        $tarj->viajesEsteMes = $tarj->multiplicador1APartirDe; // para probar el descuento 1
        $saldoFinal -= ($cole->costePasaje * $tarj->multiplicador1);
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . $saldoFinal);

        $tarj->viajesEsteMes = $tarj->multiplicador2APartirDe - 1; // para probar el descuento 2
        $saldoFinal -= ($cole->costePasaje * $tarj->multiplicador1);
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . $saldoFinal);
        $this->assertEquals($tarj->viajesEsteMes, $tarj->multiplicador2APartirDe);
        $saldoFinal -= ($cole->costePasaje * $tarj->multiplicador2);
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . $saldoFinal);
        
    }

}