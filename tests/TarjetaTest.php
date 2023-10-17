<?php 

namespace TrabajoSube;

use PHPUnit\Framework\TestCase;

class TarjetaTest extends TestCase{

    // Cargar $500 a una tarjeta con $0 de saldo
    public function testcargaTarjeta(){
        $saldoinicial = 0;
        $carga = 500;
        $saldofinal = $saldoinicial + $carga;
        $tarj = new Tarjeta($saldoinicial);
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),'Se han cargado $' . $carga . '. Saldo final: $' . $saldofinal);
    }

    // Comprobar que no se haga la carga si se ingresa un valor de carga no listado en la consigna
    public function testcargaTarjetaInval(){
        $tarj = new Tarjeta();
        $carga = 666;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),"Valor de carga invalido. Los valores validos son: 150, 200, 250, 300, 350, 400, 450, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 2000, 2500, 3000, 3500 o 4000");
    }

    // Hacer una carga a una tarjeta con saldo negativo para comprobar que se descuente lo que se debe
    public function testcargaPlus(){
        $saldoinicial = -100;
        $carga = 200;
        $tarj = new Tarjeta($saldoinicial);
        $saldofinal = $saldoinicial + $carga;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),'Se han cargado $' . $carga . '. Saldo final: $' . $saldofinal);
    }

    // Comprobar que se pueda usar el pasaje plus
    public function testpagarConPlus(){
        $saldoinicial = 0;
        $tarj = new Tarjeta($saldoinicial);
        $cole = new Colectivo();
        $saldofinal = $saldoinicial - $cole->costePasaje;
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. La tarjeta adeuda $" . -$saldofinal);
    }

    public function test4MedioBoletos(){
        $cole = new Colectivo();
        $saldoinicial = 1000;
        $tarj = new FranquiciaParcial($saldoinicial);
        
        $cole->verif = true; // Para que se pueda testear el beneficio sin estar en fecha u horario habil
        
        // Test de los 4 medios boletos diarios
        $cole->timerNuevoPago = 0; // Seteamos en 0 para que se puedan pagar boletos aunque no hayan pasado los 5 minutos, solo para el test
        $saldofinal = $saldoinicial - ($cole->costePasaje / 2);
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . $saldofinal);
        $saldofinal -= $cole->costePasaje / 2;
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . $saldofinal);
        $saldofinal -= $cole->costePasaje / 2;
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . $saldofinal);
        $saldofinal -= $cole->costePasaje / 2;
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . $saldofinal);
        $saldofinal -= $cole->costePasaje;
        $this->assertEquals($tarj->viajesHoy,4);
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . $saldofinal);
    }

    // Comprobar cuando se exceda el saldo maximo en una carga, se cargue hasta este saldo maximo, y comprobar que se acredite la carga pendiente al pagar un pasaje
    public function testcargaTarjetaMax(){
        $saldoinicial = 6400;
        $tarj = new Tarjeta($saldoinicial);
        $cole = new Colectivo();
        $carga = 400;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),"Te pasaste del saldo maximo ($6600). Se cargará la tarjeta hasta este saldo y el excedente se acreditará a medida que se use la tarjeta.");
        if(($tarj->saldoMax - $saldoinicial) <= $carga){
            $acr = $carga - ($tarj->saldoMax - $saldoinicial);
        }
        else $acr = $carga;
        $this->assertEquals($tarj->saldoPendiente, $acr);

        $cole->timerNuevoPago = 0; // para poder pagar varios pasajes uno atras del otro
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . 6600);
        $this->assertEquals($tarj->saldoPendiente, 80);
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . 6560);
        $this->assertEquals($tarj->saldoPendiente, 0);
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . 6440);
    }

    // Comprobar que dependiendo de cuantos viajes se hayan hecho, se aplicaran los distintos descuentos
    public function testcomprobarDescuento(){
        $saldoinicial = 1000;
        $tarj = new Tarjeta($saldoinicial);
        $cole = new Colectivo();
        $cole->timerNuevoPago = 0; // Seteamos en 0 para que se puedan pagar boletos aunque no hayan pasado los 5 minutos, solo para el test
        
        $saldofinal = $saldoinicial - $cole->costePasaje;
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . $saldofinal); // tarifa normal
        $tarj->viajesEsteMes = $tarj->multiplicador1APartirDe; // para probar el 20%
        $saldofinal -= ($cole->costePasaje * $tarj->multiplicador1);
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . $saldofinal);

        $tarj->viajesEsteMes = $tarj->multiplicador2APartirDe - 1; // para probar el 25%
        $saldofinal -= ($cole->costePasaje * $tarj->multiplicador1);
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . $saldofinal);
        $this->assertEquals($tarj->viajesEsteMes, $tarj->multiplicador2APartirDe);
        $saldofinal -= ($cole->costePasaje * $tarj->multiplicador2);
        $this->assertEquals($cole->pagarCon($tarj), "Pago exitoso. Saldo restante: $" . $saldofinal);
        
    }

}