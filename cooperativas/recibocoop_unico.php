<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a>";
exit;
}

include "../conn.php";
include "../funcoes.php";
include "../classes/regiao.php";
include "../classes/cooperativa.php";
include "../classes/cooperado.php";
include "../classes/curso.php";


//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$decript = explode("&",$link);

$regiao = $decript[0];
$folha = $decript[1];
$id_coop = $_GET['coop'];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA

$REFolha = mysql_query("SELECT *,date_format(data_inicio, '%d/%m/%Y')as data_inicio2, date_format(data_inicio, '%Y')as ano, 
date_format(data_fim, '%d/%m/%Y')as data_fim2,date_format(data_proc, '%d/%m/%Y')as data_proc2 FROM folhas where id_folha = '$folha'");
$RowFolha = mysql_fetch_array($REFolha);

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$MesInt = (int)$RowFolha['mes'];
$Nomemes = $meses[$MesInt];
$ano = $RowFolha['ano'];
//SELECIONANDO OS CLTS JA CADASTRADOS NA TAB FOLHA_PROC QUE ESTEJAM COM STATUS 2 = SELECIONADO ANTERIORMENTE
$REFolha_pro = mysql_query("SELECT * FROM folha_cooperado where id_folha = '$folha' and id_autonomo = '$id_coop' AND status = '3' ORDER BY nome ASC");
$num_clt_pro = mysql_num_rows($REFolha_pro);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<title>::: RECIBO ::::</title>
<style type="text/css">
<!--
body {
	margin:0;
	background-color: #FFFFFF;
	font-family:"Courier New", Courier, monospace;
	font-size:12px;
}
h1 {
	page-break-after: always 
}
-->
</style>
</head>
<body>
<table width="98%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" align="center">
  <tr>
    <td width="67%" align="center" valign="top">
    <br />
    <?php
	$cont = 0;
	//AKI COMEÇA O WHILE PARA MOSTRAR CADA FUNCIONARIO
	while($RowP = mysql_fetch_array($REFolha_pro)){

	//LOOP PARA EXIBIR  2 VIAS		
	for($i=0;$i<2;$i++) :		
		
		
	//REUNINDO DADOS DO PARTICIPANTE
	$idParticipante = $RowP['id_autonomo'];
	$participante = new cooperado();
	$participante -> MostraCoop($idParticipante);
	$id_cooperativa = $participante -> id_cooperativa;
	
	$nomeP = $participante -> nome;
	$nomeP = str_split($nomeP, 30);
	$campo3 = $participante -> campo3;
	$campo3 = sprintf("%03s",$campo3);
	$id_curso = $participante -> id_curso;
	//DADOS DA ATIVIDADE DO COOPERADO
	$atividade = new tabcurso();
	$atividade -> MostraCurso($id_curso);
	$nomeAtividade = $atividade -> nome;
	$nomeAtividadeT = str_split($nomeAtividade, 30);
	$HoraAtividade = $atividade -> hora_mes;
	//REUNINDO DADOS DA COOPERATIVA DO PARTICPANTE
	$coop = new cooperativa();
	$coop -> MostraCoop($id_cooperativa);
	
	$nome = $coop -> nome;
	$fantasia = $coop -> fantasia;
	$cnpj = $coop -> cnpj;
	?>
    <table width="762" height="459" border="0" cellspacing="0" cellpadding="0" background="recibocoop.gif">
      <tr>
        <td height="111" align="left" valign="top">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2">
            <div style="margin-left:55px; margin-top:40px;">
              <?=$fantasia?>
              <br />CNPJ: <?=$cnpj?>
            </div>
            </td>
            <td align="left" valign="top" colspan="2">
            <div style="margin-left:2px; margin-top:40px;">
              <br />Periodo: <?=$Nomemes.'/'.$ano?>
            </div>
            </td>
          </tr>
          <tr>
            <td width="44%">
            <div style="margin-left:55px; margin-top:13px;">
              <?=$campo3." - ".$nomeP[0]?>
            </div>
            </td>
            <td colspan="2" align="left" valign="top">
              <div style="margin-left:1px; margin-top:13px;">
                <?=$nomeAtividadeT[0]?>
              </div>
            </td>
            <td width="23%" align="left" valign="top">&nbsp;</td>
          </tr>
          </table>
          </td>
        </tr>
      <tr>
        <td height="246" align="left" valign="top">
        <div style="margin-left:56px; margin-top:10px; height:10px; width:580px; font-size:10px"></div>
        <div style="margin-left:56px; margin-top:1px; height:220px; width:577px;">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
			<?php
			//DESCRIÇÃO DOS VENCIMENTOS E DESCONTOS
			
			$SalBase   = number_format($RowP['salario'],2,",",".");
			//$Benefic = number_format($RowP['parte2'],2,",",".");
			$Adicional = number_format($RowP['adicional'],2,",",".");
			$Desconto  = number_format($RowP['desconto'],2,",",".");
			$Inss  = number_format($RowP['inss'],2,",",".");
			$Irrf  = number_format($RowP['irrf'],2,",",".");
			$Quota = number_format($RowP['quota'],2,",",".");
			$Ajuda = number_format($RowP['ajuda_custo'],2,",",".");
			
			$TO_Proventos  = $RowP['adicional'] + $RowP['salario'] + $RowP['ajuda_custo'];
			$TO_ProventosF = number_format($TO_Proventos,2,",",".");
			
			$TO_Descontos  = $RowP['desconto'] + $RowP['inss'] + $RowP['irrf'] + $RowP['quota'];
			$TO_DescontosF = number_format($TO_Descontos,2,",",".");
			
			$SalLiq   = number_format($RowP['salario_liq'],2,",",".");
			
			$BaseInss = number_format($RowP['base_imposto'],2,",",".");
			$BaseIrrf = number_format($RowP['base_irrf'],2,",",".");
			// IRRF = 5021
			// INSS COOPERADOS = 5024
			// VALE TRANSPORTE = 7001
			
			// --------------- VALOR BASE --------------------
			echo "<tr>";
            echo "<td width='40'>001</td>";
            echo "<td width='255'>Valor Base</td>";
            echo "<td width='50' align='center'>-</td>";
            echo "<td width='115' align='right'>$SalBase</td>";
            echo "<td width='117' align='right'>&nbsp;</td>";
            echo "</tr>";
			// tamanho maximo 577 width
			
			/*// --------------- BENEFICIO --------------------
			echo "<tr>";
            echo "<td width='40'>001</td>";
            echo "<td width='255'>Beneficio</td>";
            echo "<td width='50' align='center'>-</td>";
            echo "<td width='115' align='right'>$Benefic</td>";
            echo "<td width='117' align='right'>&nbsp;</td>";
            echo "</tr>";*/
			
			// --------------- Rendimento --------------------
			if($Adicional == 0){ $linhaRendimento = "style='display:none'"; }else{ $linhaRendimento = ""; }
			echo "<tr $linhaRendimento>";
           	echo "<td>0002</td>";
           	echo "<td>Rendimento</td>";
            echo "<td align='center'>-</td>";
            echo "<td align='right'>$Adicional</td>";
            echo "<td align='right'>&nbsp;</td>";
            echo "</tr>";
			
			// --------------- Descontos --------------------
			if($Desconto == 0){ $linhaDesc = "style='display:none'"; }else{ $linhaDesc = ""; }
			echo "<tr $linhaDesc>";
           	echo "<td>0003</td>";
           	echo "<td>Descontos</td>";
            echo "<td align='center'>-</td>";
            echo "<td align='right'>&nbsp;</td>";
            echo "<td align='right'>$Desconto</td>";
            echo "</tr>";
			
			// --------------- INSS --------------------
			if($Inss == 0){ $linhainss = "style='display:none'"; }else{ $linhainss = ""; }
			echo "<tr $linhainss>";
           	echo "<td>5024</td>";
           	echo "<td>INSS</td>";
            echo "<td align='center'>$RowP[t_inss]</td>";
            echo "<td align='right'>&nbsp;</td>";
            echo "<td align='right'>$Inss</td>";
            echo "</tr>";
			
			// --------------- IRRF --------------------
			if($Irrf == 0){ $linhairrf = "style='display:none'"; }else{ $linhairrf = ""; }
			echo "<tr $linhairrf>";
           	echo "<td>5021</td>";
           	echo "<td>Imposto de Renda</td>";
            echo "<td align='center'>$RowP[t_irrf]</td>";
            echo "<td align='right'>&nbsp;</td>";
            echo "<td align='right'>$Irrf</td>";
            echo "</tr>";
			
			// --------------- QUOTA --------------------
			if($Quota == 0){ $linhaquota = "style='display:none'"; }else{ $linhaquota = ""; }
			echo "<tr $linhaquota>";
           	echo "<td>0055</td>";
           	echo "<td>Quota</td>";
            echo "<td align='center'>$RowP[p_quota]</td>";
            echo "<td align='right'>&nbsp;</td>";
            echo "<td align='right'>$Quota</td>";
            echo "</tr>";
			
			// --------------- Ajuda de Custo --------------------
			if($Ajuda == 0){ $linhaAjuda = "style='display:none'"; }else{ $linhaAjuda = ""; }
			echo "<tr $linhaAjuda>";
           	echo "<td>5011</td>";
           	echo "<td>Ajuda de Custo</td>";
            echo "<td align='center'>-</td>";
            echo "<td align='right'>$Ajuda</td>";
            echo "<td align='right'>&nbsp;</td>";
            echo "</tr>";
			?>
            
            
          </table>
        </div>
        </td>
      </tr>
      <tr>
        <td height="102" valign="top">
        <div style="margin-left:56px; margin-top:13px; height:78px; width:577px;">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td colspan="3" align="left" valign="top">Horas M&ecirc;s:
              <?=$RowP['h_mes']." h"?>
            </td>
            <td width="20%" align="right"><?=$TO_ProventosF?></td>
            <td width="20%" align="right"><?=$TO_DescontosF?></td>
          </tr>
          <tr>
            <td height="23" colspan="3" align="left"><!-- Valor Hora:  <?=$HoraAtividade." h"?> -->
              Horas Trabalhadas: <?=$RowP['h_trab']." h"?>
            </td>
            <td>&nbsp;</td>
            <td align="right" valign="bottom"><?=$SalLiq?></td>
          </tr>
          <tr>
            <td width="23%" height="34" align="left" valign="bottom">&nbsp;              <?=$SalLiq?></td>
            <td width="23%" align="center" valign="bottom"><?=$RowP['t_inss']?></td>
            <td colspan="3" valign="bottom">
            <div style="float:left; width:125px;" align="center"><?=$BaseInss?></div>
            <div style="float:left; width:115px;" align="center"><?=$BaseIrrf?></div>
            <div style="float:left; width:60px;"  align="center"><?=$RowP['t_irrf']?></div>
            </td>
          </tr>
        </table>
         </div>
        </td>
      </tr>
    </table>
    <?php
	unset($coop);
	unset($participante);
	endfor;
	
	if($cont < $num_clt_pro and $cont % 2) { 
		echo "<h1><!---Aqui a página é quebrada--> </h1>"; 
	} else { 
		echo '<br>'; 
	}
	
	$cont ++;
	
	
	
	} ?>
    <br />
   </td>
  </tr>
</table>
</body>
</html>