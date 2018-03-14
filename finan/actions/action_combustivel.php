<?php

include "../../conn.php";
include "../../classes/uploadfile.php";
require("../../wfunction.php");
require("../../classes/LogClass.php");
include("../../classes/AbastecimentoClass.php");

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
$usuario = carregaUsuario();
$log = new Log();

$objCombustivel = new Abastecimento();

switch ($action) {
    case 'form_liberar_combustivel' : 
        $dadosCombustivel = $objCombustivel->getAbastecimentoAPagar($usuario['id_regiao'], $_REQUEST['id']);
//        print_array($dadosCombustivel);
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <script>$(".valor").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});</script>
        <form action="" method="post" id="form_combustivel" class="form-horizontal">
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-4">
                    <div class="input-group">
                        <label class="input-group-addon pointer" for="aprovar"><input type="radio" id="aprovar" value="1" name="apro" checked="true"></label>
                        <label class="form-control pointer" for="aprovar">Aprovar</label>
                    </div>
                </div>
                <div class="col-sm-offset-1 col-sm-4">
                    <div class="input-group">
                        <label class="input-group-addon pointer" for="recusar"><input type="radio" id="recusar" value="2" name="apro"></label>
                        <label class="form-control pointer" for="recusar">Recusar</label>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Nº Vale</label>
                <div class="col-sm-9"><input name='vale' type='text' class='form-control' id='vale' value='<?=sprintf("%04d",$dadosCombustivel['id_combustivel'])?>'/></div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Valor do Vale</label>
                <div class="col-sm-9"><input name='valor' type='text' class='form-control valor' id='valor' /></div>
            </div>
            
            <input type='hidden' id='regiao' name='regiao' value='<?=$usuario['id_regiao']?>'/>
            <input type='hidden' id='idcomb' name='idcomb' value='<?=$dadosCombustivel['id_combustivel']?>'/>
            <input type='hidden' id='action' name='action' value='liberar_combustivel'/>
            
            
            
            <div class="clear"></div>
        </form><?php
    break;

    case 'liberar_combustivel' : 
        
        $apro = $_REQUEST['apro'];
        $vale = $_REQUEST['vale'];
        $valor = $_REQUEST['valor'];
        $regiao = $_REQUEST['regiao'];
        $idComb = $_REQUEST['idcomb'];
        $dataCad = date('Y-m-d');
        if ($apro == 1) {
            mysql_query("UPDATE fr_combustivel SET status_reg = '2', data_libe = NOW(), numero='$vale', user_libe = '{$_COOKIE['logado']}' WHERE 
                    id_combustivel = '$idComb'");
            $log->gravaLog("Liberação Conbustivel", "Aprovação de Conbustível id $idComb");
            $retorno = 1;
        } else {
            mysql_query("UPDATE fr_combustivel SET status_reg = '0', data_libe = NOW(), user_libe = '{$_COOKIE['logado']}' WHERE id_combustivel = '$idComb'");
            $log->gravaLog("Liberação Conbustivel", "Recusa de Conbustível id $idComb");
            $retorno = 0;
        }
        echo $retorno;
    break;
    
    default:
        echo 'action: ' . $action;
    break;
}