<?php
if(empty($_COOKIE['logado'])) {
    return false;
}

include('../../conn.php');
include('../../wfunction.php');
include ('../../classes/LogClass.php');

$log = new Log();

$unidade = $_POST['unidade'];
$banco = $_POST['banco'];
$duracao = $_POST['duracao'];

$inicio_db = implode('-', array_reverse(explode('/', $_POST['data_inicio'])));
$fim_db = date('Y-m-d', strtotime('+'.$duracao.' months', strtotime($inicio_db)));
$unidades = ($unidade) ? array($unidade) : array_keys((array) json_decode($_POST['unidades_id']));
$unidades_id = json_decode($_POST['unidades_id'], true);
$codigos = json_decode($_POST['codigos'], true);
$descricoes = json_decode(utf8_encode($_POST['descricoes']), true);
$valores = json_decode($_POST['valores'], true);
$qr_orcamento = mysql_query("SELECT * FROM gestao_orcamentos WHERE unidade_id = '{$unidade}' AND inicio > '{$inicio_db}' AND fim < '{$inicio_db}'");
$row_orcamento = mysql_fetch_assoc($qr_orcamento);
$orcamento_existente = mysql_num_rows($qr_orcamento);

//echo '<pre>';print_r($unidades);exit;
if(!$orcamento_existente) {

    foreach($unidades as $unidade) {
//        echo "B";exit;
        $unidade_id = $unidades_id[$unidade];
//        print_array($unidade_id);exit;
//      echo "INSERT INTO gestao_orcamentos (projeto_id, unidade_id, banco_id, inicio, fim) VALUES (2, '{$unidade_id}', '{$banco}', '{$inicio_db}', '{$fim_db}')<br>";
//        $query_cabecalho = mysql_query("INSERT INTO gestao_orcamentos (projeto_id, unidade_id, banco_id, inicio, fim) VALUES (2, '{$unidade_id}', '{$banco}', '{$inicio_db}', '{$fim_db}')") or die(mysql_error());
        $query_cabecalho = mysql_query("INSERT INTO gestao_orcamentos (banco_id, inicio, fim) VALUES ('{$banco}', '{$inicio_db}', '{$fim_db}')") or die("ERRO Ao CRIAR ORÇAMENTO" . mysql_error());
        
        $orcamento_id = mysql_insert_id();
        $log->gravaLog('Financeiro', "Orçamento Importado: ID{$orcamento_id}");
        
        if(is_array($unidade_id))
            foreach ($unidade_id as $value) 
                $query_assoc = mysql_query("INSERT INTO gestao_orcamentos_unidades_associativas (`id_unidade`, `id_orcamento`) VALUES ('{$value}', '{$orcamento_id}');") or die("ERRO AO ASSOSSIAR AS UNIDADES" . mysql_error());
        else
            $query_assoc = mysql_query("INSERT INTO gestao_orcamentos_unidades_associativas (`id_unidade`, `id_orcamento`) VALUES ('{$unidade_id}', '{$orcamento_id}');") or die("ERRO AO ASSOSSIAR AS UNIDADES" . mysql_error());

        foreach($codigos[$unidade] as $chave => $codigo) {
            for($mes=1; $mes<=$duracao; $mes++) {
                $propriedade = mysql_real_escape_string(utf8_decode($descricoes[$unidade][$chave]));
                $valor = str_replace(',', '.', str_replace('.', '', $valores[$unidade][$chave][$mes]));
//              echo "INSERT INTO gestao_orcamentos_valores (orcamento_id, mes, codigo, propriedade, valor) VALUES ({$orcamento_id}, '{$mes}', '{$codigo}', '{$propriedade}', '{$valor}')<br>";
                $query_cabecalho = mysql_query("INSERT INTO gestao_orcamentos_valores (orcamento_id, mes, codigo, propriedade, valor) VALUES ({$orcamento_id}, '{$mes}', '{$codigo}', '{$propriedade}', '{$valor}')") or die(mysql_error());
            }
        }

    }
//	echo "A";exit;
} else {

	foreach($codigos[$unidade] as $chave => $codigo) {
		for($mes=1; $mes<=$duracao; $mes++) {
			$valor = $valores[$unidade][$chave][$mes];
			$query_cabecalho = mysql_query("UPDATE gestao_orcamentos_valores SET valor = '{$valor}' WHERE orcamento_id = '{$row_orcamento[id]}' AND codigo = '{$codigo}' AND mes = '{$mes}'") or die(mysql_error());
		}
	}

}

header('Location: index.php');
exit;
?>
