<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../conn.php";

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
<link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Controle de Cota&ccedil;&otilde;es</title>
</head>
<style>
table tr {
border:1px solid  #CCC;
height:30px;	
}
</style>

<body>
<div id="corpo">
	<div id="conteudo">

      <?php
include "../empresa.php";
$img= new empresa();
$img -> imagemCNPJ();
?>
        <h3 style="color:#FF5959">  AUTORIZA&Ccedil;&Atilde;O DA COMPRA</h3>
         <h3>   NUMERO DO PROCESSO: <?php print "$row[num_processo]";?></h3>
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
		  

		  
          ?>
          <table class="relacao"  style="margin-top:30px;">
          <tr>
          	<td class="secao">Fornecedor 1:</td>
          	<td align="left"><?php echo $row_fornecedor['nome']?> </td>
          	<td class="secao">Contato:</td>
          	<td align="left"><?php echo $row_fornecedor['contato'];?></td>
          
          	<td class="secao">Telefone:</td>
          	<td align="left"><?php echo $row_fornecedor['tel'];?></td>
          </tr>
          </table>
          
          
                    
           <table width="96%" align="center" border = "1" cellpadding="0" cellspacing="0" class="relacao" style=" border-collapse:collapse;"> 
              <tr>
                <td colspan="6" class="titulo_tabela1">  DESCRI&Ccedil;&Atilde;O DO PROCESSO</td>
              </tr>
              <tr >
                <td  valign="middle" class="secao">ITEM: &nbsp;</td>
                <td colspan="5" align="left" valign="middle" >&nbsp;<?php print "$row[nome_produto]";?></td>
              </tr>
              <tr >
                <td  align="right" valign="middle" ><strong>DESCRI&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td  colspan="5" align="left" valign="middle">&nbsp;<?php print "$row[descricao_produto]";?></td>
              </tr>
              <tr >
                <td  valign="middle" class="secao"><strong>MARCA: &nbsp;</strong></td>
                <td  colspan="5" align="left" valign="middle" >&nbsp;
				<?php print "$marca";?></td>
              </tr>
              <tr >
                <td  align="right" valign="middle" class="secao"><strong>OBSERVA&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td  colspan="5" align="left" valign="middle" >&nbsp;
				<?php print "$obs";?></td>
              </tr>
             
              <tr class="titulo_tabela1">
                <td  colspan="2">VALOR DOS IMPOSTOS</td>
                <td width="123" >VALOR DO FRETE</td>
                <td width="140">DESCONTOS</td>
                <td width="141">PRE&Ccedil;O UNIT&Aacute;RIO</td>
                <td width="136">PRE&Ccedil;O FINAL</td>
              </tr>
              <tr >
                <td  colspan="2" align="center" valign="middle" >R$ 
				<?php print "$imposto";?></td>
                <td width="123" align="center" valign="middle" >R$ <?php print "$frete";?></td>
                <td width="140" align="center" valign="middle" >R$ <?php print "$desconto";?></td>
                <td width="141" align="center" valign="middle" >R$ <?php print "$valor_uni";?></td>
                <td width="136"  align="center" valign="middle" >R$ 
				<?php print "$valor";?></td>
              </tr>
              <tr class="titulo_tabela1">
                <td  colspan="2" >NECESSARIO PARA:</td>
                <td >DATA DO PEDIDO</td>
                <td >DATA PARA ENTREGA</td>
                <td ><div align="center">
                  <div align="center" class="style11">QUANTIDADE</div>
                </div>
                </td>
                <td>SOLICITADO PO</td>
              </tr>
              <tr >
                <td  colspan="2" align="center" valign="middle" ><?php print "$row[data_produto]";?></td>
                <td align="center" valign="middle" ><?php print "$row[data_requisicao]";?></td>
                <td  align="center" valign="middle" ><?php print "$prazo";?></td>
                <td  align="center" valign="middle" ><?php print "$row[quantidade]";?></td>
                <td  align="center" valign="middle" ><?php print "$row_user[nome1]";?></td>
              </tr>
              <tr class="titulo_tabela1">
                <td colspan="6" > MOTIVO DA DECIS&Atilde;O</td>
              </tr>
              <tr>
                <td  colspan="6" align="center" valign="middle" >
<?php

$descricao = str_replace("#","<br>",$row['descricao_compra']);

print "$descricao";


?>

	</td>
</tr>
</table>
          
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
              <?php print "<a href='../gestaocompras.php?id=1&regiao=$regiao'><img src='../imagens/voltar.gif' border=0></a>"; ?> <Br />
              
            </div></td>
        </tr>
        
        <tr >
          <td ><div align="center"><span class="style12"><strong><br />
<?php
$end = new empresa();
$end -> endereco('#003300','7px');
?>&nbsp;&nbsp; &nbsp; </span></div></td>
        </tr>
      </table>
<?php

$rod = new empresa();
$rod -> rodape();
?>

</div>
</div>
</body>
</html>
<?php
}else{
	
	?>
    

<?php

$regiao = $_REQUEST['regiao'];
$compra = $_REQUEST['compra'];
$id = $_REQUEST['id'];

switch ($id) {
case 1:

print "<link href='../adm/css/estrutura.css' rel='stylesheet' type='text/css'>
<br>
<div id='corpo'>
<div id='conteudo'>
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


	
	mysql_query("UPDATE compra SET id_user_aprovacao='$id_user', data_decisao='$data', acompanhamento = '8' where id_compra = '$compra'") or die ("<center>ERRO!<br> tente novamente mais tarde<br><br>".mysql_error());


	///mysql_query("UPDATE compra SET id_user_aprovacao='$id_user', data_decisao='$data', acompanhamento = '5' where id_compra = '$compra'") or die ("<center>ERRO!<br> tente novamente mais tarde<br><br>".mysql_error());



mysql_query("INSERT INTO saida(id_regiao,id_user,nome,especifica,tipo,valor,data_proc,data_vencimento,id_compra) values 
('$regiao','$id_user','$row[nome_produto]','$row[descricao_produto]','$tipo','$row[preco_final]','$data_proc','$row[data_produto]','$compra')") or die ("O servidor não respondeu conforme deveria, tente novamente mais tarde, Obrigado!<br><hr>".mysql_error());

$id_saida = mysql_insert_id();

$qr_assoc = mysql_query("INSERT INTO compra_saida_assoc (id_compra, id_saida) VALUES ('$compra', '$id_saida')");


print "<link href='adm/css/estrutura.css' rel='stylesheet' type='text/css'>
<br>
<div id='corpo'>
<div id='conteudo'>
<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\">
<br>
<hr>
<center>
<font color=#FFFFFF>
Informações gravadas com sucesso!<br><br>
</font>
<br><br>
<a href='../gestaocompras.php?id=1&regiao=$regiao'><img src='imagens/voltar.gif' border=0></a>
</center>
";


break;
}

}
?>
</div>
</div>
</body>
</html>
<?php

}

?>