<?php

class Clt {
    public $idClt;
    
    function setIdClt($idClt){
        $this->idClt = $idClt;
    }
    function getIdClt(){
        return $this->idClt;
    }
    function setProjeto(Projeto $projeto){
        $this->projeto = $projeto;
    }
    function getProjeto(){
        return $this->projeto;
    }
    function setRegiao(Regiao $regiao){
        $this->regiao = $regiao;
    }
    function getRegiao(){
        return $this->regiao;
    }
    function setCurso(Curso $curso){
        $this->curso = $curso;
    }
    function getCurso(){
        return $this->curso;
    }
    function setCbo($cbo){
        $this->cbo = $cbo;
    }
    function getCbo(){
        return $this->cbo;
    }
}