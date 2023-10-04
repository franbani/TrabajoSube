<?php 

namespace TrabajoSube;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase{

    public function testcargaTarjeta(){ // Carga $500 a una tarjeta con $0 de saldo
        $tarj = new Tarjeta(0);
        $carga = 500;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),500);
    }

    public function testcargaPlus(){ // Hace una carga a una tarjeta con saldo negativo para comprobar que se descuente lo que se debe
        $tarj = new Tarjeta(-100);
        $carga = 200;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),100);
    }

    public function testcargaTarjetaMax(){ // Comprueba que no se pueda exceder el saldo maximo de 6600 pesos
        $tarj = new Tarjeta(6000);
        $carga = 800;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),"Te pasaste del saldo maximo (6600)");
    }

    public function testcargaTarjetaInval(){ // Comprueba que no se haga la carga si se ingresa un valor de carga no listado en la consigna
        $tarj = new Tarjeta(0);
        $carga = 333;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),"Valor de carga invalido. Los valores validos son: 150, 200, 250, 300, 350, 400, 450, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 2000, 2500, 3000, 3500 o 4000");
    }

    public function testpagarConSaldo(){ // Comprueba que se descuente el precio del pasaje
        $cole = new Colectivo();
        $tarj = new Tarjeta(500);
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: 380");
    }

    public function testpagarConPlus(){ // Comprueba que se pueda usar el pasaje plus
        $cole = new Colectivo();
        $tarj = new Tarjeta(0);
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: -120");
    }


    public function testpagarSinSaldo(){ // Comprueba que se devuelva FALSE al intentar pagar con una tarjeta que ya gasto el plus
        $cole = new Colectivo();
        $tarj = new Tarjeta(-100);
        $this->assertEquals($cole->pagarCon($tarj),FALSE);
    }

}
