<?php

class Rescisao {

    private $codMovimentacao;
    private $dataDemi;
    private $codigoSaque;
    private $avisoCodigo;
    private $dataAviso;
    private $terceiroSs;
    private $avisoValor;

    function setCodMovimentacao($codMovimentacao) {
        $this->codMovimentacao = $codMovimentacao;
    }

    function getCodMovimentacao() {
        return $this->codMovimentacao;
    }

    function setDataDemi($dataDemi) {
        $this->dataDemi = $dataDemi;
    }

    function getDataDemi() {
        return $this->dataDemi;
    }

    function setDataAviso($dataAviso) {
        $this->dataAviso = $dataAviso;
    }

    function getDataAviso() {
        return $this->dataAviso;
    }

    function setCodSaque($codigoSaque) {
        $this->codSaque = $codigoSaque;
    }

    function getCodSaque() {
        return $this->codSaque;
    }

    function setAvisoCodigo($avisoCodigo) {
        $this->avidoCodigo = $avisoCodigo;
    }

    function getAvisoCodigo() {
        return $this->avidoCodigo;
    }

    function setTerceiroSs($terceiroSs) {
        $this->terceiroSs = $terceiroSs;
    }

    function getTerceiroSs() {
        return $this->terceiroSs;
    }

    function setAvisoValor($avisoValor) {
        $this->avisoValor = $avisoValor;
    }

    function getAvisoValor() {
        return $this->avisoValor;
    }

}
