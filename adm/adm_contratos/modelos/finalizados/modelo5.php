<?php
include('../../include/restricoes.php');
include('../../../../conn.php');
include('../../../../classes/formato_valor.php');
include('../../../../classes/formato_data.php');
include('../../../../classes/valor_extenso.php');

$id_entregue 	= $_POST['entregue_id'];
$ano_competencia = $_POST['ano_competencia'];


$qr_anexo_xv = mysql_query("SELECT * FROM obrigacoes_anexo_xv WHERE entregue_id = '$id_entregue' AND exercicio = '$ano_competencia'");
$row_anexo_xv = mysql_fetch_assoc($qr_anexo_xv);







// Mês em Extenso
$meses_pt = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Anexo XV</title>
<style type="text/css">
.pontilhado {
	border-bottom:1px dotted #999;
	padding-bottom:2px;
	font-style:italic;
	font-weight:bold;
}
td.secao {
	background-color:#eee;
	text-align:right;
	font-weight:bold;
	padding-right:5px;
}
tr.secao td {
	background-color:#eee;
	text-align:center;
	font-weight:bold;
}
td {
	background-color:#fff;
}
</style>
</head>
<body style="text-align:center; margin:0; background-color:#fff; font-family:Arial, Helvetica, sans-serif; font-size:13px;">
<table style="margin:50px auto; width:790px; text-align:left;" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center">
    
		<p><strong>ANEXO 15</strong><strong><br />
	    DEMONSTRATIVO INTEGRAL DAS RECEITAS E DESPESAS</strong><br />
		  <br />
	  </p>
        
    <table style="background-color:#000; width:100%;" cellpadding="4" cellspacing="1">
      <tr>
        <td colspan="2" class="secao">ÓRGÃO PÚBLICO PARCEIRO</td>
        <td colspan="6"><?php echo $row_anexo_xv['prefeitura']; ?></td>
      </tr>
      <tr>
        <td colspan="2" class="secao">ENTIDADE PARCEIRA (OSCIP)</td>
        <td colspan="6"><?php echo $row_anexo_xv['entidade_parceira']; ?></td>
      </tr>
      <tr>
        <td colspan="2" class="secao">CNPJ</td>
        <td colspan="6"><?php echo $row_anexo_xv['cnpj'];?></td>
      </tr>
      <tr>
        <td colspan="2" class="secao">ENDERE&Ccedil;O E CEP</td>
        <td colspan="6">
		<?php echo $row_anexo_xv['endereco']; ?>
     
         <?php echo $row_anexo_xv['cidade']; ?> <?php echo  $row_anexo_xv['uf']; ?> CEP: <?php echo  $row_anexo_xv['cep']; ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="secao">RESPONS&Aacute;VEL (EIS) PELA ENTIDADE</td>
        <td colspan="6" style="text-transform:uppercase"><?php echo $row_anexo_xv['responsavel_entidade']; ?></td>
      </tr>
      <tr>
        <td colspan="2" class="secao">OBJETO DO TERMO DE PARCERIA</td>
        <td colspan="6"><?php echo $row_anexo_xv['obj_termo']; ?></td>
      </tr>
      <tr>
        <td colspan="2" class="secao">EXERC&Iacute;CIO</td>
        <td colspan="6"><?php echo $row_anexo_xv['exercicio']; ?></td>
      </tr>
  </table>




    <p>&nbsp;</p>
<table style="background-color:#000; width:100%;" cellpadding="4" cellspacing="1">
  <tr class="secao">
    <td colspan="2">DOCUMENTO</td>
    <td colspan="2">DATA</td>
    <td colspan="2">VIG&Ecirc;NCIA</td>
    <td colspan="2">VALOR (R$)</td>
  </tr>
  <tr>
    <td style="text-transform:uppercase;"><?php echo $row_anexo_xv['tipo_projeto'];?></td>
    <td> <?php echo 'N&ordm '.$row_anexo_xv['numero_contrato'];?> </td>
    <td colspan="2" align="center"><?php echo formato_brasileiro($row_anexo_xv['data_assinatura']); ?></td>
    <td colspan="2" align="center"><?php echo $row_anexo_xv['vigencia']; ?></td>
    <td colspan="2" align="center">R$ <?php echo formato_real($row_anexo_xv['valor']); ?></td>
  </tr>
 
  </table>

<p>&nbsp;</p>
<table style="background-color:#000; width:100%;" cellpadding="4" cellspacing="1">
  <tr class="secao">
    <td colspan="5">DEMONSTRATIVO DOS REPASSES P&Uacute;BLICOS RECEBIDOS</td>
  </tr>
  <tr class="secao">
    <td>ORIGEM DOS RECURSOS (1)</td>
    <td>VALORES PREVISTOS - R$</td>
    <td width="163px">DOC. DE CR&Eacute;DITO N.&ordm;</td>
    <td width="78px">DATA</td>
    <td width="163px">REPASSADOS - R$</td>
  </tr>
  
  <?php 
  
  $qr_valor_previsto = mysql_query("SELECT * FROM valor_prev_anexo_xv WHERE obrigacoes_anexo_xv_id = '$row_anexo_xv[obrigacoes_anexo_xv_id]' ") or die(mysql_error());
  while($row_valor_previso = mysql_fetch_assoc($qr_valor_previsto)):
  
  ?>
  
       
              <tr>
                <td>MUNICIPAL</td>
                <td align="center">R$ <?php echo formato_real($row_valor_previso['valor_previsto']);?></td>
                <td colspan="3" style="margin:0px; padding:0px;">
                
                
                	<table width="100%" height="100%" cellpadding="7" cellspacing="0">
                		<?php
                        $qr_repasses = mysql_query("SELECT * FROM repasse_anexo_xv WHERE valor_previsto_id  = '$row_valor_previso[valor_prev_anexo_xv_id]' 
																						ORDER BY data ASC") or die(mysql_error());
						while($row_repasse = mysql_fetch_assoc($qr_repasses)):
						
						?>
                			<tr align="center">
                				<td  width="40%" style="border-right: 1px #000 solid; ">
                					<?php 
                					echo $row_repasse['doc_credito'];
						  			?>
                				</td>
                				<td  width="20%" style="border-right: 1px #000 solid; ">
                					<?php echo formato_brasileiro($row_repasse['data']); ?>
                				</td>
                				<td  width="40%">
                					R$ <?php echo formato_real($row_repasse['repassado']);
									$total_repasse += $row_repasse['repassado'];
									 ?>
                				</td>
                			</tr>
                		<?php endwhile;?>
                		</table>		
						
					
					</td>
              </tr>
  
  <?php 
  
  endwhile;
   ?>
  
  <tr class="secao">
    <td colspan="4" align="right">RECEITA COM APLICA&Ccedil;&Otilde;ES FINANCEIRAS DOS REPASSES P&Uacute;BLICOS</td>
    <td>R$ <?php 

   //echo formato_real($totalizador_repasse); 
   echo formato_real($total_repasse);
   ?></td>
  </tr>
  <tr>
    <td colspan="4" align="right" class="secao">TOTAL:</td>
    <td align="center">R$ <?php echo formato_real($total_repasse); //echo formato_real($totalizador_repasse); ?></td>
  </tr>
  <tr>
    <td colspan="4" class="secao">RECURSOS PR&Oacute;PRIOS APLICADOS PELA OSCIP</td>
    <td align="center">R$ 0,00</td>
  </tr>
  <tr>
    <td colspan="5">&nbsp;</td>
  </tr>
</table>

<table style="background-color:#000; width:100%;" cellpadding="4" cellspacing="1">
  <tr>
    <td colspan="8">(1) Verba: Federal, Estadual ou Municipal</td>
  </tr>
  <tr>
    <td colspan="8">O(s) signat&aacute;rio(s), na qualidade de    representante(s) da entidade parceira:</td>
  </tr>
  <tr>
    <td colspan="8">
      <br />
      <span style="text-transform:uppercase"><?php echo $row_anexo_xv['responsavel_entidade']; ?></span><br />
      <br />
      _____________________________________
    </td>
  </tr>
  <tr>
    <td colspan="8"><span style="text-transform:uppercase"><?php echo $row_anexo_xv['nome_oscip']; ?></span></td>
  </tr>
  <tr>
    <td colspan="8">
      <br />
      <br />
      <br />
      Vem indicar, na forma abaixo detalhada, a aplica&ccedil;&atilde;o dos recursos recebidos no    exerc&iacute;cio supra mencionado, na import&acirc;ncia total de R$  
      R$ <?php echo formato_real($total_repasse); ?>
&nbsp;(<?php echo htmlentities(valorPorExtenso($total_repasse),ENT_COMPAT,'UTF-8'); unset($total_repasse);?>)
    </td>
  </tr>
</table>

<p>&nbsp;</p>
<table style="background-color:#000; width:100%;" cellpadding="4" cellspacing="1">
  <tr class="secao">
    <td colspan="8">DEMONSTRATIVO DAS DESPESAS REALIZADAS</td>
  </tr>
  <tr class="secao">
    <td colspan="2">CATEGORIA OU FINALIDADE DA DESPESA</td>
    <td colspan="2">PER&Iacute;ODO DE REALIZA&Ccedil;&Atilde;O</td>
    <td colspan="2">ORIGEM DO RECURSO(2)</td>
    <td colspan="2">VALOR APLICADO-R$</td>
  </tr>
  
  
  
  <?php
  
  
  
  


  $qr_despesas = mysql_query("SELECT * FROM  despesas_realizadas_anexo_xv WHERE 	obrigacoes_anexo_xv_id = '$row_anexo_xv[obrigacoes_anexo_xv_id]'") or die(mysql_error());
  while($row_despesas= mysql_fetch_assoc($qr_despesas)):				
				
	?>
			
		
			<tr>
			  <td colspan="2" style="text-transform:uppercase"><?php echo $row_despesas['categoria_despesa']; ?> </td>
			  <td colspan="2" align="center"><?php echo formato_brasileiro($row_despesas['periodo_inicio']);	 ?> à <?php echo formato_brasileiro($row_despesas['periodo_fim']);?></td>
			  <td colspan="2" align="center">MUNICIPAL</td>
			  <td colspan="2" align="center">R$ <?php echo formato_real($row_despesas['valor_aplicado']); 
			  		$total_despesas += $row_despesas['valor_aplicado'];
			   ?></td>
			</tr>
            
 <?php	endwhile;?>
  
  <tr>
    <td colspan="8">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="6" class="secao">TOTAL DE DESPESAS</td>
    <td colspan="2" align="center">R$ <?php echo formato_real($total_despesas); ?></td>
  </tr>
  <tr>
    <td colspan="6" class="secao">RECURSO P&Uacute;BLICO N&Atilde;O APLICADO</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="6" class="secao">VALOR DEVOLVIDO AO &Oacute;RG&Atilde;O PARCEIRO</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="6" class="secao">VALOR AUTORIZADO PARA APLICA&Ccedil;&Atilde;O NO EXERC&Iacute;CIO SEGUINTE</td>
    <td colspan="2">&nbsp;</td>
  </tr>
</table>

<p>&nbsp;</p>
<table style="background-color:#000; width:100%;" cellpadding="4" cellspacing="1">
  <tr>
    <td colspan="8">(2) Verba: Federal, Estadual, Municipal e Recursos Pr&oacute;prios</td>
  </tr>
  <tr>
    <td colspan="8">Declaro(amos), na qualidade de    respons&aacute;vel(eis)pela entidade supra epigrafada, sob as penas da Lei, que a defesa relacionada comprova a exata aplica&ccedil;&atilde;o dos recursos recebidos para os fins indicados, conforme programa de trabalho aprovado, proposto ao &Oacute;rg&atilde;o Parceiro.</td>
  </tr>
  <tr>
    <td colspan="8">&nbsp;</td>
  </tr>
  <tr>
    <td class="secao">
      LOCAL E DATA:</td>
    <td colspan="7"><?php echo $row_anexo_xv['local_data']; ?>
	 &nbsp;
      ____________________________________
    </td>
  </tr>
  <tr>
    <td class="secao">RESPONS&Aacute;VEL:</td>
    <td colspan="7" style="text-transform:uppercase;"><?php echo $row_anexo_xv['responsavel_entidade']; ?></td>
  </tr>
  <tr>
    <td colspan="2" class="secao">
      &nbsp;
     
      MEMBROS DO CONSELHO FISCAL:
    </td>
    <td colspan="6" style="text-transform:uppercase;"><?php echo $row_anexo_xv['mebro_conselho1']; ?>
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
    <td colspan="6" style="text-transform:uppercase;"><?php echo $row_anexo_xv['membro_conselho2']; ?></td>
  </tr>
  
  
</table>

</td>
</tr>

<tr>
	<td align="center">  
   
    
    </td>
</tr>

</table>
</body>
</html>