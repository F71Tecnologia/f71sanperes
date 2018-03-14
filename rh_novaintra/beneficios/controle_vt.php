<?php
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.f71iabassp.com/intranet/login.php?entre=true");
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/SodexoClass.php");
include("../../classes/ValeTransporteClass.php");

include("../../classes/ValeAlimentacaoRefeicaoClass.php");
include("../../classes/ValeAlimentacaoRefeicaoRelatorioClass.php");

$objPedido = new ValeAlimentacaoRefeicaoClass();
$objParticipantes = new ValeAlimentacaoRefeicaoRelatorioClass();

$objVT = new ValeTransporteClass;

$tipo = 3;
$id = $_REQUEST['id'];
$usuario = $_COOKIE['logado'];

$pedido = $objVT->listar($id);

$mes = sprintf("%02d", $pedido['mes']);
$ano = $pedido['ano'];
$id_regiao = $pedido['id_regiao'];

if($id_regiao == 3){
    $cod_cliente = 1733884;
}else{
    $cod_cliente = 1717075;
}

$sodexo = new SodexoClass($mes, $ano, $id, $tipo, $pedido['data_entrega'], $pedido['data_credito'], $cod_cliente);

$cod_cliente = $sodexo->cod_cliente;

$nome_file = normalizaNometoFile("SDXV5_{$cod_cliente}_VT_{$mes}_{$ano}.txt");
$arquivo = fopen($nome_file, "w");

// DADOS DA EMPRESA
$empregador = $objVT->getEmpregador($id_regiao);

//DADOS DA UNIDADE
$unidade = $objVT->getUnidade($id);

$sodexo->montaReg0($arquivo, $empregador);

while($res_unidade = mysql_fetch_assoc($unidade)){
    $sodexo->montaReg3($arquivo, $res_unidade);
    
    $empregado = $objVT->getParticipantes($res_unidade['id_pedido'], $res_unidade['id_unidade']);        
    
    while($res_empregado = mysql_fetch_assoc($empregado)){               
        $sodexo->montaReg4($arquivo, $res_empregado, $linha_clt);
    }
}

$sodexo->montaReg9($arquivo);

fclose($arquivo);

// BAIXA O ARQUIVO
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-type: application/x-msdownload");
header("Content-Length: " . filesize($nome_file));
header("Content-Disposition: attachment; filename=$nome_file");
flush();

readfile($nome_file);
exit();
?>