<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../conn.php";

// PEGA O ID DO FUNCIONÁRIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

//FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$projeto = $_REQUEST['pro'];
$regiao = $_REQUEST['reg'];

//SELECIONANDO OS DADOS DO PROJETO
$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_pro = mysql_fetch_array($result_pro);

$data_hoje = date('d/m/Y');

//SELECIONANDO OS BANCOS CADASTRADOS NO PROJETO SELECIONADO
$result_banco = mysql_query("SELECT * FROM bancos WHERE id_projeto = '$projeto' and id_regiao = '$regiao'");
$cont_bancos = mysql_num_rows($result_banco);

/*
if($cont_bancos == 0) {
	echo "<div align='center' class='Texto10'>Não existe nenhum banco cadastrado para este projeto!</div>";
	exit;
}*/

$contagem1 = '0';
$contagem2 = '0';
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net1.css" rel="stylesheet" type="text/css">
<style>
h1 { page-break-after: always }
.totalizador{
	font-size:14px; 
	font-family:Arial, Helvetica, sans-serif; 
	font-weight:bold;
}
</style>
</head>
<body>
<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">
  <tr>
    <td width="100%" align="center" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="186" align="center" valign="middle" bgcolor="#CFCFCF">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86'/>
          <br>
          <span style="font-size:10px"><?=$row_master['razao']?></span>
          <br><br>
          <span class='title'><?php print "$row_pro[nome] <br> $row_pro[regiao] "; ?></span>
          </td>
        </tr>
    </table></td>
  </tr>
  <tr> 
    <td><div align="center">
        <p><strong><br>
          Informações Bancárias dos Participantes<br>
          <br>
          <?php
/*MOSTRA OS BANCOS CADASTRADOS NO PROJETO SELECIONADO
while($row_banco = mysql_fetch_array($result_banco)){

   print "
    <table border='0' align='center'>
        <tr>
          <td align='center'>
		  <img src='../imagens/bancos/$row_banco[id_nacional].jpg' width='50' height='50' align='absmiddle'></td>
		  <td align='center'>
		  <span class='title'>$row_banco[0] - $row_banco[nome]</span>
			<br>
		  <span class='title' style='font-size:13px; font-color=#999;'>AG: $row_banco[agencia] CC: $row_banco[conta]</span>
		  </td>
        </tr>
      </table>
   
   <hr width=150>
   ";
*/
  print "
  <table width=97% border='0' align='center' cellpadding='0' cellspacing='0' style='font-size:12px; line-height:26px;'>
  <tr height=25>
  <td bgcolor='#CCCCCC' align='center' class='style23'>Cod</td>
  <td bgcolor='#CCCCCC' class='style23'>Nome</td>
  <td bgcolor='#CCCCCC' class='style23'>CPF</td>
   <td bgcolor='#CCCCCC' class='style23'>Banco</td>
  <td bgcolor='#CCCCCC' class='style23'>Tipo de Conta</td>
  <td bgcolor='#CCCCCC' align='center' class='style23'>Agência</td>  
  <td bgcolor='#CCCCCC' align='center' class='style23'>Conta</td>
  <td bgcolor='#CCCCCC' align='center' class='style23'>Salário</td>  
  <td bgcolor='#CCCCCC' align='center' class='style23'>Tipo Contratação</td>  
  </tr>";


// SELECIONANDO OS AUTONOMOS/COOPERADOS QUE ESTAO CADASTRADOS NO BANCO Q ESTÁ RODANDO ATUALMENTE
$result_bolsista = mysql_query("SELECT * FROM autonomo WHERE id_projeto = '$projeto' AND status = '1' ORDER BY nome");

$cont = "0";
while($row_bolsista = mysql_fetch_array($result_bolsista)){
	
	$qry_bol = mysql_query("SELECT * FROM bancos where id_banco='$row_bolsista[banco]'");
	$dados_bol = mysql_fetch_assoc($qry_bol);
 
	$result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_bolsista[id_curso]'");
	$row_curso = mysql_fetch_array($result_curso);
	
	//---- EMBELEZAMENTO DA PAGINA ----------------------------------
	if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
	$nome = str_split($row_bolsista['nome'], 30);
	$nomeT = sprintf("% -30s", $nome[0]);
	$bord = "style='border-bottom:#000 solid 1px;'";
	if($row_bolsista['tipo_contratacao'] == "1"){ $contratacao = "Autonomo"; }else{ $contratacao = "Colaborador"; }
	//-----------------
	
	print "
	<TR height='20' bgcolor=$color>
	<TD align='center' $bord>&nbsp;$row_bolsista[campo3]</TD>
	<TD $bord>&nbsp;$nomeT</TD>
	<TD $bord>&nbsp;$row_bolsista[cpf]</TD>
	<TD $bord>&nbsp;$dados_bol[nome]</TD>
	<TD $bord>&nbsp;".ucwords($row_bolsista['tipo_conta'])."</TD>
	<TD align='center' $bord>&nbsp;$row_bolsista[agencia]</TD>
	<TD align='center' $bord>&nbsp;$row_bolsista[conta]</TD>
	<TD align='center' $bord>R$ $row_curso[valor]</TD>
	<TD align='center' $bord>$contratacao</TD>
	</TR>";
	
	$valor_smp = str_replace(",",".",$row_curso['salario']);
	$valor_soma = $valor_soma + $valor_smp;
	
	$cont ++;	  
	$contagem1 ++;
}

//----------------------------------------------------------------------------------------------------------------------------
//SELECIONA OS CLTS QUE ESTAO CADASTRADOS NO BANCO Q ESTÁ RODANDO ATUALMENTE
$result_clt = mysql_query("SELECT * FROM rh_clt WHERE status = '10' AND id_projeto = '$projeto' ORDER BY nome");
$contclt = "0";
while($row_clt = mysql_fetch_array($result_clt)){
	
	$qry_bol = mysql_query("SELECT * FROM bancos where id_banco='$row_clt[banco]'");
	$dados_bol = mysql_fetch_assoc($qry_bol);
 
	$result_cursoclt = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'");
	$row_cursoclt = mysql_fetch_array($result_cursoclt);
	
	if($contclt % 2){ $colorclt="#f0f0f0"; }else{ $colorclt="#dddddd"; }
	
	$contratacao = "CLT";
	
	print "
	<TR bgcolor=$colorclt>
	<TD $bord align='center'>$row_clt[campo3]</TD>
	<TD $bord>$row_clt[nome]</TD>
	<TD $bord>&nbsp;$row_clt[cpf]</TD>";
	if ($row_clt['nome_banco'] == ''){
	echo "<TD $bord>&nbsp;$dados_bol[nome]</TD>";
	}else{
	echo "<TD $bord>&nbsp;$row_clt[nome_banco]</TD>";
			}
	
	echo "<TD $bord>&nbsp;$row_clt[tipo_conta]</TD>
	<TD $bord>$row_clt[agencia]</TD>
	<TD $bord>$row_clt[conta]</TD>
	<TD $bord>R$ $row_cursoclt[valor]</TD>
	<TD $bord align='center'>$contratacao</TD>
	</TR>";
	
	$valor_smpclt = str_replace(",",".",$row_cursoclt['salario']);
	$valor_somaclt = $valor_somaclt + $valor_smpclt;
	
	
	$contclt ++;	  
	$contagem1 ++;
}

unset($contratacao);

$total_por_banco = $cont + $contclt;
$valor_por_banco = $valor_soma + $valor_somaclt;

$valor_por_banco_f = number_format($valor_por_banco,2,",",".");

print "
<TR bgcolor=#FFFFFF>
<TD $bord colspan=2><span class='totalizador'>&nbsp;&nbsp;&nbsp;$total_por_banco Participantes</span></TD>
<TD $bord colspan=4 align='right'><span class='totalizador'>&nbsp;&nbsp;&nbsp;Valor total:</span></TD>
<TD $bord colspan=2><span class='totalizador'>&nbsp;&nbsp;&nbsp;R$ $valor_por_banco_f</span></TD>
</TR>";
print "</TABLE><Br>";

$somando_os_totais = $somando_os_totais + $total_por_banco;
$somando_os_valores = $somando_os_valores + $valor_por_banco;

$somando_os_valores_f = number_format($somando_os_valores,2,",",".");

//ZERANDO OS DADOS
$total_por_banco = "0";
$cont = "0";
$contclt = "0";
$valor_por_banco = "0"; 
$valor_soma = "0"; 
$valor_somaclt = "0";

//}
?> 

</strong></p>
<p><strong><br>
          <br>
        </strong></p>
        <table width="33%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px">
          <tr>
            <td height="24" colspan="2" align="center" valign="middle" bgcolor="#CCCCCC" class="title"><span class="totalizador">TOTALIZADORES</span></td>
          </tr>
          <tr>
            <td height="27" align="right" valign="middle" bgcolor="#f0f0f0"><span class="Texto10">Participantes Listados:</span></td>
            <td height="27" align="left" valign="middle" bgcolor="#f0f0f0"><span class="Texto10"> &nbsp;&nbsp;<B style="font-family:Arial, Helvetica, sans-serif; font-size:11px">
              <?=$somando_os_totais?>
            </B></span></td>
          </tr>
          <tr>
            <td height="27" align="right" valign="middle" bgcolor="#f0f0f0" class="Texto10">Valor Pago pelos Bancos:</td>
            <td height="27" align="left" valign="middle" bgcolor="#f0f0f0" class="Texto10">&nbsp;&nbsp;<B style="font-family:Arial, Helvetica, sans-serif; font-size:11px">
              R$ <?=$somando_os_valores_f?>
            </B></td>
          </tr>
        </table>
        <p><strong>  <br>
        </strong></p>
        <br>
        <br>
    </div></td>
  </tr>
</table>
</body>
</html>
<?php
/* Liberando o resultado */
mysql_free_result($result_banco);
mysql_free_result($result_bolsista);
//mysql_free_result($result_curso);

//mysql_free_result($RE_bol2);
///mysql_free_result($result_curso2);

/* Fechando a conexão */
mysql_close($conn);

?>