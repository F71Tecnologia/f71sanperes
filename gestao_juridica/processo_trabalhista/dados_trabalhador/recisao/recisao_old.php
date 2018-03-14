<?php
include ("../include/restricoes.php");
// Acrescentar Salário Variável a Férias Vencidas e Proporcionais

include('../../conn.php');
include('../../classes/funcionario.php');
include('../../classes/curso.php');
include('../../classes/clt.php');
include('../../classes/projeto.php');
include('../../classes/calculos.php');
include('../../funcoes.php');

$Fun    = new funcionario();
$Fun   -> MostraUser(0);
$user	= $Fun -> id_funcionario;
$regiao = $_REQUEST['regiao'];

$Curs 	 = new tabcurso();
$Clt 	 = new clt();
$ClasPro = new projeto();
$Calc	 = new calculos();

if(!empty($_REQUEST['ajax'])) {
	$data   = implode('-', array_reverse(explode('/', $_REQUEST['ajax'])));
	$depois = date('d/m/Y', strtotime("$data +30 day"));
	echo $depois;
	exit;
}

if(empty($_REQUEST['tela'])) {
	$tela = 1;
} else {
	$tela = $_REQUEST['tela'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet :: Rescis&atilde;o</title>
<link href="../../net1.css" rel="stylesheet" type="text/css">
<link href="../../favicon.ico" rel="shortcut icon" />
<link href="../../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../js/ramon.js"></script>
<script type="text/javascript" src="../../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
<script type="text/javascript">
$(function() {
	$('#data_aviso').datepicker({
		changeMonth: true,
	    changeYear: true
	});
});
</script>
<style>
body {
	background-color:#FAFAFA;
	text-align:center;
	margin:0px;
}
p {
	margin:0px;
}
#corpo {
	width:90%;
	background-color:#FFF;
	margin:0px auto;
	text-align:left;
	padding-top:20px;
	padding-bottom:10px;
}
</style>
</head>
<body>
<div id="corpo">
<?php if($tela == 1) { ?>
<div id="topo" style="width:95%; margin:0px auto;">
	<div style="float:left; width:25%;">
        <a href='../../principalrh.php?regiao=<?=$regiao?>'>
            <img src='../../imagens/voltar.gif'>
        </a>
    </div>
	<div style="float:left; width:50%; text-align:center; font-family:Arial; font-size:24px; font-weight:bold; color:#000;">
    	RESCIS&Atilde;O
    </div>
	<div style="float:right; width:25%; text-align:right; font-family:Arial; font-size:12px; color:#333;">
    	<br><b>Data:</b> <?=date('d/m/Y')?>&nbsp;
    </div>
	<div style="clear:both;"></div>
</div>
<?php }

switch($tela) {
	case 1:
	
	// SÓ PEGA OS CLTS QUE PEDIRAM DEMISSÃO, OU FORAM DISPENSADOS STATUS 200 = AGUARDANDO DEMISSÃO
	$REClt = mysql_query("SELECT * FROM rh_clt WHERE status = '200' AND id_regiao = '$regiao' ORDER BY nome ASC");
	$verifica_aguardo = mysql_num_rows($REClt);
	if(!empty($verifica_aguardo)) { ?>
    
<table width="95%" align="center" border="0" cellpadding="8" cellspacing="0" style="margin-top:20px;">
    <tr bgcolor="#999999">
      <td colspan="6" class="show">
          <span style="color:#F90; font-size:32px;">&#8250;</span>
          Participantes aguardando a Rescis&atilde;o
      </td>
    </tr>
    <tr class="novo_tr">
      <td width="6%">COD</td>
      <td width="35%">NOME</td>
      <td width="20%">PROJETO</td>
      <td width="20%">UNIDADE</td>
      <td width="19%">CARGO</td>
    </tr>
    <?php
	while($row_clt = mysql_fetch_array($REClt)) {
		
		$Curs -> MostraCurso($row_clt['id_curso']);
		$NomeCurso = $Curs -> nome;
		
		$ClasPro -> MostraProjeto($row_clt['id_projeto']);
		$NomeProjeto = $ClasPro -> nome;
	
		//-- ENCRIPTOGRAFANDO A VARIAVEL
		$link = encrypt("$regiao&$row_clt[0]"); 
		$link2 = str_replace("+","--",$link);
		// -----------------------------
	?>
    <tr style="background-color:<?php if($alternateColor++%2!=0) { echo "#F0F0F0"; } else { echo "#FDFDFD"; } ?>">
        <td><?=$row_clt['campo3']?></td>
        <td><a href="<?php echo "recisao.php?tela=2&enc=$link2"; ?>"><?=$row_clt['nome']?></a></td>
        <td><?=$NomeProjeto?></td>
        <td><?=$row_clt['locacao']?></td>
        <td><?=$NomeCurso?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>
<br />
<table width="95%"  align="center" border="0" cellpadding="8" cellspacing="0">
  <tbody>
    <tr bgcolor="#999999">
      <td colspan="5"  class="show">
      	<span class="seta" style='color:#F90; font-size:32px;'>&#8250;</span> Participantes Desativados
      </td>
    </tr>
    <tr class="novo_tr">
      <td width="6%">COD</td>
      <td width="35%">NOME</td>
      <td width="26%">PROJETO</td>
      <td width="20%" align="center">DATA</td>
      <td width="13%" align="center">RESCIS&Atilde;O</td>
    </tr>
    <?php
	$cont = "1";
	# S&Oacute; PEGA OS CLTS QUE EST&Atilde;O PEDIRAM DEMISS&Atilde;O, OU FORAM DISPENSADOS STATUS 200 = AGUARDANDO DEMISS&Atilde;O
	$REClt = mysql_query("SELECT *, date_format(data_saida, '%d/%m/%Y')as data_saida2 FROM rh_clt WHERE status IN ('60','61','62','63','80') AND id_regiao = '$regiao' ORDER BY nome");
	
	while ($row_clt = mysql_fetch_array($REClt)){
		
		$Curs -> MostraCurso($row_clt['id_curso']);
		$NomeCurso = $Curs -> nome;
		
		$ClasPro -> MostraProjeto($row_clt['id_projeto']);
		$NomeProjeto = $ClasPro -> nome;
		
		if($cont1 % 2){ $classcor="corfundo_um"; }else{ $classcor="corfundo_dois"; };
		
		$re_resc = mysql_query("SELECT *,date_format(data_demi, '%d/%m/%Y')AS data_demi2 FROM rh_recisao WHERE id_clt = '$row_clt[0]'");
		$row_resc = mysql_fetch_array($re_resc);
		
		//-- ENCRIPTOGRAFANDO A VARIAVEL
		$link = encrypt("$regiao&$row_clt[0]&1&$row_resc[0]"); 
		$link2 = str_replace("+","--",$link);
		// -----------------------------
		
		//-- ENCRIPTOGRAFANDO A VARIAVEL
		$link4 = encrypt("$regiao&$row_clt[0]"); 
		$link24 = str_replace("+","--",$link4);
		// -----------------------------

		$linkA4 = "recisao.php?tela=2&enc=$link24";
		#<a href='$linkA4'>
		
		if(mysql_num_rows($re_resc) == 0){
			$linkA = "<img src='../../imagens/pdf.gif' border='0' style=\"opacity:0.2; filter:alpha(opacity=20)\">";
		}else{
			$linkA = "<a href=\"../arquivos/recisaopdf/rescisao_".$row_clt['0']."_1.pdf\" class=\"link\" target=\"_blank\">
			<img src='../../imagens/pdf.gif' border='0'></a>";
		} ?>
	
	<tr style="background-color:<?php if($alternateColor++%2!=0) { echo "#F0F0F0"; } else { echo "#FDFDFD"; } ?>">
        <td align='left'><a href='recisao.php?tela=5&enc=<?=$link2?>'>.</a><?=$row_clt['campo3']?></td>
        <td><?=$row_clt['nome']?></td>
        <td align='left'><?=$NomeProjeto?></td>
        <td align='center'><?=$row_resc['data_demi2']?></td>
        <td align='center'><?=$linkA?></td>
	</tr>
	<?php $cont ++;
   
   	} // END WHILE USUÁRIOS
      
	?>
  </tbody>
</table>

<?php 
break;
case 2:

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$teste = explode("&",$link);
$regiao = $teste[0];
$idclt = $teste[1];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA

$Clt -> MostraClt($idclt);

$nome = $Clt -> nome;
$codigo = $Clt -> campo3;
$data_demi = $Clt -> data_demi;
$contratacao = $Clt -> tipo_contratacao;
$data_aviso_previo = $Clt -> data_aviso;


$data_exp = explode("-", $data_demi);
if($data_exp[2] >= 30){
	$dias_trab = 30;
}else{
	$dias_trab = $data_exp[2];
}

$mes_demissao = $data_exp[1];

$dias_trab = (int)$dias_trab;
$mes_demissao = (int)$mes_demissao;

//CALCULANDO SALDO FGTS
$REFGTS = mysql_query("SELECT SUM(salliquido)as liquido FROM rh_folha_proc WHERE id_clt = '$idclt' AND status = '3'");
$RowFGTS = mysql_fetch_array($REFGTS);
$FGTS = $RowFGTS[0] * 0.08;

$FGTS = number_format($FGTS,2,",",".");

$data_demiF = $Fun -> ConverteData($data_demi);

?>

<table width="95%"  align="center" border="0" cellpadding="0" cellspacing="0" bgcolor="#f5f5f5">
  <tr>
    <td>
    
    <br />
    <form action="recisao.php" name="form1" method="post" onsubmit="return validaForm()">
    <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px" bgcolor="#FFFFFF">
    <tr>
    
    <td height="27" colspan="2" class="show" align="center"><?=$codigo." - ".$nome?></td>
    </tr>
    <tr>
      <td width="32%" height="30" class="secao">Tipo de Dispensa:</td>
      <td width="68%" align="left">&nbsp;&nbsp;
        <select name="dispensa" id="dispensa">
          <?php $REDispensa = mysql_query("SELECT codigo,especifica FROM rhstatus WHERE tipo = 'recisao' ORDER BY codigo ASC");
				 while($RowDispensa = mysql_fetch_array($REDispensa)){
					   echo '<option value="'.$RowDispensa['codigo'].'">'.$RowDispensa['codigo'].' - '.$RowDispensa['especifica'].'</option>';
				   } ?>
        </select></td>
      </tr>
    <tr>
      <td height="30" class="secao">Fator:</td>
      <td>&nbsp;&nbsp;
        <select id="fator" name="fator">
          <option value="empregado">empregado</option>
          <option value="empregador">empregador</option>
        </select>
        </td>
    </tr>
    <tr>
      <td height="30" class="secao">Dias de Saldo do Sal&aacute;rio:</td>
      <td>&nbsp;&nbsp;<input name="diastrab" type="text" id="diastrab" value="<?=$dias_trab?>" size="2" maxlength="2"> 
        dias&nbsp;&nbsp;<span class="red">(data para demissão: <?=$data_demiF?>)</span></td>
      </tr>
    <tr>
      <td height="30" class="secao"><span class="red">REMUNERA&Ccedil;&Atilde;O PARA FINS RECIS&Oacute;RIOS:</span></td>
      <td>&nbsp;&nbsp;
        <input name="valor" type="text" id="valor" onkeydown="FormataValor(this,event,17,2)" value="0,00" size="13"/></td>
    </tr>
    <tr>
      <td height="30" class="secao">Aviso pr&eacute;vio:</td>
      <td><div  style="float:left">&nbsp;&nbsp;
        <select id="aviso" name="aviso">
          <option value="indenizado">indenizado</option>
          <option value="trabalhado">trabalhado</option>
          </select>
        &nbsp;&nbsp;&nbsp;&nbsp;</div>
        <div id="divaviso" style="float:left">
          <input name="previo" type="text" id="previo" size="2" maxlength="2" />
          dias de indeniza&ccedil;&atilde;o ou dias de trabalho
          </div>
        </td>
    </tr>
    <tr>
      <td height="30" class="secao">Data do Aviso:</td>
      <td>&nbsp;&nbsp;<input type="text" id="data_aviso" name="data_aviso" size="9"
      						 onkeyup="mascara_data(this); pula(10,this.id,devolucao.id)" /></td>
    </tr>
    <tr>
      <td class="secao">Devolu&ccedil;&atilde;o de Cr&eacute;dito Indevido:</td>
      <td>&nbsp;&nbsp;<input name="devolucao" id="devolucao" size="6" onkeydown="FormataValor(this,event,17,2)" /></td>
    </tr>
    <tr>
      <td height="30" class="secao">Previs&atilde;o do Saldo FGTS:</td>
      <td>&nbsp;&nbsp;R$ 
        <input name="fgts" type="text" id="fgts" size="13" onkeydown="FormataValor(this,event,17,2)" value="<?=$FGTS?>"/></td>
    </tr>
    <tr>
      <td height="30" class="secao">Incluir FGTS do m&ecirc;s anterior:</td>
      <td>
      &nbsp;&nbsp;<label><input type="radio" name="anterior" id="anterior" value="1"/>Sim</label>
      &nbsp;&nbsp;
      &nbsp;&nbsp;<label><input type="radio" name="anterior" id="anterior" value="0" checked/>Não</label>
      </td>
    </tr>
    <tr>
      <td height="38" colspan="2" align="center" valign="middle">
       <table width="50%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input type="submit" value="Avançar" class="botao" align="center"/></td>
          <td><a href="recisao.php?tela=1&regiao=<?=$regiao?>" class="botao">Cancelar</a></td>
          </tr>
      </table></td>
      </tr>
    </table>
    <input type="hidden" name="tela" id="tela" value="3" />
    <input type="hidden" name="idclt" id="idclt" value="<?=$idclt?>" />
    <input type="hidden" name="regiao" id="regiao" value="<?=$regiao?>" />
    </form>
<script language="javascript">
function validaForm(){
d = document.form1;
	
	if (d.valor.value == "" ){
	alert("O campo Valor deve ser preenchido!");
	d.valor.focus();
	return false;
	}
	
return true;   
}
    </script>
    <br />
    <br /></td>
  </tr>
</table>

<?php
break;

case 3:

$idclt		= $_REQUEST['idclt'];
$regiao		= $_REQUEST['regiao'];
$fator		= $_REQUEST['fator'];
$dispensa	= $_REQUEST['dispensa'];
$diastrab	= $_REQUEST['diastrab'];
$aviso		= $_REQUEST['aviso'];
$previo		= $_REQUEST['previo'];
$fgts		= $_REQUEST['fgts'];
$anterior	= $_REQUEST['anterior'];
$valor		= $_REQUEST['valor'];
$data_aviso	= implode('-', array_reverse(explode('/', $_REQUEST['data_aviso'])));
$devolucao 	= $_REQUEST['devolucao'];
$devolucao = str_replace(".","",$devolucao);
$devolucao = str_replace(",",".",$devolucao);
$restatus = mysql_query("SELECT especifica FROM rhstatus WHERE codigo = '$dispensa'");
$status = mysql_fetch_array($restatus);

$Clt -> MostraClt($idclt);

$nome = $Clt -> nome;
$codigo = $Clt -> campo3;
$data_demi = $Clt -> data_demi;
$data_entrada = $Clt -> data_entrada;   
$idprojeto = $Clt -> id_projeto;
$idcurso = $Clt -> id_curso;
$idregiao = $Clt -> id_regiao;



//--------------------------------------------------------------------






$data_demiF = $Fun -> ConverteData($data_demi);
$data_entradaF = $Fun -> ConverteData($data_entrada);
$data_proc = date('Y-m-d H:i:s');


$UltSal = mysql_query("SELECT salbase,max(mes) FROM rh_folha_proc WHERE id_clt = '$idclt'");
$UltSal = mysql_fetch_array($UltSal);

#$SalBase = $UltSal['salbase'];			NAO É MAIS O ULTIMO SALARIO

if($valor == "0,00"){
	
	$Curs -> MostraCurso($idcurso);
	$SalBase = $Curs -> salario;
	
}else{
	
	$valor = str_replace(".","",$valor);
	$valor = str_replace(",",".",$valor);
	$SalBase = $valor;
	
}

// TRABALHANDO COM AS DATAS
$data_exp = explode("-", $data_demi);
$data_adm = explode("-", $data_entrada);

$dia_demissao = $data_exp[2];
$dia_demissao = (int)$dia_demissao;
$mes_demissao = $data_exp[1];
$mes_demissao = (int)$mes_demissao;
$ano_demissao = $data_exp[0];

$ano_admissao = $data_adm[0];
$ano_admissao = (int)$ano_admissao;
$mes_admissao = $data_adm[1];
$mes_admissao = (int)$mes_admissao;
$dia_admissao = $data_adm[2];
$dia_admissao = (int)$dia_admissao;
//------------------------------

// VERIFICANDO SE O FUNCIONÁRIO POSSUIU 1 ANO DE CONTRATAÇÃO
$completa_ano = date('Y-m-d', mktime(0,0,0, $mes_admissao,  $dia_admissao , $ano_admissao + 1));
if(date('Y-m-d') >= $completa_ano){
	// SIM O FUNCIONÁRIO TEM + DE UM ANO
	$um_ano = '1';
}else{
	// NÃO O FUNCIONÁRIO TEM - DE UM ANO
	$um_ano = '0';
}
// ---- END VERIFICA 1 ANO

#60= com Justa Causa, 61 = Sem Justa Causa, 62 = por outros motivos, 81 = obito



#--------------------------------
#		COM JUSTA CAUSA
#--------------------------------
if($dispensa == 60){
					# 0 = NÃO, 1 = SIM, 2 = PAGA, 3 = DEPOSITADO
	$t_ss = '1';	#SALDO SALARIO
	$t_ap = '0';	#AVISO PREVIO
	
	/* if($um_ano == 1){	#SE O FUNCIONARIO TIVER + D 1 ANO, VAI VERIFICAR FÉRIAS VENCIDAS
		$t_fv = '1';	#FERIAS VENCIDAS
	}else{
		$t_fv = '0';	#FERIAS VENCIDAS
	} */
	
	$t_fv = '1';
	$t_fp = '0';	#FERIAS PROPORCIONAIS
	
	//if($um_ano == 1){
		$t_fa = '1';	#FERIAS 1/3 ADICIONAL
	//}else{
	//	$t_fa = '0';	#FERIAS 1/3 ADICIONAL
	//}
	
	$t_13 = '0';	#DECIMO TERCEIRO
	$t_dtss = '0';   #DECIMO TERCEIRO SOBRE SOLDO DE SALARIO
	$t_f8 = '3';	#FGTS 8
	$t_f4 = '0';	#FGTS MULTA 40
	$t_mu = '0';	#MULTA ART 479
	
	$cod_saq_fgts = 'H';



#--------------------------------
#		DISPENSA SEM JUSTA CAUSA ANTECIPADO FIM CONTRATO EMPREGADOR
#--------------------------------
}elseif($dispensa == 64){

	$t_ss = '1';	#SALDO SALARIO
	$t_ap = '1';	#AVISO PREVIO
	$t_fv = '1';	#FERIAS VENCIDAS
	$t_fp = '1';	#FERIAS PROPORCIONAIS
	$t_fa = '1';	#FERIAS 1/3 ADICIONAL
	$t_13 = '1';	#DECIMO TERCEIRO
	$t_dtss = '0';  #DECIMO TERCEIRO SOBRE SOLDO DE SALARIO
	$t_f8 = '3';	#FGTS 8
	$t_f4 = '1';	#FGTS MULTA 40
	$t_mu = '1';	#MULTA ART 479
	$cod_saq_fgts = '01';



#--------------------------------
#		DISPENSA SEM JUSTA CAUSA
#--------------------------------
}elseif($dispensa == 65){

	$t_ss = '1';	#SALDO SALARIO
	$t_ap = '1';	#AVISO PREVIO
	$t_fv = '1';	#FERIAS VENCIDAS
	$t_fp = '1';	#FERIAS PROPORCIONAIS
	$t_fa = '1';	#FERIAS 1/3 ADICIONAL
	$t_13 = '1';	#DECIMO TERCEIRO
	$t_dtss = '0';  #DECIMO TERCEIRO SOBRE SOLDO DE SALARIO
	$t_f8 = '3';	#FGTS 8
	$t_f4 = '1';	#FGTS MULTA 40
	$t_mu = '1';	#MULTA ART 479
	$cod_saq_fgts = '01';



#--------------------------------
#		SEM JUSTA CAUSA
#--------------------------------
}elseif($dispensa == 61){
					# 1 = SIM, 0 = NÃO
	$t_ss = '1';	#SALDO SALARIO
	$t_ap = '1';	#AVISO PREVIO
	$t_fv = '1';	#FERIAS VENCIDAS
	$t_fp = '1';	#FERIAS PROPORCIONAIS
	$t_fa = '1';	#FERIAS 1/3 ADICIONAL
	$t_13 = '1';	#DECIMO TERCEIRO
	$t_dtss = '1';  #DECIMO TERCEIRO SOBRE SOLDO DE SALARIO
	$t_f8 = '1';	#FGTS 8
	$t_f4 = '1';	#FGTS MULTA 40
	$t_mu = '0';	#MULTA ART 479
	
	$cod_saq_fgts = '11';
	
	if($fator == "empregado"){
		
	  /*if($um_ano == 0){
			$t_fa = '0';	#FERIAS 1/3 ADICIONAL
		}*/
		$t_f4 = '0';	#FGTS MULTA 40
		$t_f8 = '3';	#FGTS 8
		$t_13 = '1';	#DECIMO TERCEIRO
		$t_dtss = '0';   #DECIMO TERCEIRO SOBRE SOLDO DE SALARIO
		
		$cod_saq_fgts = 'J';
		
		if($aviso == "indenizado"){
			$t_ap = '2';	#AVISO PREVIO (PAGA)
		}else{
			$t_ap = '1';	#AVISO PREVIO (PEDIDO PELO EMPREGADO, ELE TRABALHADA)
		}
		
	}
	
	if($fator == "empregador"){
		if($aviso != "indenizado"){
			$t_dtss = '0';
		}
	}
		

	
#--------------------------------
#		POR ÓBITO
#--------------------------------	
}elseif($dispensa == 62 or $dispensa == 81){
					# 1 = SIM, 0 = NÃO
	$t_ss = '1';	#SALDO SALARIO
	$t_ap = '1';	#AVISO PREVIO
	$t_fv = '1';	#FERIAS VENCIDAS
	$t_fp = '1';	#FERIAS PROPORCIONAIS
	$t_fa = '1';	#FERIAS 1/3 ADICIONAL
	$t_13 = '1';	#DECIMO TERCEIRO
	$t_dtss = '1';   #DECIMO TERCEIRO SOBRE SOLDO DE SALARIO
	$t_f8 = '1';	#FGTS 8
	$t_f4 = '1';	#FGTS MULTA 40
	$t_mu = '0';	#MULTA ART 479
	
	$cod_saq_fgts = '11';



#------------------------------------
# PEDIDO DE DISPENSA ANTES DO PRAZO
#------------------------------------
}elseif($dispensa == 63){

	$t_ss = '1';	#SALDO SALARIO
	$t_ap = '0';	#AVISO PREVIO //2
	
	if($fator == "empregador"){
		$t_ap = '1';	#AVISO PREVIO
	}
	
	/* if($um_ano == 1){	#SE O FUNCIONARIO TIVER + D 1 ANO, VAI VERIFICAR FÉRIAS VENCIDAS
		$t_fv = '1';	#FERIAS VENCIDAS
	}else{
		$t_fv = '0';	#FERIAS VENCIDAS
	} */
	$t_fv = '1';
	$t_fp = '1';	#FERIAS PROPORCIONAIS
	$t_fa = '1';	#FERIAS 1/3 ADICIONAL
	$t_13 = '1';	#DECIMO TERCEIRO
	$t_dtss = '0';   #DECIMO TERCEIRO SOBRE SOLDO DE SALARIO
	$t_f8 = '3';	#FGTS 8
	$t_f4 = '1';	#FGTS MULTA 40
	$t_mu = '1';	#MULTA ART 479
	
	$cod_saq_fgts = '01';
}

//----------- PEGANDO TODOS OS LANÇAMENTOS COMO SEMPRE PARA INCREMENTAR NO SÁLARIO BASE -------------//
$result_sempre = mysql_query("SELECT * FROM rh_movimentos_clt WHERE tipo_movimento = 'CREDITO' AND id_clt = '$idclt' AND status = '1' AND lancamento = '2'");
		  
while($row_sempre = mysql_fetch_array($result_sempre)){
	$cred_exp = explode(",",$row_sempre['incidencia']);
	$cont_cred_exp = count($cred_exp);
			  
	#-- ACRESCENTA OS VALORES MARCADOS COMO SEMPRE NA BASE DE INSS,IRRF E FGTS
	for($i=0; $i <= $cont_cred_exp; $i++){
		if($cred_exp[$i] == 5020){
			$salario_calc_inss = $salario_calc_inss + $row_sempre['valor_movimento'];
		}
			if($cred_exp[$i] == 5021){
			$salario_calc_IR = $salario_calc_IR + $row_sempre['valor_movimento'];
		}
		if($cred_exp[$i] == 5023){
			$salario_calc_FGTS = $salario_calc_FGTS + $row_sempre['valor_movimento'];
		}
	}
	
	#-- NOVO SALARIO BASE ( SALÁRIO BASE + TODOS OS MOVIMENTOS MARCADOS COMO SEMPRE )
	if($valor == "0,00"){
	     $SalBase = $SalBase + $row_sempre['valor_movimento'];
		
	}
	#--
		  
		  
	$total_rendi = $total_rendi + $row_sempre['valor_movimento'];
		  
	$AR_rendimentos[] = $row_sempre['cod_movimento'];
	$AR_rendimentosva[] = $row_sempre['valor_movimento'];
	
	$AR_rendsempre[] = $row_sempre['valor_movimento'];
		  
	unset($cred_exp);
	unset($cont_cred_exp);
}
	  
// ---------------------------------- FIM DOS LANÇAMENTOS MARCADOS COMO SEMPRE

if($AR_rendsempre == ''){
	$AR_rendsempre[] = '0';
}

//




// --- INSALUBRIDADE / PERICULOSIDADE --
	
	$Calc -> insalubridade($idclt,$data_demi);
	$valor_insalubridade = $Calc -> valor;
	//$valor_p_dia = $valor_insalubridade / 30;
	//$valor_insalubridade = $valor_p_dia * $diastrab;
	unset($valor_p_dia);
// ------------





//-- SALDO DE SALARIO
//$valor_salario_dia = $SalBase / 30;
$valor_salario_dia = ($SalBase - array_sum($AR_rendsempre)) / 30;
$saldo_de_salario  = $valor_salario_dia * $diastrab;

$data_base = $data_exp[0].'-'.$data_exp[1].'-'.$data_exp[2];

// Calculando Pervidência
$Calc -> MostraINSS($saldo_de_salario+$valor_insalubridade,$data_base);
$previ_ss = $Calc -> valor;

if($t_ss == 1) {

	//-----------CALCULANDO INSS SOBRE SALDO DE SALARIOS-------------------
	$Calc -> MostraINSS($saldo_de_salario,$data_exp);
	$inss_saldo_salario = $Calc -> valor;
	//
	
	$base_irrf_saldo_salarios = $saldo_de_salario - $inss_saldo_salario - $previ_ss + $valor_insalubridade;
	
	//-----------CALCULANDO IRRF SOBRE SALDO DE SALARIOS-------------------
	$Calc -> MostraIRRF($base_irrf_saldo_salarios,$idclt,$idprojeto,$data_base);
	$irrf_saldo_salario = $Calc -> valor;
	//
	

$to_saldo_salario	= $saldo_de_salario - $inss_saldo_salario - $irrf_saldo_salario;
$to_descontos 		= $irrf_saldo_salario + $inss_saldo_salario;
$to_rendimentos 	= $saldo_de_salario + $terceiro_ss;

}else{
$to_saldo_salario = 0;
}
//---- END SALDO DE SALARIO

if($dispensa == 63) {
	$art_480 = ($SalBase / 30) * ((abs((int)floor((strtotime("$data_entrada +45 day") - strtotime($data_demi)) / 86400)) - 1) / 2);
	if($fator == 'empregado') {
		$art_480_desc = $art_480;
	} elseif($fator == 'empregador') {
		$art_480_rend = $art_480;
	}
}

// AVISO PRÉVIO ---
if($aviso == "indenizado" and $t_ap == 2){
	$aviso_previo = "PAGO pelo funcionário";
	//$aviso_previo_valor_d = $SalBase - $valor_insalubridade; 			# valor desconto
	$aviso_previo_valor_d = $SalBase;
	#$aviso_previo_valor_d = 456.5; 			# valor desconto
}elseif($aviso == "indenizado" and $t_ap == 1){
	$aviso_previo = "indenizado";
	
	$aviso_previo_valor_a = $SalBase; 			# valor acréscimo Padrão, sem calculos
	
	if($dispensa == 63 or $dispensa == 64){
		
		$pri = $data_demi;
		$seg = date('Y-m-d', mktime(0,0,0,$mes_admissao,$dia_admissao + 90,$ano_admissao));
		#VERIFICANDO A QUANTIDADE DE DIAS QUE FALTAM PARA TERMINAR O AVISO PRÉVIO
		#EX: FOI DEMITIDO EM 01/01/2009 E O FIM DOS 90 DIAS SERIA 10/01/2009 ENTÃO FALTARAM 9 DIAS
		$re = mysql_query("SELECT data FROM ano WHERE data > '$pri' AND data < '$seg'");
		$dias = mysql_num_rows($re);
		#OS 9 DIAS Q FALTARAM SÃO DIVIDOS POR 2
		$qntD = $dias/2;
		$valordia = $SalBase / 30;
		#É PAGO O VALOR DIÁRIO VEZES OS 9 DIAS Q FALTARAM DIVIDOS POR 2
		$art_479 = ($SalBase / 30) * $qntD ;		
		# valor acréscimo, calculando pois saiu antes do periodo do contrato
		$aviso_previo_valor_a = 0;
	}
	
	
	
}elseif($aviso == "trabalhado" and $t_ap == 1){
	$aviso_previo = "trabalhado até $data_aviso_previo";
	// $data_fim_avprevio  = date('Y-m-d', mktime(0,0,0, $mes_demissao, $dia_demissao + $previo,$ano_demissao));
	// $aviso_previo      .= $Fun -> ConverteData($data_fim_avprevio);
}elseif($t_ap == 0){
	$aviso_previo = "Não recebe";
}

$to_descontos   = $to_descontos   + $aviso_previo_valor_d;
$to_rendimentos = $to_rendimentos + $aviso_previo_valor_a;
//---- END AVISO PRÉVIO

$total_outros_descontos = aviso_previo_valor_d + $devolucao + $art_480_desc;

// DÉCIMO TERCEIRO (DT)
if($t_13 == 1) {

# -----------------------------------------------------------------|
#   VERIFICANDO SE O CLT FOI CONTRATADO NO MESMO ANO DA DEMISSÃO   |
# -----------------------------------------------------------------|
#        A VARIAVEL $MESES_ATIVO_DT VAI SERVIR SÓ PARA DT          |
# -----------------------------------------------------------------|

if($idclt == 4137) {
	
	$dia_quinze		= 15;
	$meses_ativo_dt = 2;
	
} else {

//2009 == 2009
if($ano_admissao == $ano_demissao) {
	
	//12 == 12
	if($mes_demissao == $mes_admissao) {		//VERIFICANDO SE O CLT FOI CONTRATADO NO MESMO MES
	
		if(date('t', mktime(0,0,0,$mes_demissao,$dia_demissao,$ano_demissao)) != 31) { 
			$dia_quinze = 15; 
		} else { 
			$dia_quinze = 16;
		}
		
		if($dia_demissao >= $dia_quinze){
			$meses_ativo_dt = 1;
		}else{
			$meses_ativo_dt = 0;
		}
	
	// 12 != 11
	} else {
		
		if(date('t', mktime(0,0,0,$mes_demissao,$dia_demissao,$ano_demissao)) != 31) {
			$dia_quinze = 15;
		} else {
		 	$dia_quinze = 16;
		}
		
		if($dia_demissao >= $dia_quinze) {
			$meses_ativo_dt = $mes_demissao - $mes_admissao + 1; 
		} else {
			$meses_ativo_dt = $mes_demissao - $mes_admissao;
		}
		
	}
	
} else {
	
	if(date('t', mktime(0,0,0,$mes_demissao,$dia_demissao,$ano_demissao)) != 31) { 
		$dia_quinze = 15; 
	} else { 
		$dia_quinze = 16; 
	}
	
	if($dia_demissao >= $dia_quinze){
		$meses_ativo_dt = $mes_demissao;
	} else {
		$meses_ativo_dt = $mes_demissao - 1; 
	}
	
}
	
	
}

$dt_valor_base_mes = $SalBase / 12;
$valor_td = $dt_valor_base_mes * $meses_ativo_dt;
$previ_dt = $valor_td * 0.08;

	//-----------CALCULANDO INSS SOBRE DÉCIMO TERCEIRO-------------------
	$Calc -> MostraINSS($valor_td,$data_exp);
	$valor_td_inss = $Calc -> valor;
	//-------------------------
	
	$base_irrf_td = $valor_td - $valor_td_inss;
	
	//-----------CALCULANDO IRRF SOBRE DÉCIMO TERCEIRO-------------------
	$Calc -> MostraIRRF($base_irrf_td,$idclt,$idprojeto,$data_demi);
	$valor_td_irrf = $Calc -> valor;
	//-------------------------

$total_dt = $valor_td - $valor_td_inss - $valor_td_irrf;
$to_descontos = $to_descontos + $valor_td_inss + $valor_td_irrf;
$to_rendimentos = $to_rendimentos + $valor_td;

} else {
	
	$total_dt    = 0;
	$meses_ativo = 0;

}
//------ END DÉCIMO TERCEIRO




//--- CALCULOS DE FÉRIAS VENCIDAS
if($t_fv == 1){
// Verifica Direito de Férias
$qr_verifica_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$idclt' ORDER BY id_ferias DESC");
$verifica_ferias = mysql_fetch_assoc($qr_verifica_ferias);
$total_verifica_ferias = mysql_num_rows($qr_verifica_ferias);

if(empty($total_verifica_ferias)) {
	$aquisitivo_ini = $data_entrada;
	$preview10 = explode("-", $data_entrada);
	$aquisitivo_end = date('Y-m-d', mktime(0,0,0, $preview10[1] , $preview10[2], $preview10[0] + 1));
} else {
	$preview10 = explode("-", $data_entrada);
	$aquisitivo_ini = date('Y-m-d', mktime(0,0,0, $preview10[1], $preview10[2], $preview10[0] + $total_verifica_ferias));
	$aquisitivo_end = date('Y-m-d', mktime(0,0,0, $preview10[1], $preview10[2], $preview10[0] + ($total_verifica_ferias + 1)));
}

// Ver se é férias dobrada!
$aquisito_final = explode("-", $aquisitivo_end);
$verifica_dobrado = date('Y-m-d', mktime(0,0,0, $aquisito_final[1] , $aquisito_final[2], $aquisito_final[0] + 1));

//-- ENCRIPTOGRAFANDO A VARIAVEL
$link = encrypt("$regiao&3&$idclt&0");
$linkferias = str_replace("+","--",$link);
//-- ENCRIPTOGRAFANDO A VARIAVEL

// Buscando Faltas

// Verificando Períodos Gozados
$qr_periodos = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$idclt' ORDER BY id_ferias ASC");
while($periodos = mysql_fetch_assoc($qr_periodos)) {
	$periodos_gozados[] = "$periodos[data_aquisitivo_ini]/$periodos[data_aquisitivo_fim]";
}
//

// Verificando Períodos Aquisitivos, Períodos Vencidos e Período Proporcional
$data_entrada2 = explode('-', $data_entrada);
$ano_data_entrada = $data_entrada2[0];
$mes_data_entrada = $data_entrada2[1];
$dia_data_entrada = $data_entrada2[2];

$quantidade_anos = date('Y') - $ano_data_entrada;

for($a=0; $a<$quantidade_anos-1; $a++) {

	$aquisitivo_inicio = date('Y-m-d', mktime('0','0','0', $mes_data_entrada, $dia_data_entrada, $ano_data_entrada + $a));
	$aquisitivo_final = date('Y-m-d', mktime('0','0','0', $mes_data_entrada, $dia_data_entrada - 1, $ano_data_entrada + $a + 1));
	
	if($aquisitivo_final > $data_demi) {
		$periodo_aquisitivo = $aquisitivo_inicio.'/'.$data_demi;
		$periodos_aquisitivos[] = $aquisitivo_inicio.'/'.$data_demi;
	} else {
		$periodo_aquisitivo = $aquisitivo_inicio.'/'.$aquisitivo_final;
		$periodos_aquisitivos[] = $aquisitivo_inicio.'/'.$aquisitivo_final;
	}
	
	if(!in_array($periodo_aquisitivo, $periodos_gozados) and $aquisitivo_final < $data_demi) {
		$periodos_vencidos[] = $aquisitivo_inicio.'/'.$aquisitivo_final;
	} elseif($aquisitivo_final >= $data_demi) {
		$periodo_proporcional[] = $aquisitivo_inicio.'/'.$data_demi;
	}

}
//

// Buscando ultimo periodo vencido
$ultimo_periodo_vencido = count($periodos_vencidos) - 1;
$ultimo_periodo_vencido = $periodos_vencidos[$ultimo_periodo_vencido];
$ultimo_periodo_vencido = explode('/', $ultimo_periodo_vencido);
//


$falta_aquisitivo_ini = explode("-", $ultimo_periodo_vencido[0]);
$falta_aquisitivo_end = explode("-", $ultimo_periodo_vencido[1]);

if($falta_aquisitivo_ini[1] == 12) {
	$limite_falta1 = "mes_mov = '$falta_aquisitivo_ini[1]'";
} else {
	$limite_falta1 = "mes_mov >= '$falta_aquisitivo_ini[1]'";
}

if($falta_aquisitivo_end[1] == 1) {
	$limite_falta2 = "mes_mov = '$falta_aquisitivo_ini[1]'";
} else {
	$limite_falta2 = "mes_mov <= '$falta_aquisitivo_ini[1]'";
}

$qr_faltas1 = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$idclt' AND id_mov = '62' AND (status = '1' or status = '5') AND $limite_falta1 AND ano_mov = '$falta_aquisitivo_ini[0]'");
$row_faltas1 = mysql_fetch_array($qr_faltas1);

$qr_faltas2 = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$idclt' AND id_mov = '62' AND (status = '1' or status = '5') AND $limite_falta2 AND ano_mov = '$falta_aquisitivo_end[0]'");
$row_faltas2 = mysql_fetch_array($qr_faltas2);
	
	$faltas = $row_faltas1['faltas'] + $row_faltas2['faltas'];
	$faltas_real = $row_faltas1['faltas'] + $row_faltas2['faltas'];
	
	if($faltas <= 5) {
		$qnt_dias_NOVOfv = 30;
	} elseif($faltas >= 6 and $faltas <= 14) {
		$qnt_dias_NOVOfv = 24;
	} elseif($faltas >= 15 and $faltas <= 23) {
		$qnt_dias_NOVOfv = 18;
	} elseif($faltas >= 24 and $faltas <= 32) {
		$qnt_dias_NOVOfv = 12;
	} elseif($faltas > 32) {
		$qnt_dias_NOVOfv = 0;
	} else {
		$qnt_dias_NOVOfv = 30;
	}
	
//
unset($falta_aquisitivo_ini, $falta_aquisitivo_end, $limite_falta1, $limite_falta2, $qr_faltas1, $qr_faltas2, $row_faltas1, $row_faltas2, $faltas, $faltas_real);
//
	

$falta_aquisitivo_ini = explode("-", $aquisitivo_ini);
$falta_aquisitivo_end = explode("-", $data_demi);

if($falta_aquisitivo_ini[1] == 12) {
	$limite_falta1 = "mes_mov = '$falta_aquisitivo_ini[1]'";
} else {
	$limite_falta1 = "mes_mov >= '$falta_aquisitivo_ini[1]'";
}

if($falta_aquisitivo_end[1] == 1) {
	$limite_falta2 = "mes_mov = '$falta_aquisitivo_ini[1]'";
} else {
	$limite_falta2 = "mes_mov <= '$falta_aquisitivo_ini[1]'";
}

$qr_faltas1 = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$idclt' AND id_mov = '62' AND (status = '1' or status = '5') AND $limite_falta1 AND ano_mov = '$falta_aquisitivo_ini[0]'");
$row_faltas1 = mysql_fetch_array($qr_faltas1);

$meio_faltas = NULL;

for($ano = $falta_aquisitivo_ini[0]+1; $ano < $falta_aquisitivo_end[0]; $ano++) {
	$qr_faltas2 = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$idclt' AND id_mov = '62' AND (status = '1' or status = '5') AND ano_mov = '$ano'");
	$row_faltas2 = mysql_fetch_array($qr_faltas2);
	$meio_faltas += $row_faltas2['faltas'];
}

$qr_faltas3 = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$idclt' AND id_mov = '62' AND (status = '1' or status = '5') AND $limite_falta2 AND ano_mov = '$falta_aquisitivo_end[0]'");
$row_faltas3 = mysql_fetch_array($qr_faltas3);
	
	$faltas = $row_faltas1['faltas'] + $meio_faltas + $row_faltas3['faltas'];
	$faltas_real = $row_faltas1['faltas'] + $meio_faltas + $row_faltas3['faltas'];
	
	if($faltas <= 5) {
		$qnt_dias_fv = 30;
	} elseif($faltas >= 6 and $faltas <= 14) {
		$qnt_dias_fv = 24;
	} elseif($faltas >= 15 and $faltas <= 23) {
		$qnt_dias_fv = 18;
	} elseif($faltas >= 24 and $faltas <= 32) {
		$qnt_dias_fv = 12;
	} elseif($faltas > 32) {
		$qnt_dias_fv = 0;
	} else {
		$qnt_dias_fv = 30;
	}
//-----------------------------------------------------


if($verifica_dobrado <= $data_demi) {
	echo '<br><div class="nota_vermelha" align="center">';
	echo "IMPOSSIVEL CONTINUAR POIS O FUNCIONÁRIO POSSUI MAIS DE UMA FÉRIAS VENCIDAS, FAVOR ACERTAR SUA SITUAÇÃO NA TELA DE FÉRIAS";
	echo '</div><br><br><center>';
	echo '<a href="../ferias/ferias.php?enc='.$linkferias.'" class="botao">Processar Férias</a>';
	echo '<br /><a href="javascript:history.go(-1)" class="botao">Voltar</a></center>';
	echo '</body></html>';
	exit;
} elseif($aquisitivo_end <= $data_demi) {
	$ferias_vencidas 		= "sim";
	$style_ferias_vencidas 	= '';
	$fv_valor_base 			= ($SalBase / 30) * $qnt_dias_NOVOfv;
	$fv_um_terco			= $fv_valor_base / 3;
	$fv_total = $fv_valor_base + $fv_um_terco;
} elseif($aquisitivo_end > date('Y-m-d')) {
	$ferias_vencidas 		= "não";
	$fv_valor_base 			= 0;
	$fv_um_terco			= 0;
}


}else{
$fv_total = 0;
}
// FIM DE FÉRIAS VENCIDAS


// FÉRIAS PROPORCIONAIS
if($idclt == 4137) {
	
	$dia_quinze		= 15;
	$meses_ativo_fp = 2;
	
	$fp_valor_mes 	= ($SalBase / 30) * $qnt_dias_fv;
	$fp_valor_total = ($fp_valor_mes  / 12) * $meses_ativo_fp;
	
	if($t_fa == 1) {
		$fp_um_terco = $fp_valor_total / 3;
		$fp_total = $fp_valor_total + $fp_um_terco;
	} else {
		$fp_total = $fp_valor_total;
	}
	
} else {

if($t_fp == 1){
	
	if(date('t', mktime(0,0,0,$mes_demissao,$dia_demissao,$ano_demissao)) != 31) {
		$dia_quinze = 15;
	} else { 
		$dia_quinze = 16;
	}
	
	// 2009 == 2009
	if($ano_demissao == $ano_admissao){
		
		// 12 == 12
		if($mes_admissao == $mes_demissao){
			
			if($dia_demissao >= $dia_quinze) {
				$meses_ativo_fp = 1;
			} else {
				$meses_ativo_fp = 0;
			}
			
		// 2009-05-01 e 2009-10-01
		} else {
			
			$meses_ativo_fp = $mes_demissao - $mes_admissao;
			
		}
		
	// 2008 == 2009
	} else {
		
		// 05 = 05
		if($mes_demissao == $mes_admissao) {
			
			if($dia_demissao >= $dia_quinze) {
				$meses_ativo_fp = 15;
			} else {
				$meses_ativo_fp = 16;
			}
				
		} else {
			
			// 2008-05-01 com 2009-10-01
			// 2008-09-01 com 2009-01-01
			if($mes_admissao < $mes_demissao) {

					$meses_ativo_fp = $mes_demissao - $mes_admissao + 1; 
				
			} else {
				
				if($dia_demissao >= $dia_quinze) {
					$meses_ativo_fp = (12 - $mes_admissao) + $mes_demissao + 1;	
				} else {
					$meses_ativo_fp = (12 - $mes_admissao) + $mes_demissao;
				}
				
			}
		}
	
	}
	
	if(($dispensa == 61 or $aviso == 'indenizado') and $meses_ativo_fp != 12 and $fator == 'empregador') {
		$meses_ativo_fp += 1;
	}
	
	$fp_valor_mes 	= ($SalBase / 30) * $qnt_dias_fv;
	$fp_valor_total = ($fp_valor_mes  / 12) * $meses_ativo_fp;
	
	if($t_fa == 1) {
		$fp_um_terco = $fp_valor_total / 3;
		$fp_total = $fp_valor_total + $fp_um_terco;
	} else {
		$fp_total = $fp_valor_total;
	}
	
} else {
	
$fp_total = 0;

}

}
// --------------------------

$ferias_total = $fp_total + $fv_total;
$to_rendimentos = $to_rendimentos + $fv_valor_base + $fp_valor_total + $fp_um_terco + $fv_um_terco;
	
	#DIA 22/12/2009 - JR DISSE Q NÃO TEM
	//-----------CALCULANDO IRRF SOBRE FÉRIAS------------------- 
	$Calc -> MostraIRRF($ferias_total,$idclt,$idprojeto,date('Y-m-d'));
	$ferias_irrf = $Calc -> valor;
	//$ferias_irrf = 0;
	//-------------------------
	
	//-----------CALCULANDO INSS SOBRE FÉRIAS------------------- 
	#$Calc -> MostraINSS($ferias_total,$data_exp);
	#$ferias_inss = $Calc -> valor;
	$ferias_inss = 0;
	//-------------------------
	

$ferias_total_final = $ferias_total - $ferias_irrf;
$to_descontos = $to_descontos + $ferias_irrf;

//---- FIM FÉRIAS




//--- OUTROS ---
	
	// --- SALARIO FAMILIA --
	$Calc -> Salariofamilia($SalBase,$idclt,$idprojeto,$data_demi,'2');    // $valor_td = valor do saldo de salário
	$valor_sal_familia 	= $Calc -> valor;
	$total_menor		= $Calc -> filhos_menores;
	
	$valor_p_dia = $valor_sal_familia / 30;
	$valor_sal_familia = $valor_p_dia * $diastrab;
	unset($valor_p_dia);
	// ------------
	
	
	// --- ADICIONAL NOTURNO --
	$Calc -> adnoturno($idclt,''); 
	$valor_adnoturno = $Calc -> valor;
	
	$valor_p_dia = $valor_adnoturno / 30;
	$valor_adnoturno = $valor_p_dia * $diastrab;
	unset($valor_p_dia);
	// ------------
	

	// --- ATRASO DE RESCISÃO ---
	
	if($aviso == "indenizado" and $fator == "empregado" and $dispensa != 63){
		
		$demissao_10 = date('Y-m-d', mktime(0,0,0,$data_exp[1],$data_exp[2] + 10,$data_exp[0]));
		
		if(date('Y-m-d') <= $demissao_10){
			$valor_atrazo = 0;
		}else{
			#$valor_atrazo = $SalBase;
			$valor_atrazo = 0;
		}
	}

	// ------------
	
	// -- DÉCIMO TERCEIRO SOBRE SALDO DE SALARIO --
	if($t_dtss == 1){
		$num_ss = 1;
		$terceiro_ss = $SalBase / 12;
	}else{
		$terceiro_ss = 0;
		$num_ss = 0;
	}
	//-------------------------
	
	
	// OUTROS Q NÃO SÃO UTILIZADOS AINDA
	$valor_comissao		= 0;
	$valor_grativicacao	= 0;
	$valor_horaextra	= 0;
	$valor_outro		= 0;
	//---------
	
	// OUTROS LANÇAMENTOS ----------
	$result_events = mysql_query("SELECT distinct(descicao),cod,id_mov FROM rh_movimentos WHERE incidencia = 'FOLHA' AND cod != '7001' 
	AND cod != '5022' AND cod != '5049' AND cod != '5021' AND cod != '5019'");

	while($row_events = mysql_fetch_array($result_events)){
		
		$result_total_evento = mysql_query("SELECT SUM(valor_movimento)as valor FROM rh_movimentos_clt WHERE id_mov = '$row_events[id_mov]' 
		AND mes_mov = '16' AND status = '1' AND id_clt = '$idclt'");
		$row_total_evento = mysql_fetch_array($result_total_evento);
		
		$debitos_tab = array('5019','5020','5021','6004','7003','8000','7009','5020','5020','5021','5021','5021','5020','9500','5030','5031','5032');
		$rendimentos_tab = array('5011','5012','5022','6006','6007','9000','5022','5024');
		
		if (in_array($row_events['cod'], $debitos_tab)) { 
			$debito = $row_total_evento['valor'];
			$rendimento = "";
		}else{
			$debito = "";
			$rendimento = $row_total_evento['valor'];
		}
		
		if($row_events['cod'] == "5024"){
			$sal_familia_anterior = $row_total_evento['valor'];
		}
		
		//SOMANDO VARIAVEIS
		$re_tot_desconto = $re_tot_desconto + $debito;
		$re_tot_rendimento = $re_tot_rendimento + $rendimento;
		
		//LIMPANDO VARIAVEIS
		$desconto = "";
		$rendimento = "";
				
	}
	
	
# -- TOTAIS GERAIS --
$total_outros = $valor_sal_familia + $valor_adnoturno + $valor_insalubridade + $valor_atrazo + $terceiro_ss + $re_tot_rendimento + $art_479 + $art_480_rend;

$total_descontos = $to_descontos + $re_tot_desconto + $previ_ss + $previ_dt + $devolucao + $art_480_desc;

$to_rendimentos = $to_rendimentos + $total_outros;

unset($re_tot_desconto);
unset($re_tot_rendimento);
// -- END OUTROS


// -- FGTS 8%
if($t_f8 == 1){
	$fgts8_total = $fgts;
	$mensagem_fgts8 = "Recebe";
}elseif($t_f8 == 2){
	$fgts8_total = 0;
}elseif($t_f8 == 3){
	$fgts8_total = $fgts;
	$mensagem_fgts8 = "Depositado";
}

// -- END FGTS 8%
if($t_f4 == 1){
	$fgts4_total = 0;
}else{
	$fgts4_total = 0;
}

// -- FGTS 40%


// -- END FGTS 40%

//SOMA TUDO

$valor_recisao_final = $to_rendimentos - $total_descontos;

if($valor_recisao_final < 0) {
	$arredondamento_positivo = abs($valor_recisao_final);
	$valor_recisao_final = NULL;
	$to_rendimentos = $to_rendimentos + $arredondamento_positivo;
} else {
	$arredondamento_positivo = NULL;
	$valor_recisao_final = $to_rendimentos - $total_descontos;
}

//--- FORMATANDO AS VARIAVEIS
$arredondamento_positivoF = number_format($arredondamento_positivo,2,",","."); 
$saldo_de_salarioF = number_format($saldo_de_salario,2,",",".");
$inss_saldo_salarioF = number_format($inss_saldo_salario,2,",",".");
$irrf_saldo_salarioF = number_format($irrf_saldo_salario,2,",",".");
$terceiro_ssF = number_format($terceiro_ss,2,",",".");
$base_irrf_saldo_salariosF = number_format($base_irrf_saldo_salarios,2,",",".");
$to_saldo_salarioF = number_format($to_saldo_salario,2,",",".");

$valor_tdF = number_format($valor_td,2,",",".");
$valor_td_inssF = number_format($valor_td_inss,2,",",".");
$valor_td_irrfF = number_format($valor_td_irrf,2,",",".");
$base_irrf_tdF = number_format($base_irrf_td,2,",",".");
$total_dtF = number_format($total_dt,2,",",".");

$fv_valor_baseF = number_format($fv_valor_base,2,",",".");
$fv_um_tercoF = number_format($fv_um_terco,2,",",".");
$fp_valor_totalF = number_format($fp_valor_total,2,",",".");
$fp_um_tercoF = number_format($fp_um_terco,2,",",".");
$ferias_totalF = number_format($ferias_total,2,",",".");
$ferias_irrfF = number_format($ferias_irrf,2,",",".");
$ferias_total_finalF = number_format($ferias_total_final,2,",",".");

$valor_sal_familiaF = number_format($valor_sal_familia,2,",",".");
$valor_adnoturnoF = number_format($valor_adnoturno,2,",",".");
$valor_insalubridadeF = number_format($valor_insalubridade,2,",",".");
$valor_atrazoF = number_format($valor_atrazo,2,",",".");
$valor_comissaoF = number_format($valor_comissao,2,",",".");
$valor_grativicacaoF = number_format($valor_grativicacao,2,",",".");
$valor_horaextraF = number_format($valor_horaextra,2,",",".");
$valor_outroF = number_format($valor_outro,2,",",".");

$aviso_previo_valor_dF = number_format($aviso_previo_valor_d,2,",",".");
$outros_descontosF = number_format($outros_descontos,2,",",".");
$aviso_previo_valor_aF = number_format($aviso_previo_valor_a,2,",",".");

$fgts8_totalF = number_format($fgts8_total,2,",",".");
$fgts4_totalF = number_format($fgts4_total,2,",",".");

$total_outrosF = number_format($total_outros,2,",",".");
$total_descontosF = number_format($total_descontos,2,",",".");
$to_rendimentosF = number_format($to_rendimentos,2,",",".");
$valor_recisao_finalF = number_format($valor_recisao_final,2,",",".");
$devolucaoF = number_format($devolucao, 2, ",", ".");
$total_outros_descontosF = number_format($total_outros_descontos, 2, ",", ".");

//=-------------------------------
//--- FORMATANDO AS VARIAVEIS PARA O ARQUIVO TXT
$saldo_de_salarioT = number_format($saldo_de_salario,2,".","");
$inss_saldo_salarioT = number_format($inss_saldo_salario,2,".","");
$irrf_saldo_salarioT = number_format($irrf_saldo_salario,2,".","");
$terceiro_ssT = number_format($terceiro_ss,2,".","");
$base_irrf_saldo_salariosT = number_format($base_irrf_saldo_salarios,2,".","");
$to_saldo_salarioT = number_format($to_saldo_salario,2,".","");
$previ_ssT = number_format($previ_ss,2,".","");
$valor_sal_familia_totT = number_format($valor_sal_familia + $sal_familia_anterior,2,".","");

$valor_tdT = number_format($valor_td,2,".","");
$valor_td_inssT = number_format($valor_td_inss,2,".","");
$valor_td_irrfT = number_format($valor_td_irrf,2,".","");
$base_irrf_tdT = number_format($base_irrf_td,2,".","");
$total_dtT = number_format($total_dt,2,".","");
$previ_dtT = number_format($previ_dt,2,".","");

$fv_valor_baseT = number_format($fv_valor_base,2,".","");
$fv_um_tercoT = number_format($fv_um_terco,2,".","");
$fp_valor_totalT = number_format($fp_valor_total,2,".","");
$fp_um_tercoT = number_format($fp_um_terco,2,".","");
$ferias_totalT = number_format($ferias_total,2,".","");
$ferias_inssT = number_format($ferias_inss,2,".","");
$ferias_irrfT = number_format($ferias_irrf,2,".","");
$ferias_total_finalT = number_format($ferias_total_final,2,".","");

$valor_sal_familiaT = number_format($valor_sal_familia,2,".","");
$valor_adnoturnoT = number_format($valor_adnoturno,2,".","");
$valor_insalubridadeT = number_format($valor_insalubridade,2,".","");
$valor_atrazoT = number_format($valor_atrazo,2,".","");
$valor_comissaoT = number_format($valor_comissao,2,".","");
$valor_grativicacaoT = number_format($valor_grativicacao,2,".","");
$valor_horaextraT = number_format($valor_horaextra,2,".","");
$valor_outroT = number_format($valor_outro,2,".","");

if($t_ap == "2"){
	$aviso_previo_valorT = number_format($aviso_previo_valor_d,2,".","");
}else{
	$aviso_previo_valorT = number_format($aviso_previo_valor_a,2,".","");
}



$fgts8_totalT = number_format($fgts8_total,2,".","");
$fgts4_totalT = number_format($fgts4_total,2,".","");

$total_outrosT = number_format($total_outros,2,".","");
$to_rendimentosT = number_format($to_rendimentos,2,".","");
$to_descontosT = number_format($total_descontos,2,".","");
$valor_recisao_finalT = number_format($valor_recisao_final,2,".","");


$UltSalF = number_format($SalBase,2,",",".");
$UltSalT = number_format($SalBase,2,".","");

$devolucaoT = number_format($devolucao, 2, ".", "");

//=-------------------------------

for($i=0; $i < count($AR_rendimentos); $i ++){
	$a_rendimentos .= $AR_rendimentos[$i].",";
	$a_rendimentosva .= $AR_rendimentosva[$i].",";
}

//-- SELECIONANDO A DISPENSA SELECIONADA PARA GRAVAR NA TABELA EVENTOS
$reev = mysql_query("SELECT * FROM rhstatus WHERE codigo = '$dispensa'");
$rowev = mysql_fetch_array($reev);
// -------------

// ---  ARQUIVO TXT -- 

$txt1 = "INSERT INTO rh_recisao(id_clt,nome,id_regiao,id_projeto,id_curso,data_adm,data_demi,data_proc,dias_saldo,um_ano,meses_ativo,";
$txt1 .= "motivo,fator,aviso,aviso_valor,dias_aviso,data_fim_aviso,fgts8,fgts40,fgts_anterior,fgts_cod,sal_base,saldo_salario,inss_ss,";
$txt1 .= "ir_ss,terceiro_ss,previdencia_ss,dt_salario,inss_dt,ir_dt,previdencia_dt,ferias_vencidas,umterco_fv,ferias_pr,umterco_fp";
$txt1 .= ",inss_ferias,ir_ferias,sal_familia,to_sal_fami,ad_noturno,insalubridade,a479,a477,comissao,gratificacao,extra,outros,movimentos";
$txt1 .= ",valor_movimentos,total_rendimento,total_deducao,total_liquido,arredondamento_positivo,avos_dt,avos_fp,data_aviso,devolucao,user,folha) VALUES ";
$txt1 .= "('$idclt','$nome','$idregiao','$idprojeto','$idcurso','$data_entrada','$data_demi','$data_proc','$diastrab','$um_ano','$meses_ativo'";
$txt1 .= ",'$dispensa','$fator','$aviso','$aviso_previo_valorT','$previo','$data_fim_avprevio','$fgts8_totalT','$fgts4_totalT','$anterior',";
$txt1 .= "'$cod_saq_fgts','$UltSalT','$saldo_de_salarioT','$inss_saldo_salarioT','$irrf_saldo_salarioT','$terceiro_ssT','$previ_ssT','$valor_tdT',";
$txt1 .= "'$valor_td_inssT','$valor_td_irrfT','$previ_dtT','$fv_valor_baseT','$fv_um_tercoT','$fp_valor_totalT','$fp_um_tercoT','$ferias_inssT'";
$txt1 .= ",'$ferias_irrfT','$valor_sal_familiaT','$valor_sal_familia_totT','$valor_adnoturnoT','$valor_insalubridadeT','$art_479','$valor_atrazoT',";
$txt1 .= "'$valor_comissaoT','$valor_grativicacaoT','$valor_horaextraT','$valor_outroT','$a_rendimentos','$a_rendimentosva','$to_rendimentosT',";
$txt1 .= "'$to_descontosT','$valor_recisao_finalT','$arredondamento_positivo','$meses_ativo_dt','$meses_ativo_fp','$data_aviso','$devolucaoT','$user','0');\r\n";

$txt1 .= "UPDATE rh_clt SET status = '$dispensa', data_saida = '$data_demi', status_demi = '1' WHERE id_clt = '$idclt' LIMIT 1;\r\n";
$txt1 .= "INSERT INTO rh_eventos(id_clt,id_regiao,id_projeto,nome_status,cod_status,id_status,data,status) VALUES ";
$txt1 .= "('$idclt','$idregiao','$idprojeto','$rowev[especifica]','$dispensa','$rowev[0]','$data_demi','1')LIMIT 1;\r\n";

$conteudo = $txt1;

$data_procT = date('dmY');

$nome_arquivo = "recisao_".$idclt."_".$data_procT.".txt";
$arquivo = "/home/ispv/public_html/intranet/rh/arquivos/".$nome_arquivo;

//TENTA ABRIR O ARQUIVO TXT
if (!$abrir = fopen($arquivo, "wa+")) {
	echo "Erro abrindo arquivo ($arquivo)";
	exit;
}

//ESCREVE NO ARQUIVO TXT
if (!fwrite($abrir, $conteudo)) {
	print "Erro escrevendo no arquivo ($arquivo)";
	exit;
}

//FECHA O ARQUIVO
fclose($abrir);



//-- ENCRIPTOGRAFANDO A VARIAVEL
$link = encrypt("$regiao&$idclt");
$linkvolt = str_replace("+","--",$link);

$linkir = encrypt("$regiao&$idclt&$nome_arquivo");
$linkir = str_replace("+","--",$linkir);
// -----------------------------
?>
<table width="95%"  align="center" border="0" cellpadding="0" cellspacing="0" bgcolor="#f5f5f5">
  <tr>
    <td><br />
      <form action="acao.php" method="post" name="Form" id="Form">
        <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px" bgcolor="#FFFFFF">
          <tr>
            <td height="27" colspan="4" class="show" align="center"><?=$codigo." - ".$nome?></td>
          </tr>
          <tr>
            <td width="24%" height="30" class="secao">Data de Admiss&atilde;o:</td>
            <td width="21%" height="30">&nbsp;&nbsp;<span style="font-size:16px"><?=$data_entradaF?></span></td>
            <td width="22%" height="30" class="secao">Data de Demiss&atilde;o:</td>
            <td width="33%" height="30">&nbsp;&nbsp;<span style="font-size:16px"><?=$data_demiF?></span></td>
          </tr>
          <tr>
            <td height="30" class="secao">Motivo do Afastamento: </td>
            <td height="30">&nbsp;&nbsp;<span class="red"><?=$dispensa." - ".$status['especifica']?></span></td>
            <td height="30" class="secao">Salario base de c&aacute;lculo: </td>
            <td height="30">&nbsp;&nbsp;R$ <span style="font-size:16px" class="red"><b><?=number_format($SalBase,2,",",".")?></b></span></td>
          </tr>
          <tr>
            <td height="30" class="secao">Fator:</td>
            <td height="30">&nbsp;&nbsp;<?=$fator?></td>
            <td height="30" class="secao">Aviso pr&eacute;vio:</td>
            <td height="30">&nbsp;<?=$aviso_previo?></td>
          </tr>
          <tr>
            <td height="51" colspan="2" align="center" >
            <span style="color:blue">RENDIMENTOS: R$ <?=$to_rendimentosF?></span>
            <br />
            <span class="red">DESCONTOS: R$ <?=number_format($total_descontos,2,",",".")?></span></td>
            <td height="51" colspan="2" align="center" >
            <span style="font-size:14px" class="red"><b>Total a ser pago:&nbsp;<?=$valor_recisao_finalF?></b></span><br />
             <?php if(!empty($arredondamento_positivo)) {
						echo "<span style='font-size:14px' class='red'><b>Arredondamento Positivo:&nbsp;$arredondamento_positivoF</b></span>";
				   } ?></td>
          </tr>
          <tr>
            <td height="23" colspan="4"><div class="divisor">Sal&aacute;rios</div></td>
          </tr>
          <tr>
            <td height="28" class="secao">Saldo de sal&aacute;rio (<?=$diastrab?>/30):</td>
            <td height="28">&nbsp;&nbsp;
            R$ <?=$saldo_de_salarioF?></td>
            <td height="28" class="secao">INSS sobre salários:</td>
            <td height="28">&nbsp;&nbsp;R$ <?=$inss_saldo_salarioF?></td>
          </tr>
          <tr>
            <td height="28" class="secao">IRRF sobre salários:</td>
            <td height="28">&nbsp;&nbsp;R$ <?=$irrf_saldo_salarioF?></td>
            <td height="28" class="secao">Previd&ecirc;ncia:</td>
            <td height="28">&nbsp;&nbsp;R$ <?=number_format($previ_ss,2,",",".")?></td>
          </tr>
          <tr>
            <td height="29" colspan="4" align="center">( base = R$
              <?=$saldo_de_salarioF?>
- INSS R$
<?=$inss_saldo_salarioF?>
= R$
<?=$base_irrf_saldo_salariosF?>
)</td>
          </tr>
          <tr>
            <td height="38" colspan="4" align="center"><span style="font-size:14px" class="red"><b>R$ <?=$to_saldo_salarioF?></b></span></td>
          </tr>
          <tr>
            <td height="23" colspan="4" <?=$style_dt?>><div class="divisor">Décimo terceiro</div></td>
          </tr>
          <tr <?=$style_dt?>>
            <td height="28" class="secao">Décimo terceiro proporcional (<?=$meses_ativo_dt?>/12):</td>
            <td height="28">&nbsp;&nbsp;R$ <?=$valor_tdF?></td>
            <td height="28" class="secao">INSS:</td>
            <td height="28">&nbsp;&nbsp;R$ <?=$valor_td_inssF?></td>
          </tr>
          <tr <?=$style_dt?>>
            <td height="28" class="secao">IRRF:</td>
            <td height="28">&nbsp;&nbsp;R$ <?=$valor_td_irrfF?></td>
            <td height="28" class="secao">Previd&ecirc;ncia</td>
            <td height="28"> &nbsp;&nbsp;R$ <?=number_format($previ_dt,2,",",".")?></td>
          </tr>
          <tr <?=$style_dt?>>
            <td height="28" colspan="4" align="center" valign="middle">&nbsp;( base = R$
              <?=$valor_tdF?>
- INSS R$
<?=$valor_td_inssF?>
= R$
<?=$base_irrf_tdF?>
)</td>
          </tr>
          <tr <?=$style_dt?>>
            <td height="38" colspan="4" align="center" valign="middle"><span style="font-size:14px" class="red"><b>R$ <?=$total_dtF?></b></span></td>
          </tr>
          <tr>
            <td height="21" colspan="4" align="left" valign="middle"><div class="divisor">Férias</div></td>
          </tr>
          <tr <?=$style_ferias_vencidas?> <?=$style_fv?>>
            <td height="28" valign="middle" class="secao" >Férias vencidas:</td>
            <td height="28" valign="middle">&nbsp;&nbsp;R$ <?=$fv_valor_baseF?></td>
            <td height="28" valign="middle" class="secao">1/3 sobre férias vencidas:</td>
            <td height="28" valign="middle">&nbsp;&nbsp;R$ <?=$fv_um_tercoF?></td>
          </tr>
          <tr <?=$style_fp?>>
            <td height="28" valign="middle" class="secao">Férias proporcionais (<?=$meses_ativo_fp?>/12): </td>
            <td height="28" valign="middle">&nbsp;&nbsp;R$ <?=$fp_valor_totalF?></td>
            <td height="28" valign="middle" class="secao">1/3 sobre férias proporcionais:</td>
            <td height="28" valign="middle">&nbsp;&nbsp;R$ <?=$fp_um_tercoF?></td>
          </tr>
          <tr>
            <td height="28" valign="middle" class="secao">INSS sobre férias:</td>
            <td height="28" valign="middle">&nbsp;&nbsp;R$ 0,00</td>
            <td height="28" valign="middle" class="secao">IRRF sobre férias:</td>
            <td height="28" valign="middle">&nbsp;&nbsp;R$ <?=$ferias_irrfF?></td>
          </tr>
          <tr>
            <td height="28" valign="middle" class="secao">Faltas no Período:</td>
            <td height="28" valign="middle">&nbsp;&nbsp;<?=$faltas?></td>
            <td height="28" valign="middle" class="secao">Quantidade de Dias:</td>
            <td height="28" valign="middle">&nbsp;&nbsp;<?=$qnt_dias_fv?></td>
          </tr>
          <tr>
            <td height="38" colspan="4" valign="middle" align="center"><span style="font-size:14px" class="red"><b>R$ <?=$ferias_total_finalF?></b></span></td>
          </tr>
          <tr>
            <td height="21" colspan="4" valign="middle"><div class="divisor">Outros vencimentos</div></td>
          </tr>
          <tr>
            <td height="28" valign="middle" class="secao">Sal&aacute;rio familia:</td>
            <td height="28" valign="middle">&nbsp;&nbsp;R$ <?=$valor_sal_familiaF?></td>
            <td height="28" valign="middle" class="secao">Adicional noturno:</td>
            <td height="28" valign="middle">&nbsp;&nbsp;R$ <?=$valor_adnoturnoF?></td>
          </tr>
          <tr>
            <td height="28" valign="middle" class="secao">Comiss&otilde;es: </td>
            <td height="28" valign="middle">&nbsp;&nbsp;R$&nbsp;<?=$valor_comissaoF?></td>
            <td height="28" valign="middle"class="secao">Gratifica&ccedil;&otilde;es:</td>
            <td height="28" valign="middle">&nbsp;&nbsp;R$&nbsp;<?=$valor_grativicacaoF?></td>
          </tr>
          <tr>
            <td height="28" valign="middle" class="secao">Horas extras:</td>
            <td height="28" valign="middle">&nbsp;&nbsp;R$&nbsp;<?=$valor_horaextraF?></td>
            <td height="28" valign="middle"class="secao">Insalubridade / Periculosidade:</td>
            <td height="28" valign="middle"> &nbsp;&nbsp;R$ <?=$valor_insalubridadeF?></td>
          </tr>
          <tr>
            <td height="28" valign="middle" class="secao">Atraso de rescis&atilde;o (477):</td>
            <td height="28" valign="middle">&nbsp;&nbsp;R$&nbsp;<?=$valor_atrazoF?></td>
            <td height="28" valign="middle"class="secao">Outros:</td>
            <td height="28" valign="middle">&nbsp;&nbsp;R$&nbsp;<?=$valor_outroF?></td>
          </tr>
          <tr>
            <td height="28" valign="middle" class="secao">Aviso Prévio:</td>
            <td height="28" valign="middle" align="left">&nbsp;&nbsp;R$&nbsp;<?=$aviso_previo_valor_aF?></td>
            <td height="28" valign="middle" class="secao">13&ordm; Saldo Indenizado (<?=$num_ss?>/12):</span></td>
            <td height="28" valign="middle" align="left">&nbsp;&nbsp;R$ <?=$terceiro_ssF?></td>
          </tr>
          <tr>
            <td height="38" valign="middle" class="secao">Indeniza&ccedil;&atilde;o Artigo 479:</td>
            <td height="38" valign="middle" align="left">&nbsp;&nbsp;R$&nbsp;<?=number_format($art_479,2,",",".")?></td>
            <?php if(!empty($art_480_rend)) { ?>
            <td height="38" valign="middle" class="secao">Indeniza&ccedil;&atilde;o Artigo 480:</td>
            <td height="38" valign="middle" align="left">&nbsp;&nbsp;R$&nbsp;<?=number_format($art_480_rend,2,",",".")?></td>
            <?php } else { ?>
            <td height="38" valign="middle" class="secao"></td>
            <td height="38" valign="middle" align="left">&nbsp;&nbsp;</td>
            <?php } ?>
          </tr>
          <tr>
            <td height="38" colspan="4" valign="middle" align="center"><table width="95%" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td height="30" colspan="4" align="center" valign="middle" class="show">Outros Eventos</td>
              </tr>
              <tr class="novo_tr_dois">
                <td width="11%" height="21" align="center" valign="middle">Evento</td>
                <td width="45%" align="left" valign="middle">Descri&ccedil;&atilde;o</td>
                <td width="21%" align="right" valign="middle">Rendimentos</td>
                <td width="23%" align="right" valign="middle">Descontos</td>
              </tr>
              
              <?php
		$cont = "0";
		$result_events = mysql_query("SELECT distinct(descicao),cod,id_mov FROM rh_movimentos WHERE incidencia = 'FOLHA' 
		AND cod != '5022' AND cod != '5049' AND cod != '5021' AND cod != '5019'");

		while($row_events = mysql_fetch_array($result_events)){
		
		if($cont % 2){ $color = "corfundo_dois"; } else { $color = "corfundo_um"; }
		$marg = "<div style='margin-right:5;'>";
		
		$result_total_evento = mysql_query("SELECT SUM(valor_movimento)as valor FROM rh_movimentos_clt WHERE id_mov = '$row_events[id_mov]' 
		AND mes_mov = '16' AND status = '1' AND id_clt = '$idclt'");
		$row_total_evento = mysql_fetch_array($result_total_evento);
		
		if(mysql_num_rows($result_total_evento) != 0){
		
			$debitos_tab = array('5019','5020','5021','6004','7003','8000','7009','5020','5020','5021','5021','5021','5020','9500','5030','5031','5032');
			$rendimentos_tab = array('5011','5012','5022','6006','6007','9000','5022','5024');
			
			if (in_array($row_events['cod'], $debitos_tab)) { 
				$debito = $row_total_evento['valor'];
				$rendimento = "";
			}else{
				$debito = "";
				$rendimento = $row_total_evento['valor'];
			}
			
			if($rendimento == 0 and $debito == 0){
				$disable = "style='display:none'";
			}else{
				$disable = "style='display:'";
			}
			
			echo "<tr class=\"novalinha $color\" $disable>";
			echo "<td height='18' align='center' valign='middle'>$row_events[cod]</td>";
			echo "<td align='left' valign='middle'>$row_events[descicao]</td>";
			echo "<td align='right' valign='middle'><b>";
			if($rendimento != ''){ echo number_format($rendimento,2,",","."); }
			echo "</b></td>";
			echo "<td align='right' valign='middle'><b>$marg";
			if($debito != ''){ echo number_format($debito,2,",",".");}
			echo "</div></b></td></tr>";
			
			//SOMANDO VARIAVEIS
			$re_tot_desconto = $re_tot_desconto + $debito;
			$re_tot_rendimento = $re_tot_rendimento + $rendimento;
		}else{
			$re_tot_desconto = 0;
			$re_tot_rendimento = 0;
		}
			
		//LIMPANDO VARIAVEIS
		$desconto = "";
		$rendimento = "";
		
		$cont ++;
        }
		
		//FORMATANDO TOTAIS POR EVENTO
		$re_tot_rendimentoF = number_format($re_tot_rendimento,2,",",".");
		$re_tot_descontoF = number_format($re_tot_desconto,2,",",".");
		
        ?>
              <tr class="novo_tr_dois">
                <td colspan="2" align="center" valign="middle">TOTAIS</td>
                <td height="20" align="right" valign="middle" style="text-align:right"><?=$re_tot_rendimentoF?></td>
                <td align="right" valign="middle" style="text-align:right"><span style="margin-right:5;">
                  <?=$re_tot_descontoF?>
                </span></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td height="38" colspan="4" valign="middle" align="center"><span style="font-size:14px" class="red"><b>R$ <?=$total_outrosF?></b></span></td>
          </tr>
          <tr >
            <td height="12" colspan="4" valign="middle"><div class="divisor">Outros descontos</div></td>
          </tr>
          <tr>
            <td height="38" class="secao">Aviso Prévio pago pelo Funcion&aacute;rio:</td>
            <td height="38">&nbsp;&nbsp;R$ <?=$aviso_previo_valor_dF?></td>
            <td height="38" class="secao">Devolu&ccedil;&atilde;o:</td>
            <td height="38">&nbsp;&nbsp;<?=$devolucaoF?></td>
          </tr>
          <?php if(!empty($art_480_desc)) { ?>
          <tr>
            <td height="38" valign="middle" class="secao">Indeniza&ccedil;&atilde;o Artigo 480:</td>
            <td height="38" valign="middle" align="left">&nbsp;&nbsp;R$&nbsp;<?=number_format($art_480_desc,2,",",".")?></td>
            <td height="38" class="secao"></td>
            <td height="38">&nbsp;&nbsp;</td>
          </tr>
          <?php } ?>
          <tr>
            <td height="38" colspan="4" valign="middle" align="center"><span style="font-size:14px" class="red"><b>R$ <?=$total_outros_descontosF?></b></span></td>
          </tr>
          <tr>
            <td height="12" colspan="4" valign="middle"><div class="divisor">FGTS</div></td>
          </tr>
          <tr style="display:none;">
            <td height="38" valign="middle" class="secao">FGTS 8%:</td>
            <td height="38" valign="middle">&nbsp;&nbsp;R$ <?=$fgts8_totalF?> (<?=$mensagem_fgts8?>)</td>
            <td height="38" valign="middle" class="secao">FGTS 40%:</td>
            <td height="38" valign="middle">&nbsp;&nbsp;R$ <?=$fgts4_totalF?></td>
          </tr>
          <tr>
            <td height="38" class="secao">Código de Saque:</td>
            <td height="38">&nbsp;&nbsp;<?=$cod_saq_fgts?></td>
            <td height="38">&nbsp;</td>
            <td height="38"><a href="<?=$nome_arquivo?>">.</a></td>
          </tr>
          <tr>
            <td height="38" colspan="4" align="center" valign="middle"><table width="50%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><a href="recisao.php?tela=4&enc=<?=$linkir?>" class="botao">Processar Rescis&atilde;o</a></td>
                <td><a href="recisao.php?tela=2&enc=<?=$linkvolt?>" class="botao">Voltar</a></td>
              </tr>
            </table></td>
          </tr>
        </table>

      </form>
      <br />
      <br /></td>
  </tr>
</table>
<br />

<?php
break;

case 4:

// RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc0 = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc0);
$link = decrypt($enc); 

$teste = explode("&",$link);
$regiao = $teste[0];
$idclt = $teste[1];
$arquivo = $teste[2];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA
	
	$file = "/home/ispv/public_html/intranet/rh/arquivos/".$arquivo;
	
	$fp = file($file);				// LE O ARQUIVO PARA DENTRO DE UM ARRAY
		
	$i = "0";
	foreach($fp as $linha){				// LE CADA LINHA DO ARQUIVO
	
		mysql_query($linha);			// EXECUTANDO UMA QUERY PRA CADA LINHA
		$i ++;
		$idi[] = mysql_insert_id();
	}
	
	
	// ENCRIPTOGRAFANDO A VARIAVEL
	$link = encrypt("$regiao&$idclt&$arquivo&$idi[0]"); 
	$link2 = str_replace("+","--",$link);
	//
	
	print "<script>location.href=\"recisao.php?tela=5&enc=$link2\"</script>";

break;

case 5:

	echo "<a href='recisaopdf.php?enc=".$_GET['enc']."' class='botao'>Termo de Recisão</a> ";



break;
}
// END TELA
?>
</div>
</body>
</html>