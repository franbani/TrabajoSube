<?php 

namespace TrabajoSube;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase{

    // Comprobar que se descuente el precio del pasaje en tarjetas comunes, y que no se pueda pagar pasaje sin que transcurran los 5 min
    public function testpagarConSaldo(){
        $cole = new Colectivo();
        $saldoInicial = 500;
        $tarj = new Tarjeta($saldoInicial);
        $saldoFinal = $saldoInicial - $cole->costePasaje;
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . $saldoFinal);
        $this->assertEquals($cole->pagarCon($tarj),"Ya has pagado un pasaje recientemente");
    }

    // Comprobar que se devuelva FALSE al intentar pagar con una tarjeta que ya gasto el plus
    public function testpagarSinSaldo(){
        $cole = new Colectivo();
        $saldoNegativo = -100;
        $tarj = new Tarjeta($saldoNegativo);
        $this->assertEquals($cole->pagarCon($tarj),FALSE);
    }

    // Comprobar una tarjeta de franquicia completa puede pagar siempre un boleto y probar generacion de boleto
    public function testFranquiciaCompleta(){
        $cole = new Colectivo();
        $costeCmp = $cole->costePasaje;
        $bole = new Boleto();
        $saldoInicial = 500;
        $id = 756442;
        $tarj = new FranquiciaCompleta($saldoInicial,$id);
        $tipoCmp = $tarj->tipo;
        
        if($cole->verif){
            $this->assertEquals($bole->conocerAbonado($cole,$tarj),0);
        }
        else{
            $this->assertEquals($bole->conocerAbonado($cole,$tarj),$costeCmp);
        }
        $this->assertEquals($bole->conocerTipo($tarj),$tipoCmp);
        $this->assertEquals($bole->conocerID($tarj),$id);

        
        $cole->verif = true; // Para que se pueda testear el beneficio sin estar en un fecha u horario habil
        
        // Test de los 2 BEG diarios
        $cole->timerNuevoPago = 0; // Seteamos en 0 para que se puedan pagar boletos aunque no hayan pasado los 5 minutos, solo para el test
        $this->assertEquals($cole->pagarCon($tarj),"Descuento completo aplicado");
        $this->assertEquals($cole->pagarCon($tarj),"Descuento completo aplicado");
        $this->assertEquals($tarj->viajesHoy, 2);
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . $saldoInicial - $cole->costePasaje);
        
    }


    // Comprobar que los objetos de clase Interurbano se generen con linea Expreso y coste de pasaje $270 por defecto
    public function testInterurbano(){
        $cole = new Interurbano();
        $tarj = new Tarjeta();
        $bole = new Boleto();
        $this->assertEquals($bole->conocerAbonado($cole,$tarj),184);
        $this->assertEquals($bole->conocerLinea($cole),"Expreso");
    }

    // Comprobar que si no se esta en fecha y hora habil no se efectuará el beneficio de franquicia, y se cobrará normalmente el pasaje
    public function testFranquiciaNoHabil(){
        $saldoInicial = 1000;
        $tarjBeg = new FranquiciaCompleta($saldoInicial);
        $tarjParcial = new FranquiciaParcial($saldoInicial);
        $cole = new Colectivo();

        $cole->verif = false; // Para testearlo sin necesidad de estar en fyh inhabil

        $this->assertEquals($cole->pagarCon($tarjBeg),"Pago exitoso. Saldo restante: $" . $saldoInicial - $cole->costePasaje);
        $this->assertEquals($cole->pagarCon($tarjParcial),"Pago exitoso. Saldo restante: $" . $saldoInicial - $cole->costePasaje);
    }


}