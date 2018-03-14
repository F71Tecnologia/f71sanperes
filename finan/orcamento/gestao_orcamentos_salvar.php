<?php
if(empty($_COOKIE['logado'])) {
    return false;
}

include('../../conn.php');
include('../../wfunction.php');

$cabecalho = $_POST['cabecalho'];
$valores = $_POST['valores'];
$banco = $cabecalho['banco'];
$inicio = $cabecalho['inicio'];
$fim = $cabecalho['fim'];
$inicio_db = implode('-', array_reverse(explode('/', $inicio)));
$fim_db = implode('-', array_reverse(explode('/', $fim)));
//print_array("SELECT *, TIMESTAMPDIFF(MONTH,inicio,ADDDATE(fim, INTERVAL 1 DAY)) AS dif_meses FROM gestao_orcamentos WHERE projeto_id = '{$projeto}' AND (inicio BETWEEN '{$inicio_db}' AND '{$fim_db}' OR fim BETWEEN '{$inicio_db}' AND '{$fim_db}')");exit;
//$qr_orcamento = mysql_query("SELECT *, TIMESTAMPDIFF(MONTH,inicio,ADDDATE(fim, INTERVAL 1 DAY)) AS dif_meses FROM gestao_orcamentos WHERE projeto_id = '{$projeto}' /* AND unidade_id = '{$unidade}' */ AND inicio >= '{$inicio_db}' AND fim <= '{$inicio_db}'");
$qr_orcamento = mysql_query("SELECT *, TIMESTAMPDIFF(MONTH,inicio,ADDDATE(fim, INTERVAL 1 DAY)) AS dif_meses FROM gestao_orcamentos WHERE banco_id = '{$banco}' AND (inicio BETWEEN '{$inicio_db}' AND '{$fim_db}' OR fim BETWEEN '{$inicio_db}' AND '{$fim_db}')");
$row_orcamento = mysql_fetch_assoc($qr_orcamento);
$orcamento_existente = mysql_num_rows($qr_orcamento);

if(!$orcamento_existente) {

    $query_cabecalho = mysql_query("INSERT INTO gestao_orcamentos (banco_id, inicio, fim) VALUES ('{$banco}', '{$inicio_db}', '{$fim_db}')") or die(mysql_error());

    $orcamento_id = mysql_insert_id();
//    echo "SELECT *, TIMESTAMPDIFF(MONTH,A.inicio,ADDDATE(A.fim, INTERVAL 1 DAY)) AS dif_meses FROM gestao_orcamentos WHERE id = '{$orcamento_id}'";exit;
    $qr_orcamento = mysql_query("SELECT *, TIMESTAMPDIFF(MONTH,inicio,ADDDATE(fim, INTERVAL 1 DAY)) AS dif_meses FROM gestao_orcamentos WHERE id = '{$orcamento_id}'");
    $row_orcamento = mysql_fetch_assoc($qr_orcamento);

    for($i=1;$i<=$row_orcamento['dif_meses'];$i++){
        foreach($valores as $valor) {
            $codigo = $valor['codigo'];
            $descricao = mysql_real_escape_string(utf8_decode($valor['propriedade']));
//            $valor = str_replace(',', '.', str_replace('.', '', $valor['valor'])) / $row_orcamento['dif_meses'];
            $valor = str_replace(',', '.', str_replace('.', '', $valor['valor']));
            $query_cabecalho = mysql_query("INSERT INTO gestao_orcamentos_valores (orcamento_id, codigo, propriedade, valor, mes) VALUES ({$orcamento_id}, '{$codigo}', '{$descricao}', '{$valor}', '{$i}')") or die(mysql_error());
        }
    }
//
//    $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '{$projeto}'");
//    $row_projeto = mysql_fetch_assoc($qr_projeto);
//
//    $qr_unidade = mysql_query("SELECT * FROM unidade WHERE id_unidade = '{$unidade}'");
//    $row_unidade = mysql_fetch_assoc($qr_unidade);

    echo json_encode(['id' => $orcamento_id, 'inicio'=> $inicio, 'fim' => $fim]);
	
} else {
    for($i=1;$i<=$row_orcamento['dif_meses'];$i++){
        foreach($valores as $valor) {
            $codigo = $valor['codigo'];
//            $valor = str_replace(',', '.', str_replace('.', '', $valor['valor'])) / $row_orcamento['dif_meses'];
            $valor = str_replace(',', '.', str_replace('.', '', $valor['valor']));
            $query_cabecalho = mysql_query("UPDATE gestao_orcamentos_valores SET valor = '{$valor}' WHERE orcamento_id = '{$row_orcamento[id]}' AND codigo = '{$codigo}' AND mes = '{$i}';") or die(mysql_error());
        }
    }
    
    echo json_encode(['id' => $orcamento_id, 'inicio'=> $inicio, 'fim' => $fim]);
}

exit;
?>
