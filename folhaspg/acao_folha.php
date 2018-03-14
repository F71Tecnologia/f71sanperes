<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}

include "../conn.php";
include "../funcoes.php";


//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$decript = explode("&",$link);

$regiao = $decript[0];
$folha = $decript[1];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA


print "
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
<title>Rela&ccedil;&atilde;o de Sal&aacute;rios L&iacute;quidos</title>
<link href='../net.css' rel='stylesheet' type='text/css' /></head><body>";
echo "<br><br>";
echo "<div class='style7' align='center'><img src='../imagens/carregando/loading.gif' border='0'><br>Aguarde...<br> Estamos trabalando 
em sua solicitação</div><br><br>";

//SELECIONANDO A FOLHA
$result_folha = mysql_query("SELECT *,date_format(data_inicio, '%d/%m/%Y')as data_inicio2,date_format(data_fim, '%d/%m/%Y')as data_fim2,date_format(data_proc, '%d/%m/%Y')as data_proc2 FROM folhas where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);


if($row_folha['contratacao'] == 1){
	$nome_arquivo_download = "autonomo_".$folha.".txt";
	$file = "/home/ispv/public_html/intranet/arquivos/folhaautonomo/".$nome_arquivo_download;
}else{
	$nome_arquivo_download = "cooperado_".$folha.".txt";
	$file = "/home/ispv/public_html/intranet/arquivos/folhacooperado/".$nome_arquivo_download;
}

// Lê todo o arquivo para um array
$fp = file($file);
    
// Faz uma iteraÃ§Ã£o com todas as linhas do arquivo
$i = "0";
foreach($fp as $linha){

	// EXECUTANDO UMA QUERY PRA CADA LINHA
	mysql_query($linha);
	$i ++;
}


//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvolt = encrypt("$regiao&$regiao"); 
$linkvolt = str_replace("+","--",$linkvolt);
// -----------------------------

$iFinal = $i - 2;

print "
<script>
alert(\"$iFinal Registros Afetados\");
location.href=\"folha.php?id=9&enc=$linkvolt&tela=1\"
</script>

</body>
</html>
";

?>