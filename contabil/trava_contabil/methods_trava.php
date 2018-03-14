<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br/><a href="../login.php">Logar</a>';
    exit;
}

error_reporting(E_ALL);

// Incluindo Arquivos
require('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include("../../classes/BotoesClass.php");
include("../../classes/ContabilLancamentoClass.php");
include("../classes/ContabilTravaClass.php");



$usuario = carregaUsuario(); // carrega dados do usuário
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)


$objLancamento = new ContabilLancamentoClass();
$objTrava = new ContabilTravaClass();

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'destravar') {
    echo $_REQUEST['id_trava'];
    $objTrava->setIdTrava($_REQUEST['id_trava']);
    
    $destravar = $objTrava->deleta();
    echo $destravar;
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'travar') {
    
    $objTrava->setPeriodo($_REQUEST['travar_periodo']); 
    $objTrava->setIdProjeto($_REQUEST['travar_projeto']);
   
    $arrayTravar = $objTrava->insert();

    echo $travar;
    exit();
}
