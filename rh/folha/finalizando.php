<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}

function FormatCPF($Acpf){
//TIRANDO TRAÇOS PONTOS BARRAS ASTERISTICOS ETC
$remover = array(".",",","+","-","*","/","=","_",")","(","&","¨","%","$","#","@","!","[","]","{","}","?","<",">",":",";","\\","|");
$conti = count($remover);
//PEGANDO A VARIAVEL
$cpf = $Acpf;
//REMOVENDO OS CARACTERS INVALIDOS
for($i=0; $i <= $conti; $i++){
	$cpf = str_replace($remover[$i],"",$cpf);
}
//FORMATANDO COM PONTO DE 3 EM 3 CARACTERES
$cpf = wordwrap($cpf,3,".",1);
//EXPLODINDO DE 3 EM 3
$cpf = explode(".",$cpf);
//JUNTANDO OS 4 EXPLODES E FINALIZANDO A FORMATAÇÃO
$cpf = $cpf[0].".".$cpf[1].".".$cpf[2]."-".$cpf[3];
return $cpf;
}


include "../../conn.php";
include "../../funcoes.php";

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$decript = explode("&",$link);

$regiao = $decript[0];
$folha = $decript[1];
$banco = $decript[2];
$id_projeto = $decript[3];
$id_user = $decript[4];
$nome = $decript[5];
$especifica = $decript[6];
$tipo = $decript[7];
$valor = $decript[8];
$data_proc = $decript[9];
$data_vencimento  = $decript[10];
$status = $decript[11];

//RECEBENDO A VARIAVEL CRIPTOGRAFADA

$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM folhas where id_folha = '$folha'")or die(mysql_error());
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'")or die(mysql_error());
$row_projeto = mysql_fetch_array($result_projeto);

$result_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'")or die(mysql_error());
$row_banco = mysql_fetch_array($result_banco);

$tiposDePagamentos = mysql_query("SELECT * FROM tipopg WHERE id_regiao = '$regiao' and campo1 = '1' and id_projeto = '$row_projeto[0]'");
$rowTipoPg = mysql_fetch_array($tiposDePagamentos);

$REContas = mysql_query("SELECT * FROM rh_folha_proc where id_folha = '$folha' and status = '3' and id_banco = '$banco' and tipo_pg='$rowTipoPg[id_tipopg]'");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../net1.css" rel="stylesheet" type="text/css" />
<title>Finalizando Folha</title>
</head>

<body>
<br />
<!--<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
  <td height="24" align="center" bgcolor="#FFFFFF">ID</td>
    <td height="24" align="center" bgcolor="#FFFFFF">NOME</td>
    <td align="center" bgcolor="#FFFFFF">CPF</td>
    <td align="center" bgcolor="#FFFFFF">Agencia</td>
    <td align="center" bgcolor="#FFFFFF">Conta</td>
  </tr> -->
<?php



while($row = mysql_fetch_array($REContas)){
	
	print "<tr bgcolor=#CCCCCC>
	<td>$row[id_clt]</td>
    <td>$row[nome]</td>
    <td align='center'>".FormatCPF($row['cpf'])."</td>
    <td align='center'>$row[agencia]</td>
    <td align='center'>$row[conta]</td>
    </tr>";
	
	//PREPARANDO ARQUIVO
	$nomeT = sprintf("% -40s", $row['nome']);
	$AGT = sprintf("%06s",$row['agencia']);
	$CCT = sprintf("%010s", $row['conta']);
	
	$conteudo .= "$nomeT  $AGT  $CCT\r\n";
	
	mysql_query("UPDATE rh_folha_proc SET status = '4' WHERE id_regiao = '$regiao' and id_folha = '$folha' and id_projeto = $id_projeto and id_banco = '$banco' and id_clt = '$row[id_clt]'")or die(mysql_error());
}



$nome_arquivo_download = "finalizado_".$folha.".txt";
$arquivo = "/home/ispv/public_html/intranet/arquivos/folhaautonomo/".$nome_arquivo_download;

print "</table>";
/*
echo "Região: ".$regiao."<BR>";
echo "id Folha: ".$folha."<BR>";
echo "id Banco: ".$banco."<BR>";
echo "id_projeto: ".$id_projeto."<BR>";
echo "id_user: ".$id_user."<BR>";
echo "nome: ".$nome."<BR>";
echo "especifica: ".$especifica."<BR>";
echo "tipo: ".$tipo."<BR>";
echo "valor: ".$valor."<BR>";
echo "data_proc: ".$data_proc."<BR>";
echo "data_vencimento: ".$data_vencimento."<BR>";
echo "status: ".$status."<BR>";
*/

mysql_query("INSERT INTO saida(id_regiao,id_projeto,id_banco,id_user,nome,especifica,tipo,valor,data_proc,data_vencimento,comprovante,tipo_arquivo,status)VALUES ('$regiao', '$id_projeto', '$banco', '$id_user', '$nome', '$especifica', '$tipo', '$valor', '$data_proc', '$data_vencimento','0','0', '$status')") or die(mysql_error());

/*print "<script>alert('Documento enviado para o finaceiro com sucesso!')</script>";*/
//echo "<br><a href='../arquivos/folhaautonomo/".$nome_arquivo_download."'>TXT</a>";

//TENTA ABRIR O ARQUIVO TXT
if (!$abrir = fopen($arquivo, "wa+")) {
	echo "Erro abrindo arquivo ($arquivo)";
	exit;
}

//ESCREVE NO ARQUIVO TXT
if (!fwrite($abrir, $conteudo)) {
	print "Erro escrevendo no arquivo ($arquivo)";
	exit;
}

//FECHA O ARQUIVO
fclose($abrir);

?>
  
</table>
<?php   
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvolt = encrypt("$regiao&$regiao"); 
$linkvolt = str_replace("+","--",$linkvolt);
// -----------------------------    

//print "<script>location.href='folha.php?id=9&enc=$linkvolt&tela=1'</script>";
?>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
<tr>
<!--	<td align="center" valign="middle" bgcolor="#CCCCCC"><b><a href='folha.php?id=9&<?="enc=".$linkvolt."&tela=1"?>' style="text-decoration:none; color:#000">VOLTAR</a></b></td> -->
</tr>
</table>
</body>
</html>