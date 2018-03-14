<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
exit;
}

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

<script language="JavaScript" type="text/JavaScript" src="../js/ramon.js"></script>
<script type="text/JavaScript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript">
$(function(){
	$('#cep').mask('99999-999');
	
	
});

</script>

<style type="text/css">
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
.style361 {	font-size: 14px;
	font-family: Verdana, Geneva, sans-serif;
}
-->
</style>

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
        
          <td width="21" rowspan="2" background="../layout/esquerdo.gif">&nbsp;</td>
          <td height="170" colspan="2" bgcolor="#FFFFFF"><table  height="114" width="95%"  align="center" cellspacing="0"  class="bordaescura1px">
           
            
            
            <tr>
            	
              <td height="45" bgcolor="#666666"><div align="right" class="style35">
                <div align="center" class="style27 style36">GERENCIAMENTO DE COOPERATIVAS E PESOAS JUR&Iacute;DICAS PARA GERENCIAMENTO DE FOLHA</div>
              </div></td>
            </tr>
             <tr>
            	<td align="right"><?php include('../reportar_erro.php');?></td>
            </tr>
            <tr>
              <td align="center" bgcolor="#FFFFFF"><span class="style40">
                <label> </label>
                </span>
                <label> </label>
                <span class="style40"><strong>
                  <label></label>
                  </strong></span>
                <table width="90%" border="0" cellspacing="0" cellpadding="0" bordercolor="#333333">
                  <tr class="campotexto">
                    <td align="center" valign="baseline"><br>
                      <br>
                      VISUALIZAR COOPERATIVAS ou PJ<br>
                      <br><a href="#"><img src="../imagens/verbolsista.gif" width="190" height="31" border="0" onClick="document.all.visualizar.style.display = (document.all.visualizar.style.display == 'none') ? '' : 'none' ;" ></a>
                      <br>
                      <br>
                      <br>
                      <br></td>
                    <td align="center" valign="baseline"><br>
                      <br>
                      CADASTRO COOPERATIVAS e PJ<br>
                      <br> 
                      <a href="#"><img src="../imagens/castrobolsista.gif" width="190" height="31" border="0" onClick="document.all.cadastro.style.display = (document.all.cadastro.style.display == 'none') ? '' : 'none' ;" ></a>                     <br>
                      <br><br></td>
                    </tr>
                </table></td>
            </tr>
          </table></td>
          <td width="26" rowspan="2" background="../layout/direito.gif">&nbsp;</td>
        </tr>
        <tr>
          <td width="155" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="549" bgcolor="#FFFFFF"><div align="center"></div></td>
        </tr>
        <tr valign="top">
          <td height="37" colspan="4" bgcolor="#e2e2e2"><img src="../layout/baixo.gif" width="750" height="38">
            <div align="center" class="style6"></div></td>
        </tr>
      </table>
      <table width="750" id="visualizar" border="0" cellpadding="0" cellspacing="0" style="display:none">
        <tr>
          <td colspan="4"><img src="../layout/topo.gif" width="750" height="38"></td>
        </tr>
        <tr>
          <td width="21" rowspan="3" background="../layout/esquerdo.gif">&nbsp;</td>
          <td width="155" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="549" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="26" rowspan="3" background="../layout/direito.gif">&nbsp;</td>
        </tr>
        <tr>
          <td height="24" colspan="2" bgcolor="#FFFFFF"><br>
            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="1"  bgcolor="#000000" class="bordaescura1px">
              <tr>
                <td colspan="4" bgcolor="#333333"><div align="center" class="style41">COOPERATIVAS E PESSOAS JURÍDICAS CADASTRADAS</div></td>
              </tr>
              <tr>
              <td width="20%" bgcolor="#CCCCCC"><div align="center"><span class="style35">TIPO</span></div> 
                <td width="40%" bgcolor="#CCCCCC"><div align="center"><span class="style35">NOME</span></div>                  
                <div align="center"></div></td>
                <td width="15%" bgcolor="#CCCCCC"><div align="center"><span class="style35">TEL</span></div></td>
                <td width="15%" bgcolor="#CCCCCC"><div align="center"><span class="style35">CONTATO</span></div></td>
                <td width="10%" bgcolor="#CCCCCC"><div align="center" class="style35">TAXA</div></td>
              </tr>
              <?php 
$RECoop = mysql_query("SELECT * FROM cooperativas WHERE id_regiao = '$regiao'");
while($row = mysql_fetch_array($RECoop)){
	$taxa = $row['taxa'] * 100;
print "
<tr>
<td bgcolor='#EEEEEE' align='center'>$row[tipo]</td>
<td bgcolor='#EEEEEE' align='center'><a href='editcooperativa.php?coop=$row[0]'>$row[nome]</a></td>
<td bgcolor='#EEEEEE' align='center'>$row[tel]</td>
<td bgcolor='#EEEEEE' align='center'>$row[contato]</td>
<td bgcolor='#EEEEEE' align='center'>$taxa</td>
</tr>";

}

?>
            </table>
          <br></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"><br></td>
          <td bgcolor="#FFFFFF"><div align="center"></div></td>
        </tr>
        <tr valign="top">
          <td height="37" colspan="4"><img src="../layout/baixo.gif" width="750" height="38">
            <div align="center" class="style6"><br>
            </div></td>
        </tr>
      </table>
      
      <table width="750" id="cadastro" border="0" cellpadding="0" cellspacing="0" style="display:none">
        <tr> 
          <td colspan="4"><img src="../layout/topo.gif" width="750" height="38"></td>
        </tr>
        
        <tr>
          <td width="21" rowspan="3" background="../layout/esquerdo.gif">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td width="26" rowspan="3" background="../layout/direito.gif">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" bgcolor="#FFFFFF"><form action="cooperativa.php" method="post" name="form1" onSubmit="return validaForm()" enctype='multipart/form-data'>
            <table width="95%"  height="705" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#003300"> 
              <tr>
              <td height="32" colspan="4" bgcolor="#333333"><div align="right" class="style35">
                <div align="center" class="style27 style36">CADASTRO DE COOPERATIVA DE TRABALHO E PESSOA JURÍDICA</div>
              </div></td>
              </tr>
              <tr>
                <td height="28" align="left" bgcolor="#EEEEEE"><div align="right" class="style40 style35"><strong>&nbsp;Tipo:&nbsp;</strong></div></td>
                <td height="28" colspan="3" align="left" bgcolor="#EEEEEE">&nbsp;&nbsp;
                  <select name="tipo" id="tipo">
                    <option value="1">COOPERATIVA</option>
                    <option value="2">PESSOA JUR&Iacute;DICA</option>
                  </select></td>
              </tr>
              <tr>
              <td width="17%" height="28" align="left" bgcolor="#EEEEEE"><div align="right" class="style40 style35"><strong>&nbsp;Nome:&nbsp;</strong></div></td>
              <td height="28" colspan="3" align="left" bgcolor="#EEEEEE">
                &nbsp;&nbsp;
  <input name="nome" type="text" id="nome" size="60" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
  style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">              </td>
              </tr>
            <tr>
              <td height="28" align="left" bgcolor="#EEEEEE"><div align="right" class="style35">&nbsp;Nome Fantasia:&nbsp;</div></td>
              <td height="28" colspan="3" align="left" bgcolor="#EEEEEE"><span class="style35">
                &nbsp;&nbsp;
                <input name="fantasia" type="text" id="fantasia" size="60" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                onChange="this.value=this.value.toUpperCase()">
              </span></td>
              </tr>
            <tr>
              <td height="28" align="left" bgcolor="#EEEEEE"><div align="right" class="style35">&nbsp;Endere&ccedil;o:&nbsp;</div></td>
              <td height="28" colspan="3" align="left" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
                <input name="endereco" type="text" id="endereco" size="50" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                onChange="this.value=this.value.toUpperCase()">
              </span></td>
              </tr>
            
            <tr>
              <td height="28" bgcolor="#EEEEEE"><div align="right" class="style35">&nbsp;Bairro:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE"><span class="style35">
                &nbsp;&nbsp;
                <input name="bairro" type="text" id="bairro" size="25" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                onChange="this.value=this.value.toUpperCase()">
              </span></td>
                 <td height="28" bgcolor="#EEEEEE"><div align="right" class="style35">&nbsp;Cidade:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE"><span class="style35">
                &nbsp;&nbsp;
                <input name="cidade" type="text" id="cidade" size="25" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                onChange="this.value=this.value.toUpperCase()">
              </span></td>
              
             
              
              
              </tr>
              <tr>
                <td height="28" bgcolor="#EEEEEE"><div align="right" class="style35">&nbsp;CEP:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE"><span class="style35">
                &nbsp;&nbsp;
                <input name="cep" type="text" id="cep" size="25" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                onChange="this.value=this.value.toUpperCase()">
              </span></td>
              
               <td height="28"  bgcolor="#EEEEEE"><div align="right" class="style35">UF: </div></td>
                <td height="28" bgcolor="#EEEEEE"><span class="style35">
                &nbsp;&nbsp;
               
                 <select name="uf">
                        <option value="">Selecione o Estado</option>
                        <option value="AC">AC</option>
                        <option value="AL">AL</option>
                        <option value="AP">AP</option>
                        <option value="AM">AM</option>
                        <option value="BA">BA</option>
                        <option value="CE">CE</option>
                        <option value="DF">DF</option>
                        <option value="ES">ES</option>
                        <option value="GO">GO</option>
                        <option value="MA">MA</option>
                        <option value="MS">MS</option>
                        <option value="MT">MT</option>
                        <option value="MG">MG</option>
                        <option value="PA">PA</option>
                        <option value="PB">PB</option>
                        <option value="PR">PR</option>
                        <option value="PE">PE</option>
                        <option value="PI">PI</option>
                        <option value="RJ">RJ</option>
                        <option value="RN">RN</option>
                        <option value="RS">RS</option>
                        <option value="RO">RO</option>
                        <option value="RR">RR</option>
                        <option value="SC">SC</option>
                        <option value="SP">SP</option>
                        <option value="SE">SE</option>
                        <option value="TO">TO</option>
                </select>
              </span></td>
              </tr>
            <tr>
              <td height="28" bgcolor="#EEEEEE"><div align="right" class="style35">CNPJ:&nbsp;</div></td>
              <td width="32%" height="28" bgcolor="#EEEEEE"><span class="style35">
                &nbsp;&nbsp;
                <input name="cnpj" type="text" id="cnpj" style="background:#FFFFFF; text-transform:uppercase;"
                  onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  onKeyUp="pula(19,this.id,tel.id)" onKeyPress="formatar('###.###.###/####-##', this)" size="19" maxlength="19">
              </span></td>
              <td width="26%" height="28" bgcolor="#EEEEEE"><span class="style35">&nbsp;Tel.:
                  &nbsp;&nbsp;
                  <input name='tel' type='text' id='tel' size='12' onKeyPress="return(TelefoneFormat(this,event))" 
                  onKeyUp="pula(13,this.id,fax.id)" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  style="background:#FFFFFF; text-transform:uppercase;">
              </span></td>
              <td width="25%" height="28" bgcolor="#EEEEEE"><span class="style35">&nbsp;&nbsp;Fax:
                  &nbsp;&nbsp;
                  <input name='fax' type='text' id='fax' size='12' onKeyPress="return(TelefoneFormat(this,event))" 
                  onKeyUp="pula(13,this.id,contato.id)" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  	style="background:#FFFFFF; text-transform:uppercase;">
              </span></td>
              </tr>
              
              <tr>
                      <td height="28" bgcolor="#EEEEEE"><div align="right" class="style35">&nbsp;CNAE:&nbsp;</div></td>
                      <td height="28" bgcolor="#EEEEEE"><span class="style35">
                        &nbsp;&nbsp;
                        <input name="cnae" type="text" id="cnae" size="25" 
                        onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                        onChange="this.value=this.value.toUpperCase()">
                      </span></td>
                  
                  <td height="28" colspan="2" bgcolor="#EEEEEE"><span class="style35">&nbsp;FPAS:                    
                    &nbsp;
                    <input name='fpas' type='text' id='fpas' size='12'  onKeyUp="pula(13,this.id,email.id)" onFocus="document.all.cel.style.background='#CCFFCC'" onBlur="document.all.cel.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;">
                  </span></td>
              </tr>
              
            <tr>
              <td height="28" bgcolor="#EEEEEE"><div align="right" class="style35">Contato:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE"><span class="style35">
                &nbsp;&nbsp;
                <input name="contato" type="text" id="contato" size="30" onFocus="document.all.contato.style.background='#CCFFCC'" onBlur="document.all.contato.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
              </span></td>
              <td height="28" colspan="2" bgcolor="#EEEEEE"><span class="style35">&nbsp;Cel:
                
                &nbsp;
                <input name='cel' type='text' id='cel' size='12' onKeyPress="return(TelefoneFormat(this,event))" onKeyUp="pula(13,this.id,email.id)" onFocus="document.all.cel.style.background='#CCFFCC'" onBlur="document.all.cel.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;">
              </span></td>
              </tr>
              
            <tr>
              <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">E-mail:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE"><span class="style35 style40">
                &nbsp;&nbsp;
                <input name="email" type="text" id="email" size="30" onFocus="document.all.email.style.background='#CCFFCC'" onBlur="document.all.email.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:lowercase;" >
              </span></td>
              <td height="28" colspan="2" bgcolor="#EEEEEE"><span class="style35 style40">Site:&nbsp;&nbsp;
<input name="site" type="text" id="site" size="35" onFocus="document.all.site.style.background='#CCFFCC'" onBlur="document.all.site.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:lowercase;">
              </span></td>
              </tr>
            <tr>
              <td height="32" colspan="4" bgcolor="#333333"><div align="right" class="style35">
                <div align="center" class="style27 style36">DADOS DOS ADMINISTRADORES</div>
              </div></td>
              </tr>
            <tr>
              <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">Presidente:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35 style40">
                <input name="presidente" type="text" id="presidente" size="25" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
              </span></td>
              <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">Matricula:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
                <input name='matriculap' type='text' id='matriculap' size='12' onKeyUp="pula(13,this.id,contato.id)" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  	style="background:#FFFFFF; text-transform:uppercase;">
              </span></td>
            </tr>
            <tr>
              <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">RG:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
                <input name='rgp' type='text' id='rgp' size='15' onKeyPress="formatar('##.###.###-#', this)" 
                  onKeyUp="pula(12,this.id,cpfp.id)" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  	style="background:#FFFFFF; text-transform:uppercase;">
              </span></td>
              <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">CPF:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
                <input name='cpfp' type='text' id='cpfp' size='15' onKeyPress="formatar('###.###.###-##', this)"  
                  onKeyUp="pula(14,this.id,enderecop.id)" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  	style="background:#FFFFFF; text-transform:uppercase;">
              </span></td>
            </tr>
            <tr>
              <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">Endere&ccedil;o:&nbsp;</div></td>
              <td height="28" colspan="3" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
                <input name="enderecop" type="text" id="enderecop" size="50" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                onChange="this.value=this.value.toUpperCase()">
              </span></td>
              </tr>
            <tr>
              <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">Diretor:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35 style40">
                <input name="diretor" type="text" id="diretor" size="25" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
              </span></td>
              <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">Matricula:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
                <input name='matriculad' type='text' id='matriculad' size='12' onKeyUp="pula(13,this.id,contato.id)" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  	style="background:#FFFFFF; text-transform:uppercase;">
              </span></td>
            </tr>
            <tr>
              <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">RG:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
                <input name='rgd' type='text' id='rgd' size='15' onKeyPress="formatar('##.###.###-#', this)" 
                  onKeyUp="pula(12,this.id,cpfd.id)" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  	style="background:#FFFFFF; text-transform:uppercase;">
              </span></td>
              <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">CPF:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
                <input name='cpfd' type='text' id='cpfd' size='15' onKeyPress="formatar('###.###.###-##', this)"  
                  onKeyUp="pula(14,this.id,enderecod.id)" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  	style="background:#FFFFFF; text-transform:uppercase;">
              </span></td>
            </tr>
            <tr>
              <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">Endere&ccedil;o:&nbsp;</div></td>
              <td height="28" colspan="3" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
                <input name="enderecod" type="text" id="enderecod" size="50" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                onChange="this.value=this.value.toUpperCase()">
                </span></td>
            </tr>
            <tr>
              <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">Entidade Sindical V&iacute;nculada:&nbsp;</div></td>
              <td height="28" colspan="3" bgcolor="#EEEEEE"><span class="style35 style40">
                &nbsp;&nbsp;
                <input name="entidade" type="text" id="entidade" size="60" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
                </span></td>
            </tr>
            <tr>
              <td height="28" bgcolor="#EEEEEE"><div class="style35" align="right">Fundo Reserva:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE"><span class="style35">
                &nbsp;&nbsp;
                <input name='reserva' type='text' id='reserva' size='12' onKeyDown="FormataValor(this,event,17,2)"
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                style="background:#FFFFFF; text-transform:uppercase;">
              </span></td>
              <td height="28" colspan="2" bgcolor="#EEEEEE"><span class="style35">Quantidade de Parcelas:&nbsp;&nbsp;
                <input name='parcelas' type='text' id='parcelas' size='5' 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                style="background:#FFFFFF; text-transform:uppercase;"></span><br>
                <span style="font-size:10px; font-family:Arial; color:#F00; font-weight:bold">(Referentes as cotas de Capital Social)</span></td>
              </tr>
            <tr>
              <td height="28" bgcolor="#EEEEEE"><div class="style35" align="right">Taxa Administrativa:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
                <input name='taxa' type='text' id='taxa' size='9' onkeypress='return SomenteNumero(event)'
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                style="background:#FFFFFF; text-transform:uppercase;">
                <script language='JavaScript'>
				function SomenteNumero(e){
					var tecla=(window.event)?event.keyCode:e.which;
										if (tecla != 8) return false;
					else return true;
					}
				
				</script>
                </span></td>
                <td height="28" bgcolor="#EEEEEE"><div class="style35" align="right">Bonificação:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
               <input name='bonificacao' type='text' id='bonificacao' size='9' onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;" onKeyDown="FormataValor(this,event,20,2)">
                </span></td>
            </tr>
            <tr>
              <td height="28" bgcolor="#EEEEEE"><div class="style35" align="right">Logo:&nbsp;</div></td>
              <td height="28" colspan="3" bgcolor="#EEEEEE">&nbsp;&nbsp;
                <input name="foto" type="file" id="foto" size="35" class="campotexto" 
              onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                style="background:#FFFFFF;"></td>
            </tr>
            <tr>
              <td height="28" colspan="4" bgcolor="#EEEEEE"><span class="style35">Realizador do Curso de Cooperativismo: &nbsp;<span class="style35 style40">
                <input name="realizador" type="text" id="realizador" size="60" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
              </span></span></td>
            </tr>
            <tr>
              <td height="28" colspan="4" bgcolor="#333333"><div align="right" class="style35">
                <div align="center" class="style27 style36">DADOS BANC&Aacute;RIOS</div>
              </div></td>
            </tr>
            
            <tr>
              <td height="28" colspan="4" bgcolor="#EEEEEE"><div class="style35 style40" align="right">
                <div align="center">BANCO:&nbsp;
                
				  <select name='banco' class='campotexto' id='banco'>
<?php
$result_banco = mysql_query("SELECT * FROM bancos where id_regiao = '$regiao'");
while ($row_banco = mysql_fetch_array($result_banco)){
print "<option value='$row_banco[0]'>$row_banco[id_banco] - $row_banco[nome]</option>";
}
?>
  </select>
				
                    
                </div>
              </div>                <label></label></td>
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
	
	if (d.fantasia.value == ""){
		alert("O campo Nome Fantasia deve ser preenchido!");
		d.fantasia.focus();
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

	return true;
}
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
$tipo = $_REQUEST['tipo'];

$id_user = $_COOKIE['logado'];
$data_cad = date('Y-m-d');

$nome = $_REQUEST['nome'];
$fantasia = $_REQUEST['fantasia'];
$endereco = $_REQUEST['endereco'];
$bairro = $_REQUEST['bairro'];
$cidade = $_REQUEST['cidade'];
$cnpj = $_REQUEST['cnpj'];
$tel = $_REQUEST['tel'];
$fax = $_REQUEST['fax'];
$contato = $_REQUEST['contato'];
$cel = $_REQUEST['cel'];
$email = $_REQUEST['email'];
$site = $_REQUEST['site'];
$cep = $_REQUEST['cep'];
$uf = $_REQUEST['uf'];
$cnae = $_REQUEST['cnae'];
$fpas = $_REQUEST['fpas'];






$presidente = $_REQUEST['presidente'];
$matriculap = $_REQUEST['matriculap'];
$rgp = $_REQUEST['rgp'];
$cpfp = $_REQUEST['cpfp'];
$enderecop = $_REQUEST['enderecop'];


$diretor = $_REQUEST['diretor'];
$matriculad = $_REQUEST['matriculad'];
$rgd = $_REQUEST['rgd'];
$cpfd = $_REQUEST['cpfd'];
$enderecod = $_REQUEST['enderecod'];

$entidade = $_REQUEST['entidade'];
$reserva = $_REQUEST['reserva'];
$parcelas = $_REQUEST['parcelas'];
$taxa = $_REQUEST['taxa'];
$bonificacao = str_replace('.','',$_REQUEST['bonificacao']);
$bonificacao = str_replace(',','.',$bonificacao);
$realizador = $_REQUEST['realizador'];
$banco = $_REQUEST['banco'];

$arquivo = isset($_FILES['foto']) ? $_FILES['foto'] : FALSE;
	
//AQUI TEM FOTO
if($arquivo['error'] == 0){
	
//aki a imagem nao corresponde com as extenções especificadas
if($arquivo['type'] != "image/x-png" && $arquivo['type'] != "image/pjpeg" && $arquivo['type'] != "image/gif" && $arquivo['type'] != "image/jpe") {     

	print "<center>
	<hr><font size=2><b>
	Tipo de arquivo não permitido, os únicos padrões permitidos são .gif, .jpg , .jpeg ou .png<br>
	- $arquivo[type] -<br><br>
	<a href='javascript:history.go(-1)'>Voltar</a>
	</b></font>"; 
exit;
	
//aqui o arquivo é realente de imagem e vai ser carregado para o servidor
} else {  
	
	$arr_basename = explode(".",$arquivo['name']); 
	$file_type = $arr_basename[1]; 
   
	if($arquivo['type'] == "image/gif"){
		$tipo_name =".gif"; 
	}elseif($arquivo['type'] == "image/jpe" or $arquivo['type'] == "image/pjpeg"){
		$tipo_name =".jpg"; 
    }elseif($arquivo['type'] == "image/x-png") { 
		$tipo_name =".png"; 
	} 
	
	
}

//FAZENDO O INSERT DO CADASTRO QUE TENHA FOTO
mysql_query("INSERT INTO cooperativas(id_regiao,tipo,nome,fantasia,endereco,bairro,cidade,cnpj,tel,fax,contato,cel,email,site,diretor,matriculad,rgd,cpfd,
enderecod,presidente,matriculap,rgp,cpfp,enderecop,entidade,fundo,parcelas,cursos,taxa,bonificacao,foto,id_banco,cooperativa_cep, cooperativa_uf, cooperativa_fpas, cooperativa_cnae) VALUES 
('$regiao', '$tipo', '$nome', '$fantasia', '$endereco', '$bairro', '$cidade', '$cnpj', '$tel', '$fax', '$contato', '$cel', '$email', '$site', '$diretor',
 '$matriculad', '$rgd', '$cpfd', '$enderecod', '$presidente', '$matriculap', '$rgp', '$cpfp', '$enderecop', '$entidade', '$reserva', '$parcelas',
 '$realizador', '$taxa', '$bonificacao', '$tipo_name' , '$banco','$cep', '$uf', '$fpas','$cnae')") or die ("ERRO<BR>".mysql_error());

$id_cooperativa = mysql_insert_id();

// Resolvendo o nome e para onde o arquivo será movido
$diretorio = "logos/";

$nome_tmp = "coop_".$id_cooperativa.$tipo_name;
$nome_arquivo = "$diretorio$nome_tmp" ;
	
move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");
	

}else{
	// AKI TERMINA A FERIFICAÇÃO SE O ARQUIVO FOI SELECIONADO ANTERIRMENTE
	
	//FAZENDO O INSERT DO CADASTRO SEM FOTO
mysql_query("INSERT INTO cooperativas 		
(id_regiao, nome, fantasia, endereco, bairro, cidade, cnpj, tel, fax, contato, cel, email, site, diretor, matriculad, rgd, cpfd, enderecod, presidente, matriculap, rgp, cpfp, enderecop, entidade, fundo, parcelas, cursos, taxa, bonificacao, foto, id_banco, cooperativa_cep, cooperativa_uf, cooperativa_fpas, cooperativa_cnae) 
			VALUES 
('$regiao', '$nome', '$fantasia', '$endereco', '$bairro', '$cidade', '$cnpj', '$tel', '$fax', '$contato', '$cel', '$email', '$site', '$diretor', '$matriculad', '$rgd', '$cpfd', '$enderecod', '$presidente', '$matriculap', '$rgp', '$cpfp', '$enderecop', '$entidade', '$reserva', '$parcelas', '$realizador', '$taxa', '$bonificacao', '0', '$banco', '$cep', '$uf', '$fpas','$cnae')") 
			or die ("ERRO<BR>".mysql_error());


}


print "
<script>
alert (\"Cooperativa cadastrada!\"); 
location.href=\"cooperativa.php?id=1&regiao=$regiao\"
</script>";

break;
}

/* Liberando o resultado */
//mysql_free_result($result);

/* Fechando a conexão */
//mysql_close($conn);
?>
