<?php 

namespace TrabajoSube;

use PHPUnit\Framework\TestCase;

class BoletoTest extends TestCase{

    // Comprobar que se genere correctamente el boleto en tarjeta normal
    public function testBoletoNormal(){
        $bole = new Boleto();
        $cole = new Colectivo();
        $costecmp = $cole->costePasaje;
        $saldoinicial = 1000;
        $id = 466752;
        $tarj = new FranquiciaParcial($saldoinicial,$id);
        $tipocompare = $tarj->tipo;


        if($cole->verif){
            $this->assertEquals($bole->conocerAbonado($cole,$tarj),$costecmp / 2);
        }
        else{
            $this->assertEquals($bole->conocerAbonado($cole,$tarj),$costecmp);
        }
        $this->assertEquals($bole->conocerTipo($tarj),$tipocompare);
        $this->assertEquals($bole->conocerID($tarj),$id);
    }

    // Comprobar que se genere correctamente el boleto en franquicia parcial
    public function testBoletoParcial(){
        $bole = new Boleto();
        $cole = new Colectivo();
        $costecmp = $cole->costePasaje;
        $saldoinicial = 1000;
        $id = 466752;
        $tarj = new FranquiciaParcial($saldoinicial,$id);
        $tipocompare = $tarj->tipo;


        if($cole->verif){
            $this->assertEquals($bole->conocerAbonado($cole,$tarj),$costecmp / 2);
        }
        else{
            $this->assertEquals($bole->conocerAbonado($cole,$tarj),$costecmp);
        }
        $this->assertEquals($bole->conocerTipo($tarj),$tipocompare);
        $this->assertEquals($bole->conocerID($tarj),$id);
    }

    // Comprobar que se genere correctamente el boleto en franquicia completa
    public function testBoletoCompleta(){
        $bole = new Boleto();
        $cole = new Colectivo();
        $costecmp = $cole->costePasaje;
        $saldoinicial = 1000;
        $id = 466752;
        $tarj = new FranquiciaParcial($saldoinicial,$id);
        $tipocompare = $tarj->tipo;


        if($cole->verif){
            $this->assertEquals($bole->conocerAbonado($cole,$tarj),$costecmp / 2);
        }
        else{
            $this->assertEquals($bole->conocerAbonado($cole,$tarj),$costecmp);
        }
        $this->assertEquals($bole->conocerTipo($tarj),$tipocompare);
        $this->assertEquals($bole->conocerID($tarj),$id);
    }

}