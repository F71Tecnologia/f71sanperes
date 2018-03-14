<?php

if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$regiao = $_REQUEST['regiao'];
$id_ctps = $_REQUEST['id'];
$id_case = $_REQUEST['case'];

$result = mysql_query("SELECT * FROM controlectps where id_controle = '$id_ctps'") or die ("Não foi possivel encontrar a carteira solicitada<br><br>".mysql_error());
$row = mysql_fetch_array($result);

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_local = mysql_fetch_array($result_local);

$result_funci = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[id_user_cad]'");
$row_funci = mysql_fetch_array($result_funci);

$data_hoje = date('d/m/Y');

switch($id_case){
case 1:

?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="net1.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
a:link {
	color: #006600;
}
a:visited {
	color: #006600;
}
a:hover {
	color: #006600;
}
a:active {
	color: #006600;
}
.style40 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.style41 {font-family: Geneva, Arial, Helvetica, sans-serif}
.style42 {font-family: Geneva, Arial, Helvetica, sans-serif; font-weight: bold; }
-->
</style>

<script language="javascript"> 

  //o parâmentro form é o formulario em questão e t é um booleano 
  function ticar(form, t) { 
    campos = form.elements; 
    for (x=0; x<campos.length; x++) 
      if (campos[x].type == "checkbox") campos[x].checked = t; 
  } 


self.print()

</script> 
</head>

<body bgcolor="#FFFFFF">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"> 
      <table width="750" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td colspan="4"><img src="layout/topo.gif" width="750" height="38"></td>
        </tr>
        
        <tr>
          <td width="21" rowspan="3" background="layout/esquerdo.gif">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td width="26" rowspan="3" background="layout/direito.gif">&nbsp;</td>
        </tr>
        
        <tr>
          <td colspan="2" bgcolor="#FFFFFF"><p>&nbsp;</p>
          <p align="center"><span style="font-size: 13pt; font-family: Arial; color: #000000;">
		  <?php
				include "empresa.php";
				$img= new empresa();
				$img -> imagem();
			?>
          <strong><!-- <img src="imagens/certificadosrecebidos.gif" width="120" height="86"> --><br>
            <br>
            RECIBO DE ENTREGA DA CARTEIRA PROFISSIONAL</strong></span><br>
            Decreto LEI N&ordm; 229, de 28/02/1967 ( Alterando o Art. 29 da Lei 5.452  - C.L.T. ) <br>
          </p>
          <blockquote>
            <p align="right"><span class="style40"><?=$row_local['regiao']?>, <?=$data_hoje?>.</span></p>
            <p class="style42">Carteira Profissional N&ordm;  :<span class="style40">
              <?=$row['numero']?>
            </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; S&eacute;rie&nbsp;: <span class="style40">
            <?=$row['serie']?>
            </span>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; UF: <span class="style40">
            <?=$row['uf']?>
            </span></p>
            <p class="style42">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nome: <span class="style40">
              <?=$row['nome']?>
            </span><br>
              <br>
            Recebido Por: <?=$row_funci['nome1']?></p>
            <p align="right" class="style41">&nbsp;</p>
            <p align="center"><span class="style40"><span style="color: #000000;">              Recebemos a Carteira Profissional supra discriminada para atender as<BR>
  anota&ccedil;&otilde;es necess&aacute;rias e que ser&aacute; devolvida dentro de 48 horas, de acordo com a<BR>
              Lei em vigor</span></span><span style="font-size: 12px; font-family: Geneva, Arial, Helvetica, sans-serif; color: #000000;">.</span></p>
            <p align="center" class="style40">&nbsp;</p>
            <p align="center"><span class="style40">__________________________________________<br>
             <?php 
$nomEmp= new empresa();
$nomEmp -> nomeEmpresa(); 
?></span><br>
              </p>
            <p align="center"><br>
              <br>
            <hr>
  <p align="center"><? print"<a href='ctps.php?regiao=$regiao'><img src='imagens/voltar.gif' border=0></a>";?>
    <br>
    <br>
          </blockquote>          
          </td>
        </tr>
        
        <tr>
          <td width="155" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="549" bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        
        <tr valign="top"> 
          <td height="37" colspan="4"> <img src="layout/baixo.gif" width="750" height="38"> 
<?php
$rod = new empresa();
$rod -> rodape();
?><br>
            </div></td>
        </tr>
      </table>
    </td>
  </tr>
</table>

</body>
</html>
<?php
break;
case 2:

switch($row['preenchimento']){
case 1:
$novafrase = "assinar";
break;
case 2:
$novafrase = "dar baixa";
break;
case 3:
$novafrase = "férias";
break;
case 4:
$novafrase = "13º salário";
break;
case 5:
$novafrase = "licança";
break;
case 6:
$novafrase = "outros";
break;
}

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
</head><body bgcolor='#D7E6D5'>";

print "
<center>
<table width='750' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
  <tr>
    <td colspan='4'><img src='layout/topo.gif' width='750' height='38' /></td>
  </tr>
  <tr>
    <td width='21' rowspan='3' background='layout/esquerdo.gif'>&nbsp;</td>
    <td colspan='2'>&nbsp;</td>
    <td width='26' rowspan='3' background='layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan='2' align='center'><table width='500' border='0' cellpadding='0' cellspacing='1' bgcolor='#FFFFFF'>
      <tr>
        <td class='style19'><div align='right'><span class='style40'><strong>Nome</strong>:</span> </div></td>
        <td colspan='3'><strong> &nbsp;&nbsp;$row[nome]</strong></td>
      </tr>
      <tr>
        <td width='25%' class='style19'><div align='right' class='style40'><strong>Numero</strong>:</div></td>
        <td width='29%'><span class='style40'><strong> &nbsp;&nbsp;$row[numero]</strong></span></td>
        <td width='25%'><span class='style40'><strong>S&eacute;rie:&nbsp;&nbsp;$row[serie]</strong></span></td>
        <td width='21%'><span class='style40'><strong>&nbsp;&nbsp;&nbsp;UF:&nbsp;&nbsp;&nbsp;$row[uf]</strong></span></td>
      </tr>
      <tr>
        <td class='style19'><div align='right'></div></td>
        <td colspan='3'><strong>&nbsp;&nbsp;</strong></td>
      </tr>
      <tr>
        <td class='style19'><div align='right'><span class='style40'><strong>Observa&ccedil;&otilde;es:</strong></span></div></td>
        <td colspan='3'>&nbsp;&nbsp;$row[obs]</td>
      </tr>
      <tr>
        <td height='19' class='style19'><div align='right'><span class='style40'><strong>Preenchimento:</strong></span></div></td>
        <td colspan='3'>&nbsp;&nbsp;$novafrase</td>
      </tr>
      <tr>
        <td height='19' class='style19'><div align='right'><span class='style40'><strong>Observações:</strong></span></div></td>
        <td colspan='3'>&nbsp;&nbsp;$row[obs_preenchimento]</td>
      </tr>
      <tr>
        <td>&nbsp;&nbsp;</td>
        <td colspan='3'>&nbsp;</td>
      </tr>
      <tr>
        <td colspan='4'><center>
          <a href='javascript:history.go(-1)'>Voltar</a>
        </center></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td width='155'>&nbsp;</td>
    <td width='549'>&nbsp;</td>
  </tr>
  <tr valign='top'>
    <td height='37' colspan='4' bgcolor='#5C7E59'><img src='layout/baixo.gif' width='750' height='38' />
        <div align='center' class='style6'><strong>Intranet do Instituto Sorrindo Para a Vida</strong> - Acesso Restrito 
          a Funcion&aacute;rios <br />
      </div></td>
  </tr>
</table>
</center>
</body>
</html>

";


break;
}
}
?>
