<?php

class  FactoryValeClass {

    static function createVale($tipoVale) {

        $dao = NULL;
        
        switch ($tipoVale) {
            case 1:
                include_once('classes/DaoValeSodexoClass.php');
                $dao = new DaoValeSodexoClass();
                break;
            case 2:
                include_once('classes/DaoValeAeloClass.php');
                $dao = new DaoValeAeloClass();
                break;
            default:
//                $dao = new DaoValeAlimentacaoAeloClass();
                break;
        }
        return $dao;
    }

}
