<?php
include('../../include/restricoes.php');
include('../../../../conn.php');
include('../../../../classes/formato_valor.php');
include('../../../../classes/formato_data.php');
include('../../../../classes/valor_extenso.php');


$ano_competencia	 = $_POST['ano_competencia'];
$entregue_id = $_POST['entregue_id'];


$qr_conc_bancaria = mysql_query("SELECT * FROM obrigacoes_conc_bancaria WHERE entregue_id = '$entregue_id' AND ano_competencia = '$ano_competencia' AND status =1") or die(mysql_error());
$row_conc 			= mysql_fetch_assoc($qr_conc_bancaria);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Concilia&ccedil;&atilde;o Banc&aacute;ria</title>
<script type="text/javascript" src="../../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript" src="../../../jquery/priceFormat.js" ></script>


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
table td table td {
	background-color:#fff;
}
table td table td p .margem{

		margin-bottom:6px;
		 width:400px;
		 height:auto;
}

</style>
</head>
<body style="text-align:center; margin:0; background-color:#fff; font-family:Arial, Helvetica, sans-serif; font-size:13px; text-transform:uppercase;">
<table style="margin:50px auto; width:790px; text-align:left; padding:10px; background-color:#fff;" cellpadding="4" cellspacing="1">
  <tr>
    <td colspan="6">
    
        <table cellpadding="4" cellspacing="1" style="width:100%;">
          <tr class="secao">
            <td style="text-align:center !important">CONCILIAÇÃO BANCÁRIA</td>
          </tr>
        </table>      
   
    </td>
  </tr>
  <tr>
    <td>
    
     <table cellpadding="4" cellspacing="1" style="background-color:#000; width:100%;">
       <tr>
        <td width="28%" class="secao">CONVENENTE</td>
        <td colspan="5"><?php echo $row_conc['razao']; ?></td>
      </tr>
      <tr>
        <td class="secao">PROJETO</td>
        <td colspan="5" ><?php echo $row_conc['projeto']; ?></td>
      </tr>
      <tr>
        <td class="secao">PROCESSO N&ordm;</td>
        <td colspan="2"><?php echo $row_conc['projeto_numero']; ?></td>
        <td width="23%" class="secao">TERMO DE PARCERIA N&ordm;</td>
        <td width="35%" colspan="2"><?php echo $row_conc['projeto_numero']; ?></td>
        
        
        
      <?php 
	  $termos_adivitivos = explode(',',$row_conc['termos_aditivos']);
	  
	foreach($termos_adivitivos as $numeros){ 
						 if($i%2 == 0) {
										 echo '</tr>';
										 echo '<tr>';
										 $i ==0;
					        		 }
        		  $i++; ?>
        <td class="secao">TERMO ADITIVO N&ordm;</td>
        <td colspan="2"><?php echo $numeros; ?></td>
     <?php
	 
	  
	  } ?>
        <td colspan="<?php echo $i?>">&nbsp;</td>
      </tr>
     </table>
     
   </td>
  </tr>
  <tr>
    <td>
    
    <table cellpadding="4" cellspacing="1" style="background-color:#000; width:100%;">
      <tr>
        <td colspan="6" align="center"><p><strong>DADOS BANC&Aacute;RIOS DA CONTA EXCLUSIVA DO  CONV&Ecirc;NIO</strong></p></td>
      </tr>
      <tr>
        <td class="secao">BANCO:</td>
        <td><?php echo $row_conc['nome_banco']; ?></td>
        <td class="secao">AG&Ecirc;NCIA:</td>
        <td><?php echo $row_conc['agencia_banco']; ?></td>
        <td class="secao">CONTA:</td>
        <td><?php echo $row_conc['conta_banco']; ?></td>
      </tr>
      <tr>
        <td class="secao">PER&Iacute;ODO:</td>
        <td colspan="5"><?php echo formato_brasileiro($row_conc['periodo_inicio']); ?> às <?php echo formato_brasileiro($row_conc['periodo_fim']); ?> </td>
      </tr>
    </table>

    </td>
  </tr>
  <tr>
    <td>
  
    <table cellpadding="4" cellspacing="1" style="background-color:#000; width:100%;">
      <tr class="secao">
        <td>ITEM</td>
        <td colspan="3">EVENTO</td>
        <td>DATA</td>
        <td>VALORES EM REAL</td>
      </tr>
      <tr>
        <td><strong>1.</strong></td>
        <td colspan="3"><strong>SALDO INICIAL</strong></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><strong>2.</strong></td>
        <td colspan="3"><p><strong>CR&Eacute;DITOS</strong><br />
          2.1. Repasse Prefeitura  Municipal.<br />
          2.2. Rendimentos<br />
          2.3. Outros</p></td>
        <td><p align="center"><br />
          <?php echo formato_brasileiro($row_conc['data_evento']);  ?><br />
          <?php echo formato_brasileiro($row_conc['data_evento']); ?><br />
          -</p></td>
        <td align="center"><br />
       
		R$ <?php echo formato_real($row_conc['total_repasse']);?>
          
          <br />
         R$	<?php echo formato_real($row_conc['total_rendimento']);?>
		
         <br />
          <span style="margin-left:-53px;"> - </span>
          </td>
      </tr>
      <tr>
        <td><strong>3.</strong></td>
        <td colspan="3">
        &nbsp;
        <p><strong>D&Eacute;BITOS</strong><br />
         <span style=" line-height:20px;">3.1.  TOTAL CHEQUES COMPENS</span><br/>         
           <span style=" line-height:20px;">3.2.  FOLHA DE PAGAMENTO.</span><br />        
         <span style=" line-height:20px;"> 3.3. GUIA  DA PREVIDENCIA SOCIAL</span><br/> 
         <span style=" line-height:20px;"> 3.4.  FGTS.</span><br/> 
         <span style=" line-height:20px;"> 3.5.  IRRF.</span><br/> 
         <span style=" line-height:20px;"> 3.6.  PIS S/ FOLHA DE PAGTO.</span><br/> 
          <span style=" line-height:20px;">3.7. PROVIS&Atilde;O DE ENC. TRAB</span><br/> 
         <span style=" line-height:20px;"> 3.8. TARIFA BANCÁRIA</span><br/> 
         <span style=" line-height:20px;"> 3.9. PERCENTUAL APURADO DA TAXA DO PROJETO</span><br />
         <span style=" line-height:20px;">4.0. PRESTADOR DE SERVI&Ccedil;OS</span>         <br/> 
        </p>
         </td>
        <td>
        &nbsp;
        <p align="center"><br />
          -<br />
       <?php for($i=0; $i<9;$i++) {?>
       
        <span style=" line-height:20px;"><?php echo formato_brasileiro($row_conc['data_evento']); ?></span><br />
       <?php }?>
         </td>
        <td align="left">
         
         &nbsp;
        <p align="left"><br />
           <span style="margin-left:80px;"> - </span>
         <br />
          <?php 
		 
		  $totais_banco =  array(
		  'total_folha',
		  'total_gps', 
		  'total_fgts', 
		  'total_irrf',
		  'total_pis', 
		  'total_provisao', 
		  'total_tarifa', 
		  'total_taxa_adm', 
		  'total_prestador');
		 
		   //verificação totais
		   foreach($totais_banco as $valor):
		  		  
			
				  echo '<span style="line-height:20px;margin-left:60px;" >R$</span> '.formato_real($row_conc[$valor])	.'<br />';
								
					
		 endforeach;  
		   
		  
		
          
          /* <?php echo formato_real($total_folha); ?><br /> 
          R$ <?php echo formato_real($total_gps); ?><br />
          R$ <?php echo formato_real($total_fgts); ?><br />
          R$ <?php echo formato_real($total_irrf); ?><br />
          R$ <?php echo formato_real($total_pis); ?><br />
          R$ <?php echo formato_real($total_provisao); ?><br />
          R$ <?php echo formato_real($total_tarifa);?><br />
          R$ <?php echo formato_real($total_taxa_adm)<br />
		  R$ <?php echo formato_real($total_prestador)
		   ; */?>
           
           <input type="hidden" id="entregue_id" value="<?php echo $entregue_id;?>"/>
           </p>
          </td>
      </tr>
      
      <tr>
        <td><strong>6.</strong></td>
        <?php
        
		
		?>
        
        <td colspan="3"><strong>SALDO ATUAL</strong></td>
        <td>&nbsp;</td>
        <td align="center"  id="saldo_atual"><strong>R$ <?php echo  formato_real($row_conc['saldo_atual']);?></strong></td>
      </tr>
      <tr>
        <td><strong>7.</strong></td>
        <td colspan="3"><strong>CHEQUES PENDENTES</strong></td>
        <td>&nbsp;</td>
        <td align="center"> - </td>
      </tr>
      <tr>
        <td><strong>8.</strong></td>
        <td colspan="3"><strong>SALDO AP&Oacute;S COMPENSA&Ccedil;&Atilde;O VALORES PENDENTES</strong></td>
        <td>&nbsp;</td>
        <td align="center"><strong>R$ 0,00</strong></td>
      </tr>
    </table>
	
    </td>
  </tr>
  <tr>
    <td>
    
    <table cellpadding="4" cellspacing="1" style="width:100%;">
      <tr>
        <td colspan="3" align="center"><strong>CONVENENTE  &ndash; PRESIDENTE</strong></td>
        <td colspan="3" align="center"><p align="center"><strong>RESP. P/ ELABORA&Ccedil;&Atilde;O PRESTA&Ccedil;&Atilde;O CONTAS</strong></p></td>
      </tr>
      <tr>
        <td class="secao">NOME:</td>
        <td colspan="2"><?php echo $row_conc['responsavel_convenente']; ?></td>
        <td class="secao">NOME:</td>
        <td colspan="2"><?php echo $row_conc['responsavel_prest_contas']; ?></td>
      </tr>
      <tr>
        <td class="secao">CARGO:</td>
        <td colspan="2"><?php echo $row_conc['cargo_convenente']; ?></td>
        <td class="secao">CARGO:</td>
        <td colspan="2"><?php echo $row_conc['cargo_prest_contas']; ?></td>
      </tr>
      <tr>
        <td colspan="6">&nbsp;</td>
      </tr>
      <tr class="secao">
        <td colspan="3">ASSINATURA:</td>
        <td colspan="3">ASSINATURA:</td>
      </tr>
      <tr>
        <td colspan="3">____________________________________________</td>
        <td colspan="3">_______________________________________________________________</td>
      </tr>
    </table>

    </td>
  </tr>
</table>
</body>
</html>

