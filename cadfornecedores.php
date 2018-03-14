<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";
include "wfunction.php";

$usuario = carregaUsuario();

$regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];

if(empty($_REQUEST['nome'])){
$regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];


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
<script type="text/javascript" src="jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="jquery/mascara/jquery.maskedinput-1.2.2.js"></script>


<link  href="adm/css/estrutura.css" type="text/css" rel="stylesheet"/>
<script>
$(function(){
	$('#tel,#tel2,#cel').mask('(99)9999-9999');
	$('#cnpj').mask('99.999.999/999-99');




});

</script>
</head>

<body>
<div id="corpo">
	<div id="conteudo">
   <div style="float:right;"> <?php include('reportar_erro.php'); ?> </div>
   
   
   <img src="imagens/logomaster<?php echo $row_master['id_master']?>.gif" />
   
<h3>   CADASTRO DE FORNECEDORES  </h3>
<form id="form1" name="form1" method="post" action="">
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0" class="relacao" height="600" bgcolor="#E5E5E5"> 
 

  <tr>
    <td height="18" colspan="6" class="titulo_tabela1">
     Cadastrar Fornecedores por data de Cadastro
     </td>
  </tr>
  
    <tr>
  <td class="secao">Projeto:</td>
    <td  align="left" colspan="5">
    <select name="id_projeto">
        <option value=0>Selecione um Projeto</option>
        <?
        $sql_proj="select * from projeto WHERE id_regiao='$regiao' order by nome";
        $sql_result_proj=mysql_query($sql_proj);
        while ($dados_proj=mysql_fetch_array($sql_result_proj)){
          $id_projeto=$dados_proj["id_projeto"];
          $nome=$dados_proj["nome"];
         ?>
                   <option value="<? echo $dados_proj["id_projeto"];?>"><? echo $dados_proj["nome"];?></option>
         <? } ?>
        </select>

        
</td>
</tr>
  
  <tr>
  <td class="secao">Tipo de fornecedor:</td>
    <td  align="left" colspan="5">
    
      <select name="obj" id="obj">
        <option>FORNECEDOR DE SERVI&Ccedil;OS</option>
        <option>FORNECEDOR DE PRODUTOS</option>
      </select>
</td>
</tr>
<tr>
	<td class="secao">Nome:</td>
    <td colspan="5"  align="left">   <input name="nome" type="text" id="nome" size="80" /> </td>
</tr>
     
<tr>
	<td class="secao">Raz&atilde;o:</td>
    <td colspan="5"  align="left"> <input name="razao" type="text" id="razao" size="80" /></td>
</tr>

<tr>
	<td class="secao">Endere&ccedil;o:</td>
	<td colspan="5" align="left"><input name="endereco" type="text" id="endereco" size="80" /></td>
</tr>
<tr>
	<td class="secao">CNPJ:</td>
	<td align="left"><input name="cnpj" type="text" id="cnpj" size="20" /></td>

	<td class="secao">IE:</td>
	<td align="left"><input name="ie" type="text" id="ie" size="20" /></td>
	<td class="secao">IM: </td>
	<td align="left"><input name="im" type="text" id="im" size="20" /></td>
</tr>
<tr>
	<td class="secao">Tel 1:</td>
	<td align="left"> <input name="tel" type="text" id="tel" size='12' maxlength="12" 
      onkeyup="pula(13,this.id,tel2.id)" /></td>

	<td class="secao">Tel 2:</td>
	<td colspan="3" align="left">   <input name="tel2" type="text" id="tel2" size='12' maxlength="12" 
     onKeyPress="return(TelefoneFormat(this,event))" onkeyup="pula(13,this.id,radio.id)" /></td>
</tr>
<tr>
	<td class="secao">Cel:</td>
	<td colspan="5" align="left"> <input name="cel" type="text" id="cel" size='12' maxlength="12" 
     onKeyPress="return(TelefoneFormat(this,event))" onkeyup="pula(13,this.id,radio2.id)"/> </td>
</tr>
<tr>
	<td class="secao">R&aacute;dio:</td>
	<td colspan="5" align="left"> <input name="radio" type="text" id="radio" size="15" /></td>
</tr>

<tr>
	<td class="secao">E-mail:</td>
	<td colspan="5" align="left"><input name="email" type="text" id="email" size="40" /></td>
</tr>
<tr>
	<td class="secao">Site:</td>
	<td colspan="5" align="left">  <input name="site" type="text" id="site" size="70" /></td>
</tr>
<tr>
	<td class="secao">Contato:</td>
	<td colspan="5" align="left"> <input name="contato" type="text" id="contato" size="40" /></td>
</tr>


<tr>
	<td class="secao">Tipos de Produtos:</td>
	<td colspan="5" align="left"> <input name="produtos" type="text" id="produtos" size="70" /></td>
</tr>
<tr>
	<td class="secao">OBS:</td>
	<td colspan="5" align="left"> <input name="obs" type="text" id="obs" size="80" /></td>
</tr>
<tr>
	<td  colspan="6">
    	<div style="text-align:center; width:100%;height:auto;" >
	    <input type="submit" name="Submit" value="GRAVAR FORNECEDOR" />
	    
        </div>
	</td>
</tr>
<tr>
	<td colspan="6">&nbsp;</td>
</tr>



</table>
<input type="hidden" name="regiao" value="<?=$regiao?>" />
</form> 
<div id="rodape">

</div>	

	<?php 
	include 'empresa.php';
	$rod = new empresa();
	$rod -> rodape();
	
	?>	


</div>
</div>


</body>
</html>


<?php


}else{

//CADASTRANDO OS FORNECEDORES AQUI

$regiao = $_REQUEST['regiao'];
$nome = $_REQUEST['nome'];
$razao = $_REQUEST['razao'];
$endereco = $_REQUEST['endereco'];
$cnpj = $_REQUEST['cnpj'];
$ie = $_REQUEST['ie'];
$im = $_REQUEST['im'];
$tel = $_REQUEST['tel'];
$tel2 = $_REQUEST['tel2'];
$radio = $_REQUEST['radio'];
$email = $_REQUEST['email'];
$site = $_REQUEST['site'];
$contato = $_REQUEST['contato'];
$cel = $_REQUEST['cel'];
$radio2 = $_REQUEST['radio2'];
$produtos = $_REQUEST['produtos'];
$obs = $_REQUEST['obs'];
$data = date('Y-m-d');
$id_projeto = $_REQUEST['id_projeto'];

mysql_query("INSERT INTO fornecedores(id_regiao,nome,razao,endereco,cnpj,ie,im,tel,tel2,radio,email,site,contato,cel,radio2,produto,obs,data,id_projeto) values ('$regiao','$nome','$razao','$endereco','$cnpj','$ie','$im','$tel','$tel2','$radio','$email','$site','$contato','$cel','$radio2','$produtos','$obs','$data','$id_projeto')") or die ("O servidor não respondeu conforme deveria, tente novamente mais tarde, Obrigado!<br><br>".mysql_error());

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
</head><body bgcolor='#D7E6D5'>";
print "<br><br><center><span class='style27'><br>Informações gravadas com sucesso! </span><br><br><a href='javascript:window.close()'><img src='imagens/voltar.gif' border=0></a><center>";

 }
 
}
?>