<?php

if(empty($_COOKIE['logado'])){
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

include "conn.php";
include "wfunction.php";

$usuario = carregaUsuario();

$id = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : 1;
$id_user = $usuario['id_funcionario'];
$regiao = $usuario['id_regiao'];

$data = date('d/m/Y');

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_local = mysql_fetch_array($result_local);

if(empty($_REQUEST['nome'])){

if(empty($_REQUEST['clt'])){
	
	$clt = "0";
	$nome = "";
	$numero = "";
	$serie = "";
	$uf = "";
	
}else{
	$clt = $_REQUEST['clt'];
	$result_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$clt'");
	$row_clt = mysql_fetch_array($result_clt);
	$nome = "$row_clt[nome]";
	$numero = "$row_clt[campo1]";
	$serie = "$row_clt[serie_ctps]";
	$uf = "$row_clt[uf_ctps]";

}


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
.style50 {font-family: Geneva, Arial, Helvetica, sans-serif; font-size: 10; font-weight: bold; color: #FFFFFF; }
.style51 {
	font-family: arial, verdana, "ms sans serif";
	font-weight: bold;
}
.style52 {font-family: arial, verdana, "ms sans serif"}
.style53 {font-family: Arial, Verdana, Helvetica, sans-serif}
.style55 {font-size: 10}
.style56 {font-family: Arial, Verdana, Helvetica, sans-serif; font-weight: bold; }
-->
</style>

<script language="javascript"> 

  //o parâmentro form é o formulario em questão e t é um booleano 
  function ticar(form, t) { 
    campos = form.elements; 
    for (x=0; x<campos.length; x++) 
      if (campos[x].type == "checkbox") campos[x].checked = t; 
  } 

</script> 
<script language="javascript" src="jquery-1.3.2.js"></script>
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
          <td width="21" rowspan="6" background="layout/esquerdo.gif">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td width="26" rowspan="6" background="layout/direito.gif">&nbsp;</td>
        </tr>
        
        <tr>
          <td colspan="2" bgcolor="#FFFFFF"> <span style="float:right;"> <?php include('reportar_erro.php'); ?> </span>
    <span style="clear:right"></span></td>
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
          <td colspan="2" bgcolor="#FFFFFF">
          
          <br>
            <table  height="114" width="95%" align="center" cellspacing="0" class="bordaescura1px">
            <tr>
              <td height="45" bgcolor="#666666"><div align="right" class="style35">
                <div align="center" class="style27 style36">DADOS DA CARTEIRA RECEBIDA<br>
                  <strong>
                  <?=$row_local['regiao']?>
                  </strong>- Data de Recebimento <strong>
                  <?=$data?>
                  </strong></div>
              </div></td>
              </tr>
            
            <tr>
              <td><span class="style40">
                <label>                </label>
              </span>                
                <label>                </label>
                <span class="style40"><strong>
                <label></label>
                </strong></span>
<form action="ctps.php" method="post" name='form1' id="form1">
                  <br>
                  <table width="100%" border="0" cellpadding="0" cellspacing="1">
                    <tr>
                      <td class="style19"><div align="right"><span class="style40"><strong>Nome</strong>:</span> </div></td>
                      <td colspan="3"><strong>
                        &nbsp;&nbsp;
                        <input name="nome" type="text" id="nome" size="80" value='<?=$nome?>'>
                      </strong></td>
                      </tr>
                    <tr>
                      <td width="16%" class="style19"><div align="right" class="style40"><span class="style40"><strong>Numero</strong>:</span></div></td>
                      <td width="44%"><span class="style40"><strong> &nbsp;&nbsp;
                          <input name="numero" type="text" id="numero" size="30" value='<?=$numero?>'>
                      </strong></span></td>
                      <td width="7%"><span class="style40 style40"><strong>S&eacute;rie:</strong></span></td>
                      <td width="33%"><span class="style40"><strong>
                        <input name="serie" type="text" id="serie" size="15" value='<?=$serie?>'>
&nbsp;&nbsp;&nbsp;UF:&nbsp;
<input name="uf" type="text" id="uf" size="2" maxlength="2" value='<?=$uf?>'>
                      </strong></span></td>
                    </tr>
                    <tr>
                      <td class="style19"><div align="right"></div></td>
                      <td colspan="3"><strong>&nbsp;&nbsp;</strong></td>
                      </tr>
                    
                    <tr>
                      <td class="style19"><div align="right"><span class="style40"><strong>Observa&ccedil;&otilde;es:</strong></span></div></td>
                      <td colspan="3">&nbsp;&nbsp;
                        <input name="obs" type="text" id="obs" size="80"></td>
                      </tr>
                    <tr>
                      <td height="28" class="style19"><div align="right"><span class="style40"><strong>Preenchimento:</strong></span></div></td>
                      <td colspan="3">&nbsp;&nbsp;
                        <table width="56%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="53%" valign="middle"><span class="style51">&nbsp;&nbsp;
                                  <label>
                        <input name="preenchimento" type="radio" id="preenchimento" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none' ;" value="1" checked>
                        assinar</label>
                              </span></td>
                            <td width="47%" valign="middle"><span class="style51">&nbsp;&nbsp;
                                  <label>
                              <input type="radio" name="preenchimento" id="preenchimento4" value="4" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none' ;">
                              13&ordm; sal&aacute;rio</label>
                            </span></td>
                            </tr>
                          <tr>
                            <td valign="middle"><span class="style51">&nbsp;&nbsp;
                                  <label>
                              <input type="radio" name="preenchimento" id="preenchimento2" value="2" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none' ;"> 
                              dar baixa</label>
                            </span></td>
                            <td valign="middle"><span class="style51">&nbsp;&nbsp;
                                  <label>
                              <input type="radio" name="preenchimento" id="preenchimento5" value="5" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none' ;">
                              licen&ccedil;a</label>
                            </span></td>
                            </tr>
                          <tr>
                            <td valign="middle"><span class="style51">&nbsp;&nbsp;
                                  <label>
                              <input type="radio" name="preenchimento" id="preenchimento3" value="3" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none' ;">
                              f&eacute;rias</label>
                            </span></td>
                            <td valign="middle"><span class="style51">&nbsp;&nbsp;
                                  <label>
                              <input type="radio" name="preenchimento" id="preenchimento6" value="6" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? '' : '' ;" > 
                              outros</label>
                            </span></td>
                            </tr>
                          <tr>
                            <td colspan="2">&nbsp;</td>
                            </tr>
                          <tr id="linha" style="display:none">
                            <td colspan="2"><strong>
                              &nbsp;<span class="style52">&nbsp;&nbsp;descreva: </span><br>
                              &nbsp;&nbsp;
                              <input name="obs_preenchimento" type="text" id="obs_preenchimento" size="45">
                            </strong></td>
                          </tr>
                        </table></td>
                    </tr>
                  </table>
<br><div align="center">
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="display:none" id="tablearquivo">
<tr>
<td width="15%" align="right"><span class="style19">SELECIONE:</span></td>
<td width="85%"><span class="style19"> &nbsp;&nbsp;
<input name="arquivo" type="file" id="arquivo" size="60" />
</span></td>
</tr>
</table>
                  
                  <br>
                    <input type="hidden" value="<?=$regiao?>" name="regiao">
                    <input type="submit" name="gerar" id="gerar" value="GERAR PROTOCOLO DE RECEBIMENTO">
                    <br>
                    <br>
                    <br>
                  </div>
                </form></td>
              </tr>
          </table>
            <br> <hr color="#003300"> <br>
            <table  height="120" width="95%" align="center" cellspacing="0"  class="bordaescura1px">
              <tr>
                <td height="29" bgcolor="#666666"><div align="right" class="style35">
                    <div align="center" class="style27 style36">CONTROLE DE CARTEIRAS A SEREM ENTREGUES</div>
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
                      <br>
                      <table width="100%" border="0" cellpadding="0" cellspacing="1">
                        <tr>
                          <td width="14%" bgcolor="#999999" class="style19"><div align="right" class="style50 style53">
                            <div align="center"><span class="style55">RECEBIMENTO</span></div>
                          </div></td>
                          <td width="17%" bgcolor="#999999"><div align="center" class="style53 style27"><strong>NOME</strong></div></td>
                          <td width="15%" bgcolor="#999999"><div align="center" class="style53 style27"><strong>N&Uacute;MERO</strong></div></td>
                          <td width="15%" bgcolor="#999999"><div align="center" class="style53 style27"><strong><span class="style55">SERIE</span></strong></div></td>
                          <td width="10%" bgcolor="#999999"><div align="center" class="style53 style27"><strong><span class="style55">UF</span></strong></div></td>
                          <td width="15%" bgcolor="#999999"><div align="center" class="style56">
                            <div align="center" class="style53 style27"><strong><span class="style55">PREENCHIMENTO</span></strong></div>
                          </div>                            <div align="center" class="style56"></div></td>
                          <td width="14%" bgcolor="#999999"><div align="center"></div></td>
                        </tr>
<?php
$result_carteiras = mysql_query("SELECT *,date_format(data_cad, '%d/%m/%Y')as data_cadas FROM controlectps where id_regiao = '$regiao' and acompanhamento = '1'");
while($row_carteiras = mysql_fetch_array($result_carteiras)){

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
<td bgcolor='#CCCCCC' class='style19 style40 style44'><div align='center'>$row_carteiras[data_cadas]</div></td>
<td bgcolor='#CCCCCC'><div align='center'><a href=ctps_receber.php?case=2&regiao=$regiao&id=$row_carteiras[0]>$row_carteiras[nome]</a></div></td>
<td bgcolor='#CCCCCC'><div align='center'>$row_carteiras[numero]</div></td>
<td bgcolor='#CCCCCC'><div align='center'>$row_carteiras[serie]</div></td>
<td bgcolor='#CCCCCC'><div align='center'>$row_carteiras[uf]</div></td>
<td bgcolor='#CCCCCC'><div align='center'>$novafrase</div></td>
<td bgcolor='#CCCCCC'><div align='center'><a href=ctps_entregar.php?case=1&regiao=$regiao&id=$row_carteiras[0]>entregar</a></div></td>
</tr>";
}

?>
                      </table>                </td>
              </tr>
              <tr>
                <td><div align="center"><a href="ctps_entregues.php?regiao=<?=$regiao?>" target="_blank"><img src="imagens/ctpsentregues.gif" alt="" width="120" height="30" border="0"></a></div></td>
              </tr>
              <tr>
                <td><div align="center"></div></td>
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
				include 'empresa.php';
				$rod = new empresa();
				$rod -> rodape();
			?>            </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<script language="javascript" src="designer_input.js"></script>
</body>
</html>

<?php

}else{ // AKI VAI RODAR O CADASTRO
		  
		  
$regiao = $_REQUEST['regiao'];
$nome = $_REQUEST['nome'];
$numero = $_REQUEST['numero'];
$serie = $_REQUEST['serie'];
$uf = $_REQUEST['uf'];
$obs = $_REQUEST['obs'];
$preenchimento = $_REQUEST['preenchimento'];
$obs_preenchimento = $_REQUEST['obs_preenchimento'];

$id_user = $_COOKIE['logado'];
$data_cad = date('Y-m-d');

mysql_query("INSERT INTO controlectps(id_regiao,id_user_cad,nome,numero,serie,uf,obs,preenchimento,obs_preenchimento,data_cad) values
('$regiao','$id_user','$nome','$numero','$serie','$uf','$obs','$preenchimento','$obs_preenchimento','$data_cad')")or die ("<hr>Erro no insert<br><hr>".mysql_error());

$row_id = mysql_insert_id();

print "
<script>
alert (\"Informações gravadas com sucesso\");
location.href=\"ctps_receber.php?regiao=$regiao&id=$row_id&case=1\"
</script>
";

}
/* Liberando o resultado */
//mysql_free_result($result);

/* Fechando a conexão */
//mysql_close($conn);

?>
