<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";
$regiao = $_REQUEST['regiao'];

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user_pedido'");
$row_user = mysql_fetch_array($result_user);

if(empty($_REQUEST['escolha'])){

$regiao = $_REQUEST['regiao'];
$id_compra = $_REQUEST['compra'];

//if($row_user[

$result = mysql_query("SELECT *,
date_format(data_produto, '%d/%m/%Y')as data_produto, 
date_format(data_requisicao, '%d/%m/%Y')as data_requisicao, 
date_format(prazo1, '%d/%m/%Y')as prazo1,  
date_format(prazo2, '%d/%m/%Y')as prazo2, 
date_format(prazo3, '%d/%m/%Y')as prazo3 
FROM compra where id_compra = '$id_compra'");
$row = mysql_fetch_array($result);

$result_reg = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$result_user = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row[id_user_pedido]'", $conn);
$row_user = mysql_fetch_array($result_user);

$data = date('d/m/Y');

if($row['tipo'] == "1"){
$tipo = "Produto";
}else{
$tipo = "Serviço";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="net1.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Controle de Cota&ccedil;&otilde;es</title>
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	color: #003300;
}
.style2 {font-size: 12px}
.style3 {
	color: #FF0000;
	font-weight: bold;
}
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style11 {font-size: 9px; font-weight: bold; color: #FFFFFF; }
.style12 {font-size: 10px}
.style29 {
	font-size: 9px;
	font-weight: bold;
}
-->
</style>
<script>
self.print()
</script>

</head>

<body>
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td colspan="4"><img src="layout/topo.gif" width="750" height="38" /></td>
  </tr>
  
  <tr>
    <td width="21" height="69" background="layout/esquerdo.gif">&nbsp;</td>
    <td colspan="2" align="center" valign="top">
      <table width="100%" cellpadding="0" cellspacing="0">
        <col width="44" />
        <col width="64" span="5" />
        <col width="9" />
        <col width="64" />
        <col width="62" />
        <col width="138" />
        <col width="133" />
        <col width="120" />
        <tr height="28">
          <td width="100%" height="28" align="left" valign="top"><div align="center">
            <table width="80%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="21%" align="center" valign="middle">
<?php
include "empresa.php";
$img= new empresa();
$img -> imagem();
?><!--<img src="imagens/certificadosrecebidos.gif" alt="log" width="89" height="63" />--><br /></td>
                <td width="79%"><div align="center"><span class="style23">IMPRESS&Atilde;O DE COMPROVANTE DE COTA&Ccedil;&Otilde;ES</span><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />
                  <br />
                      <strong>NUMERO DO PROCESSO: <?php print "$row[num_processo]";?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
                </tr>
            </table>
            </div></td>
        </tr>
        
        <tr height="32">
          <td height="22"><?php
		  
		  $result_fornecedor = mysql_query("SELECT * FROM fornecedores where id_fornecedor = '$row[fornecedor1]'");
		  $row_fornecedor = mysql_fetch_array($result_fornecedor);
		  
	  print "
		  <label>
           
           <span class='Texto10'><strong>
		   Fornecedor 1: $row_fornecedor[nome]     Contato: $row_fornecedor[contato]      Telefone: $row_fornecedor[tel]
		   </strong></span>
		   </label>
           <br />
           <br />";
		  
          ?>
          
          
           
           <span class="style2">
           <table width="96%" align="center" cellpadding="0" cellspacing="0" class="tarefa"> 
              <tr>
                <td height="10" colspan="6" bgcolor="#003300">
                <div align="center" class="style6">DESCRI&Ccedil;&Atilde;O DO PROCESSO</div></td>
              </tr>
              <tr height="32">
                <td width="93" height="31" align="right" valign="middle" class="border2">ITEM: &nbsp;</td>
                <td height="31" colspan="5" align="left" valign="middle" class="border3">&nbsp;<?php print "$row[nome_produto]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" class="border2"><strong>DESCRI&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" class="border3">&nbsp;<?php print "$row[descricao_produto]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" class="border2"><strong>MARCA: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" class="border3">&nbsp;<?php print "$row[marca1]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" class="border2"><strong>OBSERVA&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" class="border3">&nbsp;<?php print "$row[obs1]";?></td>
              </tr>
              <col width="44" />
              <col width="64" span="5" />
              <col width="9" />
              <col width="64" />
              <col width="62" />
              <col width="138" />
              <col width="133" />
              <col width="120" />
              <tr>
                <td height="16" colspan="2" bgcolor="#003300"><div align="center" class="style11">VALOR DOS IMPOSTOS</div></td>
                <td width="123" bgcolor="#003300"><div align="center" class="style11">VALOR DO FRETE</div></td>
                <td width="140" bgcolor="#003300"><div align="center" class="style11">DESCONTOS</div></td>
                <td width="141" bgcolor="#003300"><div align="center" class="style11">PRE&Ccedil;O UNIT&Aacute;RIO</div></td>
                <td width="136" height="16" bgcolor="#003300"><div align="center" class="style11">PRE&Ccedil;O FINAL</div></td>
              </tr>
              <tr height="32">
                <td height="31" colspan="2" align="center" valign="middle" class="border2">R$ <?php print "$row[imposto1]";?></td>
                <td width="123" align="center" valign="middle" class="border2">R$ <?php print "$row[frete1]";?></td>
                <td width="140" align="center" valign="middle" class="border2">R$ <?php print "$row[desconto1]";?></td>
                <td width="141" align="center" valign="middle" class="border2">R$ <?php print "$row[valor_uni1]";?></td>
                <td width="136" height="31" align="center" valign="middle" class="border3">R$ <?php print "$row[preco1]";?></td>
              </tr>
              <tr>
                <td height="16" colspan="2" bgcolor="#003300"><div align="center" class="style11">NECESSARIO PARA:</div></td>
                <td bgcolor="#003300"><div align="center" class="style11">DATA DO PEDIDO</div></td>
                <td height="16" bgcolor="#003300"><div align="center">
                  <div align="center" class="style11">DATA PARA ENTREGA</div>
                </div></td>
                <td height="16" bgcolor="#003300"><div align="center">
                  <div align="center" class="style11">QUANTIDADE</div>
                </div></td>
                <td height="16" bgcolor="#003300"><div align="center">
                  <div align="center" class="style11">SOLICITADO POR</div>
                </div></td>
              </tr>
              <tr height="32">
                <td height="31" colspan="2" align="center" valign="middle" class="border2"><?php print "$row[data_produto]";?></td>
                <td align="center" valign="middle" class="border2"><?php print "$row[data_requisicao]";?></td>
                <td height="31" align="center" valign="middle" class="border2"><?php print "$row[prazo1]";?></td>
                <td height="31" align="center" valign="middle" class="border2"><?php print "$row[quantidade]";?></td>
                <td height="31" align="center" valign="middle" class="border3"><span class="border2"><?php print "$row_user[nome1]";?></span></td>
              </tr>
            </table>
            <div align="center">            </div></span><br />
            <hr />
                <strong><?php
		  
		  $result_fornecedor2 = mysql_query("SELECT * FROM fornecedores where id_fornecedor = '$row[fornecedor2]'");
		  $row_fornecedor2 = mysql_fetch_array($result_fornecedor2);

		  print "
		  
           <span class='Texto10'><strong>
		   Fornecedor 2: $row_fornecedor2[nome]     Contato: $row_fornecedor2[contato]      Telefone: $row_fornecedor2[tel]
		   </strong></span>
		   
           <br />
           <br />";
		  
          ?>
          
          
           
           <span class="style2">
           <table width="96%" align="center" cellpadding="0" cellspacing="0" class="tarefa"> 
              <tr>
                <td height="10" colspan="6" bgcolor="#003300">
                <div align="center" class="style6">DESCRI&Ccedil;&Atilde;O DO PROCESSO</div></td>
              </tr>
              <tr height="32">
                <td width="93" height="31" align="right" valign="middle" class="border2">ITEM: &nbsp;</td>
                <td height="31" colspan="5" align="left" valign="middle" class="border3">&nbsp;<?php print "$row[nome_produto]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" class="border2"><strong>DESCRI&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" class="border3">&nbsp;<?php print "$row[descricao_produto]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" class="border2"><strong>MARCA: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" class="border3">&nbsp;<?php print "$row[marca2]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" class="border2"><strong>OBSERVA&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" class="border3">&nbsp;<?php print "$row[obs2]";?></td>
              </tr>
              <col width="44" />
              <col width="64" span="5" />
              <col width="9" />
              <col width="64" />
              <col width="62" />
              <col width="138" />
              <col width="133" />
              <col width="120" />
              <tr>
                <td height="16" colspan="2" bgcolor="#003300"><div align="center" class="style11">VALOR DOS IMPOSTOS</div></td>
                <td width="123" bgcolor="#003300"><div align="center" class="style11">VALOR DO FRETE</div></td>
                <td width="140" bgcolor="#003300"><div align="center" class="style11">DESCONTOS</div></td>
                <td width="141" bgcolor="#003300"><div align="center" class="style11">PRE&Ccedil;O UNIT&Aacute;RIO</div></td>
                <td width="136" height="16" bgcolor="#003300"><div align="center" class="style11">PRE&Ccedil;O FINAL</div></td>
              </tr>
              <tr height="32">
                <td height="31" colspan="2" align="center" valign="middle" class="border2">R$ <?php print "$row[imposto2]";?></td>
                <td width="123" align="center" valign="middle" class="border2">R$ <?php print "$row[frete2]";?></td>
                <td width="140" align="center" valign="middle" class="border2">R$ <?php print "$row[desconto2]";?></td>
                <td width="141" align="center" valign="middle" class="border2">R$ <?php print "$row[valor_uni2]";?></td>
                <td width="136" height="31" align="center" valign="middle" class="border3">R$ <?php print "$row[preco2]";?></td>
              </tr>
              <tr>
                <td height="16" colspan="2" bgcolor="#003300"><div align="center" class="style11">NECESSARIO PARA:</div></td>
                <td bgcolor="#003300"><div align="center" class="style11">DATA DO PEDIDO</div></td>
                <td height="16" bgcolor="#003300"><div align="center">
                  <div align="center" class="style11">DATA PARA ENTREGA</div>
                </div></td>
                <td height="16" bgcolor="#003300"><div align="center">
                  <div align="center" class="style11">QUANTIDADE</div>
                </div></td>
                <td height="16" bgcolor="#003300"><div align="center">
                  <div align="center" class="style11">SOLICITADO POR</div>
                </div></td>
              </tr>
              <tr height="32">
                <td height="31" colspan="2" align="center" valign="middle" class="border2"><?php print "$row[data_produto]";?></td>
                <td align="center" valign="middle" class="border2"><?php print "$row[data_requisicao]";?></td>
                <td height="31" align="center" valign="middle" class="border2"><?php print "$row[prazo2]";?></td>
                <td height="31" align="center" valign="middle" class="border2"><?php print "$row[quantidade]";?></td>
                <td height="31" align="center" valign="middle" class="border3"><span class="border2"><?php print "$row_user[nome1]";?></span></td>
              </tr>
            </table>
            <div align="center">            </div></span><br />
                <hr />
                <?php
		  
		  $result_fornecedor3 = mysql_query("SELECT * FROM fornecedores where id_fornecedor = '$row[fornecedor3]'");
		  $row_fornecedor3 = mysql_fetch_array($result_fornecedor3);
		  
		  print "
		  
           <span class='Texto10'><strong>
		   Fornecedor 3: $row_fornecedor3[nome]     Contato: $row_fornecedor3[contato]      Telefone: $row_fornecedor3[tel]
		   </strong></span>
		
           <br />
           <br />";
		  
          ?>
          
          
           
           <span class="style2">
           <table width="96%" align="center" cellpadding="0" cellspacing="0" class="tarefa"> 
              <tr>
                <td height="10" colspan="6" bgcolor="#003300">
                <div align="center" class="style6">DESCRI&Ccedil;&Atilde;O DO PROCESSO</div></td>
              </tr>
              <tr height="32">
                <td width="93" height="31" align="right" valign="middle" class="border2">ITEM: &nbsp;</td>
                <td height="31" colspan="5" align="left" valign="middle" class="border3">&nbsp;<?php print "$row[nome_produto]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" class="border2"><strong>DESCRI&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" class="border3">&nbsp;<?php print "$row[descricao_produto]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" class="border2"><strong>MARCA: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" class="border3">&nbsp;<?php print "$row[marca3]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" class="border2"><strong>OBSERVA&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" class="border3">&nbsp;<?php print "$row[obs3]";?></td>
              </tr>
              <col width="44" />
              <col width="64" span="5" />
              <col width="9" />
              <col width="64" />
              <col width="62" />
              <col width="138" />
              <col width="133" />
              <col width="120" />
              <tr>
                <td height="16" colspan="2" bgcolor="#003300"><div align="center" class="style11">VALOR DOS IMPOSTOS</div></td>
                <td width="123" bgcolor="#003300"><div align="center" class="style11">VALOR DO FRETE</div></td>
                <td width="140" bgcolor="#003300"><div align="center" class="style11">DESCONTOS</div></td>
                <td width="141" bgcolor="#003300"><div align="center" class="style11">PRE&Ccedil;O UNIT&Aacute;RIO</div></td>
                <td width="136" height="16" bgcolor="#003300"><div align="center" class="style11">PRE&Ccedil;O FINAL</div></td>
              </tr>
              <tr height="32">
                <td height="31" colspan="2" align="center" valign="middle" class="border2">R$ <?php print "$row[imposto3]";?></td>
                <td width="123" align="center" valign="middle" class="border2">R$ <?php print "$row[frete3]";?></td>
                <td width="140" align="center" valign="middle" class="border2">R$ <?php print "$row[desconto3]";?></td>
                <td width="141" align="center" valign="middle" class="border2">R$ <?php print "$row[valor_uni3]";?></td>
                <td width="136" height="31" align="center" valign="middle" class="border3">R$ <?php print "$row[preco3]";?></td>
              </tr>
              <tr>
                <td height="16" colspan="2" bgcolor="#003300"><div align="center" class="style11">NECESSARIO PARA:</div></td>
                <td bgcolor="#003300"><div align="center" class="style11">DATA DO PEDIDO</div></td>
                <td height="16" bgcolor="#003300"><div align="center">
                  <div align="center" class="style11">DATA PARA ENTREGA</div>
                </div></td>
                <td height="16" bgcolor="#003300"><div align="center">
                  <div align="center" class="style11">QUANTIDADE</div>
                </div></td>
                <td height="16" bgcolor="#003300"><div align="center">
                  <div align="center" class="style11">SOLICITADO POR</div>
                </div></td>
              </tr>
              <tr height="32">
                <td height="31" colspan="2" align="center" valign="middle" class="border2"><?php print "$row[data_produto]";?></td>
                <td align="center" valign="middle" class="border2"><?php print "$row[data_requisicao]";?></td>
                <td height="31" align="center" valign="middle" class="border2"><?php print "$row[prazo3]";?></td>
                <td height="31" align="center" valign="middle" class="border2"><?php print "$row[quantidade]";?></td>
                <td height="31" align="center" valign="middle" class="border3"><span class="border2">
				<?php print "$row_user[nome1]";?></span></td>
              </tr>
            </table>
          </span></strong></td>
        </tr>
        
        <tr height="32">
          <td height="32"><div align="center">
<?php
$end = new empresa();
$end -> endereco('#003300','7px');
?></td>
        </tr>
      </table>
    </td>
    <td width="26" background="layout/direito.gif">&nbsp;</td>
  </tr>
  
  
  
  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td width="349" height="19" align="right" valign="top" class="style3">&nbsp;</td>
    <td width="354" align="center" valign="middle" class="style3"></td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr valign="top">
    <td height="37" colspan="4" bgcolor="#5C7E59"><img src="layout/baixo.gif" width="750" height="38" /></td>
  </tr>
</table>
</body>
</html>
<?php
}else{

$id_user = $_COOKIE['logado'];

$escolha = $_REQUEST['escolha'];
$pedido = $_REQUEST['pedido'];
$motivo = $_REQUEST['motivo'];
$data = date('Y-m-d');

$result = mysql_query("SELECT * FROM compra where id_compra = '$pedido'");
$row = mysql_fetch_array($result);

switch ($escolha){
case 1:
$fornecedor = $escolha;
$prazo = $row['prazo1'];
$marca = $row['marca1'];
$preco = $row['preco1'];
break;
case 2:
$fornecedor = $escolha;
$prazo = $row['prazo2'];
$marca = $row['marca2'];
$preco = $row['preco2'];
break;
case 3:
$fornecedor = $escolha;
$prazo = $row['prazo3'];
$marca = $row['marca3'];
$preco = $row['preco3'];
}

mysql_query("UPDATE compra SET id_user_escolha='$id_user', data_escolha='$data', 
fornecedor_escolhido = '$fornecedor', produto='$row[nome_produto]', 
prazo='$prazo', marca='$marca', preco_final='$preco', descricao_compra='$motivo', 
acompanhamento='4' where id_compra = '$pedido'") or die ("<center>ERRO!<br> tente novamente mais tarde<br><br>".mysql_error());


print "
<link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
<br>
<hr>
<center>
<font color=#FFFFFF> 
Dados gravados com sucesso!<br><br>
</font>
<br><br>
<a href='gestaocompras.php?id=1&regiao=$regiao'><img src='imagens/voltar.gif' border=0></a>
</center>
";


}
}

?>