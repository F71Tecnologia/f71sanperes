<?php

if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
}else{

include "../conn.php";

$id = $_REQUEST['id'];


switch($id){

case 1:

$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];
$mes = date('m');

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="../js/ramon.js"></script>
</head>

<body>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"> 
      <table width="750" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td colspan="4"><img src="../layout/topo.gif" width="750" height="38"></td>
        </tr>
        
        <tr>
          <td width="21" rowspan="4" background="../layout/esquerdo.gif">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td width="26" rowspan="4" background="../layout/direito.gif">&nbsp;</td>
        </tr>
    
         <tr>
              <td colspan="2" align="right" bgcolor="#FFF"><?php include('../reportar_erro.php');?></td>
            </tr>

        <tr>
          <td colspan="2" bgcolor="#FFFFFF"><br>
            <table width="97%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px">
            <tr>
              <td colspan="6" class="show">SINDICATOS CADASTRADOS</td>
            </tr>
            <tr class="novo_tr_dois">
              <td width="36%">NOME</td>
              <td width="12%">M&Ecirc;S DE DESCONTO</td>
              <td width="10%">M&Ecirc;S DE DISS&Iacute;DIO</td>
              <td width="15%">TEL</td>
              <td width="27%">CONTATO</td>
            </tr>
<?php 
$cont = 0;
$result = mysql_query("SELECT * FROM rhsindicato WHERE id_regiao = '$regiao' AND status = '1'");
while($row = mysql_fetch_array($result)){
	
if($cont % 2){ $classcor="corfundo_um"; }else{ $classcor="corfundo_dois"; }

$nome = substr($row['nome'],0,20);
$mes_desconto = $meses[$row['mes_desconto']];
$mes_dissidio = $meses[$row['mes_dissidio']];
print "
<tr class=\"novalinha $classcor\">
<td><a href=../rh/rh_sindicatos.php?id=3&regiao=$regiao&sindicato=$row[0]>$nome ...</a></td>
<td>$mes_desconto</td>
<td>$mes_dissidio</td>
<td>$row[tel]</td>
<td>$row[contato]</td>
</tr>";
$cont ++;
}

?>
          </table>
            <br>
            <br>
            <form action="rh_sindicatos.php" method="post" name="form1" onSubmit="return validaForm()">
            <table  height="450" width="95%" border="0" align="center" cellspacing="0">
            <tr>
              <td height="45" colspan="6" bgcolor="#CCCCCC"><div align="right" class="style35">
                <div align="center" class="style27 style36"><img src="imagensrh/sindicatos.gif" alt="empresa" width="150" height="40"></div>
              </div></td>
              </tr>
            <tr>
              <td><div align="right" class="style40 style35"><strong>Nome:</strong></div></td>
              <td colspan="5">
                <label>
                  <input name="nome" type="text" id="nome" size="90" onFocus="document.all.nome.style.background='#CCFFCC'" onBlur="document.all.nome.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
                  </label>                           </td>
              </tr>
            <tr>
              <td><div align="right" class="style35">Endere&ccedil;o:</div></td>
              <td colspan="5"><input name="endereco" type="text" id="endereco" size="90" onFocus="document.all.endereco.style.background='#CCFFCC'" onBlur="document.all.endereco.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()"></td>
              </tr>
            
            <tr>
              <td colspan="6"><span class="style35">CNPJ
                  <input name="cnpj" type="text" id="cnpj" style="background:#FFFFFF; text-transform:uppercase;"
                  onFocus="document.all.cnpj.style.background='#CCFFCC'" onBlur="document.all.cnpj.style.background='#FFFFFF'" onKeyUp="pula(19,this.id,tel.id)"
                  OnKeyPress="formatar('###.###.###/####-##', this)" size="19" maxlength="19">
                  &nbsp;&nbsp;Tel.:
                  <input name='tel' type='text' id='tel' size='12' onKeyPress="return(TelefoneFormat(this,event))" onKeyUp="pula(13,this.id,fax.id)" onFocus="document.all.tel.style.background='#CCFFCC'" onBlur="document.all.tel.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;">                  
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fax:
                  <input name='fax' type='text' id='fax' size='12' onKeyPress="return(TelefoneFormat(this,event))" onKeyUp="pula(13,this.id,contato.id)" onFocus="document.all.fax.style.background='#CCFFCC'" onBlur="document.all.fax.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;">
              </span></td>
              </tr>
            <tr>
              <td colspan="6"><span class="style35">Contato:
                  <input name="contato" type="text" id="contato" size="60" onFocus="document.all.contato.style.background='#CCFFCC'" onBlur="document.all.contato.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
&nbsp;&nbsp;&nbsp;Cel:
<input name='cel' type='text' id='cel' size='12' onKeyPress="return(TelefoneFormat(this,event))" onKeyUp="pula(13,this.id,email.id)" onFocus="document.all.cel.style.background='#CCFFCC'" onBlur="document.all.cel.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;">
              </span></td>
            </tr>
            <tr>
              <td colspan="6"><span class="style35 style40">E-mail:
                  <input name="email" type="text" id="email" size="40" onFocus="document.all.email.style.background='#CCFFCC'" onBlur="document.all.email.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:lowercase;" >
&nbsp;&nbsp;&nbsp;Site:
<input name="site" type="text" id="site" size="40" onFocus="document.all.site.style.background='#CCFFCC'" onBlur="document.all.site.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:lowercase;">
              </span></td>
            </tr>
            <tr>
              <td colspan="6" bgcolor="#CCCCCC"><div align="center"><span class="style35">DADOS DA CATEGORIA</span></div></td>
            </tr>
            
            <tr>
              <td colspan="6"><span class="style35">M&ecirc;s de Desconto:
                  <select name="mes_desconto" id="mes_desconto">
                   <?php
				   
				   for ($i2 = 01; $i2 <= 12; $i2 ++) {
					   print "<option value=$i2>$meses[$i2]</option>";
				   }
				   
				   ?>
                   
                  </select>
&nbsp;&nbsp;&nbsp;&nbsp;M&ecirc;s de Diss&iacute;dio:&nbsp;
<select name="mes_dissidio" id="mes_dissidio">
  <?php
  
  for ($i2 = 01; $i2 <= 12; $i2 ++) {
	  print "<option value=$i2>$meses[$i2]</option>";
  }
				   
  ?>
</select>
&nbsp;&nbsp;&nbsp;</span></td>
            </tr>
            <tr>
              <td height="26" colspan="6"><p class="style35">Piso Salarial:
                  <input name="piso" type="text" id="piso" size="20" onFocus="document.all.piso.style.background='#CCFFCC'" onBlur="document.all.piso.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
              &nbsp;&nbsp;&nbsp;&nbsp;Multa do FGTS: 
                  <input name="multa" type="text" id="multa" size="10" onFocus="document.all.multa.style.background='#CCFFCC'" onBlur="document.all.multa.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
                % </p>
                </td>
            </tr>
            <tr>
              <td colspan="6" bgcolor="#FFFFFF"><span class="style35">F&eacute;rias (meses):
                  <input name="ferias" type="text" id="ferias" size="2" onFocus="document.all.ferias.style.background='#CCFFCC'" onBlur="document.all.ferias.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
&nbsp;&nbsp;&nbsp;Fra&ccedil;&atilde;o
<input name="fracao" type="text" id="fracao" size="5" onFocus="document.all.fracao.style.background='#CCFFCC'" onBlur="document.all.fracao.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
&nbsp;&nbsp;&nbsp;&nbsp;13  (meses):
<input name="decimo_terceiro" type="text" id="decimo_terceiro" size="2" onFocus="document.all.decimo_terceiro.style.background='#CCFFCC'" onBlur="document.all.decimo_terceiro.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
&nbsp;&nbsp;&nbsp;&nbsp;Rescis&atilde;o  (meses):
<input name="recisao" type="text" id="recisao" size="2" onFocus="document.all.recisao.style.background='#CCFFCC'" onBlur="document.all.recisao.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
&nbsp;&nbsp;&nbsp;&nbsp;Patronal:
<select name="pratonal" id="pratonal">
  <option value="1">SIM</option>
  <option value="2">N&Atilde;O</option>
</select>
              </span></td>
            </tr>
            <tr>
              <td colspan="6" bgcolor="#FFFFFF"><span class="style35">Evento Relacionado:
                <select name="evento" id="evento">
                  <option value="5019">CONTRIBUI&Ccedil;&Atilde;O SINDICAL</option>
                </select>
&nbsp;&nbsp;&nbsp;Entidade Sindical:
              <input name="entidade" type="text" id="entidade" size="20" 
              onFocus="document.all.entidade.style.background='#CCFFCC'" 
              onBlur="document.all.entidade.style.background='#FFFFFF'" 
              style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
</span></td>
            </tr>
          </table>
            <div align="center">
            <br>
            <input type="hidden" name="id" value="2">
            <input type="hidden" name="regiao" value="<?=$regiao?>">
            <br>
            <input type="submit" name="button" id="button" value="Cadastrar">
            </div>
            
            </form>
         
          <script language="javascript">
function validaForm(){
           d = document.form1;

           if (d.nome.value == ""){
                     alert("O campo Nome deve ser preenchido!");
                     d.nome.focus();
                     return false;
          }

           if (d.endereco.value == ""){
                     alert("O campo Endereco deve ser preenchido!");
                     d.endereco.focus();
                     return false;
          }
		  
           if (d.cnpj.value == ""){
                     alert("O campo CNPJ deve ser preenchido!");
                     d.cnpj.focus();
                     return false;
          }

           if (d.contato.value == ""){
                     alert("O campo Contato deve ser preenchido!");
                     d.contato.focus();
                     return false;
          }


		return true;   }
</script>
          </td>
        </tr>
        
        <tr>
          <td width="155" bgcolor="#FFFFFF">&nbsp;          </td>
          <td width="549" bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        
        <tr valign="top"> 
          <td height="37" colspan="4"> <img src="../layout/baixo.gif" width="750" height="38"> 
<?php
include "../empresa.php";
$rod = new empresa();
$rod -> rodape();
?></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
<?php

break;

case 2:  //CADASTRANDO OS DADOS

$regiao = $_REQUEST['regiao'];
$id_user_cad = $_COOKIE['logado'];
$data_cad = date('Y-m-d');

$nome = $_REQUEST['nome'];
$endereco = $_REQUEST['endereco'];
$cnpj = $_REQUEST['cnpj'];
$tel = $_REQUEST['tel'];
$fax = $_REQUEST['fax'];
$contato = $_REQUEST['contato'];
$cel = $_REQUEST['cel'];
$email = $_REQUEST['email'];
$site = $_REQUEST['site'];
$mes_desconto = $_REQUEST['mes_desconto'];
$mes_dissidio = $_REQUEST['mes_dissidio'];
$piso = $_REQUEST['piso'];
$multa = $_REQUEST['multa'];
$ferias = $_REQUEST['ferias'];
$fracao = $_REQUEST['fracao'];
$decimo_terceiro = $_REQUEST['decimo_terceiro'];
$recisao = $_REQUEST['recisao'];
$pratonal = $_REQUEST['pratonal'];
$evento = $_REQUEST['evento'];
$entidade = $_REQUEST['entidade'];

mysql_query("INSERT INTO rhsindicato(id_regiao ,id_user_cad ,data_cad ,nome ,endereco ,cnpj ,tel ,fax ,contato ,cel ,email ,site ,mes_desconto ,mes_dissidio ,piso ,multa ,ferias ,fracao ,decimo_terceiro ,recisao ,pratonal ,evento ,entidade ) VALUES ('$regiao', '$id_user_cad', '$data_cad', '$nome', '$endereco', '$cnpj', '$tel', '$fax', '$contato', '$cel', '$email', '$site', '$mes_desconto', '$mes_dissidio', '$piso', '$multa', '$ferias', '$fracao', '$decimo_terceiro', '$recisao', '$pratonal', '$evento', '$entidade')") or die ("ERRO<BR>".mysql_error());


print "
<script>
alert (\"Sindicato cadastrado!\"); 
location.href=\"../rh/rh_sindicatos.php?id=1&regiao=$regiao\"
</script>";


break;
case 3:  //MOSTRANDO OS DADOS

$regiao = $_REQUEST['regiao'];
$sindicato = $_REQUEST['sindicato'];

$result = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '$sindicato'");
$row = mysql_fetch_array($result);

print "<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<link href='../net2.css' rel='stylesheet' type='text/css'>
<style type='text/css'>
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style34 {
	font-size: 12px;
	font-weight: bold;
	color: #FFFFFF;
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
.style41 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	color: #FFFFFF;
	font-weight: bold;
}
-->
</style>
</head>
<body bgcolor='#FFFFFF'>";

print "
<table width='750' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' align='center'>
  <tr>
    <td colspan='4'><img src='../layout/topo.gif' width='750' height='38' /></td>
  </tr>
  
  
  <tr>
    <td width='21' rowspan='2' background='../layout/esquerdo.gif'>&nbsp;</td>
    <td colspan='2'><br />
      <table  height='450' width='95%' border='1' align='center' cellspacing='0' bordercolor='#CCFF99'>
            <tr>
              <td height='45' colspan='6' bgcolor='#CCFF99'><div align='right' class='style35'>
                  <div align='center' class='style27 style36'><img src='imagensrh/sindicatos.gif' alt='empresa' width='100' height='40' /></div>
              </div></td>
            </tr>
            <tr>
              <td width='20%'><div align='right' class='style40 style35'><strong>Nome:</strong></div></td>
              <td width='80%' colspan='5'>&nbsp;&nbsp;<span class='style40'>$row[nome]              </span></td>
        </tr>
            <tr>
              <td><div align='right' class='style35'>Endere&ccedil;o:</div></td>
              <td colspan='5'>&nbsp;&nbsp;<span class='style40'>$row[endereco]</span></td>
        </tr>
            <tr>
              <td colspan='6'><span class='style35'>CNPJ:&nbsp;&nbsp;</span><span class='style40'>$row[cnpj]</span><span class='style35'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tel.:&nbsp;&nbsp;</span><span class='style40'>$row[tel] </span><span class='style35'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fax:&nbsp;&nbsp;</span><span class='style40'>$row[fax]</span> </td>
        </tr>
            <tr>
              <td colspan='6'><span class='style35'>Contato: </span><span class='style40'>&nbsp;$row[contato] </span><span class='style35'>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cel: <span class='style40'>&nbsp;</span></span><span class='style40'>$row[cel] </span> </td>
        </tr>
            <tr>
              <td colspan='6'><span class='style35 style40'>E-mail:&nbsp;&nbsp;</span><span class='style40'><span class='style40'>$row[email] </span></span><span class='style35 style40'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Site:
              </span><span class='style40'><span class='style40'>$row[site] </span></span> </td>
        </tr>
            <tr>
              <td colspan='6' bgcolor='#CCFF99'><div align='center'><span class='style35'>DADOS DA CATEGORIA</span></div></td>
            </tr>
            <tr>
              <td colspan='6'><span class='style35'>M&ecirc;s de Desconto:&nbsp;              </span><span class='style40'><span class='style40'>$row[mes_desconto] </span></span><span class='style35'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;M&ecirc;s de Diss&iacute;dio:&nbsp;&nbsp;</span><span class='style40'><span class='style40'>$row[mes_dissidio] </span></span><span class='style35'>&nbsp;&nbsp;&nbsp;</span></td>
        </tr>
            <tr>
              <td height='26' colspan='6'><p><span class='style35'>Piso Salarial:&nbsp;</span><span class='style40'>&nbsp;R$ $row[piso] </span>                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style35'>Multa do FGTS:</span>
                  <span class='style40'>$row[multa] </span>              % </p></td>
            </tr>
            <tr>
              <td colspan='6' bgcolor='#FFFFFF'><span class='style35'>F&eacute;rias (meses)</span>:
                &nbsp;<span class='style40'>$row[ferias] </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style35'>Fra&ccedil;&atilde;o</span>: <span class='style40'>$row[fracao] </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style35'>13  (meses):</span>                <span class='style40'>$row[decimo_terceiro] </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style35'>Rescis&atilde;o  (meses):</span><span class='style40'>&nbsp;$row[recisao] </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style35'>Patronal: </span><span class='style40'>$row[pratonal] </span> </td>
        </tr>
            <tr>
              <td colspan='6' bgcolor='#FFFFFF'><span class='style35'>Evento Relacionado:<span class='style40'>&nbsp;&nbsp;</span></span><span class='style40'><span class='style40'>$row[evento]</span></span><span class='style35'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Entidade Sindical:</span><span class='style40'>&nbsp;<span class='style40'>$row[entidade] </span> </span></td>
        </tr>
      </table>
        <br />
		<br />
		<center>
		<a href='javascript:history.go(-1)' class='link'><img src='../imagens/voltar.gif' border=0></a>
		</center>
		<br />
		<br />
		</td>
    <td width='26' rowspan='2' background='../layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td width='155'>&nbsp;</td>
    <td width='549'>&nbsp;</td>
  </tr>
  <tr valign='top'>
    <td height='37' colspan='4' bgcolor='#5C7E59'><img src='../layout/baixo.gif' width='750' height='38' />
        <div align='center' class='style6'><strong>Intranet do Instituto Sorrindo Para a Vida</strong> - Acesso Restrito 
          a Funcion&aacute;rios <br />
      </div></td>
  </tr>
</table>
</body></html>
";


break;
}
}
/* Liberando o resultado */
//mysql_free_result($result);

/* Fechando a conexão */
//mysql_close($conn);
?>
