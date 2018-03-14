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

.style41 {

	font-family: Geneva, Arial, Helvetica, sans-serif;

	color: #FFFFFF;

	font-weight: bold;

}

.style43 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; }

.style44 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: #003300; }

.style45 {

	font-family: arial, verdana, "ms sans serif";

	font-weight: bold;

	font-size: 14px;

}

.style46 {font-family: Verdana, Times, serif}

.style47 {font-size: 10px}

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

<link href="../net1.css" rel="stylesheet" type="text/css">
</head>



<body>

<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">

  <tr>

    <td align="center" valign="top"> 

      <table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">

        <tr> 

          <td colspan="4"><img src="../layout/topo.gif" width="750" height="38"></td>

        </tr>

        

        <tr>

          <td width="21" rowspan="6" background="../layout/esquerdo.gif">&nbsp;</td>

          <td>&nbsp;</td>

          <td>&nbsp;</td>

          <td width="26" rowspan="6" background="../layout/direito.gif">&nbsp;</td>

        </tr>

        <tr>

          <td>&nbsp;</td>

          <td>&nbsp;</td>

        </tr>

        <tr>

          <td colspan="2" background="../imagens/fundo_cima.gif"><div align="center"><span class="style38"><br>

            CADASTRO DE EVENTOS</span><br>

            <br>

          </div></td>

        </tr>

        <tr>

          <td>&nbsp;</td>

          <td><div align="center"></div></td>

        </tr>

        <tr>

          <td colspan="2"><br>

            <table  height="285" width="95%" border="0" align="center" cellspacing="0" class="bordaescura1px">

            <tr>

              <td height="45" colspan="2" bgcolor="#CCCCCC"><div align="right" class="style35">

                <div align="center" class="style27 style36"><img src="imagensrh/eventos.gif" alt="empresa" width="150" height="40"></div>

              </div></td>
              </tr>

            <tr>

              <td width="13%" height="20" bgcolor="#999999"><div align="center"><span class="style41">C&Oacute;DIGO</span></div></td>

              <td width="87%" height="20" bgcolor="#FFFFFF"><label>

                <input name="nome" type="text" id="nome" size="20" onFocus="document.all.nome.style.background='#CCFFCC'" onBlur="document.all.nome.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">

              </label></td>
            </tr>

            <tr>

              <td height="20" bgcolor="#999999"><div align="center"><span class="style41">IDESCRI&Ccedil;&Atilde;O</span></div></td>

              <td height="20" bgcolor="#FFFFFF"><input name="nome" type="text" id="nome" size="90" onFocus="document.all.nome.style.background='#CCFFCC'" onBlur="document.all.nome.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()"></td>
            </tr>

            <tr>

              <td height="43" bgcolor="#999999"><div align="center"><span class="style41">CATEGORIA</span></div></td>

              <td height="43" bgcolor="#FFFFFF"><select name="nome2"  id="nome2" style="background:#FFFFFF;" onFocus="document.all.nome.style.background='#CCFFCC'" onBlur="document.all.nome.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()">

                <option value="2">ESTAGI&Aacute;RIO</option>

                <option value="1">CLT</option>

                                          </select> </td>
            </tr>

            <tr>

              <td height="43" bgcolor="#999999"><div align="center"><span class="style41">TIPO</span></div></td>

              <td height="43" bgcolor="#FFFFFF"><select name="nome3"  id="nome3" style="background:#FFFFFF;" onFocus="document.all.nome.style.background='#CCFFCC'" onBlur="document.all.nome.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()">

                <option value="D">DESCONTO</option>

                <option value="R">RENDIMENTO</option>

                            </select> <span class="style25"><span class="style35">VALOR FIXO

                            <input name="nome5" type="text" id="nome5" size="20" onFocus="document.all.nome.style.background='#CCFFCC'" onBlur="document.all.nome.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">

PERCENTUAL</span> <span class="style35">

<input name="nome6" type="text" id="nome6" size="20" onFocus="document.all.nome.style.background='#CCFFCC'" onBlur="document.all.nome.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">

</span></span></td>
            </tr>

            

            <tr>

              <td height="20" bgcolor="#999999"><div align="center"><span class="style41">FAIXAS</span></div></td>

              <td height="20" bgcolor="#FFFFFF"><span class="style44">QUANTIDADE:&nbsp;

                <select name="nome8"  id="nome8" style="background:#FFFFFF;" onFocus="document.all.nome.style.background='#CCFFCC'" onBlur="document.all.nome.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()">

                  <option value="1">1</option>

                  <option value="2">2</option>

                  <option value="3">3</option>

                  <option value="4">4</option>

                  <option value="5">5</option>

                  <option value="6">6</option>

                  <option value="7">7</option>

                  <option value="8">8</option>

                  <option value="9">9</option>

                  <option value="10">10</option>
                                </select>

                &nbsp;&nbsp;INCID&Ecirc;NCIA SOBRE 

                <select name="nome9"  id="nome9" style="background:#FFFFFF;" onFocus="document.all.nome.style.background='#CCFFCC'" onBlur="document.all.nome.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()">

                  <option value="1">&lt;LISTAR TAXAS&gt;</option>
                                                </select> 

                TIPO 

                <select name="nome10"  id="nome10" style="background:#FFFFFF;" onFocus="document.all.nome.style.background='#CCFFCC'" onBlur="document.all.nome.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()">

                  <option value="D">DESCONTO</option>

                  <option value="R">RENDIMENTO</option>

                  <option value="P">PERCENTUAL</option>
                                </select>

                <br>

              <br>

              VALOR INICIAL:&nbsp;<span class="style35">

                <input name="nome4" type="text" id="nome4" size="20" onFocus="document.all.nome.style.background='#CCFFCC'" onBlur="document.all.nome.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">

              </span>&nbsp;&nbsp;VALOR FINAL: <span class="style35">

              <input name="nome7" type="text" id="nome7" size="20" onFocus="document.all.nome.style.background='#CCFFCC'" onBlur="document.all.nome.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">

              <br>

              <br>

              </span></span></td>
            </tr>

            <tr>

              <td height="20" bgcolor="#999999"><div align="center"><span class="style41">INCID&Ecirc;NCIAS</span></div></td>

              <td height="20" bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="3">

                <tr>

                  <td class="style43"><label>

                    <div align="right">

                      <input type="checkbox" name="checkbox" id="checkbox">
                      </div>

                  </label></td>

                  <td class="style44">INFORME DE RENDIMENTOS</td>

                  <td class="style44"><div align="right">

                    <input type="checkbox" name="checkbox5" id="checkbox5">

                  </div></td>

                  <td class="style44">RAIS</td>
                </tr>

                <tr>

                  <td class="style43"><div align="right">

                    <input type="checkbox" name="checkbox2" id="checkbox2">

                  </div></td>

                  <td class="style44">INSS</td>

                  <td class="style44"><div align="right">

                    <input type="checkbox" name="checkbox6" id="checkbox6">

                  </div></td>

                  <td class="style44">IRRF</td>
                </tr>

                <tr>

                  <td class="style43"><div align="right">

                    <input type="checkbox" name="checkbox3" id="checkbox3">

                  </div></td>

                  <td class="style44">PIS</td>

                  <td class="style44"><div align="right">

                    <input type="checkbox" name="checkbox7" id="checkbox7">

                  </div></td>

                  <td class="style44">FGTS</td>
                </tr>

                <tr>

                  <td class="style43"><div align="right">

                    <input type="checkbox" name="checkbox4" id="checkbox4">

                  </div></td>

                  <td class="style44">ENCARGOS</td>

                  <td class="style44"><div align="right">

                    <input type="checkbox" name="checkbox8" id="checkbox8">

                  </div></td>

                  <td class="style44">VALOR HORA</td>
                </tr>

                <tr>

                  <td class="style43"><div align="right">

                    <input type="checkbox" name="checkbox9" id="checkbox9">

                  </div></td>

                  <td class="style44">F&Eacute;RIAS</td>

                  <td class="style44"><div align="right">

                    <input type="checkbox" name="checkbox11" id="checkbox11">

                  </div></td>

                  <td class="style44">RECIS&Atilde;O</td>
                </tr>

                <tr>

                  <td class="style43"><div align="right">

                    <input type="checkbox" name="checkbox10" id="checkbox10">

                  </div></td>

                  <td class="style44">AVISO PR&Eacute;VIO</td>

                  <td class="style44"><div align="right">

                    <input type="checkbox" name="checkbox12" id="checkbox12">

                  </div></td>

                  <td class="style44">AFASTAMENTOS</td>
                </tr>

                

                

              </table></td>
            </tr>

            

            

            <tr>

              <td colspan="2"><br>

                <div align="center">

                <label>

                <input type="submit" name="criar" id="criar" value="CRIAR">
                </label>

                <br>

                <br>
                </div></td>
              </tr>

            <tr>

              <td colspan="2" bgcolor="#666666"><div align="center" class="style45"><span class="style27"><br>

                RELA&Ccedil;&Atilde;O DE EVENTOS CADASTRADOS</span><br>

                <br>

              </div></td>
            </tr>

            <tr>

              <td colspan="2"><table width="100%" border="1" cellpadding="2" cellspacing="0" bordercolor="#333333">

                <tr>

                  <td bgcolor="#999999"><span class="style27 style46 style47"><strong>COD</strong></span></td>

                  <td bgcolor="#999999"><span class="style27 style46 style47"><strong>NOME</strong></span></td>

                  <td bgcolor="#999999"><span class="style27 style46 style47"><strong>TIPO</strong></span></td>

                  <td bgcolor="#999999"><span class="style27 style46 style47"><strong>UNIDADE</strong></span></td>

                  <td bgcolor="#999999"><span class="style27 style46 style47"><strong>VALOR</strong></span></td>

                  <td bgcolor="#999999"><span class="style27 style46 style47"><strong>PERCENTUAL</strong></span></td>

                  <td bgcolor="#999999"><span class="style27 style46 style47"><strong>INCID&Ecirc;NCIA </strong></span></td>

                  <td bgcolor="#999999"><span class="style27 style46 style47"><strong>FAIXA INICIAL</strong></span></td>

                  <td bgcolor="#999999"><span class="style27 style46 style47"><strong>FAIXA FINAL</strong></span></td>

                  <td bgcolor="#999999"><span class="style27 style46 style47"><strong>CATEGORIA</strong></span></td>
                </tr>

                <tr>

                  <td>&nbsp;</td>

                  <td>&nbsp;</td>

                  <td>&nbsp;</td>

                  <td>&nbsp;</td>

                  <td>&nbsp;</td>

                  <td>&nbsp;</td>

                  <td>&nbsp;</td>

                  <td>&nbsp;</td>

                  <td>&nbsp;</td>

                  <td>&nbsp;</td>
                </tr>

                

              </table></td>
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

          <td height="37" colspan="4" bgcolor="#5C7E59"> <img src="../layout/baixo.gif" width="750" height="38"> 

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

<?php

}

/* Liberando o resultado */

//mysql_free_result($result);



/* Fechando a conexão */

//mysql_close($conn);

?>

</body>

</html>

