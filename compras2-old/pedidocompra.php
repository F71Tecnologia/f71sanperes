<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../conn.php";

include('../classes_permissoes/acoes.class.php');

$ACOES = new Acoes();

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user_pedido'");
$row_user = mysql_fetch_array($result_user);

if(empty($_REQUEST['pagina'])){

$regiao = $_REQUEST['regiao'];
$id_compra = $_REQUEST['compra'];
$id_user = $_COOKIE['logado'];

$result = mysql_query("SELECT *,date_format(data_produto, '%d/%m/%Y')as data_produto, date_format(data_requisicao, '%d/%m/%Y')as data_requisicao FROM compra where id_compra = '$id_compra'");
$row = mysql_fetch_array($result);

$result_reg = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$result_user = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row[id_user_pedido]'", $conn);
$row_user = mysql_fetch_array($result_user);

$result_user_logado = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'", $conn);
$row_user_logado = mysql_fetch_array($result_user_logado);



$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user_logado[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);

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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - GEST&Atilde;O DE COMPRAS</title>
<link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css">

</head>

<body>
<div id="corpo">
	<div id="conteudo">
    
    
    
     <img src="../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/>
     <h3><img src="../imagensmenu2/compras.gif" alt="cotas" width="20" height="20" align="absmiddle" /> <span class="style3">VISUALIZANDO PEDIDO</span></h3>
<table width="750" border="1" cellpadding="0"  cellspacing="0" class="relacao" style="border-collapse:collapse;">
  
 <tr>
        <td height="40" colspan="4" align="center" valign="middle">N&uacute;mero do Pedido:&nbsp;<?php print "$row[num_processo]";?></td>
        </tr>
      <tr>
        <td width="18%" height="40" valign="middle" class="secao">Nome do Pedido:</td>
        <td height="40" colspan="3" align="left" valign="middle"><strong>&nbsp;<?php print "$row[nome_produto]";?></strong></td>
        </tr>
      <tr>
        <td height="40" valign="middle"  class="secao">Descri&ccedil;&atilde;o do Pedido:</td>
        <td height="40" colspan="3" align="left" valign="middle" ><strong>&nbsp;<?php print "$row[descricao_produto]";?></strong></td>
      </tr>
      <tr>
        <td height="40" valign="middle"  class="secao">Necessidade:</td>
        <td height="40" colspan="3" align="left" valign="middle"><strong>&nbsp;<?php print "$row[necessidade]";?></strong></td>
      </tr>
      <tr>
        <td height="40" valign="middle"  class="secao">Usu&aacute;rio:</td>
        <td height="40" align="left" valign="middle"><strong>&nbsp;<?php print "$row_user[nome1]";?></strong></td>
        <td height="40" align="left" valign="middle"  class="secao">Quantidade:</td>
        <td height="40" align="left" valign="middle"><strong>&nbsp;<?php print "$row[quantidade]";?></strong></td>
      </tr>
      <tr>
        <td height="40" valign="middle"  class="secao">Pedido para a Data:</td>
        <td width="34%" height="40" align="left" valign="middle"><strong>&nbsp;<?php print "$row[data_produto]";?></strong></td>
        <td width="25%" height="40" valign="middle"  class="secao">Data de Processamento:</td>
        <td width="23%" height="40" align="left" valign="middle"><strong>&nbsp;<?php print "$row[data_requisicao]";?></strong></td>
      </tr>
      
      <tr>
        <td height="40" valign="middle"  class="secao">Valor M&eacute;dio:</td>
        <td height="40" align="left" valign="middle">&nbsp;<strong>R$&nbsp;<?php print "$row[valor_medio]";?></strong></td>
        <td height="40" valign="middle"  class="secao">Tipo do Pedido:</td>
        <td height="40" align="left" valign="middle"><strong>&nbsp;<?php print "$tipo";?></strong></td>
      </tr>
    </table>
    <strong><br />
    <a href="#"></a></strong>
    <table width="98%" border="0" caellspacing="0" cellpadding="0">
      <tr>
        <td align="center">

<?php
$verifica = $row_user_logado['0'];
if($ACOES->verifica_permissoes(74)){
print "
<form action='pedidocompra.php' method='post' name='form1' id='form1'>
<label>
<input type='radio' id='autorizado' name='autorizado' value='2'>
Sim Autorizo
</label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label>
<input type='radio' id='autorizado' name='autorizado' value='0'>
Não Autorizo
</label><br><Br>
<input name='regiao' type='hidden' id='regiao' value='$regiao'/>
<input name='pedido' type='hidden' id='pedido' value='$row[0]'/>
<input name='pagina' type='hidden' id='pagina' value='2'/>
<input type='submit' name='button' id='button' value='Enviar' />
</form> ";
}

		?>
        </td>
      </tr>
    </table
  
><?php
include "../empresa.php";
$rod = new empresa();
$rod -> rodape();
?>

</div>
</div>
</body>
</html>
<?php
}else{               //----------------FAZENDO O UPDATE DO REGISTRO----------------------//


$regiao = $_REQUEST['regiao'];
$pedido = $_REQUEST['pedido'];
$id_user = $_COOKIE['logado'];
$data = date('Y-m-d');
$registro = $_REQUEST['autorizado'];


mysql_query("UPDATE compra2 SET id_user_autoriza='$id_user', data_processo='$data', status_requisicao = '$registro', acompanhamento = '$registro' where id_compra = '$pedido'") or die ("<center>ERRO!<br> tente novamente mais tarde<br><br>".mysql_error());


//if($_COOKIE['logado'] == 87  and $_COOKIE['logado'] == 9){
	if($registro == 0){
		
		header("Location: ../gestaocompras.php");
	}else {
	
		header("Location: ../compras/ver_autorizacao.php?id=1&regiao=$regiao&compra=$pedido");
	}
/*	
} else {
	
	print "
	<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\">
	<br>
	<hr>
	<center>
	<font color=#FFFFFF>
	Informações cadastradas com sucesso!<br><br>
	</font>
	<br><br>
	<a href='../gestaocompras.php?id=1&regiao=$regiao'><img src='imagens/voltar.gif' border=0></a>
	</center>
	";


}*/
}

}

?>