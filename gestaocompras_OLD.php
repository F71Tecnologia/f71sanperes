<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";
$regiao = $_REQUEST['regiao'];

$id_user = $_COOKIE['logado'];
$result_user_logado = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'", $conn);
$row_user_logado = mysql_fetch_array($result_user_logado);

$verifica = $row_user_logado['tipo_usuario'];

if($verifica == "1" or $verifica == "2"){		
$class_3 = "";
}else{
$class_3 = "style='display:none'";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - GEST&Atilde;O DE COMPRAS</title>
<style type="text/css">
<!--
body {
	background-color: #5C7E59;
}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	color: #003300;
}
.style2 {font-size: 12px}
.style3 {
	color: #333;
	font-weight: bold;
}
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style28 {
	font-size: 10px;
	font-weight: bold;
}
.style7 {color: #003300}
.style29 {color: #FF0000}
-->
</style>
<link href="net.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td colspan="4"><img src="layout/topo.gif" width="750" height="38" /></td>
  </tr>
  
  <tr>
    <td width="21" rowspan="2" background="layout/esquerdo.gif">&nbsp;</td>
    <td height="25" colspan="2" align="right" valign="middle" bgcolor="#FFFFFF"><?php include('reportar_erro.php'); ?></td>
    <td width="26" rowspan="2" background="layout/direito.gif">&nbsp;</td>
  </tr>
  
  <tr>
    <td height="18" colspan="2" align="left" valign="top"><img src="imagensmenu2/compras.gif" alt="cotas" width="20" height="20" align="absmiddle" /> <span class="style3" style="font-weight:bold">GEST&Atilde;O DE COMPRAS</span></td>
  </tr>
  
  <tr>
    <td height="27" background="layout/esquerdo.gif">&nbsp;</td>
    <td colspan="2" rowspan="2" align="center" valign="top">
    
    <table width="98%" border="0" align="center">
      <tr>
        <td colspan="6" bgcolor="#FFFFCC" class="style28"><div align="center"><strong><span class="style2">Visualiza&ccedil;&atilde;o dos Pedidos em andamento:</span></strong></div></td>
        </tr>
      <tr>
        <td width="16%" bgcolor="#FFFFCC" class="style28">N. REQUISI&Ccedil;&Atilde;O</td>
        <td width="8%" bgcolor="#FFFFCC" class="style28">DATA</td>
        <td width="15%" bgcolor="#FFFFCC" class="style28">TIPO</td>
        <td width="29%" bgcolor="#FFFFCC" class="style28">NOME</td>
        <td width="15%" bgcolor="#FFFFCC" class="style28">SOLICITADO POR:</td>
        <td width="17%" bgcolor="#FFFFCC" class="style28">VALOR</td>
        </tr>
      
        <?php
		$result_1 = mysql_query("SELECT *,date_format(data_requisicao, '%d/%m/%Y')as data_requisicao FROM compra
		WHERE status_requisicao = '1' and acompanhamento = '1' and id_regiao = '$regiao'");
		$cont_color1 = "1";
		while($row_1 = mysql_fetch_array($result_1)){
		
		$result_user1 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = $row_1[id_user_pedido]");
		$row_user1 = mysql_fetch_array($result_user1);
		
		if($row_1['tipo'] == "1"){ $tipo="PRODUTO"; }else{ $tipo="SERVIÇO"; }
		if($cont_color1 % 2){ $color="#EBEBEB"; }else{ $color="#EBEBFF"; }
		
		print "
		<tr bgcolor=$color>
        <td><a href=pedidocompra.php?id=1&compra=$row_1[0]&regiao=$regiao><font class='style28'>$row_1[num_processo]</font>
		</a></td>
        <td><font class='style28'>$row_1[data_requisicao]</font></td>
		<td><font class='style28'>$tipo</font></td>
        <td><font class='style28'>$row_1[nome_produto]</font></td>
        <td><font class='style28'>$row_user1[0]</font></td>
        <td><font class='style28'>R$ $row_1[valor_medio]</font></td>
		</tr>";
		
		$cont_color1 ++;
		
	  }
	  
	  ?>
    </table>
    <br />
    <table width="95%" border="0" align="center">
      <tr>
        <td colspan="6" bgcolor="#FFFFCC" class="style28"><div align="center"><strong><span class="style2">Visualiza&ccedil;&atilde;o dos Processos aguardando pesquisa:</span></strong></div></td>
      </tr>
      <tr>
        <td width="16%" bgcolor="#FFFFCC" class="style28">N. PROCESSO</td>
        <td width="8%" bgcolor="#FFFFCC" class="style28">DATA</td>
        <td width="15%" bgcolor="#FFFFCC" class="style28">TIPO</td>
        <td width="29%" bgcolor="#FFFFCC" class="style28">NOME</td>
        <td width="14%" bgcolor="#FFFFCC" class="style28">SOLICITADO POR:</td>
        <td width="18%" bgcolor="#FFFFCC" class="style28">VALOR</td>
      </tr>
        <?php
		$result_2 = mysql_query("SELECT *,date_format(data_requisicao, '%d/%m/%Y')as data_requisicao FROM compra
		WHERE status_requisicao = '2' and acompanhamento = '2' and id_regiao = '$regiao'");
		$cont_color2 = "1";
		while($row_2 = mysql_fetch_array($result_2)){
		
		$result_user2 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = $row_2[id_user_pedido]");
		$row_user2 = mysql_fetch_array($result_user2);
		
		if($row_2['tipo'] == "1"){ $tipo="PRODUTO"; }else{ $tipo="SERVIÇO"; }
		if($cont_color2 % 2){ $color="#EBEBEB"; }else{ $color="#EBEBFF"; }
		
		print "
		<tr bgcolor=$color>
        <td><a href=cotacoes.php?id=1&compra=$row_2[0]&regiao=$regiao><font class='style28'>$row_2[num_processo]</font>
		</a></td>
        <td><font class='style28'>$row_2[data_requisicao]</font></td>
		<td><font class='style28'>$tipo</font></td>
        <td><font class='style28'>$row_2[nome_produto]</font></td>
        <td><font class='style28'>$row_user2[0]</font></td>
        <td><font class='style28'>R$ $row_2[valor_medio]</font></td>
		</tr>";
		
		$cont_color2 ++;
		
	  }
	  
	  ?>
    </table>
    <br />
    <table width="98%" border="0" align="center" <?php print"$class_3"; ?>>
      <tr>
        <td colspan="6" bgcolor="#FFFFCC" class="style28"><div align="center"><strong><span class="style2">Visualiza&ccedil;&atilde;o das Decis&otilde;es a serem tomadas:</span></strong></div></td>
      </tr>
      <tr>
        <td width="16%" bgcolor="#FFFFCC" class="style28">N. PROCESSO</td>
        <td width="8%" bgcolor="#FFFFCC" class="style28">DATA</td>
        <td width="14%" bgcolor="#FFFFCC" class="style28">TIPO</td>
        <td width="30%" bgcolor="#FFFFCC" class="style28">NOME</td>
        <td width="15%" bgcolor="#FFFFCC" class="style28">SOLICITADO POR:</td>
        <td width="17%" bgcolor="#FFFFCC" class="style28">VALOR</td>
      </tr>
        <?php
		$result_3 = mysql_query("SELECT *,date_format(data_requisicao, '%d/%m/%Y')as data_requisicao FROM compra
		WHERE acompanhamento = '3' and id_regiao = '$regiao'");
		$cont_color3 = "1";
		while($row_3 = mysql_fetch_array($result_3)){
		
		$result_user3 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = $row_3[id_user_pedido]");
		$row_user3 = mysql_fetch_array($result_user3);
		
		if($row_3['tipo'] == "1"){ $tipo="PRODUTO"; }else{ $tipo="SERVIÇO"; }
		if($cont_color3 % 2){ $color="#EBEBEB"; }else{ $color="#EBEBFF"; }
		
		print "
		<tr bgcolor=$color>
        <td><a href=decisao.php?id=1&compra=$row_3[0]&regiao=$regiao><font class='style28'>$row_3[num_processo]</font>
		</a></td>
        <td><font class='style28'>$row_3[data_requisicao]</font></td>
		<td><font class='style28'>$tipo</font></td>
        <td><font class='style28'>$row_3[nome_produto]</font></td>
        <td><font class='style28'>$row_user3[0]</font></td>
        <td><font class='style28'>R$ $row_3[valor_medio]</font></td>
		</tr>";
		
		$cont_color3 ++;
		
	  }
	  
	  ?>
    </table>    
    <br />
    <table width="98%" border="0" align="center" <?php print"$class_3"; ?>>
      <tr>
        <td colspan="7" bgcolor="#FFFFCC" class="style28"><div align="center"><strong><span class="style2">Visualiza&ccedil;&atilde;o das Autoriza&ccedil;&otilde;es a serem efetuadas:</span></strong> </div></td>
      </tr>
      <tr>
        <td width="19%" bgcolor="#FFFFCC" class="style28">N. PROCESSO</td>
        <td width="13%" bgcolor="#FFFFCC" class="style28 style29"><p align="center">NECESS&Aacute;RIO PARA</p></td>
        <td width="9%" bgcolor="#FFFFCC" class="style28">TIPO</td>
        <td width="11%" bgcolor="#FFFFCC" class="style28">NOME</td>
        <td width="16%" bgcolor="#FFFFCC" class="style28">DECIDIDO POR</td>
        <td width="10%" bgcolor="#FFFFCC" class="style28">VALOR</td>
        <td width="22%" bgcolor="#FFFFCC" class="style28"> FORNECEDOR<br />
 DATA ENTREGA</td>
        </tr>
        <?php
		$result_4 = mysql_query("SELECT *,date_format(data_requisicao, '%d/%m/%Y')as data_requisicao, 
		date_format(prazo, '%d/%m/%Y')as prazo FROM compra WHERE acompanhamento = '4' and id_regiao = '$regiao'");
		$cont_color4 = "1";
		while($row_4 = mysql_fetch_array($result_4)){
		
		$result_user4 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = $row_4[id_user_escolha]");
		$row_user4 = mysql_fetch_array($result_user4);
		
		$result_f4 = mysql_query("SELECT * FROM fornecedores where id_fornecedor = '$row_4[fornecedor_escolhido]'");
		$row_for4 = mysql_fetch_array($result_f4);
		
		if($row_4['tipo'] == "1"){ $tipo="PRODUTO"; }else{ $tipo="SERVIÇO"; }
		if($cont_color4 % 2){ $color="#EBEBEB"; }else{ $color="#EBEBFF"; }
		
		print "
		<tr bgcolor=$color>
        <td><a href='confirmandocompra.php?compra=$row_4[0]&regiao=$regiao'>
		<font class='style28'>$row_4[num_processo]</font>
		</a></td>
		<td><font class='style28'>$row_4[data_requisicao]</font></td>
		<td><font class='style28'>$tipo</font></td>
        <td><font class='style28'>$row_4[nome_produto]</font></td>
        <td><font class='style28'>$row_user4[0]</font></td>
        <td><font class='style28'>R$ $row_4[preco_final]</font></td>
		<td><font class='style28'>$row_for4[nome]<br>$row_4[prazo]</font></td>
		</tr>";
		
		$cont_color4 ++;
		
	  }
	  
	  ?>
    </table>
    <br />
    <table width="98%" border="0" align="center" <?php print"$class_3"; ?>>
      <tr>
        <td colspan="6" bgcolor="#FFFFCC" class="style28"><div align="center"><strong><span class="style2">Acompanhamento Geral:</span></strong></div></td>
      </tr>
      <tr>
        <td width="19%" bgcolor="#FFFFCC" class="style28">N. PROCESSO</td>
        <td width="13%" bgcolor="#FFFFCC" class="style28 style29"><p align="center">NECESS&Aacute;RIO PARA</p></td>
        <td width="15%" bgcolor="#FFFFCC" class="style28">NOME</td>
        <td width="20%" bgcolor="#FFFFCC" class="style28">PEDIDO POR</td>
        <td width="14%" bgcolor="#FFFFCC" class="style28">VALOR</td>
        <td width="19%" bgcolor="#FFFFCC" class="style28">STATUS</td>
      </tr>
      <?php
	$result_5 = mysql_query("SELECT *,date_format(data_produto, '%d/%m/%Y')as data_produto, 
	date_format(prazo, '%d/%m/%Y')as prazo FROM compra WHERE id_regiao = '$regiao' and acompanhamento >= '5' or acompanhamento = '0' and id_regiao = '$regiao'");
	$cont_color5 = "1";
	while($row_5 = mysql_fetch_array($result_5)){
		
		$result_user5 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = $row_5[id_user_pedido]");
		$row_user5 = mysql_fetch_array($result_user5);
		
		if($cont_color5 % 2){ $color="#EBEBEB"; }else{ $color="#EBEBFF"; }
		
		if($row_5['acompanhamento'] == "5"){ 
		$status="Enviado para Financeiro"; 
		}else if($row_5['acompanhamento'] == "0"){
		$status="<font color=red>Não autorizado</font>"; 
		}else{		
		$status="Pago"; 
		}
		
		print "
		<tr bgcolor=$color>
        <td><font class='style28'>$row_5[num_processo]</font></td>
		<td><font class='style28'>$row_5[data_produto]</font></td>
        <td><font class='style28'>$row_5[nome_produto]</font></td>
        <td><font class='style28'>$row_user5[0]</font></td>
        <td><font class='style28'>R$ $row_5[preco_final]</font></td>
        <td><font class='style28'>$status</font></td>
		</tr>";
		
		$cont_color5 ++;
		
	  }
	  
	  ?>
    </table>
    <br /></td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="69" background="layout/esquerdo.gif">&nbsp;</td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  

  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td height="19" align="left" valign="middle" class="style3">&nbsp;</td>
    <td align="left" valign="middle" class="style3">&nbsp;</td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td height="19" colspan="2" align="left" valign="middle"><font face="Verdana, Geneva, sans-serif" color="#FF0000" size="-2"><strong>Informa&ccedil;&otilde;es: PEDIDO = Compra solicitada sendo avaliada<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ABERTURA = Compra em processo de aprova&ccedil;&atilde;o<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PESQUISA = Comrpa em cota&ccedil;&atilde;o com os fornecedores<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DECIS&Atilde;O = Setor financeiro avaliando o fornecedor<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AUTORIZA&Ccedil;&Atilde;O = Compra autorizada pelo Gerente do Projeto e encaminhada para financeiro</strong></font></td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td height="19" align="left" valign="middle" class="style3">&nbsp;</td>
    <td align="left" valign="middle" class="style3">&nbsp;</td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td height="19" align="left" valign="middle" class="style3">
    <div align="left"><strong>
	<?php print "<a href='solicitacompra.php?regiao=$regiao' style='TEXT-DECORATION: none;'>"; ?>
    <img src="imagensfinanceiro/saidas.gif" alt="saidas" width="25" height="25" align="absmiddle" border="0"/>
    SOLICITAR COMPRA</strong></div></td>
    <td align="left" valign="middle" class="style3">&nbsp;</td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td width="349" height="19" align="right" valign="top" class="style3">&nbsp;</td>
    <td width="354" align="center" valign="middle" class="style3"></td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr valign="top">
    <td height="37" colspan="4" bgcolor="#E2E2E2"><img src="layout/baixo.gif" width="750" height="38" />
<?php
include "empresa.php";
$rod = new empresa();
$rod -> rodape();
?></td>
  </tr>
</table>
</body>
</html>
<?php

}

?>