<?php
if(empty($_COOKIE['logado'])) {
	print "Efetue o Login<br><a href='login.php'>Logar</a> ";
} else {

include('conn.php');

$id_user     = $_COOKIE['logado'];
$sql         = "SELECT * FROM funcionario where id_funcionario = '$id_user'";
$result_user = mysql_query($sql, $conn);
$row_user    = mysql_fetch_array($result_user);

$result_regi = mysql_query("SELECT * FROM regioes where id_regiao = '$row_user[id_regiao]'", $conn);
$row_regi    = mysql_fetch_array($result_regi);

$grupo_usuario   = $row_user['grupo_usuario'];
$regiao_usuario  = $row_user['id_regiao'];
$apelido_usuario = $row_user['nome1'];
$tipo_user       = $row_user['tipo_usuario'];

$data = date('d/m/Y');

$cont_result = mysql_query("SELECT COUNT(*) FROM tarefa where usuario = '$apelido_usuario' and id_regiao = '$regiao_usuario' and status_tarefa = '1'  and status_reg = '1'", $conn);
$row_cont    = mysql_fetch_array($cont_result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel='shortcut icon' href='favicon.ico'>
<link href="net1.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location=	'"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}

function popupfinanceiro(caminho,nome,largura,altura,rolagem) {
	var esquerda = (screen.width - largura) / 2;
	var cima = (screen.height - altura) / 2 -50;
	window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
}
//-->
</script>
<style type="text/css">
body {
	margin:0 17px 0 0;
	background-color:#E2E2E2;	
}
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<!-- <div style="border-bottom:solid 1px #999; width:100%"> -->
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
   <td align="center" valign="top">
<table width="750" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-left:1px solid #888; border-right:1px solid #888;">
        <tr align="center" valign="top"> 
          <td width="528" align="left">      
            <table width="99%" height="100%" border="0" align="left" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr>
                  <td width="19%" height="80"><img src="imagens/logomaster<?=$row_user['id_master']?>.gif" width="110" height="79"></td>
                  <td width="55%" valign="middle"><div style="color:#333;">Ol&aacute;,
				  <?php 
				  print "<br><span class='red'><b>$row_user[nome]</b></span>  <br>Data: <b>$data</b> <br>";
				  if ($tipo_user == "1" or $tipo_user == "4"){
				  print "você está visualizando a Região: <b>$row_regi[regiao]</b>" ;
				  }
				  ?>
				  </b><br>
                </div></td>
            <td width="26%" align="center" valign="middle" background="imagens/quadro_tarefa.gif"><font size="3"><br>
                    <br>
                      <strong><?php echo $row_cont['0']; ?></strong></font></td>
              </tr>
          </table> </td>
          <td width="220" align="center" valign="middle"><span class="style1"><a href="logof.php" class="link" target="_parent"><font color='#FF0000'><img  src="imagens/sair.gif" border="0"></font></a><br>
            <br>

<?php // Visualizando Regiões
	  if($tipo_user == '1' or $tipo_user == '4') { ?>

<form action="cadastro2.php" method="post" name="form1" target="_parent">
	<span id="labregiao1">
	<select name="regiao" class="campotexto" id="regiao" onchange="MM_jumpMenu('parent',this,0)">
        <option value="">- Selecione -</option>
        <optgroup label="Regiões em Funcionamento">

<?php
// Acesso a Administração
$ids_administracao = array('5','9','27','28','82','64','71','77','24','65','68','88','89','22');
$ids_sistema = array('9','68','75','87','89','22');

if(in_array($id_user,$ids_administracao)) {
	$acesso_administracao = true;
}
if(in_array($id_user,$ids_sistema)) {
	$acesso_sistema = true;
}
//

	$qr_regioes_ativas = mysql_query("SELECT * FROM regioes WHERE id_master = '$row_user[id_master]' AND status = '1'");
	while($row_regiao = mysql_fetch_array($qr_regioes_ativas)) {
		
		if($regiao_usuario == $row_regiao['id_regiao']) {
			$selected = 'selected';
		} else {
			$selected = NULL;
		}
		
		if(($row_regiao['id_regiao'] == '15' and isset($acesso_administracao)) or
		   ($row_regiao['id_regiao'] != '15')) {
		
		if(($row_regiao['id_regiao'] == '36' and isset($acesso_sistema)) or 
		   ($row_regiao['id_regiao'] != '36')) { ?>

				<option value="cadastro2.php?regiao=<?=$row_regiao['id_regiao']?>&regiao_de=<?=$regiao_usuario?>&user=<?=$id_user?>&id_cadastro=13" <?=$selected?>><?=$row_regiao['id_regiao'].' - '.$row_regiao['regiao']?></option>
		
	<?php } } } ?>
	
</optgroup>
<optgroup label="Regiões Desativadas">

<?php // Acesso a Regiões Desativadas
$ids_desativadas = array('1','5','9','27','20','57','64','68','51','77','87','82','88','79','59','65','22','89');

if(in_array($id_user,$ids_desativadas)) {
	
	$qr_desativadas = mysql_query("SELECT * FROM regioes WHERE id_master = '$row_user[id_master]' AND status = '0'");
	while($row_regiao = mysql_fetch_array($qr_desativadas)) {
		
		if($regiao_usuario == $row_regiao['id_regiao']) {
			$selected = 'selected';
		} else {
			$selected = NULL;
		} ?>
		
        <option value="cadastro2.php?regiao=<?=$row_regiao['id_regiao']?>&regiao_de=<?=$regiao_usuario?>&user=<?=$id_user?>&id_cadastro=13" <?=$selected?>><?=$row_regiao['id_regiao'].' - '.$row_regiao['regiao']?></option>
		
<?php } } ?>

</optgroup>
</select>
</span>

<?php } // Fim de Regiões

//-------------------------------------------TROCA DE MASTER---------------------------------------------------------
// $id_user == "9" or 
if ($id_user == "9" or $id_user == "1" or $id_user == "5" or $id_user == "57" or $id_user == "51" or $id_user == "33" or $id_user == "32" or $id_user == "27" or $id_user == "68" or $id_user == "77" or $id_user == "24" or $id_user == "71" or $id_user == '64' or $id_user == '73' or $id_user == '82' or $id_user == '88' or $id_user == '22' or $id_user == '65' or $id_user == '85'){

print "
<select name='master' class='campotexto' id='master' onchange=\"MM_jumpMenu('parent',this,0)\">
<option value=''>- Selecione -</option>";

$result_master = mysql_query("SELECT * FROM master where status = '1'");
while ($row_master = mysql_fetch_array($result_master)){

if ($row_user['id_master'] == "$row_master[0]"){

print "<option value=$row_master[0] selected>$row_master[nome]</option>";
} else {
print "<option value='cadastro2.php?master_de=$row_user[id_master]&id_cadastro=26&user=$row_user[id_funcionario]&master=$row_master[0]'>$row_master[nome]</option>";
}
}

//<input type='submit' name='Submit' value='ALTERAR' class='campotexto'>
print "
</select>

<input type='hidden' name='regiao_de' value='$row_user[id_regiao]'>
<input type='hidden' name='id_cadastro' value='13'>
<input type='hidden' name='user' value='$row_user[id_funcionario]'>
</form>

";
}
			?>
            </span></td>
        </tr>
        
        
      </table></td>
  </tr>
</table>
<!-- </div> -->
<?php
}
/* Liberando o resultado */
mysql_free_result($result_user);
mysql_free_result($result_regi);
mysql_free_result($cont_result);

/* Fechando a conexão */
mysql_close($conn);
?>
</body>
</html>
