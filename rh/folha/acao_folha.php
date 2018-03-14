<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/FolhaClass.php";
include "../../classes/LogClass.php";

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 
$decript = explode("&",$link);
$regiao = $decript[0];
$id_folha = $decript[1];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA


print "
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
<link href='../net.css' rel='stylesheet' type='text/css' /></head><body>";
echo "<br><br>";
echo "<div class='style7' align='center'><img src='../../imagens/carregando/loading.gif' border='0'><br>Aguarde...<br> Estamos trabalando em sua solicitação</div><br><br>";
echo "<pre>";


$file = $_SERVER['DOCUMENT_ROOT']."/intranet/arquivos/folhaclt/idfolha_$id_folha.txt";

// Lê todo o arquivo para um array
$fp = file($file);

// Faz uma iteraÃ§Ã£o com todas as linhas do arquivo
$i = "0";
foreach($fp as $linha){

    // EXECUTANDO UMA QUERY PRA CADA LINHA
    mysql_query($linha);
    $i ++;
    
}

//LOG PARA FECHAR FOLHA
$folhas = new Folha();
$log = new Log();
$folhas->logFecharFolha($id_folha, $_COOKIE['logado']);
$log->gravaLog('Folha de Pgto.', "Fechamento de Folha CLT: ID{$id_folha}");

//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvolt = encrypt("$regiao&1"); 
$linkvolt = str_replace("+","--",$linkvolt);
// -----------------------------


print "
<script>
alert(\"$i Registros Afetados\");
location.href=\"folha.php?enc=$linkvolt&tela=1\"
</script>

</body>
</html>
";

?>