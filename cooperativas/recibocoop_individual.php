<?php
// Verificação de Login
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="www.netsorrindo.com.br/intranet/login.php">Logar</a>';
	exit;
}

// Incluindo arquivos
include('../conn.php');
include('../funcoes.php');
include('../classes/regiao.php');
include('../classes/cooperativa.php');
include('../classes/cooperado.php');
include('../classes/curso.php');

// Recebendo a variável
$cooperado = $_GET['coo'];

// Array dos Meses
$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$ano = $row_folha['ano'];

// Consulta dos Participantes
$qr_paticipante   = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$cooperado' AND status = '1'");
$row_participante = mysql_fetch_array($qr_paticipante);

// Consulta das Folhas
$qr_folhas = mysql_query("SELECT * FROM folha_cooperado WHERE id_autonomo = '$cooperado' AND status = '3' ORDER BY id_folha ASC");

// Dados do Participante
$id_participante = $row_participante['id_autonomo'];
$id_cooperativa  = $row_participante['id_cooperativa'];
$id_curso        = $row_participante['id_curso'];

// Dados da Atividade do Cooperado
$atividade     = new tabcurso();
$atividade    -> MostraCurso($id_curso);
$nomeAtividade = str_split($atividade -> nome, 30);
$HoraAtividade = $atividade -> hora_mes;

// Dados da Cooperativa do Participante
$coop     = new cooperativa();
$coop    -> MostraCoop($id_cooperativa);
$nome     = $coop -> nome;
$fantasia = $coop -> fantasia;
$cnpj     = $coop -> cnpj;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>::: RECIBO ::::</title>
<style type="text/css">
body { margin:0; background-color:#FFF; font-family:"Courier New", Courier, monospace; font-size:12px; }
h1 { page-break-after:always; margin-bottom:100px; }
</style>
</head>
<body>
<table width="98%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" align="center">
  <tr>
    <td width="67%" align="center" valign="top">
    <br />
    
    <?php // Loop das Folhas
		  while($row_folha = mysql_fetch_assoc($qr_folhas)) { ?>
            
        <table width="762" height="459" border="0" cellspacing="0" cellpadding="0" background="recibocoop.gif">
          <tr>
            <td height="111" align="left" valign="top">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="2">
                <div style="margin-left:55px; margin-top:40px;">
                  <?php echo $fantasia; ?>
                  <br />CNPJ: <?php echo $cnpj; ?>
                </div>
                </td>
                <td align="left" valign="top" colspan="2">
                  <div style="margin-left:2px; margin-top:40px;">
                    <br />Periodo: <?php echo $meses[(int)$row_folha['mes']].'/'.$row_folha['ano']; ?>
                  </div>
                </td>
              </tr>
              <tr>
                <td width="44%">
                  <div style="margin-left:55px; margin-top:13px;">
                    <?php echo $id_participante.' - '.$row_folha['nome']; ?>
                  </div>
                </td>
                <td colspan="2" align="left" valign="top">
                  <div style="margin-left:1px; margin-top:13px;">
                    <?php echo $nomeAtividade[0]; ?>
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
                <?php // DESCRIÇÃO DOS VENCIMENTOS E DESCONTOS
                
                $SalBase   = number_format($row_folha['salario'],2,',','.');
                //$Benefic = number_format($row_folha['parte2'],2,',','.');
                $Adicional = number_format($row_folha['adicional'],2,',','.');
                $Desconto  = number_format($row_folha['desconto'],2,',','.');
                $Inss      = number_format($row_folha['inss'],2,',','.');
                $Irrf      = number_format($row_folha['irrf'],2,',','.');
                $Quota     = number_format($row_folha['quota'],2,',','.');
                $Ajuda     = number_format($row_folha['ajuda_custo'],2,',','.');
                
                $TO_Proventos  = $row_folha['adicional'] + $row_folha['salario'] + $row_folha['ajuda_custo'];
                $TO_ProventosF = number_format($TO_Proventos,2,',','.');
                $TO_Descontos  = $row_folha['desconto'] + $row_folha['inss'] + $row_folha['irrf'] + $row_folha['quota'];
                $TO_DescontosF = number_format($TO_Descontos,2,',','.');
                
                $SalLiq   = number_format($row_folha['salario_liq'],2,',','.');
                $BaseInss = number_format($row_folha['base_imposto'],2,',','.');
                $BaseIrrf = number_format($row_folha['base_irrf'],2,',','.');
				
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
                if($Adicional == 0){ $linhaRendimento = "style='display:none'"; }else{ $linhaRendimento = NULL; }
                echo "<tr $linhaRendimento>";
                echo "<td>0002</td>";
                echo "<td>Rendimento</td>";
                echo "<td align='center'>-</td>";
                echo "<td align='right'>$Adicional</td>";
                echo "<td align='right'>&nbsp;</td>";
                echo "</tr>";
                
                // --------------- Descontos --------------------
                if($Desconto == 0){ $linhaDesc = "style='display:none'"; }else{ $linhaDesc = NULL; }
                echo "<tr $linhaDesc>";
                echo "<td>0003</td>";
                echo "<td>Descontos</td>";
                echo "<td align='center'>-</td>";
                echo "<td align='right'>&nbsp;</td>";
                echo "<td align='right'>$Desconto</td>";
                echo "</tr>";
                
                // --------------- INSS --------------------
                if($Inss == 0){ $linhainss = "style='display:none'"; }else{ $linhainss = NULL; }
                echo "<tr $linhainss>";
                echo "<td>5024</td>";
                echo "<td>INSS</td>";
                echo "<td align='center'>$row_folha[t_inss]</td>";
                echo "<td align='right'>&nbsp;</td>";
                echo "<td align='right'>$Inss</td>";
                echo "</tr>";
                
                // --------------- IRRF --------------------
                if($Irrf == 0){ $linhairrf = "style='display:none'"; }else{ $linhairrf = NULL; }
                echo "<tr $linhairrf>";
                echo "<td>5021</td>";
                echo "<td>Imposto de Renda</td>";
                echo "<td align='center'>$row_folha[t_irrf]</td>";
                echo "<td align='right'>&nbsp;</td>";
                echo "<td align='right'>$Irrf</td>";
                echo "</tr>";
                
                // --------------- QUOTA --------------------
                if($Quota == 0){ $linhaquota = "style='display:none'"; }else{ $linhaquota = NULL; }
                echo "<tr $linhaquota>";
                echo "<td>0055</td>";
                echo "<td>Quota</td>";
                echo "<td align='center'>$row_folha[p_quota]</td>";
                echo "<td align='right'>&nbsp;</td>";
                echo "<td align='right'>$Quota</td>";
                echo "</tr>";
                
                // --------------- Ajuda de Custo --------------------
                if($Ajuda == 0){ $linhaAjuda = "style='display:none'"; }else{ $linhaAjuda = NULL; }
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
                  <?=$row_folha['h_mes'].' h'?>
                </td>
                <td width="20%" align="right"><?=$TO_ProventosF?></td>
                <td width="20%" align="right"><?=$TO_DescontosF?></td>
              </tr>
              <tr>
                <td height="23" colspan="3" align="left"><!-- Valor Hora:  <?=$HoraAtividade.' h'?> -->
                  Horas Trabalhadas: <?=$row_folha['h_trab'].' h'?>
                </td>
                <td>&nbsp;</td>
                <td align="right" valign="bottom"><?=$SalLiq?></td>
              </tr>
              <tr>
                <td width="23%" height="34" align="left" valign="bottom">&nbsp;<?=$SalLiq?></td>
                <td width="23%" align="center" valign="bottom"><?=$row_folha['t_inss']?></td>
                <td colspan="3" valign="bottom">
                <div style="float:left; width:125px;" align="center"><?=$BaseInss?></div>
                <div style="float:left; width:115px;" align="center"><?=$BaseIrrf?></div>
                <div style="float:left; width:60px;"  align="center"><?=$row_folha['t_irrf']?></div>
                </td>
              </tr>
            </table>
             </div>
            </td>
          </tr>
        </table>
    
	<?php if($cont < $total_participantes and $cont % 2) { 
			  echo '<h1><!---Aqui a página é quebrada--></h1>'; 
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