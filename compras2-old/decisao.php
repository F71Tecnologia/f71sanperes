<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../conn.php";
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
<link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css">

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Controle de Cota&ccedil;&otilde;es</title>
<style>
table{
border-collapse:collapse;	
}

 table tr{

border: 1px #CCC solid;
}

</style>

</head>

<body>
<div id="corpo">
  <div id="conteudo">
				<?php
				include "../empresa.php";
				$img= new empresa();
				$img -> imagemCNPJ();
				?><br />
          <h3>  ESCOLHA DO FORNECEDOR</h3>
         <h3>    NUMERO DO PROCESSO: <?php print "$row[num_processo]";?></h3>
       
     
          
          <br />
          <?php
		  
		  $result_fornecedor = mysql_query("SELECT * FROM fornecedores where id_fornecedor = '$row[fornecedor1]'");
		  $row_fornecedor = mysql_fetch_array($result_fornecedor);
		  
	  print "<h3 class='titulo_projeto'>FORNECEDOR 1: $row_fornecedor[nome]     Contato: $row_fornecedor[contato]      Telefone: $row_fornecedor[tel]</h3>"
	  ?>
          
          
           
       
           <table width="96%" align="center" cellpadding="0" cellspacing="0" class="relacao" border="1" > 
              <tr>
                <td height="10" colspan="6" class="titulo_tabela1">
                DESCRI&Ccedil;&Atilde;O DO PROCESSO</td>
              </tr>
              <tr height="32">
                <td width="93" height="31" align="right" valign="middle" >ITEM: &nbsp;</td>
                <td height="31" colspan="5" align="left" valign="middle" >&nbsp;<?php print "$row[nome_produto]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" ><strong>DESCRI&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" >&nbsp;<?php print "$row[descricao_produto]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" ><strong>MARCA: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" >&nbsp;<?php print "$row[marca1]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" ><strong>OBSERVA&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" >&nbsp;<?php print "$row[obs1]";?></td>
              </tr>
              <col width="44" />
              <col width="64" span="5" />
              <col width="9" />
              <col width="64" />
              <col width="62" />
              <col width="138" />
              <col width="133" />
              <col width="120" />
              <tr class="titulo_tabela1">
                <td height="16" colspan="2">VALOR DOS IMPOSTOS</td>
                <td width="123">VALOR DO FRETE</td>
                <td width="140">DESCONTOS</div></td>
                <td width="141">PRE&Ccedil;O UNIT&Aacute;RIO</td>
                <td width="136" height="16">PRE&Ccedil;O FINAL</td>
              </tr>
              <tr height="32">
                <td height="31" colspan="2" align="center" valign="middle" >R$ <?php print "$row[imposto1]";?></td>
                <td width="123" align="center" valign="middle" >R$ <?php print "$row[frete1]";?></td>
                <td width="140" align="center" valign="middle" >R$ <?php print "$row[desconto1]";?></td>
                <td width="141" align="center" valign="middle" >R$ <?php print "$row[valor_uni1]";?></td>
                <td width="136" height="31" align="center" valign="middle" >R$ <?php print "$row[preco1]";?></td>
              </tr>
              <tr>
                <td height="16" colspan="2">NECESSARIO PARA:</div></td>
                <td>DATA DO PEDIDO</div></td>
                <td height="16"><div align="center">
                  DATA PARA ENTREGA</div>
                </div></td>
                <td height="16"><div align="center">
                  QUANTIDADE</div>
                </div></td>
                <td height="16"><div align="center">
                  SOLICITADO POR</div>
                </div></td>
              </tr>
              <tr height="32">
                <td height="31" colspan="2" align="center" valign="middle" ><?php print "$row[data_produto]";?></td>
                <td align="center" valign="middle" ><?php print "$row[data_requisicao]";?></td>
                <td height="31" align="center" valign="middle" ><?php print "$row[prazo1]";?></td>
                <td height="31" align="center" valign="middle" ><?php print "$row[quantidade]";?></td>
                <td height="31" align="center" valign="middle" ><span ><?php print "$row_user[nome1]";?></span></td>
              </tr>
            </table>
      
      <br />
      <br />
      <br />
      <hr />
                <?php
		  
		  $result_fornecedor2 = mysql_query("SELECT * FROM fornecedores where id_fornecedor = '$row[fornecedor2]'");
		  $row_fornecedor2 = mysql_fetch_array($result_fornecedor2);

		   print "<h3 class='titulo_projeto'> FORNECEDOR 2: $row_fornecedor2[nome]     Contato: $row_fornecedor2[contato]      Telefone: $row_fornecedor2[tel]</h3>"
	  ?>
		  
          
           
           <span class="style2">
           <table width="96%" align="center" cellpadding="0" cellspacing="0" class="relacao" border="1" > 
              <tr>
                <td height="10" colspan="6"  class="titulo_tabela1">
                DESCRI&Ccedil;&Atilde;O DO PROCESSO</div></td>
              </tr>
              <tr height="32">
                <td width="93" height="31" align="right" valign="middle" >ITEM: &nbsp;</td>
                <td height="31" colspan="5" align="left" valign="middle" >&nbsp;<?php print "$row[nome_produto]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" ><strong>DESCRI&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" >&nbsp;<?php print "$row[descricao_produto]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" ><strong>MARCA: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" >&nbsp;<?php print "$row[marca2]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" ><strong>OBSERVA&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td height="31" colspan="5" align="left" valign="middle" >&nbsp;<?php print "$row[obs2]";?></td>
              </tr>
             
              <tr  class="titulo_tabela1">
                <td height="16" colspan="2">VALOR DOS IMPOSTOS</div></td>
                <td width="123">VALOR DO FRETE</div></td>
                <td width="140">DESCONTOS</div></td>
                <td width="141">PRE&Ccedil;O UNIT&Aacute;RIO</div></td>
                <td width="136" height="16">PRE&Ccedil;O FINAL</div></td>
              </tr>
              <tr height="32">
                <td height="31" colspan="2" align="center" valign="middle" >R$ <?php print "$row[imposto2]";?></td>
                <td width="123" align="center" valign="middle" >R$ <?php print "$row[frete2]";?></td>
                <td width="140" align="center" valign="middle" >R$ <?php print "$row[desconto2]";?></td>
                <td width="141" align="center" valign="middle" >R$ <?php print "$row[valor_uni2]";?></td>
                <td width="136" height="31" align="center" valign="middle" >R$ <?php print "$row[preco2]";?></td>
              </tr>
              <tr>
                <td height="16" colspan="2">NECESSARIO PARA:</div></td>
                <td>DATA DO PEDIDO</div></td>
                <td height="16"><div align="center">
                  DATA PARA ENTREGA</div>
                </div></td>
                <td height="16"><div align="center">
                  QUANTIDADE</div>
                </div></td>
                <td height="16"><div align="center">
                  SOLICITADO POR</div>
                </div></td>
              </tr>
              <tr height="32">
                <td height="31" colspan="2" align="center" valign="middle" ><?php print "$row[data_produto]";?></td>
                <td align="center" valign="middle" ><?php print "$row[data_requisicao]";?></td>
                <td height="31" align="center" valign="middle" ><?php print "$row[prazo2]";?></td>
                <td height="31" align="center" valign="middle" ><?php print "$row[quantidade]";?></td>
                <td height="31" align="center" valign="middle" ><span ><?php print "$row_user[nome1]";?></span></td>
              </tr>
            </table>
           
           <br />
      <br />
      <br />
      <hr />
              <?php
		  
		  $result_fornecedor3 = mysql_query("SELECT * FROM fornecedores where id_fornecedor = '$row[fornecedor3]'");
		  $row_fornecedor3 = mysql_fetch_array($result_fornecedor3);
		  
		  print "<h3 class='titulo_projeto'>FORNECEDOR 3: $row_fornecedor3[nome]     Contato: $row_fornecedor3[contato]      Telefone: $row_fornecedor3[tel]</h3>"
	  ?>
		  
          
           
           <span class="style2">
           <table  align="center" cellpadding="0" cellspacing="0" class="relacao" border="1" > 
              <tr>
                <td height="10" colspan="7"  class="titulo_tabela1">
               DESCRI&Ccedil;&Atilde;O DO PROCESSO</div></td>
              </tr>
              <tr height="32">
                <td width="93" height="31" align="right" valign="middle" >ITEM: &nbsp;</td>
                <td height="31" colspan="6" align="left" valign="middle" >&nbsp;<?php print "$row[nome_produto]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" ><strong>DESCRI&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td height="31" colspan="6" align="left" valign="middle" >&nbsp;<?php print "$row[descricao_produto]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" ><strong>MARCA: &nbsp;</strong></td>
                <td height="31" colspan="6" align="left" valign="middle" >&nbsp;<?php print "$row[marca3]";?></td>
              </tr>
              <tr height="32">
                <td height="31" align="right" valign="middle" ><strong>OBSERVA&Ccedil;&Atilde;O: &nbsp;</strong></td>
                <td height="31" colspan="6" align="left" valign="middle" >&nbsp;<?php print "$row[obs3]";?></td>
              </tr>
              <col width="44" />
              <col width="64" span="5" />
              <col width="9" />
              <col width="64" />
              <col width="62" />
              <col width="138" />
              <col width="133" />
              <col width="120" />
              <tr  class="titulo_tabela1">
                <td height="16" colspan="2">VALOR DOS IMPOSTOS</td>
                <td width="123">VALOR DO FRETE<td>
                <td width="140">DESCONTOS</td>
                <td width="141">PRE&Ccedil;O UNIT&Aacute;RIO</td>
                <td width="136" height="16">PRE&Ccedil;O FINAL</td>
              </tr>
              <tr height="32">
                <td height="31" colspan="2" align="center" valign="middle" >R$ <?php print "$row[imposto3]";?></td>
                <td width="123" align="center" valign="middle" >R$ <?php print "$row[frete3]";?></td>
                <td width="140" align="center" valign="middle" >R$ <?php print "$row[desconto3]";?></td>
                <td width="141" align="center" valign="middle" >R$ <?php print "$row[valor_uni3]";?></td>
                <td width="136" height="31" align="center" valign="middle" >R$ <?php print "$row[preco3]";?></td>
              </tr>
              <tr>
                <td height="16" colspan="2">NECESSARIO PARA:</div></td>
                <td>DATA DO PEDIDO</div></td>
                <td height="16"><div align="center">
                  DATA PARA ENTREGA</div>
                </div></td>
                <td height="16"><div align="center">
                  QUANTIDADE</div>
                </div></td>
                <td height="16" colspan="2"><div align="center">
                  SOLICITADO POR</div>
                </div></td>
              </tr>
              <tr height="32">
                <td height="31" colspan="2" align="center" valign="middle" ><?php print "$row[data_produto]";?></td>
                <td align="center" valign="middle" ><?php print "$row[data_requisicao]";?></td>
                <td height="31" align="center" valign="middle" ><?php print "$row[prazo3]";?></td>
                <td height="31" align="center" valign="middle" ><?php print "$row[quantidade]";?></td>
                <td height="31" align="center" valign="middle"  colspan="2">
				<?php print "$row_user[nome1]";?></td>
              </tr>
            </table>
            <br />
      <br />
      <br />
      <hr />
            <a href="<?=print "../reldecisao.php?compra=$id_compra&regiao=$regiao";?>" target="_blank" style="text-decoration:none">IMPRIMIR COTA&Ccedil;&Otilde;ES</a> <img src="../imagens/impressora.jpg" alt="Imprimir" width="30" height="30" align="absmiddle" />&nbsp;&nbsp;&nbsp;&nbsp;
            
              <a href="<?=print "imprimir_anexo.php?compra=$id_compra&regiao=$regiao";?>" target="_blank" style="text-decoration:none">IMPRIMIR ANEXOS</a> <img src="../imagens/impressora.jpg" alt="Imprimir" width="30" height="30" align="absmiddle" /><br />
            <br />
            Selecione o Fornecedor pelo qual o Produto ou Servi&ccedil;o ser&aacute; adquirido:</strong>
        <form action="decisao.php" method="post" name="form1" class="style2" id="form3"><div align="center">
                  <p><strong>
                <label>
                <select name="escolha" id="escolha"> 
                  <option value="1"><?php echo $row_fornecedor['nome'];?> </option>
                  <option value="2"><?php echo $row_fornecedor2['nome'];?> </option>
                  <option value="3"><?php echo $row_fornecedor3['nome'];?> </option>
                </select>
                </label>
                <br />
                <br />
                  <br />
                Relat&oacute;rio de Decis&atilde;o do Produto ou servi&ccedil;o: </strong><br />
                <span class="style13">Descreva a baixo os motivos que levaram a tomada de decis&atilde;o para o fornecedor escolhido.<br />
                (Caso queira pular linha insira use o caracter #)</span><br />  
                <br />  
                <label>
                  <textarea name="motivo" cols="80" rows="6" id="motivo"></textarea>
                  </label>
                <br />
              </p>
              <p><br />
                <input type="submit" name="GRAVAR4" id="GRAVAR4" value="Selecionar Fornecedor" />
                <br />
                <br />
                <input type="hidden" name="regiao" value="<?php print "$regiao";?>" />
                <input type="hidden" name="pedido" value="<?php print "$row[0]";?>" />
                <br />
              </p>
            </div>
            </form>
            <?php print "<a href='../gestaocompras.php?id=1&regiao=$regiao'><img src='../imagens/voltar.gif' border=0></a>"; ?></td>
        </tr>
        
        <tr height="32">
          <td height="32"><div align="center">
<br>
<br>
<?php
$end = new empresa();
$end -> endereco('black',10);
?></div></td>
        </tr>
      </table>
 
  

<?php
$rod = new empresa();
$rod -> rodape('#003300','7px');
?>
</div>
</div>
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

mysql_query("UPDATE compra2 SET id_user_escolha='$id_user', data_escolha='$data', 
fornecedor_escolhido = '$fornecedor', produto='$row[nome_produto]', 
prazo='$prazo', marca='$marca', preco_final='$preco', descricao_compra='$motivo'
where id_compra = '$pedido'") or die ("<center>ERRO!<br> tente novamente mais tarde<br><br>".mysql_error());

//acompanhamento='5' 

//if($_COOKIE['logado'] == 87){
	
	header("Location: ver_selecao.php?compra=$pedido");
/*	
} else {
print "
<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\">
<br>
<hr>
<center>
<font color=#FFFFFF> 
Dados gravados com sucesso!<br><br>
</font>
<br><br>
<a href='../gestaocompras.php?id=1&regiao=$regiao'><img src='../imagens/voltar.gif' border=0></a>
</center>
";

}*/
}
}

?>