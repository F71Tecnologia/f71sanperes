<?php

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br/><a href="../login.php">Logar</a>';
    exit;
}

//error_reporting(E_ALL);

// Incluindo Arquivos
require('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include("../../classes/BotoesClass.php");

$usuario = carregaUsuario(); // carrega dados do usuário
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;
$method = (isset($_REQUEST['method'])) ? strtolower($_REQUEST['method']) : null;
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"38", "area"=>"Contabilidade", "ativo"=>"SPED", "id_form"=>"form-sped-contabil");
switch ($method) {
    case 'inicio':
    default:
        $anos[0] = '« Selecione o Ano »';
        for($i = date('Y')-6;$i<=date('Y')+1;$i++){
            $anos[$i] = $i;
        }
        $meses = mesesArray();
        
        require_once 'inicio.php';
        break;
}
