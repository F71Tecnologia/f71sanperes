<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";
include "wfunction.php";

$usuario = carregaUsuario();

echo $regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);


$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Financeiro</title>

<link href="adm/css/estrutura.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="corpo">
	<div id="conteudo">
 	<div style="float:right;"> <?php include('reportar_erro.php'); ?> </div>

	<div> <img src="imagens/logomaster<?php echo $row_master['id_master']?>.gif"/> </div>

<h3> <img src="imagensfinanceiro/cadastrofornecedores.gif" alt="cotas" width="25" height="25" align="absmiddle" /> VISUALIZA&Ccedil;&Atilde;O DE FORNECEDORES </h3>

<table border="0" class="relacao">

    
      <tr class="titulo_tabela1">
      <td width="4%"  >Cód</td>
        <td width="13%">Nome</td>
        <td width="15%"  >Endere&ccedil;o</td>
        <td width="15%"  >Tipo de servi&ccedil;os</td>
        <td width="15%"  >e-mail</td>
        <td width="11%"  >Tel</td>
        <td width="9%"  >Contato</td>
      </tr>
      <?php
	  
	  $result_fornecedores = mysql_query("SELECT * FROM fornecedores WHERE id_regiao = '$regiao'");
	  while($row_fornecedores = mysql_fetch_array($result_fornecedores)){
		  
	  if($cont++ % 2){ $class = 'class="linha_um"'; }else{ $class = 'class="linha_dois"'; }
      print "
	  <tr  $class>
        <td >$row_fornecedores[0]</td>
		<td >$row_fornecedores[nome]</td>
        <td >$row_fornecedores[endereco]</td>
        <td >$row_fornecedores[produto]</td>
        <td >$row_fornecedores[email]</td>
        <td >$row_fornecedores[tel]</td>
        <td class=border3>$row_fornecedores[contato]</td>
      </tr>";
	  $cont ++;
	  }
      ?>
     
   <!-- <td colspan="4"> <strong><img src="imagensfinanceiro/saidas.gif" alt="saida" width="25" height="25" align="absmiddle" /> REMOVER FORNECEDOR </strong> </td>
      </tr>
  -->
    </table>
    
    
    
  
	<?php print "<a href='cadfornecedores.php?regiao=$regiao' style='TEXT-DECORATION: none;'>"; ?>
    
  
   
  </tr>
  
  </td>
  </tr>
</table>
<div id="rodape">
<?php
include "empresa.php";
$rod = new empresa();
$rod -> rodape();
?>
</div>
	</div>
</div>
</body>
</html>
<?php

}

?>