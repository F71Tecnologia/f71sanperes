<?php 
include ("../include/restricoes.php");


include "../../conn.php";

$id_saida = $_POST['ID'];
$tipo = $_POST['tipo'];
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];

$query_nome = mysql_query("SELECT * FROM entradaesaida_nomes WHERE id_nome = '$nome'");
$row_nome = mysql_fetch_assoc($query_nome);
$descricao_decodificado = utf8_decode($descricao);

$id_prestador = $_POST['interno'];
$regiao_prestador = $_POST['regiao-prestador'];


$sql = "UPDATE saida SET ";
if(!empty($nome)){
	$matriz[] = "nome = '$row_nome[nome]'";
	$matriz[] = "id_nome = '$row_nome[id_nome]'";
}
$matriz[] = "especifica = '$descricao_decodificado'";
if(!empty($tipo)){
	$matriz[] = "tipo = '$tipo'";
}

$sql .= implode(", ",$matriz);

$sql .= " WHERE id_saida = '$id_saida' LIMIT 1;";
if(!mysql_query($sql)) {
	echo '';
}else{
	echo $id_saida;
	
}

// EdiÃ§Ã£o de prestador
if($tipo == '132' or $tipo == '32') {
	
	$qr_prestador_pg = mysql_query("SELECT id_pg FROM prestador_pg WHERE id_saida = '$id_saida' LIMIT 1");
	$num_prestador_pg = mysql_num_rows($qr_prestador_pg);
	$id_pg = @mysql_result($qr_prestador_pg,0);
	
	// BUSCANCO O NOME DO PRESTADOR DE SERVIÇO PARA ATUALIZAR O NOME DA SAIDA
	$qr_nome_prestador = mysql_query("SELECT c_fantasia FROM prestadorservico WHERE id_prestador = '$id_prestador' LIMIT 1");
	$nome_prestador = @mysql_result($qr_nome_prestador,0);
	
	// so entra nesse primeiro if se a saida já tiver prestador cadastrado e estará atualizando o apenas os dados da nota la no prestador
	if(!empty($id_pg) and !empty($regiao_prestador) and !empty($id_prestador)){
		
		
		
		$sql = "UPDATE prestador_pg SET id_prestador = '$id_prestador', id_regiao = '$regiao_prestador' WHERE id_pg = '$id_pg' LIMIT 1";
		mysql_query($sql);
		// atualiza o nome da saida para o nome fantasia do prestador
		mysql_query("UPDATE saida SET nome = '$nome_prestador', id_nome = '0' WHERE id_saida = '$id_saida' LIMIT 1");
		
	}elseif(!empty($_POST['lancamento_prest'])){
		mysql_query("UPDATE prestador_pg SET id_saida = '$id_saida' WHERE id_pg = '$_POST[lancamento_prest]' LIMIT 1");
		
		
		
	}else{
		/* MAIKOM JAMES 19/04/2011 "CADASTRA UMA NOTA NO PRESTADOR SE NÂO MARCAR UMA NENHUMA NOTA"*/
		if($tipo == '132'){
			$tipo_prestador = 'NOTA'; 
		}elseif($tipo == '32'){
			$tipo_prestador = 'FOLHA';
		}
		$query_prestador = mysql_query("SELECT MAX(parcela) FROM prestador_pg WHERE id_prestador = '$id_prestador'");
		$prestador = @mysql_result($query_prestador,0);
		$prestador = $prestador + 1;
		
		
		
		mysql_query("UPDATE saida SET nome = '$nome_prestador', id_nome = '0' WHERE id_saida = '$id_saida' LIMIT 1");
		
		
		// buscanco o valor da saida
		$qr_valor_saida = mysql_query("SELECT valor,data_pg FROM saida WHERE id_saida = '$id_saida' LIMIT 1");
		$row_valor_saida = mysql_fetch_assoc($qr_valor_saida);
		$valor_saida 	= $row_valor_saida['valor'];
		$data_pg 		= $row_valor_saida['data_pg'];
		
		mysql_query("INSERT INTO prestador_pg (id_prestador,	id_regiao,	id_saida, tipo,	valor,	data,	documento,	parcela, gerado,	status_reg,	comprovante)
		VALUES ('$id_prestador', '$regiao_prestador', '$id_saida', '$tipo_prestador', '$valor_saida', '$data_pg', '$descricao_decodificado', '$prestador', '1', '1', '0');
		");
	}
}else{
	
	// LIMPANDO PRESTADOR CASO O EXISTA PRESTADOR CADASTRADO PARA ESSA SAIDA
	
		$qr_prestador_pg = mysql_query("SELECT id_pg FROM prestador_pg WHERE id_saida = '$id_saida' LIMIT 1");
		
	    $num_prestador_pg = mysql_num_rows($qr_prestador_pg);
		
		if(!empty($num_prestador_pg)){
			$row_prestador = mysql_fetch_array($qr_prestador_pg);
			mysql_query("UPDATE prestador_pg SET id_saida = NULL WHERE id_pg = '$row_prestador[0]' LIMIT 1;");
		}

	
	
}

/*
if($tipo == '132' or $tipo == '32'){
	if(empty($id_prestador) or empty($regiao_prestador)) continue;
		$qr_prestador_pg = mysql_query("SELECT id_pg FROM prestador_pg WHERE id_saida = '$id_saida' LIMIT 1");
		$num_prestador_pg = mysql_num_rows($qr_prestador_pg);
		if(empty($num_prestador_pg)) continue;
		$id_pg = @mysql_result($qr_prestador_pg,0);
		
		$sql = "UPDATE prestador_pg SET id_prestador = '$id_prestador', id_regiao = '$regiao_prestador' WHERE id_pg = '$id_pg' LIMIT 1";
		
		mysql_query($sql);
	if(empty($_POST['lancamento_prest'])){
		mysql_query("UPDATE prestador_pg SET id_saida = '$id_saida' WHERE id_pg = '$_POST[lancamento_prest]' LIMIT 1");
	}
		
}*/



?>