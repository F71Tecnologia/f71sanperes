<?php

include_once("../../conn.php");
include_once("../../classes/FolhaClass.php");
include_once("../../classes/CalculoFolhaClass.php");
include_once("../../classes/SqlInjectionClass.php");

//SQLINJECTION
$sqlInjection = new SqlInjection();
//CACULOS
$calculos = new Calculo_Folha();
$data = $calculos->getPeriodoFolha("2014-06-01");
$data_fim = $data['finalizado_em'];
//FOLHA
$folha = new Folha($sqlInjection);

$obj = new stdClass;
$obj->parte = 1;
$obj->data_proc = "2014-06-01";
$obj->mes = 06;
$obj->ano = 2014;
$obj->ferias = 0;
$obj->data_inicio = "2014-06-01";
$obj->data_fim = $data_fim;
$obj->regiao = 45;
$obj->projeto = 3302;
$obj->terceiro = 2;
$obj->tipo_terceiro = "";
$obj->user = 204;


$folha->criaFolha($obj, true);







