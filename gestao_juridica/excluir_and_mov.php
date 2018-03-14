<?php
include ("include/restricoes.php");
include('../conn.php');
include('../funcoes.php');

$id_processo = mysql_real_escape_string($_GET['id_processo']);


if(isset($_GET['tp'])) {
	$id_andamento = mysql_real_escape_string($_GET['id']);
	
	
	$tipo_trabalhador =  mysql_real_escape_string($_GET['tp']);
	$qr_andammento = mysql_query("UPDATE proc_trab_andamento SET	andamento_status = 0 WHERE andamento_id='$id_andamento' LIMIT 1");
	
	$qr_andamento  = mysql_query("SELECT * FROM proc_trab_andamento WHERE andamento_id = '$id_andamento' ");
	$row_andamento = mysql_fetch_assoc($qr_andamento);
	
	
	
	$verifica_financeiro = mysql_num_rows(mysql_query("SELECT * FROM processo_status WHERE proc_status_id = '$row_andamento[proc_status_id]' AND vinculo_financeiro = 1"));

	
	if($verifica_financeiro != 0){
		
		$qr_andamento_assoc = mysql_query("SELECT * FROM andamento_saida_assoc WHERE andamento_id = '$id_andamento'");
			 while($row_assoc = mysql_fetch_assoc($qr_andamento_assoc)):
			 
			 	mysql_query("DELETE FROM saida WHERE id_saida = '$row_assoc[id_saida]' LIMIT 1");
			 endwhile;
			
				mysql_query("DELETE FROM andamento_saida_assoc WHERE andamento_id = '$id_andamento' ");
			
	

	}
	
	
	
	
	
	
	if($tipo_trabalhador == 2){
		header("Location: processo_trabalhista/dados_trabalhador/ver_trabalhador.php?id_processo=$id_processo");
	} else {
		header("Location: ver_trabalhador.php?id_processo=$id_processo");
	}

}

if(isset($_GET['id_movimento'])) {

$id_movimento = $_GET['id_movimento'];
mysql_query("UPDATE proc_trab_movimentos SET status = 0 WHERE proc_trab_mov_id = '$id_movimento'");

header("Location: processo_trabalhista/dados_trabalhador/ver_trabalhador.php?id_processo=$id_processo");

}
?>