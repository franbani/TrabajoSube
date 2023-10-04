<?php 

namespace TrabajoSube;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase{

    // Cargar $500 a una tarjeta con $0 de saldo
    public function testcargaTarjeta(){
        $saldoinicial = 0;
        $carga = 500;
        $saldofinal = $saldoinicial + $carga;
        $tarj = new Tarjeta($saldoinicial);
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),$saldofinal);
    }

    // Hacer una carga a una tarjeta con saldo negativo para comprobar que se descuente lo que se debe
    public function testcargaPlus(){
        $cole = new Colectivo();
        $saldoinicial = -100;
        $carga = 200;
        $tarj = new Tarjeta($saldoinicial);
        $saldofinal = $saldoinicial + $carga;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),$saldofinal);
    }

    // Comprobar que no se pueda exceder el saldo maximo de 6600 pesos
    public function testcargaTarjetaMax(){
        $tarj = new Tarjeta(6000);
        $carga = 800;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),"Te pasaste del saldo maximo (6600)");
    }

    // Comprobar que no se haga la carga si se ingresa un valor de carga no listado en la consigna
    public function testcargaTarjetaInval(){
        $tarj = new Tarjeta(0);
        $carga = 333;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),"Valor de carga invalido. Los valores validos son: 150, 200, 250, 300, 350, 400, 450, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 2000, 2500, 3000, 3500 o 4000");
    }

    // Comprobar que se descuente el precio del pasaje en tarjetas comunes
    public function testpagarConSaldo(){
        $cole = new Colectivo();
        $saldoinicial = 500;
        $tarj = new Tarjeta($saldoinicial);
        $saldofinal = $saldoinicial - $cole->costePasaje;
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: " . $saldofinal);
    }

    // Comprobar que se pueda usar el pasaje plus
    public function testpagarConPlus(){
        $cole = new Colectivo();
        $saldoinicial = 0;
        $tarj = new Tarjeta($saldoinicial);
        $saldofinal = $saldoinicial - $cole->costePasaje;
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: " . $saldofinal);
    }

    // Comprobar que se devuelva FALSE al intentar pagar con una tarjeta que ya gasto el plus
    public function testpagarSinSaldo(){
        $cole = new Colectivo();
        $tarj = new Tarjeta(-100);
        $this->assertEquals($cole->pagarCon($tarj),FALSE);
    }

    // Comprobar una tarjeta de FranquiciaCompleta siempre puede pagar un boleto
    public function testFranquiciaCompleta(){
        $cole = new Colectivo();
        $saldoinicial = 500;
        $tarjbeg = new FranquiciaCompleta(500);
        $this->assertEquals($cole->pagarCon($tarjbeg),"Pago exitoso. Saldo restante: " . $saldoinicial);
        $this->assertEquals($cole->pagarCon($tarjbeg),"Pago exitoso. Saldo restante: " . $saldoinicial);
        $this->assertEquals($cole->pagarCon($tarjbeg),"Pago exitoso. Saldo restante: " . $saldoinicial);
    }

    // Comprobar que el monto del boleto pagado con medio boleto es siempre la mitad del normal
    public function testFranquiciaParcial(){
        $cole = new Colectivo();
        $saldoinicial = 500;
        $tarj = new FranquiciaParcial($saldoinicial);
        $saldofinal = $saldoinicial - ($cole->costePasaje / 2);
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: " . $saldofinal);
    }

}
