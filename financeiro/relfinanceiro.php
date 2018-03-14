<?php
include ("include/restricoes.php");
include "../conn.php";
//--VERIFICANDO MASTER -----------------
$id_user = $_COOKIE['logado'];
$REuser = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($REuser);
$tipo_user  = $row_user['tipo_usuario'];
$REMaster = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($REMaster);
// ---- FINALIZANDO MASTER -----------------
$regiao = $_REQUEST['regiao'];
$mes2 = date('F');
$dia_h = date('d');
$mes_h = date('m');
$ano = date('Y');
$mes_q_vem = $mes_h + 1;
$meses = array('Erro','Janeiro','Fevereiro','Mar�o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$MesInt = (int)$mes_h;
$mes = $meses[$MesInt];
$data_hoje = "$dia_h/$mes_h/$ano";
//EMBELEZAMENTO
$bord = "style='border-bottom:#000 solid 1px; font-size: 12px; font-face:Arial;'";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>:: Financeiro ::</title>

<!-- highslide -->
<link rel="stylesheet" type="text/css" href="../js/highslide.css" />
<script type="text/javascript" src="../js/highslide-with-html.js"></script>
<script type="text/javascript" >
	hs.graphicsDir = '../images-box/graphics/';
	hs.outlineType = 'rounded-white';
	hs.showCredits = false;
	hs.wrapperClassName = 'draggable-header';
</script>
<!-- highslide -->

<script>
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
</script>
<link href="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>

<script type="text/javascript">
$(function(){
	
	
	
	
	$('.date').datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		changeYear: true
	});
	
	$('.ano').parent().next().hide();
	$(".ano").click(function(){
		$(this).parent().next().slideToggle();
		$('.ano').parent().next().hide();
	});
	$('.dataautonomos').parent().next().hide();
	$('.dataautonomos').click(function(){
		$(this).parent().next().slideToggle();
		$('.dataautonomos').parent().next().hide();
	});
	$('a.recisao').click(function(){
		$(this).next().toggle();		
	});
	
	
                            
                $('#tipo_anual').change(function(){ 
               
                    if($(this).val() == 'entrada') {
                        
                        $('#select_entrada').show();
                        $('#select_saida').hide();
                    
                    } else {
                        
                        $('#select_entrada').hide();
                        $('#select_saida').show();
                    
                    }
                });

});
</script>
<style type="text/css">
body {
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	margin:0px;
	background:#F0F0F0;
}
#baseCentral{
	width:980px;
	margin:0px auto;
}
#topo{
	position:fixed;
	top:0px;
	background-color:#FFF;
	z-index:1000;
	width:978px;
	height:135px;
	border-top-width: 1px;
	border-right-width: 1px;
	border-left-width: 1px;
	border-top-style: solid;
	border-right-style: solid;
	border-left-style: solid;
	border-top-color: #666;
	border-right-color: #666;
	border-left-color: #666;
}
#conteudo {
	position:relative;
	top:135px;
	background-color:#FFF;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	border-right-color: #666;
	border-bottom-color: #666;
	border-left-color: #666;
}
*html #conteudo {
	top:0px;
}
<!--
.style2 {font-size: 12px}
.style3 {
	color: #FF0000;
	font-weight: bold;
	text-align: center;
}
.style6 {
	font-size: 14px;
	font-weight: bold;
	color: #FFFFFF;
}
.style9 {color: #FF0000}
.style12 {
	font-size: 12px;
	font-weight: bold;
	color: #003300;
}
.style29 {color: #000000}
.style31 {	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 14px;
	color: #FF0000;
}
.style32 {font-size: 10px}
.style33 {font-family: Verdana, Arial, Sans-Serif}
.style27 {color:#FFF}
-->

#geral h1{
	font-size: 14px;
	color: #F00;
	font-variant: small-caps;
	text-decoration: none;
	margin: 0px;
	padding-top: 0px;
	padding-right: 0px;
	padding-bottom: 0px;
	padding-left: 30px;
}
.linha_um {
 background-color:#f5f5f5;
}
.linha_dois {
 background-color:#ebebeb;
}
.linha_um td, .linha_dois td {
 	border-bottom:1px solid #ccc;
}
</style>
<link href="../novoFinanceiro/style/form.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="baseCentral">
<div id="topo">
<?php 
	$query_master = mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$regiao'");
	$query_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'");
	$id_master = @mysql_result($query_master,0);
?>
	<table width="980">
    	<tr>
<td width="110" rowspan="3">
                  <img src="../imagens/logomaster<?=$id_master?>.gif" width="110" height="79">
          </td>
          <td align="left" valign="top">
          	<br />
                Data:&nbsp;<strong><?=date("d/m/Y");?></strong>&nbsp;<br />
        voc&ecirc; est&aacute; visualizando a Regi&atilde;o:&nbsp;<strong><?=@mysql_result($query_regiao,0);?></strong></td>
        	<td>
            <br />
            
        <!--Controle de regiao -->
        <div>
        <form name="formRegiao" id="formRegiao" method="get">
        <table>
        <tr>
            <td><span style="color:#000">Regi&atilde;o</span></td>
            <td>
                
                 <?php // Visualizando Regiões
                      if($tipo_user == '1' or $tipo_user == '4') : ?>
                    <span id="labregiao1">
                    <select name="regiao" class="campotexto" id="regiao" onchange="MM_jumpMenu('parent',this,0)">
                        <option value="">- Selecione -</option>
                        <optgroup label="Regi&ocirc;es em Funcionamento">
                
                <?php
                // Acesso a Administração
                $ids_administracao = array('5','9','27','28','33','64','71','77','24','82','24','75','22','89');
                $ids_sistema = array('9','68','75','87');
                
                if(in_array($id_user,$ids_administracao)) {
                    $acesso_administracao = true;
                }
                if(in_array($id_user,$ids_sistema)) {
                    $acesso_sistema = true;
                }
                //
                
                    $qr_regioes_ativas = mysql_query("SELECT * FROM regioes WHERE status = '1'");
                    while($row_regiao = mysql_fetch_array($qr_regioes_ativas)) {
                        
                        if($regiao == $row_regiao['id_regiao']) {
                            $selected = 'selected';
                        } else {
                            $selected = NULL;
                        }
                        
                        if(($row_regiao['id_regiao'] == '15' and isset($acesso_administracao)) or
                           ($row_regiao['id_regiao'] != '15')) {
                        
                        if(($row_regiao['id_regiao'] == '36' and isset($acesso_sistema)) or 
                           ($row_regiao['id_regiao'] != '36')) { 
						   
						   
						   ?>
                				
                                <option value="<?="?regiao=".$row_regiao['id_regiao'];?>" <?=$selected?>><?=$row_regiao['id_regiao'].' - '.$row_regiao['regiao']?></option>
                        
                    <?php } } } ?>
                    
                </optgroup>
                <optgroup label="Regi&ocirc;es Desativadas">
                
                <?php // Acesso a Regiões Desativadas
                $ids_desativadas = array('1','5','9','27','57','64','68','51','77','75','87','24','71','22','89');
                
                if(in_array($id_user,$ids_desativadas)) {
                    
                    $qr_desativadas = mysql_query("SELECT * FROM regioes WHERE status = '0'");
                    while($row_regiao = mysql_fetch_array($qr_desativadas)) {
                        
                        if($regiao_usuario == $row_regiao['id_regiao']) {
                            $selected = 'selected';
                        } else {
                            $selected = NULL;
                        } 
						
							
						?>
                        
                        <option value="<?="?regiao=".$row_regiao['id_regiao'];?>" <?=$selected?>><?=$row_regiao['id_regiao'].' - '.$row_regiao['regiao']?></option>
                        
                <?php } } ?>
                
                </optgroup>
                </select>
                </span>
                <?php endif; // Fim de Regiões?>
                
            </td>
        </tr>
        </table>
        </form>
        </div>
           <!--Controle de regiao -->
       
            </td>
      </tr>          
    </table>
      <center>
        <img src="imagensfinanceiro/relatorio-32.png" alt="fornecedor" width="32" height="32" />&nbsp;<span class="style31">RELATO&#769;RIOS FINANCEIROS</span>
      </center>
</div>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"  id="conteudo"> 

  <tr>
    <td height="26" align="center" valign="middle" bgcolor="#E8E8E8">      
    <div align="center" class="style6"> 
        <div align="left">&nbsp;&nbsp;<span class="style2"><img src="imagensfinanceiro/entradas-up-32.png" alt="fornecedor" width="32" height="32" align="absmiddle">&nbsp;<span class="style3">ENTRADAS</span></span></div>
      </div></td>
    <td height="26" valign="middle" bgcolor="#E8E8E8"><div align="left"><span class="style2"> &nbsp;&nbsp;<img src="imagensfinanceiro/saida-32.png" alt="fornecedor" width="32" height="32" align="absmiddle"><span class="style31">&nbsp;</span></span><span class="style3">SAI&#769;DAS</span></div></td>
  </tr>
  <tr>
    <td height="16" valign="top">
    <form action="relfinanceiro2.php" method="post" name="for1">
    &nbsp;&nbsp;&nbsp;&nbsp;<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">Selecione o Projeto:</span>
    <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' AND status_reg = '1'");
print "<select name='projeto' class='textarea2'>";
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]>$row_projeto[0] -  $row_projeto[nome] </option>";
}
print "</select>";
?>
    <br>
    &nbsp;&nbsp;&nbsp;&nbsp;<br>
    &nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Visualizar Entradas de 
    <input name="data_ini" type="text" id="data_ini" size="10" maxlength='10' class='date'>
    at&eacute; 
    <input name="data_fim" type="text" id="data_fim" size="10" maxlength='10' class='date'>
     </span><span class="style12 style29"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
     <br />
     &nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" class="submit-go" value="Gerar">
<input type="hidden" value="1" name="id">
<input type="hidden" value="<?=$regiao;?>" name="regiao">
<br>
&nbsp;&nbsp;&nbsp;&nbsp;<br>
    </form>   
    </td>
    
    
    <td height="16" valign="top"><form action="relfinanceiro2.php" method="post" name="for2">
  &nbsp;&nbsp;&nbsp;&nbsp;<br>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">Selecione o Projeto:</span>
  <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' AND status_reg = '1'");
print "<select name='projeto' class='textarea2'>";
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]>$row_projeto[0] - $row_projeto[nome] </option>";
}
print "</select>";
?>
  <br>
  &nbsp;&nbsp;&nbsp;&nbsp;<br>
  &nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Visualizar Sa&iacute;das de
  <input name="data_ini" type="text" size="10" maxlength='10' class='date'>
    at&eacute;
  <input name="data_fim" type="text" size="10" maxlength='10' class='date'>
  </span><span class="style12 style29"> <br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" class="submit-go" value="Gerar">
  <input type="hidden" value="2" name="id">
  <input type="hidden" value="<?=$regiao;?>" name="regiao">
  <br>
    </form></td>
  </tr>
  <tr bgcolor="#E8E8E8">
  	<td><div align="left">&nbsp;&nbsp;<span class="style9">&nbsp;<span class="style2"><img src="imagensfinanceiro/entradas-up-32.png" alt="fornecedor" width="32" height="32" align="absmiddle" /><span class="style31"><img src="imagensfinanceiro/saida-32.png" alt="fornecedor" width="32" height="32" align="absmiddle" /></span>&nbsp;</span></span><span class="style3"> ENTRADAS E SAI&#769;DAS</span></div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="18" valign="top">
<form action="relfinanceiro2.php" method="post" name="for5">
  <p><span class="style12 style29">&nbsp;</span></p>
  <p><span class="style12 style29">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Selecione o Projeto:</span>
    <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' AND status_reg = '1'");
print "<select name='projeto' class='textarea2'>";
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]>$row_projeto[0] - $row_projeto[nome] </option>";
}
print "</select>";
?>
    <br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="relfinanceiro2.php?id=5&regiao=<?=$regiao;?>&select=1" class="style12 style29" style="TEXT-DECORATION: none;">
      <label>
        Visualizar lan&ccedil;amentos n&atilde;o pagos&nbsp;&nbsp;
        <input type="radio" name="select" id="select" value="1">
        </label>
      <br>
      </a>
    <br>
    <span class="style12 style29">
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <label>
        Visualizar lan&ccedil;amentos futuros&nbsp;&nbsp;
        <input type="radio" name="select" id="select" value='2'>
        </label>
      </span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="submit" class="submit-go" value="Gerar">
    <input type="hidden" value="5" name="id">
    <input type="hidden" value="<?=$regiao;?>" name="regiao">
    <br>
    &nbsp;&nbsp;&nbsp;</p>
</form>
</td>
<td valign="top"><form action="relfinanceiro2.php" method="post" name="for4" id="for4">
  &nbsp;<br />
  <br />
  &nbsp;&nbsp;&nbsp;<span class="style12 style29">&nbsp;Selecione a Conta:</span>
  <?php
$result_banco = mysql_query("SELECT * FROM bancos where id_regiao = '$regiao' and interno ='1' AND status_reg = '1'");
print "<select name='banco' class='textarea2'>";
while($row_banco = mysql_fetch_array($result_banco)){
print "<option value=$row_banco[0]>$row_banco[0] - $row_banco[nome] - $row_banco[agencia] / $row_banco[conta]</option>";
}
print "</select>";
?>
  <br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">Ou marque aqui para exibir todas as contas:</span>
  <input type="checkbox" name="todas_contas" id="todas_contas" value="1" />
  &nbsp;&nbsp;&nbsp;&nbsp;<br />
  &nbsp;&nbsp;&nbsp;&nbsp;<br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">Selecione o m&ecirc;s e o ano de refer&ecirc;ncia:&nbsp;&nbsp;&nbsp;
  <select name="mes" id="mes" class='textarea2'>
  <?php 
  $qr_meses = mysql_query("SELECT * FROM ano_meses");
  while($row_meses = mysql_fetch_assoc($qr_meses)){
	  $selected = (date('m') == $row_meses['num_mes']) ? 'selected="selected"' : '';
	  echo '<option '.$selected.' value="'.$row_meses['num_mes'].'">'.$row_meses['num_mes'].' - '.$row_meses['nome_mes'].'</option>';
  }
  ?>
  </select>
  </span> <span class="style12 style29"> &nbsp;&nbsp;&nbsp;&nbsp;
  <select name="ano" id="ano" class='textarea2'>
  <?php 
  	for($i=2005; $i<2014; $i++){
	  $selected = (date('Y') == $i) ? 'selected="selected"' : '';
	  echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
	}
  ?>
  </select>
  </span>
  <label></label>
  <span class="style12 style29"><br />
  &nbsp; </span> <span class="style12 style29">&nbsp;&nbsp;</span><span class="style12 style29">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
  <input type="submit" class="submit-go" value="Gerar" />
  <input type="hidden" value="4" name="id" />
  <input type="hidden" value="<?=$regiao;?>" name="regiao" />
  <br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</form></td>
  </tr>
  <tr>
    <td height="18" align="right" valign="top" bgcolor="#E8E8E8">
    <div align="left">&nbsp;&nbsp;<span class="style2"><img src="imagensfinanceiro/caixa-32.png" alt="fornecedor" width="32" height="32" align="absmiddle">&nbsp;<span class="style3">CAIXA</span></span></div>
    </td>
    <td bgcolor="#E8E8E8">&nbsp;</td>
  </tr>
    <tr>
      <td height="18"  valign="top">
        <form action="relfinanceiro2.php" method="post" name="for3">
          &nbsp;&nbsp;&nbsp;&nbsp;<br>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">Selecione o Projeto:</span>
  <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' AND status_reg = '1'");
print "<select name='projeto' class='textarea2'>";
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]> $row_projeto[0] - $row_projeto[nome] </option>";
}
print "</select>";
?>
  <br>
  &nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Visualizar Entradas de
  <input name="data_ini" type="text" size="10" maxlength='10' class='date'>
    at&eacute;
  <input name="data_fim" type="text" size="10" maxlength='10' class='date'>
  &nbsp;&nbsp;<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>
  <input type="submit" class="submit-go" value="Gerar">
  <input type="hidden" value="3" name="id">
  <input type="hidden" value="<?=$regiao;?>" name="regiao">
  <br>
  <br>
      </form></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="33" colspan="2" bgcolor="#E8E8E8"><span class="style3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DEMAIS RELATORIOS</span></td>
    </tr>
    <tr>
    <td><div align="left" style="background-color:#FFF">
      <form action="abastecimento.php" method="post" name="formabas" id="formabas">
        <p>&nbsp;&nbsp;<img src="../imagensmenu2/c1.gif" alt="cobrinha" width="20" height="14" align="absmiddle" />&nbsp;<b>RELAT&Oacute;RIOS DE ABASTECIMENTOS</b></p>
        <p align="center"><span class="style12 style29">
          <label>Marque para ver o relat&oacute;rio anual:&nbsp;
            <input type="checkbox" name="anotodo" id="anotodo" value="1" onclick="document.formabas.mes.style.display = (document.formabas.mes.style.display == 'none') ? '' : 'none' ;" />
          </label>
          <br />
          <br />
          &nbsp;
          <select name="mes" id="mes" class='textarea'>
		   <?php 
          $qr_meses = mysql_query("SELECT * FROM ano_meses");
          while($row_meses = mysql_fetch_assoc($qr_meses)){
              $selected = (date('m') == $row_meses['num_mes']) ? 'selected="selected"' : '';
              echo '<option '.$selected.' value="'.$row_meses['num_mes'].'">'.$row_meses['num_mes'].' - '.$row_meses['nome_mes'].'</option>';
          }
          ?>
          </select>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <select name="ano" id="ano" class='textarea'>
		  <?php 
            for($i=2005; $i<2014; $i++){
              $selected = (date('Y') == $i) ? 'selected="selected"' : '';
              echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
            }
          ?>
          </select>
          <br />
          <br />
          </span>
          <input type="submit" class="submit-go" value="Visualizar Relat&oacute;rio" />
          <br />
        </p>
      </form>
    </div></td>
     <td valign="top"><div align="left" style="background-color:#FFF">
       <p>&nbsp;&nbsp;<img src="../imagensmenu2/gestao.gif" alt="cobrinha" width="20" height="22" align="absmiddle" />&nbsp;<b>RELAT&Oacute;RIOS DE FECHAMENTO</b></p>
       <p align="center"> <a onclick="MM_openBrWindow('../relsdescritivodetalhado.php?regiao=<?=$regiao;?>','scrollbars=yes,resizable=yes,width=770,height=550')" href="#"><img alt="rel" src="../imagens/ver_detalhado.gif" align="middle" border="0" /></a>&nbsp;&nbsp;&nbsp;&nbsp; <a onclick="MM_openBrWindow('../reldescritivo.php?regiao=<?=$regiao;?>','','scrollbars=yes,resizable=yes,width=770,height=550')" href="#"><img alt="rel" src="../imagens/ver_descritivo.gif" align="middle" border="0" /></a>&nbsp;&nbsp;&nbsp;<a onclick="MM_openBrWindow('../reldescritivoanual.php?regiao=<?=$regiao;?>','','scrollbars=yes,resizable=yes,width=770,height=550')" href="#"><img alt="rel" src="../imagens/ver_anual.gif" align="middle" border="0" /></a>&nbsp;&nbsp;<a onclick="MM_openBrWindow('../reldesempenho.php?regiao=<?=$regiao;?>&amp;id=1','','scrollbars=yes,resizable=yes,width=770,height=550')" href="#"><img alt="rel" src="../imagens/ver_desempenho.gif" align="middle" border="0" /></a>&nbsp;&nbsp;<a onclick="MM_openBrWindow('../reldescritivo_entrada_saida.php?master=<?=$id_master;?>&regiao=<?=$regiao;?>','scrollbars=yes,resizable=yes,width=770,height=550')" ><img alt="rel" src="../imagens/ver_indicador.jpg" align="middle" border="0" /></a>&nbsp;&nbsp;<br />
         &nbsp;<br />
       </p>
     </div></td>
  </tr>
  <tr>
  	<td height="32" colspan="2" bgcolor="#E8E8E8">
    	<div align="left"><span class="style2"> &nbsp;&nbsp;<img src="imagensfinanceiro/saida-32.png" alt="fornecedor" width="32" height="32" align="absmiddle"><span class="style31">&nbsp;</span></span><span class="style3">BUSCAR SAI&#769;DAS</span></div>           
    </tr>
    </td>
  </tr>
  <tr>
  	<td colspan="2">
    	<?php include "views/busca.php"; ?>
    </td>
  </tr>
  <tr>
    <td height="32" colspan="2" align="left" valign="middle">
    
    <hr>
    <p>
      <?php 
// Relatorio financeiro por grupo criado por maikom 
// filtro de usuarios
$usuarios = array('5','77','9','64','27','75');
if(in_array($id_user,$usuarios)):?>
</p>
    <div id="geral">
        <h1 style="text-align: center">&nbsp;&nbsp;&nbsp;RELATORIO GERENCIAL</h1>
    <form action="../novoFinanceiro/relatorio.gerencial.php" method="get" name="relatorio">
    <table align="center">
        <tr>
            <td align="center">M&ecirc;s</td>
            <td align="center">Ano</td>
            <td align="center">Projeto</td>
            <td align="center">Banco</td>
        </tr>
        <tr>
        	<td>
        	  <select name="mes" id="mes">
        	    <?php
			  $query_mes = mysql_query("SELECT * FROM  ano_meses ORDER BY num_mes");
			  while($row_mes = mysql_fetch_assoc($query_mes)){
				  if($row_mes['num_mes'] == date('m'))
				  	echo '<option value="'.$row_mes['num_mes'].'" selected="selected">'.$row_mes['nome_mes'].'</option>';
				  else
				  	echo '<option value="'.$row_mes['num_mes'].'" >'.$row_mes['nome_mes'].'</option>';
			  }
			  ?>
        	    </select>
      	  </td>
            <td>
              <select name="ano" id="ano">
              <?php 
				$ano = array(2008,2009,2010,2011,2012);
				foreach($ano as $an){
					if($an == date('Y'))
						echo '<option value="'.$an.'" selected="selected">'.$an.'</option>';
					else
					 	echo '<option value="'.$an.'" >'.$an.'</option>';
				}
			  ?>
              </select>
            </td>
            <td>
              <select name="projeto" id="projeto">
              <?php 
			  $query_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' AND status_reg = '1'");
				while($row_projeto = mysql_fetch_array($query_projeto)){
					echo '<option value="'.$row_projeto[0].'">'.$row_projeto[0].' - '.$row_projeto['nome'].'</option>';
				}
				?>
              </select>
            </td>
            <td>
                <select name="bancos" >
                    <option value="">Todos os bancos</option>
                    <?php
                        $qr_bancos = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$regiao' AND status_reg = '1'");
                        while($row_bancos = mysql_fetch_assoc($qr_bancos)){
                            echo "<option value=\"$row_bancos[id_banco]\">$row_bancos[id_banco] - $row_bancos[nome]</option>";
                        }
                    ?>
                </select>
            </td>
            </tr>
        <tr>
        	<td colspan="4" align="center"><input name="button" type="submit" class="submit-go" id="button" value="       GERAR RELATORIO       "></td>
            </tr>
    </table>
    </form>
</div>
<?php endif; ?>
<?php
//BLOQUEIO PAULO MONTEIRO SJR 16-03 - 17hs
if($id_user != '73') {
	
	?> 

<tr>
        <td height="32" colspan="2" bgcolor="#E8E8E8"><div align="left"><span class="style9">&nbsp;&nbsp;<span class="style2"><img src="../imagens/rel_anual.gif" alt="contas" width="25" height="25" align="absmiddle" />&nbsp;</span></span><span class="style3"> &nbsp;RELAT&Oacute;RIO ANUAL</span></div></td>
  </tr>
  <tr>
  <td align="center" colspan="2">


   	 
            
           
            
       <form action="../reldescritivoanual2.php" method="post" enctype="multipart/form-data" name='form1' onSubmit="return validaForm()" id="form1">
             <table width="100%" border="0" align="center" cellspacing="0" bordercolor="#999999">
            <tr>
            
                    
                        <td height="27" align="right" bgcolor="#FFFFFF">SELECIONE O ANO:</td>
                        <td height="27" align="center" bgcolor="#FFFFFF"><div align="left">
                          &nbsp;&nbsp;
                          <select name="ano" id="ano">
                            <?php
                            for($i = 2005; $i <= (date('Y')); $i ++){
                                $selected = ($i == date('Y')) ? 'selected="selected"' : '';
                                echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
                            }
                            ?>
                            
                          </select>
                        </div></td>
                      </tr>
                      <tr>
                        <td height="27" align="right" bgcolor="#FFFFFF">
                          SELECIONE O TIPO:            
                        </span></td>
                        <td height="27" align="center" bgcolor="#FFFFFF"><span class="style24">
                          <div align="left">&nbsp;&nbsp;
                            <select name="tipo_anual" id="tipo_anual">
                            <option value="">Escolha o tipo...</option>
                              <option value="entrada">Entrada</option>
                              <option value="saida" >Sa&iacute;da</option>
                        </select>
                              </label>
                        </div>
                          </span></td>
                      </tr>
                      
                      <!---------- TIPOS DE ENTRADA --------------------->
                      <tr id="select_entrada"  style="display:none;">
                        <td height="27" align="right" bgcolor="#FFFFFF">SELECIONE O TIPO DE ENTRADA:</td>
                        <td height="27" align="center" valign="middle" bgcolor="#FFFFFF"><span class="style24">
                          <div align="left">&nbsp;&nbsp;
                            <select name="tipo_entrada" id="tipo_entrada">
                            
                             <option value="">Escolha o tipo...</option>
                              <option value="todos" >TODOS</option>
                             <?php
                             $qr_tipo_entrada = mysql_query("SELECT * FROM entradaesaida WHERE tipo = 1");
                             while($row_tipo_entrada = mysql_fetch_assoc($qr_tipo_entrada)):
                             
                                echo '<option value="'.$row_tipo_entrada['id_entradasaida'].'">'.$row_tipo_entrada['cod'].' - '.$row_tipo_entrada['nome'].'</option>';
                                
                             endwhile;
                             ?>
                        </select>
                          </div>
                        </span></td>
                      </tr>
                      
                      <!---------- TIPOS DE SAÍDA  --------------------->
                      <tr id="select_saida" style="display:none;">
                        <td height="27" align="right" bgcolor="#FFFFFF">SELECIONE O TIPO DE SA&Iacute;DA</td>
                        <td height="27" align="center" valign="middle" bgcolor="#FFFFFF"><span class="style24">
                          <div align="left">&nbsp;&nbsp;
                            <select name="tipo_saida" id="tipo_saida">
                                
                                  <option value="">Escolha o tipo...</option>
                                 <option value="todos" >TODOS</option>
                                  <?php
                             $qr_tipo_entrada = mysql_query("SELECT * FROM entradaesaida WHERE tipo = 0");
                             while($row_tipo_entrada = mysql_fetch_assoc($qr_tipo_entrada)):
                             
                                echo '<option value="'.$row_tipo_entrada['id_entradasaida'].'">'.$row_tipo_entrada['cod'].' - '.$row_tipo_entrada['nome'].'</option>';
                                
                             endwhile;
                             ?>
                            </select>
                          </div>
                        </span></td>
                      </tr>
                      
                      <tr>
                        <td height="39" colspan="2" align="center" bgcolor="#FFFFFF"><label>
                          <input name="gerar" type="submit" class="submit-go" id="gerar" value="GERAR RELATORIO" />
                        </label></td>
                        </tr>
                    </table>
     </form>               
 
 
                
</td>
<tr>
	<td height="32" colspan="2" bgcolor="#E8E8E8"><div align="left"><span class="style9">&nbsp;&nbsp;<span class="style2"><img src="../imagensfinanceiro/contas.gif" alt="contas" width="25" height="25" align="absmiddle" />&nbsp;</span></span><span class="style3"> &nbsp;CONTROLE DE SALDOS</span></div></td>
</tr>
<tr>
<td colspan="2"><br>
    <table width="95%" border="0" align="center" cellpadding="2" cellspacing="1" >
      <tr class="linha_um"> 
            <td width="5%" bgcolor="#333333"><div align="center" class="style27">COD</div></td>
            <td width="25%" bgcolor="#333333"><div align="center" class="style27">BANCO</div></td>
            <td width="9%" bgcolor="#333333"><div align="center" class="style27">AG</div></td>
            <td width="10%" bgcolor="#333333"><div align="center" class="style27">CC</div></td>
            <td width="30%" bgcolor="#333333"><div align="center" class="style27">PROJETO</div></td>
            <td width="17%" bgcolor="#333333"><div align="center" class="style27">SALDO PARCIAL </div></td>
           <td width="17%" bgcolor="#333333"><div align="center" class="style27">QUANT. SAIDAS HOJE</div></td>

        </tr>
		  <?php
		  $cont = "0";
		  $div = "<div align='center' class='style24'>";
		  //1 - ramon
		  //5 - fabio
		  //9 - sabino
		  //27 - silvania
		  //32 - renato
		  //75 -  Maikom james
		  //$id_user == '1' or 
		  if($id_user == '64' or $id_user == '5' or $id_user == '9' or $id_user == '27' or $id_user == '77' or $id_user == '75'){
			  $RERegioes = mysql_query("SELECT * FROM regioes Where id_master = '$row_master[0]' and status='1'");
			  
			  while($RowRegioes = mysql_fetch_array($RERegioes)){
				  $REBancos = mysql_query("SELECT * FROM bancos where id_regiao = '$RowRegioes[0]' and interno ='1' AND status_reg = '1'");
				  $NumBancos = mysql_num_rows($REBancos);
				  
				  if($NumBancos != 0){
				  //S� VAI PRINTAR ESSAS INFORMA��ES SE A REGI�O SELECIONADA TIVER DIFERENTE DE 0
				  echo "<tr bgcolor='#666666'>";
				  echo "<td colspan='7' width='5%' align='center' $bord><div style='font-size:14px; color:#FFF'><b>$RowRegioes[regiao]</b></div></td>";
				  echo "</tr>";
				  
				  while($RowBancos = mysql_fetch_array($REBancos)){
					  
					  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
					  // verificando se existem saidas confirmadas hoje
					  $qr_saidas = mysql_query("SELECT * 
												FROM  `saida` 
												WHERE id_banco =  '$RowBancos[id_banco]'
												AND DAY( data_pg ) =  '".date("d")."'
												AND MONTH( data_pg ) =  '".date("m")."'
												AND YEAR( data_pg ) =  '".date("Y")."'
												AND status = '2';");
						$quant = @mysql_num_rows($qr_saidas);
					if(empty($quant)){
						$color = "#FFB09D";
					}
					$quant = NULL;
					$qr_saidas_hj = mysql_query("SELECT * FROM `saida` WHERE id_banco = '$RowBancos[id_banco]' AND DAY(data_vencimento) =  '".date("d")."' AND MONTH(data_vencimento) = '".date("m")."' AND YEAR(data_vencimento) = '".date("Y")."' AND status = '1'");
			  		$saidas_hoje = @mysql_num_rows($qr_saidas_hj);
					
						  $REProjeto = mysql_query("SELECT * FROM projeto where id_projeto = '$RowBancos[id_projeto]' AND status_reg = '1'");
						  $RowProjeto = mysql_fetch_array($REProjeto);
			  
						  $ValorBanc = str_replace(",", ".", $RowBancos['saldo']);
			  			  $ValorBancF = number_format($ValorBanc,2,",",".");
						  
						  echo "<tr bgcolor='$color'>";
						  echo "<td width='5%' $bord>$div $RowBancos[id_banco]</div></td>";
						  echo "<td width='25%' $bord>$div $RowBancos[nome] 
						  <a href='../novoFinanceiro/view/controle.saldo.php?id_banco=$RowBancos[id_banco]' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe', width: 540 } )\" \">
				   <img src=\"../novoFinanceiro/image/seta.gif\" />
				   		</a></div></td>";
						  echo "<td width='9%' $bord>$div $RowBancos[agencia]</div></td>";
						  echo "<td width='10%' $bord>$div $RowBancos[conta]</div></td>";
						  echo "<td width='30%' $bord>$div $RowProjeto[nome]&nbsp;</div></td>";
						  echo "<td width='17%' $bord>$div $ValorBancF </div></td>";
						  echo "<td width='17%' $bord>$div $saidas_hoje </div></td>";
						  echo "</tr>";
		  
						  $cont ++;
				  	  }
				    
			
				  }// S� VAI RODAR ISSO AE EM CIMA, SE TIVER BANCO NA REGIAO
				  
			  }
			  
		  }else{
			  $REBanc = mysql_query("SELECT * FROM bancos where id_regiao='$regiao' and interno ='1' AND status_reg = '1'");
			  while($RowBanc = mysql_fetch_array($REBanc)){
				  
				  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
			  
				  $REProjeto = mysql_query("SELECT * FROM projeto where id_projeto = '$RowBanc[id_projeto]' AND status_reg = '1'");
				  $RowProjeto = mysql_fetch_array($REProjeto);
			  
				  $ValorBanc = str_replace(",", ".", $RowBanc['saldo']);
				  $ValorBancF = number_format($ValorBanc,2,",",".");
						  
				  echo "<tr bgcolor='$color'>";
				  echo "<td width='5%' $bord>$div $RowBanc[id_banco] 
				  		
				   </div></td>";
				  echo "<td width='25%' $bord>$div $RowBanc[nome]</div></td>";
				  echo "<td width='9%' $bord>$div $RowBanc[agencia]</div></td>";
				  echo "<td width='10%' $bord>$div $RowBanc[conta]</div></td>";
				  echo "<td width='30%' $bord>$div $RowProjeto[nome]&nbsp;</div></td>";
				  echo "<td width='17%' $bord>$div $ValorBancF </div></td>";
				  echo "</tr>";
		  
				  $cont ++;
			  }
		  }
		  
		  
		  ?>
      </table>  
	  <?php 
	  }  
		  ?>
         
    <br></td>
  </tr>
  
  <tr>
    <td width="499"></td>
    <td></td>
  </tr>
  <tr>
    <td align="center" valign="middle" class="style3"></td>
    <td width="499" align="center" valign="middle" class="style3"></td>
  </tr>
  <tr valign="top">
    <td height="18" colspan="4">
    </td>
  </tr>
  <tr valign="top">
    <td height="18" colspan="4" bgcolor="#999999">
<?php
include "../empresa.php";
$rod = new empresa();
$rod -> rodape();
?></td>
  </tr>
</table> 
</div>
</body>
</html>
<?php
/* Liberando o resultado */
mysql_free_result($result_projeto);
/* Fechando a conex�o */
mysql_close($conn);

?>