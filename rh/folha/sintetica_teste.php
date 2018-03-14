<?php include('sintetica/cabecalho_folha.php'); 

///QNT DE FERIADOS NO MÊS
$qnt_feriados  = mysql_num_rows(mysql_query("SELECT * FROM rhferiados WHERE MONTH(data) = '$mes' AND  (id_regiao = '$row_folha[id_regiao]' OR id_regiao = 0)"));

/////////PEGANDO A QUANTO DADE DE DIAS ÚTEIS
$total_dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
$qnt_semanas    = 0;

for($i=0; $i < $total_dias_mes; $i++){
    $dia = strtotime($ano.'-'.$mes.'-'.$i); 
	if(date('w',$dia) >=1 and date('w',$dia) <=5){ $dias_uteis =  $dias_uteis+1;}
	
	if(date('w',$dia) == 6){ $qnt_semanas = $qnt_semanas + 1;}
	
	
}
///////////VERIFICA SE EXISTE O AQRQUIVO XML DA FOLHA GERADO, CAS EXISTA REDIRECIONA PARA A PÁGINA COM OS PARTICIPANTES VINDOS DO XML




////criando o arquivo XML
$arquivo = fopen("xml_horista/$folha.xml",'w');	

fwrite($arquivo, "<?xml version=\"1.0\" encoding=\"utf8\"?> \r\n");
fwrite($arquivo,"<folha>\r\n" );
fwrite($arquivo,"</folha>\r\n" );
fclose($arquivo);
/////////////////////////
$xml = simplexml_load_file("xml_horista/$folha.xml");



?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: Folha Sint&eacute;tica de CLT (<?=$folha?>)</title>
<link href="sintetica/folha.css" rel="stylesheet" type="text/css">
<link href="../../favicon.ico" rel="shortcut icon">
<link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
<script src="../../js/highslide-with-html.js" type="text/javascript"></script>
<script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="js/calculo_horista.js" type="text/javascript"></script>
<script>
	hs.graphicsDir = '../../images-box/graphics/'; 
	hs.outlineType = 'rounded-white';
</script>
<style type="text/css">
	.highslide-html-content { width:600px; padding:0px; }
</style>
</head>
<body>
    <div id="corpo">
    <table cellspacing="4" cellpadding="0" id="topo">
      <tr>
        <td width="15%" rowspan="3" valign="middle" align="center">
          <img src="../../imagens/logomaster<?=mysql_result($qr_projeto, 0, 2)?>.gif" width="110" height="79">
        </td>
        <td colspan="3" style="font-size:12px;">
        	<b><?=mysql_result($qr_projeto, 0, 1).' ('.$mes_folha.')'?></b>
        </td>
      </tr>
      <tr>
        <td width="35%"><b>Data da Folha:</b> <?=$row_folha['data_inicio_br'].' &agrave; '.$row_folha['data_fim_br']?></td>
        <td width="30%"><b>Região:</b> <?=$regiao.' - '.mysql_result($qr_regiao, 0, 1)?></td>
        <td width="20%"><b>Participantes:</b> <?=$total_participantes?></td>
      </tr>
      <tr>
        <td><b>Data de Processamento:</b> <?=$row_folha['data_proc_br']?></td>
        <td><b>Gerado por:</b> <?=@abreviacao(mysql_result($qr_usuario, 0), 2)?></td>
        <td><b>Folha:</b> <?=$folha?></td>
      </tr>
    </table>
     
    	<table cellpadding="0" cellspacing="1" id="folha">
            <tr>
              <td colspan="2">
                <a href="<?=$link_voltar?>" class="voltar">Voltar</a>
              </td>
              <td colspan="8">
              <?php if(empty($decimo_terceiro)) { ?>
                <div style="float:right;">
                    <div class="legenda"><div class="nota entrada"></div>Admissão</div>
                    <div class="legenda"><div class="nota evento"></div>Licen&ccedil;a</div>
                    <div class="legenda"><div class="nota faltas"></div>Faltas</div>
                    <div class="legenda"><div class="nota ferias"></div>F&eacute;rias</div>
                    <div class="legenda"><div class="nota rescisao"></div>Rescis&atilde;o</div>
                </div>
              <?php } ?>
              </td>
            </tr>
            <tr>
              <td colspan="10">
              		
                    <table cellspacing="1" cellpadding="0" width="100%" id="folha">
                     <tr class="secao">
                      <td>COD</td>
                      <td  align="left" style="padding-left:5px;">NOME</td>                 
                     
                      <?php
					  if($projeto_tipo_folha == 1){
						  
						echo '<td  width="6%">HORAS</td>';
						echo '<td  width="6%">HORAS ATRASO</td>'; 
						echo '<td  width="6%">ADICIONAL NOTURNO</td>';  
						echo '<td  width="6%">DSR</td>';  
					  }
					  ?>
                      
                      <td >BASE</td>
                      <td >RENDIMENTOS</td>
                      <td >DESCONTOS</td>
                      <td>INSS</td>
                      <td >IRRF</td>
                      <td >FAM&Iacute;LIA</td>
                      <td >L&Iacute;QUIDO</td>
                     </tr>
                   
            
       
<?php 
// Início do Loop dos Participantes da Folha
	  while($row_participante = mysql_fetch_array($qr_participantes)) {
		  
		  // Id do Participante
		  $clt = $row_participante['id_clt'];
		  
		  // Link para Relatório
		  $relatorio = str_replace('+', '--', encrypt("$clt&$folha"));

		 
		  
		  // Calculando a Folha
		  include('sintetica/calculos_folha_teste.php');
         include('gerar_xml_parte1.php');
	
		 
           ?>
         
		 <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?> destaque">
            <td width="4%" title="COD."><?=$clt?></td>
		    <td width="28%" align="left" title="NOME">
				<a href="sintetica/relatorio<?php if(!empty($decimo_terceiro)) { echo '_dt'; } ?>.php?enc=<?=$relatorio?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="participante" title="Ver relatório de <?=$row_clt['nome']?>">
                	<span class="
                    <?php 		if(isset($dias_entrada))    { echo 'entrada';
                          } elseif(isset($sinaliza_evento)) { echo 'evento';
                          } elseif(isset($dias_ferias))     { echo 'ferias';
                          } elseif(!empty($num_rescisao))   { echo 'rescisao';
                          } elseif(isset($dias_faltas))     { echo 'faltas';
                          } else                            { echo 'normal';
                          } ?>
                          "><?php echo abreviacao($row_clt['nome'], 4, 1);?></span>
                    <img src="sintetica/seta_<?php if($seta++%2==0) { echo 'um'; } else { echo 'dois'; } ?>.gif">
                </a>
                
                <input type="hidden" name="id_clt" class="id_clt" value="<?php echo $clt?>"/>
                <input type="hidden" name="id_folha" class="id_folha" value="<?php echo $row_folha['id_folha']; ?>"/>
            </td>        
      
             <?php
			  if($projeto_tipo_folha == 1){
				  
				echo '<td align="center"><input type="text" value="'.$horas.'" style="background-color:transparent;border:0; width:30px;"  class="horas" />/'.$total_hora_mes.'</td>'; 
				echo   '<td><input name="horas_atraso" type="text" class="horas_atraso" style="background-color:transparent; border:0;" size="2" value="'.$horas_atraso.'"/></td>';
				
				echo '<td align="center">
						<input type="text" value="'.$horas_noturnas.'" style="background-color:transparent;border:0; width:30px;"  class="hora_noturna" />
				 <span class="adicional_noturno_mes" >'.number_format($valor_adicional_noturno,2,',','.').'</span></td>';  
				 
				 echo '<td align="center" class="DSR">'.number_format($DSR,2,',','.').'</td>';
			  
			  }
			  ?>
            
			<td width="8%" class="base" title="BASE"><?php if(!empty($decimo_terceiro)) { echo formato_real($decimo_terceiro_credito); } else { echo formato_real($salario); } ?></td>
			<td width="10%" class="rendimentos" title="RENDIMENTOS"><?=formato_real($rendimentos)?></td>
			<td width="10%" class="descontos" title="DESCONTOS"><?=formato_real($descontos)?></td>
            <td width="8%" class="inss_completo" title="INSS"><?=formato_real($inss_completo)?></td>
            <td width="8%" class="irrf_completo" title="IRRF"><?=formato_real($irrf_completo)?></td>
            <td width="8%" class="familia" title="FAMÍLIA"><?=formato_real($familia)?></td>
			<td width="10%" title="LÍQUIDO"><span class="liquido">  <?=formato_real(abs($liquido))?></span>
		 
		
        <!-----------LINHA CONTENDO OS VALORES DOS MOVIMENTOS PARA CÁLCULO VIA JAVASCRIPT QUANDO FOR FEITA A ALTERAÇÃO NO CAMPO "HORAS"-------------------->	
          <?php
          $array_valores = array("salario" => formato_real($salario), 	
		  						  "xml_base" => formato_real($base), 				
						          "decimo_terceiro" 		=> formato_real($decimo_terceiro_credito),
						          "ferias" 				=> formato_real($valor_ferias), 			
						          "desconto_ferias" 		=> formato_real($desconto_ferias), 		
						          "rescisao" 				=> formato_real($valor_rescisao) , 			
						          "desconto_rescisao"	 	=> formato_real($desconto_rescisao), 		
						          "inss" 					=> formato_real($inss), 					
						          "inss_dt" 				=> formato_real($inss_dt), 				
						          "inss_ferias" 			=> formato_real($inss_ferias), 			
						          "inss_rescisao" 			=> formato_real($inss_rescisao),	 		
						          "irrf" 					=> formato_real( $irrf),				 
						          "irrf_dt" 				=> formato_real($irrf_dt), 				
									"irrf_ferias" 			=> formato_real($irrf_ferias) , 			
									"irrf_rescisao" 		=> formato_real($irrf_rescisao), 			
									"fgts" 					=> formato_real($fgts), 					
									"fgts_dt" 				=> formato_real($fgts_dt), 				
									"fgts_ferias" 			=> formato_real($fgts_ferias), 			
									"fgts_rescisao" 		=> formato_real( $fgts_rescisao), 			
									"fgts_completo" 		=> formato_real($fgts_completo), 			
									"vale_refeicao" 		=> formato_real($vale_refeicao), 			
									"familia" 				=> formato_real($familia), 				
									"salario_maternidade" 	=> formato_real($salario_maternidade), 	
									"vale_transporte" 		=> formato_real($vale_transporte) , 		
									"sindicato" 			=> formato_real($sindicato) , 				
									"base_inss" 			=> formato_real($base_inss) ,				
									"base_inss_empresa" 	=> formato_real(($base_inss * 0.2)), 		
									"base_inss_rat" 		=> formato_real(($base_inss * $percentual_rat)),
									"base_inss_terceiros"  	=> formato_real(($base_inss * 0.058)), 	
									"base_irrf" 			=> formato_real(($base_irrf - $ddir)),		
									"base_fgts" 			=> formato_real($base_fgts) , 				
									"base_fgts_ferias" 		=> formato_real($base_fgts_ferias), 		
									"ddir" 					=> formato_real($ddir));	
			foreach($array_valores  as $nome_campo => $valor){
				
				echo '<input name="'.$nome_campo.'" type="hidden" value="'.$valor.'" class="'.$nome_campo.'"/>';
				
			}
										  
		  ?>
         
			
            
            </td>
          </tr>
          
        <!-------------------------------------------FIM ALTERAÇÃO NO CAMPO "HORAS"------------------------------------------------------------------------->	    
		<?php 
			
		
		include('sintetica/update_participante_teste.php');
		  include('sintetica/totalizadores_resets.php');
		 	// Fim do Loop de Participantes
	  	    } ?>
            
      	<tr class="totais">
          <td colspan="2">
		      <?php if($total_participantes > 10) { ?>
          	      <a href="#corpo" class="ancora">Subir ao topo</a>
              <?php } ?></td>
             <?php
					  if($projeto_tipo_folha == 1){
						  
						echo '<td>&nbsp;</td>';  
					  }
					  ?>  
              
          <td>TOTAIS:</td>
          <td class="total_adicional_noturno"><?php echo formato_real($total_valor_adicional_noturno); ?></td>
          <td class="total_DSR"><?php echo formato_real($total_DSR); ?></td>
          <td class="total_base"><?php if(!empty($decimo_terceiro)) { echo formato_real($decimo_terceiro_total); } else { echo formato_real($salario_total); } ?></td>
		  <td class="total_rendimentos"><?=formato_real($rendimentos_total)?></td>
		  <td class="total_descontos"><?=formato_real($descontos_total)?></td>
          <td class="total_inss_completo"><?=formato_real($inss_completo_total)?></td>
          <td class="total_irrf_completo"><?=formato_real($irrf_completo_total)?></td>
          <td class="total_familia"><?=formato_real($familia_total)?></td>
		  <td class="total_liquido"><?=formato_real($liquido_total)?></td>
        </tr>
        
        
        
        
        
        
        </table>
        
        </td>
        </tr>
    </table> 
    
    
    
    
    
    
    
    <?php include('sintetica/estatisticas_folha_teste.php'); ?>
</div>
<?php //include('sintetica/updates_teste.php'); 
		


?>


    <?php
    ////////////////////SALVANDO XML//////////////////////		
	file_put_contents ("xml_horista/$folha.xml", $xml->asXML());
	///////////////////////////////////////////////////////
	
	//////////////INSERINDO OS DADOS DA TABELA RH_FOLHA NO XML
		include('gerar_xml_parte2.php'); 
		
	?>
    
</body>
</html>