<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
} else {

include('../conn.php');

$id_user 	   = $_COOKIE['logado'];
$result_user   = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user      = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master    = mysql_fetch_array($result_master);

$projeto  = $_REQUEST['pro'];
$regiao   = $_REQUEST['reg'];
$tipo     = $_REQUEST['tipo'];
$tela     = $_REQUEST['tela'];
$id       = $_REQUEST['id'];
$ano_base = 2012;//$_REQUEST['ano_base'];

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto    = mysql_fetch_array($result_projeto);

$data_hoje = date('d/m/Y');

function formata($valor) {
	$valor_formatado = str_replace('', ',', $valor);
	return $valor_formatado;
}

function formata2($valor) {
	$valor_formatado = number_format($valor, 2, ',', '');
	return $valor_formatado;
}


?>

<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Ficha Financeira</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css" media="screen">
<link href="css/estrutura.css" rel="stylesheet" type="text/css" media="print">
<style >
.coluna2{
width: 56px;
font-size:10px;	
text-align:left;
}

.coluna_total{
width: 56px;
font-size:10px;	
text-align:right;	
}

</style>

<style media="print">
.coluna2{
width: 56px;
font-size:10px;	
text-align:left;
}

.coluna_total{
width: 56px;
font-size:10px;	
text-align:right;	
}

</style>

</head>
<body style="background-color:#FFF; margin-top:30px;">

    
    
<?php
          // Consultas para Dados Pessoais
		  switch($tipo) {
			 case 1:
					$result = mysql_query("SELECT * , date_format(data_nasci, '%d/%m/%Y') AS data_nasci, date_format(data_entrada, '%d/%m/%Y') AS data_entrada, date_format(data_rg, '%d/%m/%Y') AS data_rg, date_format(data_saida, '%d/%m/%Y') AS data_saida FROM autonomo WHERE id_autonomo = '$id' AND tipo_contratacao = '1' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
             break;
			 case 2:
			 
			 $qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_master = 1");
			 while($row_regioes= mysql_fetch_assoc($qr_regioes)):
			 
			 
			 $regioes[] = $row_regioes['id_regiao'];
			 
			 endwhile;
			
			 $regioes = implode(',',$regioes);
			 
					$result = mysql_query("SELECT * , date_format(data_nasci, '%d/%m/%Y') AS data_nasci, date_format(data_entrada, '%d/%m/%Y') AS data_entrada, date_format(data_rg, '%d/%m/%Y') AS data_rg, date_format(data_saida, '%d/%m/%Y') AS data_saida FROM rh_clt 					
											INNER JOIN rh_folha_proc 
											ON rh_folha_proc.id_clt = rh_clt.id_clt
											WHERE rh_clt. tipo_contratacao = '2' 											 											
											AND rh_folha_proc.status = 3 
											AND  (rh_folha_proc.a5021 != '0.00' OR rh_folha_proc.a5021 != '') 
											AND rh_folha_proc.ano = '$ano_base'
											AND rh_clt.id_regiao IN($regioes)
																					
											GROUP BY rh_clt.id_clt") or die(mysql_error());
											
				$quantidade = mysql_num_rows($result);							
				
             break;
			 case 3:
					$result = mysql_query("SELECT * , date_format(data_nasci, '%d/%m/%Y') AS data_nasci, date_format(data_entrada, '%d/%m/%Y') AS data_entrada, date_format(data_rg, '%d/%m/%Y') AS data_rg, date_format(data_saida, '%d/%m/%Y') AS data_saida FROM autonomo WHERE id_autonomo = '$id' AND tipo_contratacao = '3' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
             break;
			 case 4:
					$result = mysql_query("SELECT * , date_format(data_nasci, '%d/%m/%Y') AS data_nasci, date_format(data_entrada, '%d/%m/%Y') AS data_entrada, date_format(data_rg, '%d/%m/%Y') AS data_rg, date_format(data_saida, '%d/%m/%Y') AS data_saida FROM autonomo WHERE id_autonomo = '$id' AND tipo_contratacao = '4' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
             break;
		 }
?>


<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; ">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
         <strong>RELATÓRIO DIRF <?=$ano_base?></strong><br>
         <?=$row_master['razao']?>
         <table width="300" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
            <tr style="color:#FFF;">	
             
              <td width="150" align="center" class="top">TOTAL</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">             
               <td align="center"><b><?=$quantidade;?></b></td>
              
            </tr>
        </table>
</td>
</tr>
</table>

<?php

		// Variáveis
		switch($tipo) {
		   case 1:
		   $banco1 = "folhas";
		   $banco2 = "folha_autonomo";
		   $coluna_id = "id_autonomo";
		   $coluna_salario = "salario_liq";
		   $coluna_contratacao = "AND contratacao = '1'";
		   $ferias = NULL;
		   break;
		   
		   case 2:   
		   $banco1 = "rh_folha";
		   $banco2 = "rh_folha_proc";  
		   $coluna_id = "id_clt";
		   $coluna_salario = "sallimpo_real";
		   $coluna_contratacao = NULL;  
		   $ferias = "AND $banco1.ferias != '1'";
		   break;
		   
		   case 3:
		   $banco1 = "folhas";
		   $banco2 = "folha_cooperado";
		   $coluna_id = "id_autonomo";
		   $coluna_salario = "salario_liq";
		   $coluna_contratacao = "AND contratacao = '3'";
		   $ferias = NULL;
		   break;
		   
		   case 4:
		   $banco1 = "folhas";
		   $banco2 = "folha_cooperado";
		   $coluna_id = "id_autonomo";
		   $coluna_salario = "salario_liq";
		   $coluna_contratacao = "AND contratacao = '4'";
		   $ferias = NULL;
		   break;
		}
		
		 
		while($participante = mysql_fetch_assoc($result)):
					 
			$get_pg = mysql_query("SELECT * FROM tipopg WHERE id_tipopg = '$participante[tipo_pagamento]'");
			$pg     = mysql_fetch_assoc($get_pg);
			
			$get_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$participante[id_curso]'");
			$curso     = mysql_fetch_assoc($get_curso);
			
			$get_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$participante[banco]'");
			$banco     = mysql_fetch_assoc($get_banco);
			
			
			
			///MOVIMENTOS
			$qr_codigos = mysql_query("SELECT distinct(cod) FROM rh_movimentos WHERE  cod != 9991 AND cod != 0001  AND cod != 5029 ORDER BY cod ASC");
			while($row_codigos = mysql_fetch_array($qr_codigos)){
				
			$codigo = "a".$row_codigos['0'];				
					
					
					$verifica_movimentos = mysql_query("SELECT $banco1.id_folha, $banco1.mes,$banco2.$codigo  FROM $banco1 
												INNER JOIN $banco2
												ON $banco1.id_folha = $banco2.id_folha 
												WHERE   year($banco1.data_inicio) = '$ano_base' $coluna_contratacao AND $banco1.projeto = '$participante[id_projeto]' AND $banco1.regiao = '$participante[id_regiao]'  AND $banco1.ferias != '1' AND $banco1.status = '3' AND $banco2.status = 3 AND ($banco2.$codigo != '0.00' OR $banco2.$codigo != '') AND $coluna_id = '$participante[$coluna_id]' ") or die(mysql_error());
												
				
												
					if(mysql_num_rows($verifica_movimentos) == 0 ) continue;
					
				
					$cod_movimento  = substr($codigo, 1);
					$qr_movimentos 	= mysql_query("SELECT * FROM rh_movimentos WHERE cod = '$cod_movimento'");
					$row_movimento 	= mysql_fetch_assoc($qr_movimentos);
					
					$total_movimentos[$cod_movimento]['nome'] = $row_movimento['descicao'];
					$total_movimentos[$cod_movimento]['categoria'] = $row_movimento['categoria'];
					
					
					
					while($row_movimento = mysql_fetch_assoc($verifica_movimentos)):	
								
							$total_movimentos[$cod_movimento][(int)$row_movimento['mes']] = $row_movimento[$codigo];	
						
						
						
					endwhile;
					
			}
			
			
			
			$total_movimentos['0001']['nome'] = 'Salário Base';
			$total_movimentos['0001']['categoria'] = 'CREDITO';
			
			$total_movimentos['0003']['nome'] 	   = ' Férias';
			$total_movimentos['0003']['categoria'] = 'CREDITO';
			
			$total_movimentos['5029']['nome']      = 'Décimo Terceiro';
			$total_movimentos['5029']['categoria'] = 'CREDITO';
			
			$total_movimentos['4007']['nome']      = 'Rescisão';
			$total_movimentos['4007']['categoria'] = 'CREDITO';
			
			
			
			for($mes = 1; $mes <= 12;$mes++){
				
					$tubarao = sprintf('%02d',$mes);
					
					// Salário Base			
					if(date("$ano_base-$tubarao-01") > date('2010-06-09')) {
						$coluna_salario = "sallimpo_real";
					} else {
						$coluna_salario = "sallimpo";
					}
					
					$qr_folha = mysql_query("SELECT * FROM $banco1 WHERE mes = '$tubarao' AND year(data_inicio) = '$ano_base' $coluna_contratacao AND projeto = '$participante[id_projeto]' AND regiao = '$participante[id_regiao]'  $ferias AND status = '3'");
					$folha = mysql_fetch_assoc($qr_folha);
					
					$qr_folha_individual = mysql_query("SELECT * FROM $banco2 WHERE id_folha = '$folha[id_folha]' AND $coluna_id = '$participante[$coluna_id]' AND status = '3'");
					$folha_individual    = mysql_fetch_assoc($qr_folha_individual);
					
					
					if($folha['terceiro'] == 1){ //Décimo terceiro
						
						$total_movimentos['5029'][$mes] 	= $folha_individual['salliquido'];
						
					} else {
							$total_movimentos['0001'][$mes] = $folha_individual[$coluna_salario];
					}
			
			
					//////FÉRIAS
					$qr_ferias  = mysql_query("SELECT * FROM rh_ferias WHERE $coluna_id = '$participante[$coluna_id]' AND ano = '$ano_base' AND mes = '$tubarao' AND status = '1'");
					$row_ferias = mysql_fetch_assoc($qr_ferias);
					
				  		
					
					if(!empty($row_ferias['total_liquido'])) {
						 $total_movimentos['0003'][$mes] = $row_ferias['total_liquido'];
					}
					
					
					// Rescisão
					$qr_rescisao  = mysql_query("SELECT * FROM rh_recisao WHERE $coluna_id = '$participante[$coluna_id]' AND year(data_demi) = '$ano_base' AND month(data_demi) = '$tubarao' AND motivo IN (60,61,62,80,81,100) AND status = '1'") or die(mysql_error());
					$row_rescisao = mysql_fetch_assoc($qr_rescisao);				
					
				
					
					if(!empty($row_rescisao['total_liquido'])) {						
						$total_movimentos['4007'][$mes] =  $row_rescisao['total_liquido'];
						
					} 
					
				
			}



?>


<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; margin-top: 60px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center" colspan="3">
    
    <table width="980" border="0" cellspacing="2" cellpadding="3" class="relacao" style="font-weight:normal; line-height:22px; ">
      <tr class="secao_pai">
        <td colspan="15"><?=$participante[$coluna_id]?> - <?=$participante['nome']?></td>
        </tr>
      <tr>
        <td class="secao" style="width:10%">Endere&ccedil;o:</td>
        <td colspan="8" style="width:50%">
		<?php echo "$participante[endereco]"; 
		      if(!empty($participante['bairro'])) { 
			       echo ", $participante[bairro]"; 
			  } if(!empty($participante['cidade'])) { 
			       echo ", $participante[cidade]"; 
			  } if(!empty($participante['uf'])) { 
			       echo " - $participante[uf]"; 
			  } ?></td>
        <td class="secao" style="width:10%">Nascimento:</td>
        <td colspan="2" style="width:10%"><?=$participante['data_nasci']?></td>
        <td class="secao" style="width:10%">Nacionalidade:</td>
        <td colspan="2" style="width:10%"><?=$participante['nacionalidade']?></td>
        </tr>
      <tr>
        <td class="secao" style="width:10%">Cargo:</td>
        <td colspan="8" style="width:50%"><?=$curso['nome']?></td>
        <td class="secao" style="width:10%">Admiss&atilde;o:</td>
        <td colspan="2" style="width:10%"><?=$participante['data_entrada']?></td>
        <td class="secao" style="width:10%">Afastamento:</td>
        <td colspan="2" style="width:10%"><?php if($participante['data_saida'] != "00/00/0000") { echo $participante['data_saida']; } ?></td>
        </tr>
        <tr>
        <td class="secao">Tipo de Pag:</td>
        <td colspan="2"><?=$pg['tipopg']?></td>
        <td class="secao">Sal&aacute;rio:</td>
        <td colspan="2"><?php echo "R$ "; echo number_format($curso['salario'], 2, ',', '.'); ?></td>
        <td class="secao">Ag&ecirc;ncia:</td>
        <td colspan="2"><?=$participante['agencia']?></td>
        <td class="secao">Conta:</td>
        <td colspan="2"><?=$participante['conta']?></td>
        <td class="secao">Banco:</td>
        <td colspan="2"><?=$banco['nome']?></td>
        </tr>
      <tr>
        <td class="secao">CPF:</td>
        <td colspan="2"><?=$participante['cpf']?></td>
        <td class="secao">RG:</td>
        <td colspan="2"><?=$participante['rg']?></td>
        <td class="secao">T&iacute;tulo:</span></td>
        <td colspan="2"><?=$participante['titulo']?></td>
        <td class="secao">CTPS:</td>
        <td colspan="2"><?=$participante['serie_ctps']?></td>
        <td class="secao">PIS/PASEP:</td>
        <td colspan="2"><?=$participante['pis']?></td>
        </tr>
      <tr class="secao">
        <td>Evento</td>
        <td>Descri&ccedil;&atilde;o</td>
        <td colspan="13">
          
          <table cellpadding="0" cellspacing="0" width="100%" class="relacao" style="border:0px;">
            <tr>
              <td class="coluna2" align="center">Jan</td>
              <td class="coluna2" align="center">Fev</td>
              <td class="coluna2" align="center">Mar</td>
              <td class="coluna2" align="center">Abr</td>
              <td class="coluna2" align="center">Mai</td>
              <td class="coluna2" align="center">Jun</td>
              <td class="coluna2" align="center">Jul</td>
              <td class="coluna2" align="center">Ago</td>
              <td class="coluna2" align="center">Set</td>
              <td class="coluna2" align="center">Out</td>
              <td class="coluna2" align="center">Nov</td>
              <td class="coluna2" align="center">Dez</td>
              <td class="coluna_total"  align="center">Total</td>
              </tr>
            </table>
          
          </td>
      </tr>
    
    <?php
	$array_cod_rend = array('5029','0001','5012',0003); 	
	$array_cod_desc = array(5019,5020,5021,9500);
	$array_tipo 	= array(1 => 'CREDITO',2 => 'DEBITO');
	 
	 
	
	 
	foreach($array_tipo as $chave => $tipo){
	
	
	
		    foreach($total_movimentos as $codigo => $valor){
				
				
							
						$nome_movimento = $total_movimentos[$codigo]['nome'];	
						
						if($total_movimentos[$codigo]['categoria'] == $tipo) {
						?>
							     <tr class="<?php if($alternateColor++%2==0) { echo 'linha_um'; } else { echo 'linha_dois'; } ?>">
									<td><?php echo  $codigo; ?></td>
									<td><?php echo $nome_movimento; ?></td>
							        <td colspan="12">
								          <table cellpadding="0" cellspacing="0" width="100%" class="relacao" style="border:1px;font-size:9px;" border="0">
					                      
								            <tr class="<?php if($alternateColor++%2==0) { echo 'linha_um'; } else { echo 'linha_dois'; } ?>">
								             <?php
											 	
										        for($i=1; $i<=12;$i++){
																									
														if($chave ==1) {
															$total_rendimentos2[$i] += $total_movimentos[$codigo][$i];
														} else {
																$total_descontos[$i] += $total_movimentos[$codigo][$i];
														}
														
														
														if(empty($total_movimentos[$codigo][$i])){ $valor = '&nbsp;'; } else { $valor = $total_movimentos[$codigo][$i]; };
														echo '<td class="coluna2" align="center">'.number_format($valor,2,',','.').'</td>';	
												}
												
												?>
                                                
                                                
							                    <td class="coluna_total" align="center">
												 <?php 
												   
												  $somatorio = array_sum($total_movimentos[$codigo]); 
												   
													 if($chave ==1) {
													
														$total_rendimentos2['total'] += $somatorio;
													
													} else {
														 $total_descontos['total']   += $somatorio;
													}
												
													
												
												   echo number_format($somatorio,2,',','.');
												   
												unset($somatorio);   
												   
												   ?>
                                                </td>
											</tr>
                                            
							                </table>
							        </td>
							   </tr>           
					      
						<?php
						}
						
			
		    }
		
		
		///EXIBINDO TOTALIZADORES
		if($chave == 1){ $nome_tipo = 'Rendimentos'; } else {$nome_tipo = 'Descontos'; }
	
	
	
		echo '<tr  style="background-color: #D1D1D1"><td colspan="2">Total '.$nome_tipo.'</td>
			<td colspan="13">
		
		 			<table cellpadding="0" cellspacing="0" width="100%" style="border:0px;" border="0">
					<tr  style="background-color: #D1D1D1">';
		
		
		
					  for($i=1; $i<=12;$i++){
						  
								if($chave ==1) {
																		
										echo '<td class="coluna2">'.number_format($total_rendimentos2[$i],2,',','.').'</td>' ;
										
								
								} else {
									
										echo  '<td class="coluna2">'.number_format($total_descontos[$i],2,',','.').'</td>';
										
								}
								
								
						  
					  }									
					
					
					if($chave ==1) {								
									
										echo '<td class="coluna_total">'.number_format($total_rendimentos2['total'],2,',','.').'</td>' ;
								
								} else {
									
										echo  '<td class="coluna_total">'.number_format($total_descontos['total'],2,',','.').'</td>';
										
								}
											
					
					
		echo '</tr></table>
		</td>
		</tr>
		';
		}
		
		//////VALOR LIQUIDO
		echo '<tr  style="background-color: #D1D1D1"><td colspan="2">Total Valor Líquido:</td>
			<td colspan="13">
		
		 			<table cellpadding="0" cellspacing="0" width="100%" style="border:0px;" border="0">
					<tr style="background-color: #D1D1D1">';
		
		
		
					  for($i=1; $i<=12;$i++){																		
										echo '<td class="coluna2">'.number_format($total_rendimentos2[$i] - $total_descontos[$i],2,',','.') .'</td>' ;	
					  }									
					
					echo  '<td class="coluna_total">'.number_format($total_rendimentos2['total'] - $total_descontos['total'],2,',','.').'</td>';
										
					
					
								
					
					
		echo '</tr>
		</table>
				
		</td>
		</tr>';
			
	?>  
    </table>
	</td>
  </tr>
</table>



</body>
</html>
<?php

unset($total_rendimentos2,$total_descontos,$total_movimentos);

	endwhile;



}
 ?>