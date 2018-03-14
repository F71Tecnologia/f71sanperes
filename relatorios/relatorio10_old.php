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

if($cont_bancos == 0) {
	echo "<div align='center' class='Texto10'>Não existe nenhum banco cadastrado para este projeto!</div>";
	exit;
}

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
          RELAT&Oacute;RIO DE CONTAS BANC&Aacute;RIAS<br>
          <br>
          <?php
//MOSTRA OS BANCOS CADASTRADOS NO PROJETO SELECIONADO
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

  print "
  <table width=97% border='0' align='center' cellpadding='0' cellspacing='0' style='font-size:12px; line-height:26px;'>
  <tr height=25>
  <td bgcolor='#CCCCCC' align='center' class='style23'>Cod</td>
  <td bgcolor='#CCCCCC' class='style23'>Nome</td>
  <td bgcolor='#CCCCCC' class='style23'>CPF</td>
   <td bgcolor='#CCCCCC' class='style23'>RG</td>
  <td bgcolor='#CCCCCC' class='style23'>Tipo de Conta</td>
  <td bgcolor='#CCCCCC' align='center' class='style23'>Agência</td>  
  <td bgcolor='#CCCCCC' align='center' class='style23'>Conta</td>
  <td bgcolor='#CCCCCC' align='center' class='style23'>Salário</td>  
  <td bgcolor='#CCCCCC' align='center' class='style23'>Tipo Contratação</td>  
  <td bgcolor='#CCCCCC' align='center' class='style23'>Forma Pagamento</td>  
  </tr>";


// SELECIONANDO OS AUTONOMOS/COOPERADOS QUE ESTAO CADASTRADOS NO BANCO Q ESTÁ RODANDO ATUALMENTE
$result_bolsista = mysql_query("SELECT A.id_curso,A.nome,A.campo3,A.cpf,A.rg,A.tipo_conta,A.agencia,A.conta,B.tipopg,B.campo1 FROM autonomo AS A
                                    LEFT JOIN tipopg AS B ON (A.tipo_pagamento=B.id_tipopg)
                                    WHERE A.banco = '$row_banco[id_banco]' AND A.id_projeto = '$projeto' AND A.status = '1' ORDER BY A.nome");

$cont = "0";
while($row_bolsista = mysql_fetch_array($result_bolsista)){
 
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
	<TD $bord>&nbsp;$row_bolsista[rg]</TD>
	<TD $bord>&nbsp;".ucwords($row_bolsista['tipo_conta'])."</TD>
	<TD align='center' $bord>&nbsp;$row_bolsista[agencia]</TD>
	<TD align='center' $bord>&nbsp;$row_bolsista[conta]</TD>
	<TD align='center' $bord>R$ $row_curso[valor]</TD>
	<TD align='center' $bord>$contratacao</TD>
	<TD align='center' $bord>{$row_bolsista['tipopg']}</TD>
	</TR>";
	
	$valor_smp = str_replace(",",".",$row_curso['salario']);
	$valor_soma = $valor_soma + $valor_smp;
	
	$cont ++;	  
	$contagem1 ++;
}

//----------------------------------------------------------------------------------------------------------------------------
//SELECIONA OS CLTS QUE ESTAO CADASTRADOS NO BANCO Q ESTÁ RODANDO ATUALMENTE
$result_clt = mysql_query("SELECT A.id_curso,A.id_curso,A.nome,A.campo3,A.cpf,A.rg,A.tipo_conta,A.agencia,A.conta,B.tipopg,B.campo1 FROM rh_clt AS A
                                LEFT JOIN tipopg AS B ON (A.tipo_pagamento=B.id_tipopg)
                                WHERE A.banco = '$row_banco[id_banco]' AND A.status = '10' AND A.id_projeto = '$projeto' ORDER BY A.nome");
$contclt = "0";
while($row_clt = mysql_fetch_array($result_clt)){
 
	$result_cursoclt = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'");
	$row_cursoclt = mysql_fetch_array($result_cursoclt);
	
	if($contclt % 2){ $colorclt="#f0f0f0"; }else{ $colorclt="#dddddd"; }
	
	$contratacao = "CLT";
	
	print "
	<TR bgcolor=$colorclt>
	<TD $bord align='center'>$row_clt[campo3]</TD>
	<TD $bord>$row_clt[nome]</TD>
	<TD $bord>&nbsp;$row_clt[cpf]</TD>
	<TD $bord>&nbsp;$row_clt[rg]</TD>
	<TD $bord>&nbsp;$row_clt[tipo_conta]</TD>
	<TD $bord>$row_clt[agencia]</TD>
	<TD $bord>$row_clt[conta]</TD>
	<TD $bord>R$ $row_cursoclt[valor]</TD>
	<TD $bord align='center'>$contratacao</TD>
	<TD $bord align='center'>{$row_clt['tipopg']}</TD>
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

}
?> 

</strong></p>
<p><strong>

<?php
// --------------------------------          --------------------- SELECIONANDO OS SEM BANCOS

   print "
   <BR>
   <span class='title'>Sem Bancos</span>
   <hr width=150>";

  print "
  <table cellpadding='4' cellspacing='1' style='font-size:11px; line-height:24px; border=0px; width:90%'>
  <tr style='background-color:#bbb; font-size:12px;'>
  <td align='center' class='style23'>Cod</td>
  <td class='style23'>Nome</td>  
<td  class='style23'>CPF</td>
<td class='style23'>RG</td>
  <td align='center' class='style23'>Telefone</td>
  <td class='style23'>Endereço</td>
  <td align='center' class='style23'>Salário</td>   
  <td align='center' class='style23'>Tipo</td>  
  <td align='center' class='style23'>Forma Pagamento</td>  
  </tr>";
  
$RE_bol2 = mysql_query("SELECT A.id_curso,A.nome,A.campo3,A.cpf,A.rg,A.tipo_conta,A.agencia,A.conta,B.tipopg,B.campo1 FROM autonomo AS A
                                LEFT JOIN tipopg AS B ON (A.tipo_pagamento=B.id_tipopg)
                                WHERE A.id_projeto = '$projeto' and A.status = '1' and (A.banco = '' or A.banco = '0') ORDER BY A.nome");
$cont2 = "0";
while($row_bolsista2 = mysql_fetch_array($RE_bol2)){

$result_curso2 = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_bolsista2[id_curso]'");
$row_curso2 = mysql_fetch_array($result_curso2);

//---- EMBELEZAMENTO DA PAGINA ----------------------------------
	if($cont2 % 2){ $color2="#f0f0f0"; }else{ $color2="#dddddd"; }
	$nome = str_split($row_bolsista2['nome'], 30);
	$nomeT = sprintf("% -30s", $nome[0]);
	$bord = "style='border-bottom:#000 solid 1px;'";
	if($row_bolsista2['tipo_contratacao'] == "1"){ $contratacao2 = "AUT"; }else{ $contratacao2 = "COO"; }
//-----------------
	
if(!empty($row_bolsista2['tel_fixo'])) {
	$telefone = $row_bolsista2['tel_fixo'];
} elseif(!empty($row_bolsista2['tel_cel'])) {
	$telefone = $row_bolsista2['tel_cel'];
} elseif(!empty($row_bolsista2['tel_rec'])) {
	$telefone = $row_bolsista2['tel_rec'];
}

$endereco = strtoupper($row_bolsista2['endereco']);
if(!empty($row_bolsista2['bairro'])) { 
	$endereco .= ', '.strtoupper($row_bolsista2['bairro']);
} if(!empty($row_bolsista2['cidade'])) { 
	$endereco .= ', '.strtoupper($row_bolsista2['cidade']); 
} if(!empty($row_bolsista2['cep'])) { 
	$endereco .= ', '.$row_bolsista2['cep']; 
}
//<TD>$endereco</TD>
$banco = "Sem Banco";
$ag = "-";
$cc = "-";
if($row_bolsista2['banco'] != "" && $row_bolsista2['banco'] != "0"){
    $banco = $row_bolsista2['nome_banco'];
    $ag = $row_bolsista2['agencia'];
    $cc = $row_bolsista2['conta'];
}

print "
<TR bgcolor=$color2>
<TD align='center'>$row_bolsista2[campo3]</TD>
<TD>$row_bolsista2[nome]</TD>
<TD $bord>&nbsp;$row_bolsista2[cpf]</TD>
<TD $bord>&nbsp;$row_bolsista2[rg]</TD>
<TD align='center'>$telefone</TD>
<TD>$endereco</TD>
<TD align='center'>$row_curso2[valor]</TD>
<TD align='center'>$contratacao2</TD>
<TD align='center'>{$row_bolsista2['tipopg']}</TD>
</TR>";

unset($telefone);

$valor_smp2 = str_replace(",",".",$row_curso2['salario']);
$valor_soma2 = $valor_soma2 + $valor_smp2;

$cont2 ++;	  
$contagem2 ++;
}

//----------------------------------------------------------------------------------------------------------------------
// PEGANDO AGORA OS CLTS SEM BANCO
$result_clt2 = mysql_query("SELECT A.id_curso,A.nome,A.campo3,A.cpf,A.rg,A.tipo_conta,A.agencia,A.conta,B.tipopg,B.campo1 FROM rh_clt AS A
                                LEFT JOIN tipopg AS B ON (A.tipo_pagamento=B.id_tipopg)
                                WHERE A.status = '10' and A.id_projeto = '$projeto' and (A.banco = '' or A.banco = '0')  ORDER BY A.nome");
$contclt2 = "0";

while($row_clt2 = mysql_fetch_array($result_clt2)){

$result_cursoclt2 = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt2[id_curso]'");
$row_cursoclt2 = mysql_fetch_array($result_cursoclt2);

if($contclt2 % 2){ $colorclt2="#f0f0f0"; }else{ $colorclt2="#dddddd"; }

$contratacaoclt2 = "CLT";

if(!empty($row_clt2['tel_fixo'])) {
	$telefone = $row_clt2['tel_fixo'];
} elseif(!empty($row_clt2['tel_cel'])) {
	$telefone = $row_clt2['tel_cel'];
} elseif(!empty($row_clt2['tel_rec'])) {
	$telefone = $row_clt2['tel_rec'];
}

$endereco = strtoupper($row_clt2['endereco']);
if(!empty($row_clt2['bairro'])) { 
	$endereco .= ', '.strtoupper($row_clt2['bairro']);
} if(!empty($row_clt2['cidade'])) { 
	$endereco .= ', '.strtoupper($row_clt2['cidade']); 
} if(!empty($row_clt2['cep'])) { 
	$endereco .= ', '.$row_clt2['cep']; 
}
//<TD>$endereco</TD>
$banco = "Sem Banco";
$ag = "-";
$cc = "-";
if($row_clt2['banco']!="" && $row_clt2['banco']!="0"){
    $banco = $row_clt2['nome_banco'];
    $ag = $row_clt2['agencia'];
    $cc = $row_clt2['conta'];
}

print "
<TR bgcolor=$colorclt2>
<TD align='center'>$row_clt2[campo3]</TD>
<TD>$row_clt2[nome]</TD>
<TD $bord>&nbsp;$row_clt2[cpf]</TD>
<TD $bord>&nbsp;$row_clt2[rg]</TD>
<TD align='center'>$telefone</TD>
<TD>$endereco</TD>
<TD align='center'>$row_cursoclt2[valor]</TD>
<TD align='center'>$contratacaoclt2</TD>
<TD align='center'>{$row_clt2['tipopg']}</TD>
</TR>";

unset($telefone);

$valor_smpclt2 = str_replace(",",".",$row_cursoclt2['salario']);
$valor_somaclt2 = $valor_somaclt2 + $valor_smpclt2;

$contclt2 ++;	  
$contagem2 ++;
}

$total_por_banco2 = $cont2 + $contclt2;
$valor_por_banco2 = $valor_soma2 + $valor_somaclt2;

$valor_por_banco2_f = number_format($valor_por_banco2,2,",",".");

print "
<TR bgcolor=#FFFFFF>
<TD $bord colspan=2><span class='totalizador'>&nbsp;&nbsp;$total_por_banco2 Participantes</span></TD>
<TD $bord colspan=2 align=right><span class='totalizador'>Valor total:</span></TD>
<TD $bord colspan=3><span class='totalizador'>R$ $valor_por_banco2_f</span></TD>
</TR>";
print "</TABLE><Br>";

//$somando_os_totais;
//$somando_os_valores;



//----------------------------------------------------------------------------------------------------------------------
// PEGANDO AGORA OS CLTS COM OUTROS BANCOS

print "
   <BR>
   <span class='title'>Outros Bancos</span>
   <hr width=150>";

  print "
  <table cellpadding='4' cellspacing='1' style='font-size:11px; line-height:24px; border=0px; width:90%'>
  <tr style='background-color:#bbb; font-size:12px;'>
  <td align='center' class='style23'>Cod</td>
  <td class='style23'>Nome</td>  
  <td class='style23'>CPF</td>
  <td class='style23'>RG</td>
  <td align='center' class='style23'>Telefone</td>
  <td class='style23'>Banco</td>
  <td class='style23'>Agência</td>
  <td class='style23'>Conta</td>
  <td align='center' class='style23'>Salário</td>   
  <td align='center' class='style23'>Tipo</td>  
  <td align='center' class='style23'>Forma Pagamento</td>  
  </tr>";

$result_clt2 = mysql_query("SELECT A.id_curso,A.nome,A.campo3,A.cpf,A.rg,A.tipo_conta,A.agencia,A.conta,B.tipopg,B.campo1,A.nome_banco FROM rh_clt AS A
                                LEFT JOIN tipopg AS B ON (A.tipo_pagamento=B.id_tipopg)
                                WHERE A.status = '10' and A.id_projeto = '$projeto' and A.banco = '9999' ORDER BY A.nome");
$contclt2 = "0";

while($row_clt2 = mysql_fetch_array($result_clt2)){

$result_cursoclt2 = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt2[id_curso]'");
$row_cursoclt2 = mysql_fetch_array($result_cursoclt2);

if($contclt2 % 2){ $colorclt2="#f0f0f0"; }else{ $colorclt2="#dddddd"; }

$contratacaoclt2 = "CLT";

if(!empty($row_clt2['tel_fixo'])) {
	$telefone = $row_clt2['tel_fixo'];
} elseif(!empty($row_clt2['tel_cel'])) {
	$telefone = $row_clt2['tel_cel'];
} elseif(!empty($row_clt2['tel_rec'])) {
	$telefone = $row_clt2['tel_rec'];
}

print "
<TR bgcolor=$colorclt2>
<TD align='center'>$row_clt2[campo3]</TD>
<TD>$row_clt2[nome]</TD>
<TD $bord>&nbsp;$row_clt2[cpf]</TD>
<TD $bord>&nbsp;$row_clt2[rg]</TD>
<TD align='center'>$telefone</TD>
<TD>{$row_clt2['nome_banco']}</TD>
<TD>{$row_clt2['agencia']}</TD>
<TD>{$row_clt2['conta']}</TD>
<TD align='center'>$row_cursoclt2[valor]</TD>
<TD align='center'>$contratacaoclt2</TD>
<TD align='center'>{$row_clt2['tipopg']}</TD>
</TR>";

unset($telefone);

$valor_smpclt2 = str_replace(",",".",$row_cursoclt2['salario']);
$valor_somaclt2 = $valor_somaclt2 + $valor_smpclt2;

$contclt2 ++;	  
$contagem2 ++;
}

print "</TABLE><Br>";


print "</TABLE><Br>";

$total = $contagem1 + $contagem2;
$valor_total = $somando_os_valores + $valor_por_banco2;

$valor_total_f = number_format($valor_total,2,",",".");

	 ?>
          <br>
          <br>
        </strong></p>
        <table width="33%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px">
          <tr>
            <td height="24" colspan="2" align="center" valign="middle" bgcolor="#CCCCCC" class="title"><span class="totalizador">TOTALIZADORES</span></td>
          </tr>
          <tr>
            <td width="49%" height="27" align="right" valign="middle" bgcolor="#f0f0f0"><span class="Texto10"> Pagos pelos Bancos:</span></td>
            <td width="51%" height="27" align="left" valign="middle" bgcolor="#f0f0f0" class="title"><span class="Texto10"> &nbsp;&nbsp;<B style="font-family:Arial, Helvetica, sans-serif; font-size:11px">
              <?=$somando_os_totais?>
            </B></span></td>
          </tr>
          <tr>
            <td height="27" align="right" valign="middle" bgcolor="#f0f0f0"><span class="Texto10">Participantes Listados:</span></td>
            <td height="27" align="left" valign="middle" bgcolor="#f0f0f0"><span class="Texto10"> &nbsp;&nbsp;<B style="font-family:Arial, Helvetica, sans-serif; font-size:11px">
              <?=$total?>
            </B></span></td>
          </tr>
          <tr>
            <td height="27" align="right" valign="middle" bgcolor="#f0f0f0" class="Texto10">Valor Pago pelos Bancos:</td>
            <td height="27" align="left" valign="middle" bgcolor="#f0f0f0" class="Texto10">&nbsp;&nbsp;<B style="font-family:Arial, Helvetica, sans-serif; font-size:11px">
              R$ <?=$somando_os_valores_f?>
            </B></td>
          </tr>
          <tr>
            <td height="27" align="right" valign="middle" bgcolor="#f0f0f0" class="Texto10">Valor Total Geral:</td>
            <td height="27" align="left" valign="middle" bgcolor="#f0f0f0" class="Texto10">&nbsp;&nbsp;<B style="font-family:Arial, Helvetica, sans-serif; font-size:11px"> R$ </B><B style="font-family:Arial, Helvetica, sans-serif; font-size:11px"><?=$valor_total_f?>
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

mysql_free_result($RE_bol2);
///mysql_free_result($result_curso2);

/* Fechando a conexão */
mysql_close($conn);

?>