<?php

$wFunction = new WorldClass();
$usuario = $wFunction->carregaUsuario();

if (isset($_REQUEST['editar'])) {
    
    $gestao = "Ediçao";
    
} else {
    
    $gestao = "Cadastro";
    $master = $usuario->id_master;
    $regiao = $usuario->id_regiao;
    $projeto = $regiao;
    $resutMaior = mysql_query("SELECT CAST(campo3 AS UNSIGNED) campo30, MAX(campo3) FROM autonomo WHERE id_regiao= '$id_regiao' AND id_projeto = '$projeto' AND campo3 != 'INSERIR' GROUP BY campo30 ASC");
    $rowMaior = mysql_num_rows($resutMaior);
    $codigo = $rowMaior + 1;
    
}

$arrRegioes = $wFunction->getRegioes();
$arrRegioes = $wFunction->toSelect($arrRegioes, 'id_regiao', 'regiao');

$arrMasters = $wFunction->getMasters();
$arrMasters = $wFunction->toSelect($arrMasters, 'id_master', 'nome');
$arrProjetos = $wFunction->getProjetos($master, $regiao);
$arrProjetos = $wFunction->toSelect($arrProjetos,'id_projeto','nome');

$arrTipoSang = $wFunction->getTiposSanguineos();
$arrTipoSang = $wFunction->toSelect($arrTipoSang, 'nome', 'nome', 0);

$arrEstadoCivil = $wFunction->getEstadosCivis();
$arrEstadoCivil = $wFunction->toSelect($arrEstadoCivil, 'id_estado_civil', 'nome_estado_civil');

$arrEscolaridade = $wFunction->getEscolaridades();
$arrEscolaridade = $wFunction->toSelect($arrEscolaridade, 'id', 'nome');

$arrBanco = $wFunction->getBancos($regiao, $projeto);
$arrBanco = $wFunction->toSelect($arrBanco, 'id_banco', 'nome');

$arrListaBancos = $wFunction->getListaBancos();
$arrListaBancos = $wFunction->toSelect($arrListaBanco, 'id_lista', 'nome');

$sqlTipoPgto = "SELECT * FROM tipopg WHERE id_projeto = '$projeto'";
$queryTipoPgto = mysql_query($sqlTipoPgto);
while ($rowTipoPgto = mysql_fetch_assoc($queryTipoPgto)) {
    $arrTipoPgto[$rowTipoPgto['id_tipopg']] = $rowTipoPgto['tipopg'];
}

$dadosHeader = $wFunction->montaCabecalhoNovo($arrRegioes, $arrMasters, $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Gestão de Autônomos");
$breadcrumb_pages = array("Visualizar Projeto" => "/intranet/rh/ver.php");
