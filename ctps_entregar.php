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
$ano_hoje = date('Y');

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
.style35 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-weight: bold;
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
.style40 {font-family: Geneva, Arial, Helvetica, sans-serif}
.style41 {	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
}
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
include_once "empresa.php";
$img= new empresa();
$img -> imagem();
?>
<strong><!--<img src="imagens/certificadosrecebidos.gif" alt="img" width="120" height="86">--><br>
            <br>
                <br>
                PROTOCOLO DE ENTREGA DA CARTEIRA  PROFISSIONAL</strong></span><br>
Decreto LEI N&ordm; 229, de 28/02/1967 ( Alterando o Art. 29 da Lei 5.452  - C.L.T. ) <br>
          </p>
          <blockquote>
            <p align="center"><br>
              <br>
              DADOS DA CARTEIRA<br>
              <br>
              <br>
            </p>
            <p class="style35">Carteira Profissional N&ordm;  :<span class="style41">
            <?=$row['numero']?>
            </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; S&eacute;rie&nbsp;: <span class="style41">
            <?=$row['serie']?>
            </span>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; UF: <span class="style41">
            <?=$row['uf']?>
            </span></p>
            <p class="style35">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nome: <span class="style41">
              <?=$row['nome']?>
              </span><br>
              <br>
              Recebido Por:
  <?=$row_funci['nome1']?>
            </p>
            <p class="style35"><br>
              <br>
            </p>
          </blockquote>
          <p align="center" class="style40">Recebi em devolu&ccedil;&atilde;o a Carteira Profissional  supra discriminada com as respectivas anota&ccedil;&otilde;es.<br>
            <br>
              <br>
                <br>
              <br>
          </p>
          <p align="center" class="style40"><span class="style41">
            <?=$row_local['regiao']?>
            ,</span> __________ de ________________________________ de <span class="style41">
            <?=$ano_hoje?>
            </span>.<br>
            <br>
            <br>
          <br>
              <br>
              <br>
              <br>
              ____________________________________________<br>
          ASSINATURA DO RESPONS&Aacute;VEL PELO RECEBIMENTO<br>
          <br>
          <br>
          <br>
          <br>
          </p>
          <hr>
          <p align="center"><br>
          </p>
          <form action="ctps_entregar.php" method="post" name="form">
          <p align="center">
            <input type="submit" name="gravar" id="gravar" value="ENTREGAR CTPS">
          <br>
          <input type="hidden" name="regiao" value="<?=$regiao?>">
          <input type="hidden" name="id" value="<?=$id_ctps?>">
          <input type="hidden" name="case" value="2">
          <br>
          </p>
          </form>
            </td>
        </tr>
        
        <tr>
          <td width="155" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="549" bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        
        <tr valign="top"> 
          <td height="37" colspan="4"><img src="layout/baixo.gif" width="750" height="38">
            <?php
include_once "empresa.php";
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

$id_user = $_COOKIE['logado'];
$data_ent = date('Y-m-d');
mysql_query("UPDATE controlectps SET acompanhamento = '2', data_ent = '$data_ent', id_user_ent ='$id_user' WHERE id_controle = '$id_ctps'");

print "
<script>
alert(\"Dados alterados com sucesso!\");
location.href=\"ctps.php?regiao=$regiao\"
</script>

";

break;
}

}

?>