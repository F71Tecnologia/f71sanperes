<?php 
if(empty($_COOKIE['logado'])) {
	print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a>";
	exit;
}

include('../../conn.php');

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'") or die(mysql_error());
$row_func = mysql_fetch_assoc($qr_funcionario);

$qr_master= mysql_query("SELECT B.* FROM regioes AS A
                         INNER JOIN master as B
                         ON A.id_master = B.id_master
                         WHERE A.id_regiao = '$row_func[id_regiao]'")  or die(mysql_error());
$row_master = mysql_fetch_assoc($qr_master);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SEFIP Cooperado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
<?php
// Variáveis
$mes  = $_GET['mes'];
$ano  = $_GET['ano'];
$tipo_empresa = $_POST['empresa'];

$data_comparacao = implode('-', array_reverse(explode('/', $_GET['data'])));
$data            = str_replace('/','',$_GET['data']);

// Buscando Ids DAS FOLHAS PARA GERAR SEFIP ---- SELECT * FROM folha_cooperado WHERE id_folha IN( 980,988)  AND status = '3'
$qr_participantes = mysql_query("SELECT * FROM folha_cooperado WHERE id_folha = '$_GET[folha]'  AND status = '3'");
while($row_participante = mysql_fetch_assoc($qr_participantes)) {
	$ids[]      = $row_participante['id_folha_pro'];
	//$ids_grrf[] = $row_participante['id_clt'];
}




$ids      = implode(',', $ids);
//$ids_grrf = implode(',', $ids_grrf);

// Buscando Arquivo
$qr_nome_arquivo = mysql_query("SELECT * FROM folhas WHERE id_folha = '$_GET[folha]'");
$nome_arquivo    = mysql_fetch_assoc($qr_nome_arquivo);

$id_regiao = $nome_arquivo['regiao'];
$id_projeto = $nome_arquivo['projeto'];




$local_arquivo   = 'arquivos/'.$nome_arquivo['regiao'].'_'.$nome_arquivo['projeto'].'_'.$nome_arquivo['mes'].'_'.$nome_arquivo['ano'].'.re';
if(file_exists($local_arquivo)) {
	unlink($local_arquivo);
}
$arquivo = fopen("$local_arquivo", "a");

// Variável para Sefip por Folha
$sefip_folha = true;

// SEFIP
require('corpo_sefip.php');
// Inserindo Sefip no Banco de Dados
mysql_query("INSERT INTO sefip (mes, ano, regiao, projeto, folha, tipo_sefip, data, autor) VALUES ('$mes', '$ano', '$_GET[regiao]', '$_GET[projeto]', '$_GET[folha]', '4', NOW(), '$_COOKIE[logado]')");

fclose($arquivo);

print "<a href='arquivos/download.php?file=".$nome_arquivo['regiao']."_".$nome_arquivo['projeto']."_".$nome_arquivo['mes']."_".$nome_arquivo['ano'].".re'>Baixar arquivo do Sefip</a><br>";
?>
</body>
</html>