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
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),'Se han cargado $' . $carga . '. Saldo final: $' . $saldofinal);
    }

    // Hacer una carga a una tarjeta con saldo negativo para comprobar que se descuente lo que se debe
    public function testcargaPlus(){
        $cole = new Colectivo();
        $saldoinicial = -100;
        $carga = 200;
        $tarj = new Tarjeta($saldoinicial);
        $saldofinal = $saldoinicial + $carga;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),'Se han cargado $' . $carga . '. Saldo final: $' . $saldofinal);
    }

    // Comprobar cuando se exceda el saldo maximo en una carga, se cargue hasta este saldo maximo, y comprobar que se acredite la carga pendiente al pagar un pasaje
    public function testcargaTarjetaMax(){
        $tarj = new Tarjeta(6400);
        $cole = new Colectivo();
        $carga = 400;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),"Te pasaste del saldo maximo ($6600). Se cargará la tarjeta hasta este saldo y el excedente se acreditará a medida que se use la tarjeta.");
        $this->assertEquals($tarj->saldo, $tarj->saldoMax);
        $this->assertEquals($tarj->saldoPendiente, 200);

        $cole->timerNuevoPago = 0; // para poder pagar varios pasajes uno atras del otro
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . 6600);
        $this->assertEquals($tarj->saldoPendiente, 80);
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . 6560);
        $this->assertEquals($tarj->saldoPendiente, 0);
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . 6440);
    }

    // Comprobar que no se haga la carga si se ingresa un valor de carga no listado en la consigna
    public function testcargaTarjetaInval(){
        $tarj = new Tarjeta(0);
        $carga = 333;
        $this->assertEquals($tarj->cargaTarjeta($tarj,$carga),"Valor de carga invalido. Los valores validos son: 150, 200, 250, 300, 350, 400, 450, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 2000, 2500, 3000, 3500 o 4000");
    }

    // Comprobar que se descuente el precio del pasaje en tarjetas comunes, y que no se pueda pagar pasaje sin que transcurran los 5 min
    public function testpagarConSaldo(){
        $cole = new Colectivo();
        $saldoinicial = 500;
        $tarj = new Tarjeta($saldoinicial);
        $saldofinal = $saldoinicial - $cole->costePasaje;
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . $saldofinal);
        $this->assertEquals($cole->pagarCon($tarj),"Ya has pagado un pasaje recientemente");
    }

    // Comprobar que se pueda usar el pasaje plus
    public function testpagarConPlus(){
        $cole = new Colectivo();
        $saldoinicial = 0;
        $tarj = new Tarjeta($saldoinicial);
        $saldofinal = $saldoinicial - $cole->costePasaje;
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. La tarjeta adeuda $" . -$saldofinal);
    }

    // Comprobar que se devuelva FALSE al intentar pagar con una tarjeta que ya gasto el plus
    public function testpagarSinSaldo(){
        $cole = new Colectivo();
        $tarj = new Tarjeta(-100);
        $this->assertEquals($cole->pagarCon($tarj),FALSE);
    }

    // Comprobar que se genere correctamente el boleto en franquicia parcial
    public function testFranquiciaParcial(){
        $cole = new Colectivo();
        $bole = new Boleto();
        $saldoinicial = 1000;
        $tarj = new FranquiciaParcial($saldoinicial,466752);

        // Test de datos de boleto
        $this->assertEquals($bole->conocerAbonado($cole,$tarj),60);
        $this->assertEquals($bole->conocerTipo($tarj),"parcial");
        $this->assertEquals($bole->conocerID($tarj),466752);

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

    // Comprobar una tarjeta de franquicia completa puede pagar siempre un boleto y probar generacion de boleto
    public function testFranquiciaCompleta(){
        $cole = new Colectivo();
        $bole = new Boleto();
        $saldoinicial = 500;
        $tarj = new FranquiciaCompleta($saldoinicial,756442);
    
        $this->assertEquals($bole->conocerAbonado($cole,$tarj),0);
        $this->assertEquals($bole->conocerTipo($tarj),"completa");
        $this->assertEquals($bole->conocerID($tarj),756442);

        // Test de los 2 BEG diarios
        $cole->timerNuevoPago = 0; // Seteamos en 0 para que se puedan pagar boletos aunque no hayan pasado los 5 minutos, solo para el test
        $this->assertEquals($cole->pagarCon($tarj),"Descuento completo aplicado");
        $this->assertEquals($cole->pagarCon($tarj),"Descuento completo aplicado");
        $this->assertEquals($tarj->viajesHoy,2);
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . $saldoinicial - 120);
    }


    // Comprobar que los objetos de clase Interurbano se generen con linea Expreso y coste de pasaje $270 por defecto
    public function testInterurbano(){
        $cole = new Interurbano();
        $tarj = new Tarjeta();
        $bole = new Boleto();
        $this->assertEquals($bole->conocerAbonado($cole,$tarj),184);
        $this->assertEquals($bole->conocerLinea($cole),"Expreso");
    }


}
