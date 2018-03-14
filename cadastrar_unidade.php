<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$master = $row_user['id_master'];

// SELECIONANDO AS REGIÕES CADASTRADAS NO BANCO
$sql = "SELECT * from regioes where id_master = '$row_user[id_master]'";
$result = mysql_query($sql, $conn);

$id_regiao = $_REQUEST['regiao'];

?>
<html>
<head><title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<script language="javascript" src="jquery-1.3.2.js"></script>
<!--<script src='ajax.js' type='text/javascript'></script>-->
<!--<script language="javascript" src='js/ramon.js' type='text/javascript'></script>-->
<link href='autocomp/css.css' type='text/css' rel='stylesheet'>
<script src='jquery/jquery-1.4.2.min.js' type='text/javascript'></script>
<script src="jquery/validationEngine/jquery.validationEngine.js" type="text/javascript"></script>
<script src="jquery/validationEngine/jquery.validationEngine-pt.js" type="text/javascript"></script>
<script src="jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
<link  href="jquery/validationEngine/validationEngine.jquery.css" type="text/css" rel="stylesheet"/>
<link  href="adm/css/estrutura.css" type="text/css" rel="stylesheet"/>
<script type="text/javascript">
$(function() {
	
	$('#cep').mask('99999-999');
	$('#tel').mask('(99)9999-9999');
	$('#tel2').mask('(99)9999-9999');
	$('#cel').mask('(99)9999-9999');
});

</script>

</head>
<body>
	<div id="corpo">
    	<div id="conteudo">  
        		 <div class="right"><?php include('reportar_erro.php'); ?></div>
       			 <div class="clear"></div>
                 
        		<img src="imagens/logomaster<?php echo $master?>.gif"/>
				<h3>CADASTRO DE UNIDADES</h3>
              
<form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">
<table width='454' border='0' cellpadding='0' cellspacing='0'  class='relacao' align='center' bgcolor="#E5E5E5">

<tr class="titulo_tabela1">
  <td colspan='2' >DADOS DA Unidades</td>
</tr>
<tr>
  <td width="30%" class="secao" align="right" >Projeto:</td>
  <td width="70%"  align="left"><select name='projeto' class='campotexto'>
  <?php
$result_pro = mysql_query("SELECT * FROM projeto where id_regiao = '$id_regiao' AND status_reg = '1'");

while ($row_pro = mysql_fetch_array($result_pro)){
print "<option value=$row_pro[0]>$row_pro[0] - $row_pro[nome]</option>";
}
?>
  </select></td>
</tr>
<tr>
<td class="secao" align="right" >Nome:</td>
<td  align="left"><input name='nome' type='text' class='campotexto' id='nome' size='30'></td>
</tr>
<tr>
<td class="secao" align="right" >Local:</td>
<td  align="left"><input name='local' type='text' class='campotexto' id='local' size='20'></td>
</tr>
<tr>
<td class="secao" align="right" >Endereco:</td>
<td  align="left"><input name='endereco' type='text' class='campotexto' id='endereco' size='60'></td>
</tr>
<tr>
<td class="secao" align="right" >Bairro:</td>
<td  align="left"><input name='bairro' type='text' class='campotexto' id='bairro' size='20'></td>
</tr>
<tr>
<td class="secao" align="right" >Cidade:</td>
<td  align="left"><input name='cidade' type='text' class='campotexto' id='cidade' size='20'></td>
</tr>
<tr>
<td class="secao" align="right" >CEP:</td>
<td  align="left"><input name='cep' type='text' class='campotexto' id='cep' size='20'></td>
</tr>
<tr>
<td class="secao" align="right" >Ponto de referência:</td>
<td  align="left"><textarea name="ponto_referencia"></textarea></td>
</tr>
<tr>
<td class="secao" align="right" >Telefone:</td>
<td  align="left"><input name='tel' type='text' id='tel' size='12'  class='campotexto'></td>
</tr>
<tr>
<td class="secao" align="right" >Telefone Recado:</td>
<td  align="left"><input name='tel2' type='text'id='tel2' size='12' class='campotexto'></td>
</tr>
<tr>
<td class="secao" align="right" >Responsável:</td>
<td  align="left"><input name='responsavel' type='text' class='campotexto' id='responsavel' size='20'></td>
</tr>
<tr>
<td class="secao" align="right" >Celular do Responsável:</td>
<td  align="left">
<input name='cel' type='text'  id='cel' size='12'  class='campotexto'></td>
</tr>
<tr>
  <td class="secao" align="right" >E-mail do Responsável:</td>
  <td align="left" ><input name='email' type='text' class='campotexto' id='email' size='20' style="text-transform: lowercase;"></td>
</tr>

<tr>
  <td height="52" colspan='2' align='center'><input type='submit' name='Submit10' value='CADASTRAR'>
  <input type='hidden' name='id_cadastro' value='17'>
  <input type='hidden' name='regiao' value='<?=$id_regiao?>'></td>
</tr>
</table>
</form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>


<script>function validaForm(){
d = document.form1;
if (d.nome.value == ""){
alert("O campo Nome deve ser preenchido!");
d.nome.focus();
return false;
}
if (d.local.value == "" ){
alert("O campo Local deve ser preenchido!");
d.local.focus();
return false;
}
return true;   }
</script>
           </script>

			</div>
            </div>
</body>
</html>
<?php
}
?>
