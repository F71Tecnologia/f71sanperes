<?php 
include "../../../conn.php";

$id_rpa = $_POST['rpa'];
$id_user = $_COOKIE['logado'];
//$valor = $_POST['valor'];
$valor = str_replace(',','.',str_replace('.','',$_POST['valor']));


$qr_rpa = mysql_query("SELECT * FROM rh_rpa WHERE id_rpa = '$id_rpa'");
$row_rpa = mysql_fetch_assoc($qr_rpa);

$qr_autonomo = mysql_query("SELECT id_autonomo,nome FROM autonomo WHERE id_autonomo = '$row_rpa[id_autonomo]'");
$id_autonomo = mysql_result($qr_autonomo,0);
$nome_autonomo = mysql_result($qr_autonomo,0,1);
$id_regiao = '15';
$id_projeto = '23';
$id_banco = '75';

$tipo = '30';

$nome_saida = "FOLHA DE PAGAMENTO AUTONOMO ($id_autonomo) $nome_autonomo";
$arquivo = '../arquivos/'.$id_rpa.'.pdf';

if(!file_exists($arquivo)) {
	echo "Arquivo não encontrado...";
	exit;
}

mysql_query("INSERT INTO saida (id_regiao, id_projeto,	id_banco,	id_user,	nome,	especifica,	tipo, valor,	data_proc,	data_vencimento, comprovante, status) VALUES ('$id_regiao','$id_projeto','$id_banco','$id_user','$nome_saida','','$tipo','$valor',NOW(), '$row_rpa[data]', '2','1')");

$saida = mysql_query("SELECT MAX(id_saida) FROM saida");
$id_saida = mysql_result($saida,0);

mysql_query("INSERT INTO saida_files (id_saida,tipo_saida_file) VALUES ('$id_saida','.pdf')");
$file = mysql_query("SELECT MAX(id_saida_file) FROM saida_files");
$id_file = mysql_result($file,0);

$nome_comprovante = $id_file.'.'.$id_saida.'.pdf';

if(!copy($arquivo,'../../../comprovantes/'.$nome_comprovante)){
	echo "Erro ao copiar arquivo...";
}else{
	echo '<script type="text/javascript">
		parent.window.location.reload();
		if (parent.window.hs) {
			var exp = parent.window.hs.getExpander();
			if (exp) {			
					exp.close();
			}
		}
	</script>';
}
?>