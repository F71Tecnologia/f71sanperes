<?php

if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
}else{

include "../conn.php";

$id_user = $_COOKIE['logado'];

$mes = date('m');

?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net2.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function popup(caminho,nome,largura,altura,rolagem) {
	var esquerda = (screen.width - largura) / 2;
	var cima = (screen.height - altura) / 2 -50;
	window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
}


function popup2(caminho,nome,largura,altura,rolagem) {
	var esquerda = (screen.width - largura) / 2;
	var cima = (screen.height - altura) / 2 -50;
	window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
}

function popup3(caminho,nome,largura,altura,rolagem) {
	var esquerda = (screen.width - largura) / 2;
	var cima = (screen.height - altura) / 2 -50;
	window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
}

function popup4(caminho,nome,largura,altura) {
	var esquerda = (screen.width - largura) / 2;
	var cima = (screen.height - altura) / 2 -60;
	window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
}

//-->
</script>

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
.style41 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	color: #FFFFFF;
	font-weight: bold;
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

</script> 
</head>

<body bgcolor="#FFFFFF">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"> 
      <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
        <tr> 
        </tr>
        
        <tr>
          <td width="21" rowspan="6" background="../layout/esquerdo.gif">&nbsp;</td>
  
          <td width="26" rowspan="6" background="../layout/direito.gif">&nbsp;</td>
        </tr>
        <tr>
        </tr>
        <tr>
          <td colspan="2" background="../imagens/fundo_cima.gif"><div align="center"><span class="style38"><br>
            CADASTRO DE IMPOSTOS, FAIXAS E TAXAS</span><br>
            <br>
          </div></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><div align="center"></div></td>
        </tr>
        <tr>
          <td colspan="2"><br>
            <table  height="114" width="95%" border="1" align="center" cellspacing="0" bordercolor="#CCFF99">
            <tr>
              <td height="45" colspan="2" bgcolor="#CCFF99"><div align="right" class="style35">
                <div align="center" class="style27 style36"><img src="imagensrh/taxas.gif" alt="empresa" width="120" height="40"></div>
              </div></td>
              </tr>
            <tr>
              <td width="13%" bgcolor="#336600"><span class="style40">
                <label>                </label>
                </span>                <label><div align="right" class="style27 style40"><strong>EDI&Ccedil;&Atilde;O</strong></div>
                </label></td>
              <td width="87%">
                <span class="style40"><strong>
                  <label></label>
                  </strong> </span>
                <table width="100%" border="1" cellspacing="0" cellpadding="0">
                  <tr>
                    <td bgcolor="#003333" class="style40"><div align="center" class="style27"><strong>FAIXA</strong></div></td>
                    <td bgcolor="#003333" class="style40"><div align="center" class="style27"><strong>Valor Inicial</strong></div></td>
                    <td bgcolor="#003333" class="style40"><div align="center" class="style27"><strong>Valor Final</strong></div></td>
                    <td bgcolor="#003333" class="style40"><div align="center" class="style27"><strong>Percentual</strong></div></td>
                    <td bgcolor="#003333" class="style40"><div align="center" class="style27"><strong>Valor Constante</strong></div></td>
                    <td bgcolor="#003333" class="style40"><div align="center" class="style27"><strong>Descri&ccedil;&atilde;o</strong></div></td>
                    <td bgcolor="#003333" class="style40"><div align="center" class="style27"><strong>Categoria</strong></div></td>
                    <td bgcolor="#003333" class="style40"><div align="center" class="style27"><strong>Insid&ecirc;ncia</strong></div></td>
                    <td bgcolor="#003333" class="style40">&nbsp;</td>
                    </tr>
                  <tr>
                    <td class="style40">
                      <div align="center">
                        <input name="faixa" type="text" id="faixa" size="4">                      
                      </div></td>
                    <td class="style40"><div align="center">
                      <input name="faixa2" type="text" id="faixa2" size="15">
                      </div></td>
                    <td class="style40"><div align="center">
                      <input name="faixa3" type="text" id="faixa3" size="15">
                      </div></td>
                    <td class="style40"><div align="center">
                      <input name="faixa4" type="text" id="faixa4" size="10">
                      </div></td>
                    <td class="style40"><div align="center">
                      <input name="faixa5" type="text" id="faixa5" size="15">
                      </div></td>
                    <td class="style40"><div align="center">
                      <input name="faixa7" type="text" id="faixa5" size="15">
                    </div></td>
                    <td class="style40"><div align="center">
                      <input name="faixa8" type="text" id="faixa5" size="15">
                    </div></td>
                    <td class="style40"><div align="center">
                      <input name="faixa9" type="text" id="faixa5" size="15">
                    </div></td>
                    <td class="style40"><label>
                      <div align="center">
                        <input type="submit" name="gravar" id="gravar" value="ALTERAR">
                        </div>
                      </label></td>
                    </tr>
                </table></td>
            </tr>
            
            <!--  __________________________________________________ -->
            
            
            <!--  __________________________________________________ -->
            <tr>
              <td colspan="2"><div align="center">
                <label>
                <input type="submit" name="criar" id="criar" value="GERAR">
                </label>
              </div></td>
              </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
            </tr>
          </table>
          <br>          </td>
        </tr>
        
        <tr>
          <td width="155">&nbsp;</td>
          <td width="549">&nbsp;</td>
        </tr>
        
        <tr valign="top"> 
          <td height="37" colspan="4" bgcolor="#5C7E59"><?php
include "../empresa.php";
$rod = new empresa();
$rod -> rodape();
?></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<?php
}
/* Liberando o resultado */
//mysql_free_result($result);

/* Fechando a conexão */
//mysql_close($conn);
?>
</body>
</html>
