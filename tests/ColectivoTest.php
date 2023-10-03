<?php 

namespace TrabajoSube;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase{

    public function testcargaTarjeta(){
        $tarj = new Tarjeta(0);
        $carga = 500;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),500);
    }

    public function testcargaTarjetaMax(){
        $tarj = new Tarjeta(6000);
        $carga = 800;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),"Te pasaste del saldo maximo (6600)");
    }

    public function testcargaTarjetaInval(){
        $tarj = new Tarjeta(0);
        $carga = 333;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),"Valor de carga invalido. Los valores validos son: 150, 200, 250, 300, 350, 400, 450, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 2000, 2500, 3000, 3500 o 4000");
    }
    
    public function testpagarConSaldo(){
        $cole = new Colectivo();
        $tarj = new Tarjeta(0);
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: -120");
    }

    public function testpagarSinSaldo(){
        $cole = new Colectivo();
        $tarj = new Tarjeta(-211);
        $this->assertEquals($cole->pagarCon($tarj),FALSE);
    }

}
