<?php

require("../../conn.php");
require("../../wfunction.php");
include "../../funcoes.php";

$charset = mysql_set_charset('utf8');

$qr_func = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_func = mysql_fetch_assoc($qr_func);

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
$grupo = isset($_REQUEST['grupo']) ? $_REQUEST['grupo'] : NULL;
$regiao = isset($_REQUEST['regiao']) ? $_REQUEST['regiao'] : NULL;
$projeto = isset($_REQUEST['projeto']) ? $_REQUEST['projeto'] : NULL;
$master = isset($_REQUEST['id_master']) ? $_REQUEST['id_master'] : NULL;
$subgrupo = isset($_REQUEST['subgrupo']) ? $_REQUEST['subgrupo'] : NULL;
$usuario = carregaUsuario();
switch ($action) {
    case 'load_projeto' :
        $rs = montaQuery("projeto", "*", "status_reg = 1 AND id_regiao = {$regiao}");
        $option = '<option value="" >Todos os projetos</option>';
        foreach ($rs as $value) {
            $selected = ($value['id_projeto'] == $projeto) ? ' selected="selected" ' : ' ';
            $option .= '<option value="' . $value['id_projeto'] . '"  ' . $selected . '  >' . $value['nome'] . ' </option>';
        }
        echo $option;
        break;
    case 'load_subgrupo':
        $rs = montaQuery("entradaesaida_subgrupo", "*", "entradaesaida_grupo = {$grupo}");
        $option = '<option value="" selected="selected" >Todos os Subgrupos</option>';
        foreach ($rs as $value) {
            $option .= '<option value="' . $value['id_subgrupo'] . '"  >' . $value['id_subgrupo'] . ' - ' . $value['nome'] . ' </option>';
        }
        if(empty($rs)){ 
            echo '<option value="" selected="selected" >Selecione o grupo</option>'; 
        }else{
            echo $option;
        }
        break;
    case 'load_tipo':
//        $rs = montaQuery("entradaesaida_subgrupo", "*", "id = {$subgrupo}");
        $option = '<option value=""  selected="selected">Todos os Tipos</option>';
        if ($subgrupo != "") {
            $query = mysql_query("SELECT id_entradasaida, cod, nome FROM  entradaesaida WHERE cod LIKE '$subgrupo%'");
            while ($row = mysql_fetch_assoc($query)) {
                $option .= '<option value="' . $row['id_entradasaida'] . '" >' . $row['cod'] . ' - ' . $row['nome'] . ' </option>';
            }
        }
        echo $option;
        break;
    
    default:
        echo 'action: ' . $action;
        break;
}