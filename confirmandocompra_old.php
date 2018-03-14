<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

if(empty($_REQUEST['financeiro'])){

$regiao = $_REQUEST['regiao'];
$id_compra = $_REQUEST['compra'];

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
<link href="net.css" rel="stylesheet" type="text/css">
<link href="net1.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Controle de Cota&ccedil;&otilde;es</title>
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
	color: #FF0000;
	font-weight: bold;
}
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style11 {font-size: 9px; font-weight: bold; color: #FFFFFF; }
.style12 {font-size: 10px}
.style14 {
	color: #000000;
	font-weight: bold;
}
-->
</style></head>

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
          <td width="100%" height="28" align="left" valign="top"><div align="center"><?php
include "empresa.php";
$img= new empresa();
$img -> imagemCNPJ();
?><br />
              <span class="style2"><strong>AUTORIZA&Ccedil;&Atilde;O DA COMPRA</strong></span><br />
              <br />
          <span class="style14">NUMERO DO PROCESSO: <?php print "$row[num_processo]";?></span></div></td>
        </tr>
        <tr height="32">
          <td height="32">&nbsp;</td>
        </tr>
        <tr height="32">
          <td height="22">
          
          <br />
          <?php
		  
		  $cases = $row['fornecedor_escolhido'];
		  switch($cases){
		  case 1:
		  $id_fornecedor = $row['fornecedor1'];
		  $imposto = $row['imposto1'];
		  $frete = $row['frete1'];
		  $desconto = $row['desconto1'];
		  $valor_uni = $row['valor_uni1'];
		  $valor = $row['preco1'];
		  $prazo = $row['prazo1'];
		  $obs = $row['obs1'];
		  $marca = $row['marca1'];
		  break;
		  case 2:
		  $id_fornecedor = $row['fornecedor2'];
  		  $imposto = $row['imposto2'];
		  $frete = $row['frete2'];
		  $desconto = $row['desconto2'];
		  $valor_uni = $row['valor_uni2'];
		  $valor = $row['preco2'];
		  $prazo = $row['prazo2'];
		  $obs = $row['obs2'];
		  $marca = $row['marca2'];
		  break;
		  case 3:
		  $id_fornecedor = $row['fornecedor3'];
		  $imposto = $row['imposto3'];
		  $frete = $row['frete3'];
		  $desconto = $row['desconto3'];
		  $valor_uni = $row['valor_uni3'];
		  $valor = $row['preco3'];
		  $prazo = $row['prazo3'];
		  $obs = $row['obs3'];
		  $marca = $row['marca3'];
		  break;
		  }
		  
		  $result_fornecedor = mysql_query("SELECT * FROM fornecedores where id_fornecedor = '$id_fornecedor'");
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
                <td height="31" colspan="5" align="left" valign="middle" class="border3">&nbsp;
				<?php print "$marca";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" class="border2"><strong>OBSERVA&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" class="border3">&nbsp;
				<?php print "$obs";?></td>
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
                <td height="31" colspan="2" align="center" valign="middle" class="border2">R$ 
				<?php print "$imposto";?></td>
                <td width="123" align="center" valign="middle" class="border2">R$ <?php print "$frete";?></td>
                <td width="140" align="center" valign="middle" class="border2">R$ <?php print "$desconto";?></td>
                <td width="141" align="center" valign="middle" class="border2">R$ <?php print "$valor_uni";?></td>
                <td width="136" height="31" align="center" valign="middle" class="border3">R$ 
				<?php print "$valor";?></td>
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
                <td height="31" align="center" valign="middle" class="border2"><?php print "$prazo";?></td>
                <td height="31" align="center" valign="middle" class="border2"><?php print "$row[quantidade]";?></td>
                <td height="31" align="center" valign="middle" class="border3"><span class="border2"><?php print "$row_user[nome1]";?></span></td>
              </tr>
              <tr>
                <td height="16" colspan="6" bgcolor="#003300"><div align="center">
                    <div align="center" class="style11">MOTIVO DA DECIS&Atilde;O</div>
                </div></td>
              </tr>
              <tr height="32">
                <td height="31" colspan="6" align="center" valign="middle" class="border2"><span class="border3">
<?php

$descricao = str_replace("#","<br>",$row['descricao_compra']);

print "$descricao";


?></span></td>
              </tr>
            </table>
            <div align="center">
            </div></span><br />
<strong><span class="style2">
            <div align="center"></div></span></strong>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%" valign="top">
                <form action="memorandocompra.php" method="post" name="form1" class="style2" id="form1" target="_blank">
                  <div align="center">
                  <input type="submit" name="GRAVAR4" id="GRAVAR4" value="Imprimir Memorando" />
                  <br />
                  <br />
                  <input type="hidden" name="regiao" value="<?php print "$regiao";?>" />
                  <input type="hidden" name="compra" value="<?php print "$row[0]";?>" />
                  </div>
                </form>
                </td>
                <td width="50%" valign="top">
                <form action="confirmandocompra.php" method="post" name="form2" class="style2" id="form2">
                  <div align="center">
                    <input type="submit" name="GRAVAR" id="GRAVAR" value="Enviar para Financeiro" />
                    <br />
                  <br />
                  <input type="hidden" name="id" value="1" />
                  <input type="hidden" name="financeiro" value="1" />
                  <input type="hidden" name="regiao" value="<?php print "$regiao";?>" />
                  <input type="hidden" name="compra" value="<?php print "$row[0]";?>" />
                  </div>
                </form></td>
              </tr>
            </table>
            <div align="center"><br />
              <?php print "<a href='gestaocompras.php?id=1&regiao=$regiao'><img src='imagens/voltar.gif' border=0></a>"; ?> <Br />
              
            </div></td>
        </tr>
        
        <tr height="32">
          <td height="32"><div align="center"><span class="style12"><strong><br />
<?php
$end = new empresa();
$end -> endereco('#003300','7px');
?>&nbsp;&nbsp; &nbsp; </span></div></td>
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
    <td height="37" colspan="4" bgcolor="#5C7E59"><img src="layout/baixo.gif" width="750" height="38" />
<?php
include "empresa.php";
$rod = new empresa();
$rod -> rodape();
?><br />
    </div></td>
  </tr>
</table>
</body>
</html>
<?php
}else{

$regiao = $_REQUEST['regiao'];
$compra = $_REQUEST['compra'];
$id = $_REQUEST['id'];

switch ($id) {
case 1:

print "<link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
<br>
<hr>
<center>
SELECIONE A REGIÃO<br><br>
<form action='confirmandocompra.php' method='post' name=form1'>
<select name='regiao' class='campotexto' id='regiao'>

";
$sql = mysql_query("SELECT * from regioes");
while ($row = mysql_fetch_array($sql)){
  print "<option value=$row[id_regiao]>$row[regiao] - $row[sigla]</option>";
}
print "</select><br><br>
<input type='submit' name='Enviar' id='Enviar' value='Enviar'>
<input type='hidden' name='id' value='2' />
<input type='hidden' name='compra' value='$compra' />
<input type='hidden' name='financeiro' value='1' />
</form>
";

break;

case 2:

$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];
$compra = $_REQUEST['compra'];
$data = date('Y-m-d');
$data_proc = date('Y-m-d H:i:s');
$tipo = "66";

$result = mysql_query("SELECT * FROM compra where id_compra = '$compra'");
$row = mysql_fetch_array($result);

mysql_query("UPDATE compra SET id_user_aprovacao='$id_user', data_decisao='$data', acompanhamento = '5' where id_compra = '$compra'") or die ("<center>ERRO!<br> tente novamente mais tarde<br><br>".mysql_error());

mysql_query("INSERT INTO saida(id_regiao,id_user,nome,especifica,tipo,valor,data_proc,data_vencimento,id_compra) values 
('$regiao','$id_user','$row[nome_produto]','$row[descricao_produto]','$tipo','$row[preco_final]','$data_proc','$row[data_produto]','$compra')") or die ("O servidor não respondeu conforme deveria, tente novamente mais tarde, Obrigado!<br><hr>".mysql_error());

print "
<link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
<br>
<hr>
<center>
<font color=#FFFFFF>
Informações gravadas com sucesso!<br><br>
</font>
<br><br>
<a href='gestaocompras.php?id=1&regiao=$regiao'><img src='imagens/voltar.gif' border=0></a>
</center>
";


break;
}

}


}

?>