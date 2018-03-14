<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
exit;
}
include "../conn.php";
include "../classes/cooperativa.php";
include "../classes/regiao.php";

if(empty($_REQUEST['update'])){

$coop = $_REQUEST['coop'];

$cooperativa = new cooperativa();
$cooperativa-> MostraCoop($coop);

$id_coop	 	= $cooperativa -> id_coop;
$id_regiao 		= $cooperativa -> id_regiao;
$tipo			= $cooperativa -> tipo;
$nome	 		= $cooperativa -> nome;
$fantasia		= $cooperativa -> fantasia;
$endereco		= $cooperativa -> endereco;	
$bairro			= $cooperativa -> bairro;	
$cidade			= $cooperativa -> cidade;	
$cnpj			= $cooperativa -> cnpj;
$tel			= $cooperativa -> tel;	
$fax			= $cooperativa -> fax;	
$contato		= $cooperativa -> contato;	
$cel			= $cooperativa -> cel;	
$email			= $cooperativa -> email;	
$site			= $cooperativa -> site;	
$diretor		= $cooperativa -> diretor;	
$matriculad		= $cooperativa -> matriculad;
$rgd			= $cooperativa -> rgd;	
$cpfd			= $cooperativa -> cpfd;	
$enderecod		= $cooperativa -> enderecod;
$presidente		= $cooperativa -> presidente;
$matriculap		= $cooperativa -> matriculap;
$rgp			= $cooperativa -> rgp;	
$cpfp			= $cooperativa -> cpfp;	
$enderecop		= $cooperativa -> enderecop;
$entidade		= $cooperativa -> entidade;	
$fundo			= $cooperativa -> fundo;	
$parcelas		= $cooperativa -> parcelas;	
$cursos			= $cooperativa -> cursos;	
$taxa			= $cooperativa -> taxa;	
$foto			= $cooperativa -> foto;	
$iss			= $cooperativa -> iss;	
$status_reg		= $cooperativa -> status_reg;
$id_banco		= $cooperativa -> id_banco;
$cooperativa_cep = $cooperativa -> cooperativa_cep;
$cooperativa_uf				=  $cooperativa -> cooperativa_uf;
$cooperativa_cnae			= $cooperativa-> cooperativa_cnae;
$cooperativa_fpas			= $cooperativa-> cooperativa_fpas;
 

$fundo = str_replace(".",",",$fundo);
$taxa = $taxa * 100;

if($foto == 0){
	$foto = '<input name="foto" type="file" id="foto" size="35" class="campotexto" onFocus="this.style.background=\'#CCFFCC\'" 
	onBlur="this.style.background=\'#FFFFFF\'" style="background:#FFFFFF;">';
}else{
	$foto = "Remover Foto";
}

?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net2.css" rel="stylesheet" type="text/css">

<script language="JavaScript" type="text/JavaScript" src="../js/ramon.js"></script>
<script type="text/JavaScript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript">
$(function(){
	$('#cooperativa_cep').mask('99999-999');
	
	
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

<body bgcolor="#FFFFFF">



<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"> 
<table width="750" id="cadastro" border="0" cellpadding="0" cellspacing="0" style="display:">
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
    <td colspan="2" bgcolor="#FFFFFF"><form action="editcooperativa.php" method="post" name="form1" onSubmit="return validaForm()" enctype='multipart/form-data'>
      <table width="95%"  height="676" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#003300">
         <tr>
          <td height="32" colspan="4" bgcolor="#FFF" align="right" > <?php include('../reportar_erro.php'); ?></td>
        </tr>
        
        <tr>
          <td height="32" colspan="4" bgcolor="#333333"><div align="right" class="style35">
            <div align="center" class="style27 style36">CADASTRO DE COOPERATIVA DE TRABALHO OU PESSOA JUR&Iacute;DICA PARA FOLHA DE PAGAMENTO</div>
          </div></td>
        </tr>
        <tr>
          <td height="28" align="left" bgcolor="#EEEEEE"><div align="right" class="style40 style35"><strong>&nbsp;Tipo:&nbsp;</strong></div></td>
          <td height="28" colspan="3" align="left" bgcolor="#EEEEEE">&nbsp;&nbsp;&nbsp;
            <label>
              <select name="tipo" id="tipo">
                    <option value="1" <?php if($tipo == "1") { echo "selected"; } ?>>COOPERATIVA</option>
                    <option value="2" <?php if($tipo == "2") { echo "selected"; } ?>>PESSOA JUR&Iacute;DICA</option>
                  </select>
            </label></td>
        </tr>
        <tr>
          <td width="17%" height="28" align="left" bgcolor="#EEEEEE"><div align="right" class="style40 style35"><strong>&nbsp;Nome:&nbsp;</strong></div></td>
          <td height="28" colspan="3" align="left" bgcolor="#EEEEEE">&nbsp;&nbsp;
            <input name="nome" type="text" id="nome" size="60" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
  style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()" value="<?=$nome?>"></td>
        </tr>
        <tr>
          <td height="28" align="left" bgcolor="#EEEEEE"><div align="right" class="style35">&nbsp;Nome Fantasia:&nbsp;</div></td>
          <td height="28" colspan="3" align="left" bgcolor="#EEEEEE"><span class="style35"> &nbsp;&nbsp;
            <input name="fantasia" type="text" id="fantasia" size="60" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                onChange="this.value=this.value.toUpperCase()" value="<?=$fantasia?>">
          </span></td>
        </tr>
        <tr>
          <td height="28" align="left" bgcolor="#EEEEEE"><div align="right" class="style35">&nbsp;Endere&ccedil;o:&nbsp;</div></td>
          <td height="28" colspan="3" align="left" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
            <input name="endereco" type="text" id="endereco" size="50" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                onChange="this.value=this.value.toUpperCase()" value="<?=$endereco?>">
          </span></td>
        </tr>
        <tr>
          <td height="28" bgcolor="#EEEEEE"><div align="right" class="style35">&nbsp;Bairro:&nbsp;</div></td>
          <td height="28" bgcolor="#EEEEEE"><span class="style35"> &nbsp;&nbsp;
            <input name="bairro" type="text" id="bairro" size="25"  value="<?=$bairro?>"
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                onChange="this.value=this.value.toUpperCase()" >
          </span></td>
          <td height="28"  colspan="2" bgcolor="#EEEEEE">&nbsp;<span class="style35">Cidade: </span>&nbsp;<span class="style35">
            <input name="cidade" type="text" id="cidade" size="25" value="<?=$cidade?>"
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                onChange="this.value=this.value.toUpperCase()">
          </span></td>
        </tr>
        
         <tr>
                <td height="28" bgcolor="#EEEEEE"><div align="right" class="style35">&nbsp;CEP:&nbsp;</div></td>
              <td height="28" bgcolor="#EEEEEE"><span class="style35">
                &nbsp;&nbsp;
                <input name="cooperativa_cep" type="text" id="cooperativa_cep" size="25" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                 value="<?php echo $cooperativa_cep; ?>">
              </span></td>
              
               <td height="28"  bgcolor="#EEEEEE"><div align="right" class="style35">UF: </div></td>
                <td height="28" bgcolor="#EEEEEE"><span class="style35">
                &nbsp;&nbsp;
               
                <select name="cooperativa_uf">
                
                         <option value="<?php echo $cooperativa_uf;?>" selected="selected"><?php echo $cooperativa_uf;?></option>

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
          <td width="32%" height="28" bgcolor="#EEEEEE"><span class="style35"> &nbsp;&nbsp;
            <input name="cnpj" type="text" id="cnpj" style="background:#FFFFFF; text-transform:uppercase;" value="<?=$cnpj?>"
                  onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  onKeyUp="pula(19,this.id,tel.id)" onKeyPress="formatar('###.###.###/####-##', this)" size="19" maxlength="19">
          </span></td>
          <td width="26%" height="28" bgcolor="#EEEEEE"><span class="style35">&nbsp;Tel.:
            &nbsp;&nbsp;
            <input name='tel' type='text' id='tel' size='12' onKeyPress="return(TelefoneFormat(this,event))" value="<?=$tel?>"
                  onKeyUp="pula(13,this.id,fax.id)" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  style="background:#FFFFFF; text-transform:uppercase;">
          </span></td>
          <td width="25%" height="28" bgcolor="#EEEEEE"><span class="style35">&nbsp;&nbsp;Fax:
            &nbsp;&nbsp;
            <input name='fax' type='text' id='fax' size='12' onKeyPress="return(TelefoneFormat(this,event))" value="<?=$fax?>"
                  onKeyUp="pula(13,this.id,contato.id)" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  	style="background:#FFFFFF; text-transform:uppercase;">
          </span></td>
        </tr>
        
         <tr>
                      <td height="28" bgcolor="#EEEEEE"><div align="right" class="style35">&nbsp;CNAE:&nbsp;</div></td>
                      <td height="28" bgcolor="#EEEEEE"><span class="style35">
                        &nbsp;&nbsp;
                        <input name="cooperativa_cnae" type="text" id="cooperativa_cnae" size="25" 
                        onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                        onChange="this.value=this.value.toUpperCase()" value="<?php echo $cooperativa_cnae; ?>">
                      </span></td>
                  
                  <td height="28" colspan="2" bgcolor="#EEEEEE"><span class="style35">&nbsp;FPAS:                    
                    &nbsp;
                    <input name='cooperativa_fpas' type='text' id='cooperativa_fpas' size='12'  onKeyUp="pula(13,this.id,email.id)" onFocus="document.all.cel.style.background='#CCFFCC'" onBlur="document.all.cel.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;" value="<?php echo $cooperativa_fpas; ?>">
                  </span></td>
              </tr>
        
        
        <tr>
          <td height="28" bgcolor="#EEEEEE"><div align="right" class="style35">Contato:&nbsp;</div></td>
          <td height="28" bgcolor="#EEEEEE"><span class="style35"> &nbsp;&nbsp;
            <input name="contato" type="text" id="contato" size="30" onFocus="document.all.contato.style.background='#CCFFCC'" onBlur="document.all.contato.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()" value="<?=$contato?>">
          </span></td>
          <td height="28" colspan="2" bgcolor="#EEEEEE"><span class="style35">&nbsp;Cel:
            
            &nbsp;
            <input name='cel' type='text' id='cel' size='12' onKeyPress="return(TelefoneFormat(this,event))" onKeyUp="pula(13,this.id,email.id)" onFocus="document.all.cel.style.background='#CCFFCC'" onBlur="document.all.cel.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;" value="<?=$cel?>">
          </span></td>
        </tr>
        <tr>
          <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">E-mail:&nbsp;</div></td>
          <td height="28" bgcolor="#EEEEEE"><span class="style35 style40"> &nbsp;&nbsp;
            <input name="email" type="text" id="email" size="30" onFocus="document.all.email.style.background='#CCFFCC'" onBlur="document.all.email.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:lowercase;" value="<?=$email?>">
          </span></td>
          <td height="28" colspan="2" bgcolor="#EEEEEE"><span class="style35 style40">Site:&nbsp;&nbsp;
            <input name="site" type="text" id="site" size="35" onFocus="document.all.site.style.background='#CCFFCC'" onBlur="document.all.site.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:lowercase;" value="<?=$site?>">
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
            <input name="presidente" type="text" id="presidente" size="25" value="<?=$presidente?>"
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
          </span></td>
          <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">Matricula:&nbsp;</div></td>
          <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35" id="matriculad">
            <input name='matriculap' type='text' id='matriculap' size='12' onKeyUp="pula(13,this.id,contato.id)" value="<?=$matriculap?>"
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  	style="background:#FFFFFF; text-transform:uppercase;">
          </span></td>
        </tr>
        <tr>
          <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">RG:&nbsp;</div></td>
          <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
            <input name='rgp' type='text' id='rgp' size='15' onKeyPress="formatar('##.###.###-#', this)" value="<?=$rgd?>"
                  onKeyUp="pula(12,this.id,cpfp.id)" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  	style="background:#FFFFFF; text-transform:uppercase;">
          </span></td>
          <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">CPF:&nbsp;</div></td>
          <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
            <input name='cpfp' type='text' id='cpfp' size='15' onKeyPress="formatar('###.###.###-##', this)"  value="<?=$cpfd?>"
                  onKeyUp="pula(14,this.id,enderecop.id)" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  	style="background:#FFFFFF; text-transform:uppercase;">
          </span></td>
        </tr>
        <tr>
          <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">Endere&ccedil;o:&nbsp;</div></td>
          <td height="28" colspan="3" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
            <input name="enderecop" type="text" id="enderecop" size="50" value="<?=$enderecop?>"
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                onChange="this.value=this.value.toUpperCase()">
          </span></td>
        </tr>
        <tr>
          <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">Diretor:&nbsp;</div></td>
          <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35 style40">
            <input name="diretor" type="text" id="diretor" size="25" value="<?=$diretor?>"
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
          </span></td>
          <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">Matricula:&nbsp;</div></td>
          <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
            <input name='matriculad' type='text' id='matriculad' size='12' onKeyUp="pula(13,this.id,contato.id)" 
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" value="<?=$matriculad?>"
                  	style="background:#FFFFFF; text-transform:uppercase;">
          </span></td>
        </tr>
        <tr>
          <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">RG:&nbsp;</div></td>
          <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
            <input name='rgd' type='text' id='rgd' size='15' onKeyPress="formatar('##.###.###-#', this)" value="<?=$rgd?>"
                  onKeyUp="pula(12,this.id,cpfd.id)" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  	style="background:#FFFFFF; text-transform:uppercase;">
          </span></td>
          <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">CPF:&nbsp;</div></td>
          <td height="28" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
            <input name='cpfd' type='text' id='cpfd' size='15' onKeyPress="formatar('###.###.###-##', this)"  value="<?=$cpfd?>"
                  onKeyUp="pula(14,this.id,enderecod.id)" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                  	style="background:#FFFFFF; text-transform:uppercase;">
          </span></td>
        </tr>
        <tr>
          <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">Endere&ccedil;o:&nbsp;</div></td>
          <td height="28" colspan="3" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
            <input name="enderecod" type="text" id="enderecod" size="50" value="<?=$enderecod?>"
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" style="background:#FFFFFF;" 
                onChange="this.value=this.value.toUpperCase()">
          </span></td>
        </tr>
        <tr>
          <td height="28" bgcolor="#EEEEEE"><div class="style35 style40" align="right">Entidade Sindical V&iacute;nculada:&nbsp;</div></td>
          <td height="28" colspan="3" bgcolor="#EEEEEE"><span class="style35 style40"> &nbsp;&nbsp;
            <input name="entidade" type="text" id="entidade" size="60" value="<?=$entidade?>"
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
          </span></td>
        </tr>
        <tr>
          <td height="28" bgcolor="#EEEEEE"><div class="style35" align="right">Fundo Reserva:&nbsp;</div></td>
          <td height="28" bgcolor="#EEEEEE"><span class="style35"> &nbsp;&nbsp;
            <input name='fundo' type='text' id='fundo' size='12' onKeyDown="FormataValor(this,event,17,2)"
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" value="<?=$fundo?>"
                style="background:#FFFFFF; text-transform:uppercase;">
          </span></td>
          <td height="28" colspan="2" bgcolor="#EEEEEE"><span class="style35">Quantidade de Parcelas:&nbsp;&nbsp;
            <input name='parcelas' type='text' id='parcelas' size='5' value="<?=$parcelas?>"
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                style="background:#FFFFFF; text-transform:uppercase;">
            </span><br>
            <span style="font-size:10px; font-family:Arial; color:#F00; font-weight:bold">(Referentes as cotas de Capital Social)</span></td>
        </tr>
        <tr>
          <td height="28" bgcolor="#EEEEEE"><div class="style35" align="right">Taxa Administrativa:&nbsp;</div></td>
          <td height="28" colspan="3" bgcolor="#EEEEEE">&nbsp;&nbsp;<span class="style35">
            <input name='taxa' type='text' id='taxa' size='2' onKeyUp="SomenteNumero(this)" value="<?=$taxa?>"
                onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" 
                style="background:#FFFFFF; text-transform:uppercase;">
            <script language='JavaScript'>
				function SomenteNumero(valor){
					  if(isNaN(valor.value)){
						alert("Valor não numérico")
						valor.value="";
						valor.focus();
						return false;
					  }
					
					
					/*
					var tecla=(window.event)?event.keyCode:e.which;
					
										if (tecla != 8) return false;
					else return true;
					*/
					}
				
				</script>
          %</span></td>
        </tr>
        <tr>
          <td height="28" bgcolor="#EEEEEE"><div class="style35" align="right">Logo:&nbsp;</div></td>
          <td height="28" colspan="3" bgcolor="#EEEEEE">&nbsp;&nbsp;<?=$foto?></td>
        </tr>
        <tr>
          <td height="28" colspan="4" bgcolor="#EEEEEE"><span class="style35">Realizador do Curso de Cooperativismo: &nbsp;<span class="style35 style40">
            <input name="cursos" type="text" id="cursos" size="60" value="<?=$cursos?>"
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
                <select name='id_banco' class='campotexto' id='id_banco'>
                  <?php
$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$id_banco'");
$row_banco = mysql_fetch_array($result_banco);
print "<option value='$row_banco[0]'>$row_banco[id_banco] - $row_banco[nome]</option>";

$result_bancos = mysql_query("SELECT * FROM bancos where id_regiao = '$id_regiao'");
while ($row_bancos = mysql_fetch_array($result_bancos)){
print "<option value='$row_bancos[0]'>$row_bancos[id_banco] - $row_bancos[nome]</option>";
}
?>
                </select>
            </div>
          </div></td>
        </tr>
        </table>
      <div align="center"> <br>
        <input type="hidden" name="update" value="1">
        <input type="hidden" name="coop" value="<?=$coop?>">
        <br>
        <input type="submit" name="button" id="button" value="Alterar">
        <br>
        <br>
        <br>
      </div>
    </form> 
      <div align="center"><a href='cooperativa.php?id=1&regiao=<?=$id_regiao?>' class='link'><img src='../imagens/voltar.gif' border=0></a>
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
      </div></td>
  </tr>
  <tr>
    <td width="155" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="549" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr valign="top">
    <td height="37" colspan="4"><img src="../layout/baixo.gif" width="750" height="38">
      <?php
include "../empresa.php";
$rod = new empresa();
$rod -> rodape();
?></td>
  </tr>
</table>
</td></tr></table>
	</body>
</html>
<?php
}else{

include "../classes/update.php";

$coop = $_REQUEST['coop'];
$tipo = $_REQUEST['tipo'];

$campos_reservados[] = 'fundo';
$campos_reservados[] = 'taxa';
$campos_reservados[] = 'button';
$campos_reservados[] = 'coop';
$campos_reservados[] = 'update';

$conteudo = new update();
$retorno = $conteudo -> capturar_campos($HTTP_POST_VARS,$campos_reservados);

//RESOLVENDO O PROBLEMA COM A ULTIMA VIRGULA
$numero = strlen($retorno);						//CONTANDO A QUANTIDADE DE CARACTERS
$numero = $numero - 4;							//DIMINUINDO CARACTERS POR 4 PARA REMOVER A VIRGULA
$retorno = str_split($retorno, $numero);		//EXPLODINDO D VARIAVEL, JA SEM A VIRGULA

// TRABALHANDO SEPARADAMENTE ESSES CAMPOS, POIS NESCESSITAM SEREM FORMATADOS ANTES DO UPDATE
$fundo = $_REQUEST['fundo'];
$taxa = $_REQUEST['taxa'] / 100;

$fundo = str_replace(".","",$fundo);
$fundo = str_replace(",",".",$fundo);

print_r($retorno);

//MONTAGEM DO UPDATE
$update = "UPDATE cooperativas SET tipo = '$tipo', taxa = '$taxa', fundo = '$fundo', ".$retorno[0]."  WHERE id_coop = '".$coop."' " ;

mysql_query($update) or die ("Erro no update <br><br>".mysql_error());

$regiao = new regiao();
$regiao -> DadosRegiaoLogado();

$id_regiao = $regiao -> id_regiao;

print "
<script>
alert(\"Registro alterado com Sucesso!\");
location.href=\"cooperativa.php?id=1&regiao=$id_regiao\";
</script>";

}
?>