<?php
include('../../conn.php');

// auto complete
$q = strtolower($_REQUEST["q"]);

mysql_query("SET NAMES 'utf8'");

$sql = "SELECT * FROM cnae WHERE descricao LIKE '%$q%' OR codigo LIKE '%$q%' ORDER BY codigo";
$rsd = mysql_query($sql);
$tot = mysql_num_rows($rsd);

//trata navegador, pois no safari, deu erro de caracter e nos outros não
$lista_navegadores = array('MSIE', 'Firefox', 'Chrome', 'Safari');
$navegador_usado = $_SERVER['HTTP_USER_AGENT'];

foreach ($lista_navegadores as $valor_verificar) {
    if (strrpos($navegador_usado, $valor_verificar)) {
        $navegador = $valor_verificar;
    }
}
if ($tot != 0) {
    while ($rs = mysql_fetch_array($rsd)) {
//        $nome = ($navegador == 'Safari') ? utf8_encode($rs['descricao']) : $rs['descricao'];
        $nome = $rs['codigo'].' - '. $rs['descricao'];
//        echo $nome . " * " . $rs['id_cnae'] . "\n";
        $servicos['servicos'][] = $nome . " * " . $rs['id_cnae'];
    }
    echo json_encode($servicos);
} else {
    echo json_encode(['servicos'=>["Nenhum registro encontrado"]]);
}
?>