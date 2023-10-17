<?php 

namespace TrabajoSube;

use PHPUnit\Framework\TestCase;

class BoletoTest extends TestCase{

    // Comprobar que se genere correctamente el boleto en tarjeta normal
    public function testBoletoNormal(){
        $bole = new Boleto();
        $cole = new Colectivo();
        $costeCmp = $cole->costePasaje;
        $saldoInicial = 1000;
        $id = 466752;
        $tarj = new Tarjeta($saldoInicial,$id);
        $tipoCmp = $tarj->tipo;

        $this->assertEquals($bole->conocerAbonado($cole,$tarj),$costeCmp);
        $this->assertEquals($bole->conocerTipo($tarj),$tipoCmp);
        $this->assertEquals($bole->conocerID($tarj),$id);
    }

    // Comprobar que se genere correctamente el boleto en franquicia parcial
    public function testBoletoParcial(){
        $bole = new Boleto();
        $cole = new Colectivo();
        $costeCmp = $cole->costePasaje;
        $saldoInicial = 1000;
        $id = 466752;
        $tarj = new FranquiciaParcial($saldoInicial,$id);
        $tipoCmp = $tarj->tipo;


        if($cole->verif){
            $this->assertEquals($bole->conocerAbonado($cole,$tarj),$costeCmp / 2);
        }
        else{
            $this->assertEquals($bole->conocerAbonado($cole,$tarj),$costeCmp);
        }

        $this->assertEquals($bole->conocerTipo($tarj),$tipoCmp);
        $this->assertEquals($bole->conocerID($tarj),$id);
    }

    // Comprobar que se genere correctamente el boleto en franquicia completa
    public function testBoletoCompleta(){
        $bole = new Boleto();
        $cole = new Colectivo();
        $costeCmp = $cole->costePasaje;
        $saldoInicial = 1000;
        $id = 466752;
        $tarj = new FranquiciaCompleta($saldoInicial, $id);
        $tipoCmp = $tarj->tipo;


        // Testear que se pueda comprar el pasaje correctamente, tanto estándose en fyh habil como no
        if($cole->verif){
            $this->assertEquals($bole->conocerAbonado($cole,$tarj), 0);
        }
        else{
            $this->assertEquals($bole->conocerAbonado($cole,$tarj), $costeCmp);
        }

        $this->assertEquals($bole->conocerTipo($tarj), $tipoCmp);
        $this->assertEquals($bole->conocerID($tarj), $id);
    }

    public function testConocerLinea(){
        $bole = new Boleto();
        $lineaCmp = "A Chapuy";
        $cole = new Colectivo($lineaCmp);
        $this->assertEquals($bole->conocerLinea($cole), $lineaCmp);
    }

}