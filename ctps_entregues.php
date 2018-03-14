<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$regiao = $_REQUEST['regiao'];

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
.style36 {font-size: 14px}
.style38 {
	font-size: 16px;
	font-weight: bold;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	color: #FFFFFF;
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
.style44 {font-size: 10}
.style50 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #FFFFFF;
}
.style51 {font-size: 10px}
.style53 {font-family: Arial, Verdana, Helvetica, sans-serif}
.style56 {font-family: Arial, Verdana, Helvetica, sans-serif; font-weight: bold; }
-->
</style>
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
          <td width="21" rowspan="5" background="layout/esquerdo.gif">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td width="26" rowspan="5" background="layout/direito.gif">&nbsp;</td>
        </tr>
        
        <tr>
          <td height="53" colspan="2" background="imagens/fundo_cima.gif"><div align="center"><span class="style38"><br>
            CONTROLE DE CARTEIRAS DE TRABALHO</span><br>
            <br>
          </div></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF"><div align="center"></div></td>
        </tr>
        <tr>
          <td colspan="2" bgcolor="#FFFFFF"><br>
            <table  height="114" width="95%" align="center" cellspacing="0" class="bordaescura1px">
              <tr>
                <td height="45" bgcolor="#666666"><div align="right" class="style35">
                    <div align="center" class="style27 style36">CONTROLE DE CARTEIRAS  ENTREGUES</div>
                </div></td>
              </tr>
              
              <tr>
                <td><span class="style40">
                  <label> </label>
                  </span>
                    <label> </label>
                    <span class="style40"><strong>
                    <label></label>
                    </strong></span>
                    <form action="patrimonio.php" method="post" enctype="multipart/form-data" name='form1' onSubmit="return validaForm()" id="form2">
                      <br>
                      <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
                        <tr>
                          <td width="21%" bgcolor="#999999" class="style19"><div align="right" class="style50 style40">
                            <div align="center">NOME</div>
                          </div></td>
                          <td width="15%" bgcolor="#999999"><div align="center"><span class="style27"></span>
                                <div align="center" class="style51 style40 style27 style27">
                                  <div align="center"><strong><span class="style40">RECEBIMENTO</span></strong></div>
                                </div>
                          </div></td>
                          <td width="16%" bgcolor="#999999"><div align="center" class="style51 style40 style27 style27">
                            <div align="center"><strong><span class="style40">RECEBIDO POR</span></strong></div>
                          </div></td>
                          <td width="16%" bgcolor="#999999"><div align="center"><span class="style50">ENTREGUE EM </span></div></td>
                          <td width="17%" bgcolor="#999999"><div align="center"><span class="style50">ENTREGUE POR</span></div></td>
                          <td width="15%" bgcolor="#999999"><div align="center"><span class="style50">PREENCHIMENTO</span></div></td>
                        </tr>
<?php
$result_carteiras = mysql_query("SELECT *,date_format(data_cad, '%d/%m/%Y')as data_cadas, date_format(data_ent, '%d/%m/%Y')as data_ents FROM controlectps where id_regiao = '$regiao' and acompanhamento = '2'");
while($row_carteiras = mysql_fetch_array($result_carteiras)){

$result_funci1 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row_carteiras[id_user_cad]'");
$row_funci1 = mysql_fetch_array($result_funci1);

$result_funci2 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row_carteiras[id_user_ent]'");
$row_funci2 = mysql_fetch_array($result_funci2);


switch($row_carteiras['preenchimento']){
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

print"
<tr>
<td bgcolor='#CCCCCC'><div align='center'><a href=ctps_receber.php?case=2&regiao=$regiao&id=$row_carteiras[0]>$row_carteiras[nome]</a></div></td>
<td bgcolor='#CCCCCC'><div align='center'>$row_carteiras[data_cadas]</div></td>
<td bgcolor='#CCCCCC'><div align='center'>$row_funci1[0]</div></td>
<td bgcolor='#CCCCCC'><div align='center'>$row_carteiras[data_ents]</div></td>
<td bgcolor='#CCCCCC'><div align='center'>$row_funci2[0]</div></td>
<td bgcolor='#CCCCCC'><div align='center'>$novafrase</a></div></td>
</tr>";
}

?>
                      </table>
                      <div align="center">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="display:none" id="tablearquivo2">
                          <tr>
                            <td width="15%" align="right"><span class="style19">SELECIONE:</span></td>
                            <td width="85%"><span class="style19"> &nbsp;&nbsp;
                                  <input name="arquivo2" type="file" id="arquivo2" size="60" />
                            </span></td>
                          </tr>
                        </table>
                        <br>
                      </div>
                    </form></td>
              </tr>
            </table>
          <br>          </td>
        </tr>
        
        <tr>
          <td width="155" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="549" bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        
        <tr valign="top"> 
          <td height="37" colspan="4"> <img src="layout/baixo.gif" width="750" height="38"> 
<?php
include "empresa.php";
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
}
?>