<?php

class Grrf {
    
//    private $rescisao;
    private $dataRecolhimento;
    private $mes;
    private $ano;
    private $regiao;
    private $projeto;
    private $clt;
    private $valorBaseInformado;
    
   
//    function setRescisao($rescisao){
//        $this->rescisao = $rescisao;
//        return $this;
//    }
//    function getRescisao(){
//        return $this->rescisao;
//    }
    function setDataRecolhimento($dataRecolhimento){
        $this->dataRecolhimento = $dataRecolhimento;
        return $this;
    }
    function getDataRecolhimento(){
        return $this->dataRecolhimento;
    }
    function setMes($mes){
        $this->mes = $mes;
        return $this;
    }
    function getMes(){
        return $this->mes;
    }
    function setAno($ano){
        $this->ano = $ano;
        return $this;
    }
    function getAno(){
        return $this->ano;
    }
    function setRegiao($regiao){
        $this->regiao = $regiao;
        return $this;
    }
    function getRegiao(){
        return $this->regiao;
    }    
    function setClt(Clt $clt){
        $this->clt = $clt;
        return $this;
    }
    function getClt(){
        return $this->clt;
    }
    function setValorBaseInformado($valorBaseInformado){
        $this->valorBaseInformado = $valorBaseInformado;
        return $this;
    }
    function getValorBaseInformado(){
        return $this->valorBaseInformado;
    }
    
    
    
}