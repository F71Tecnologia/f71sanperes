<?php

abstract class IGrrfTplMethod {

    private $download;
    private $idClt;
    private $empresa;

    function tplMethod() {
        $this->download();
        $this->loadUser();
        $this->loadClt();
//        $this->loadEmpresa();
    }

    abstract function download();

    abstract function loadUser();

    abstract function loadClt();

    abstract function loadEmpresa();
}