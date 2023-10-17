<?php 

namespace TrabajoSube;

use PHPUnit\Framework\TestCase;

class ColectivoTest extends TestCase{

    // Comprobar que se descuente el precio del pasaje en tarjetas comunes, y que no se pueda pagar pasaje sin que transcurran los 5 min
    public function testpagarConSaldo(){
        $cole = new Colectivo();
        $saldoinicial = 500;
        $tarj = new Tarjeta($saldoinicial);
        $saldofinal = $saldoinicial - $cole->costePasaje;
        $this->assertEquals($cole->pagarCon($tarj),"Pago exitoso. Saldo restante: $" . $saldofinal);
        $this->assertEquals($cole->pagarCon($tarj),"Ya has pagado un pasaje recientemente");
    }

    // Comprobar que se devuelva FALSE al intentar pagar con una tarjeta que ya gasto el plus
    public function testpagarSinSaldo(){
        $cole = new Colectivo();
        $saldonegativo = -100;
        $tarj = new Tarjeta($saldonegativo);
        $this->assertEquals($cole->pagarCon($tarj),FALSE);
    }

    // Comprobar una tarjeta de franquicia completa puede pagar siempre un boleto y probar generacion de boleto
    public function testFranquiciaCompleta(){
        $cole = new Colectivo();
        $costecmp = $cole->costePasaje;
        $bole = new Boleto();
        $saldoinicial = 500;
        $id = 756442;
        $tarj = new FranquiciaCompleta($saldoinicial,$id);
        $tipocmp = $tarj->tipo;
        
        if($cole->verif){
            $this->assertEquals($bole->conocerAbonado($cole,$tarj),0);
        }
        else{
            $this->assertEquals($bole->conocerAbonado($cole,$tarj),$costecmp);
        }
        $this->assertEquals($bole->conocerTipo($tarj),$tipocmp);
        $this->assertEquals($bole->conocerID($tarj),$id);

        
        $cole->verif = true; // Para que se pueda testear el beneficio sin estar en un fecha u horario habil
        
        // Test de los 2 BEG diarios
        $cole->timerNuevoPago = 0; // Seteamos en 0 para que se puedan pagar boletos aunque no hayan pasado los 5 minutos, solo para el test
        $this->assertEquals($cole->pagarCon($tarj),"Descuento completo aplicado");
        $this->assertEquals($cole->pagarCon($tarj),"Descuento completo aplicado");
        $this->assertEquals($tarj->viajesHoy, 2);
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

    // Comprobar que si no se esta en fecha y hora habil no se efectuará el beneficio de franquicia, y se cobrará normalmente el pasaje
    public function testFranquiciaNoHabil(){
        $saldoinicial = 1000;
        $tarjbeg = new FranquiciaCompleta($saldoinicial);
        $tarjparcial = new FranquiciaParcial($saldoinicial);
        $cole = new Colectivo();

        $cole->verif = false; // Para testearlo sin necesidad de estar en fyh inhabil

        $this->assertEquals($cole->pagarCon($tarjbeg),"Pago exitoso. Saldo restante: $" . $saldoinicial - $cole->costePasaje);
        $this->assertEquals($cole->pagarCon($tarjparcial),"Pago exitoso. Saldo restante: $" . $saldoinicial - $cole->costePasaje);
    }


}