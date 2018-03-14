<?php
include('../include/restricoes.php');
include('../../../conn.php');
include('../../../classes/formato_valor.php');
include('../../../classes/formato_data.php');
include('../../../classes/valor_extenso.php');

$projeto    = $_POST['projeto'];
$master     = $_POST['master'];
$ano	    = $_POST['ano'];
$subprojeto = $_POST['subprojeto'];
$data       = $_POST['data'];

list($data_ano,$data_mes,$data_dia) = explode('-',$data);


// Consulta do Projeto
//$qr_projeto  = mysql_query("SELECT * FROM projeto LEFT JOIN parceiros ON id_parceiro1 = parceiro_id WHERE id_projeto = '$projeto'");
$qr_projeto  = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto' ");
$row_projeto = mysql_fetch_assoc($qr_projeto);



list($ano_inicio,$mes_inicio,$dia_inicio) = explode('-',$row_projeto['inicio']);
list($ano_termino,$mes_termino,$dia_termino) = explode('-',$row_projeto['termino']);

// Consulta de Regi�o
$qr_regiao  = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_projeto[id_regiao]'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

// Consulta da Empresa
$qr_empresa  = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$row_projeto[id_regiao]'");
$row_empresa = mysql_fetch_assoc($qr_empresa);

// Consulta do Master
$qr_master  = mysql_query("SELECT * FROM master WHERE id_master = '$master'");
$row_master = mysql_fetch_assoc($qr_master);

// Consulta
for($a=1; $a<14; $a++) {
	
	$mes            = sprintf('%02d', $a);
	$qr_repasse     = mysql_query("SELECT * FROM entrada WHERE tipo = '12' AND id_projeto = '$projeto' AND month(data_pg) = '$mes' AND year(data_pg) = '$ano'");
	$row_repasse    = mysql_fetch_assoc($qr_repasse);
	$total_repasse += str_replace(',', '.', $row_repasse['valor']);
	
}

// M�s em Extenso
$meses_pt = array('Erro','Janeiro','Fevereiro','Mar�o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
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
        <td colspan="2" class="secao">�RG�O P�BLICO PARCEIRO</td>
        <td colspan="6">Prefeitura Municipal de <?php echo $row_regiao['regiao']; ?></td>
      </tr>
      <tr>
        <td colspan="2" class="secao">ENTIDADE PARCEIRA (OSCIP)</td>
        <td colspan="6"><?php echo $row_master['razao']; ?> - OSCIP N&ordm; 08026.012349/2004-40</td>
      </tr>
      <tr>
        <td colspan="2" class="secao">CNPJ</td>
        <td colspan="6"><?php echo $row_master['cnpj']; ?></td>
      </tr>
      <tr>
        <td colspan="2" class="secao">ENDERE&Ccedil;O E CEP</td>
        <td colspan="6">
		<?php echo implode(', ', explode(',',$row_master['endereco'],-3)); ?>
        <?php list($nulo,$nulo,$nulo,$cidade,$uf,$cep) = explode(',',$row_master['endereco']); ?>
         <?php echo $cidade; ?><?php echo $uf; ?> CEP: <?php echo $cep; ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="secao">RESPONS&Aacute;VEL (EIS) PELA ENTIDADE</td>
        <td colspan="6" style="text-transform:uppercase"><?php echo $row_master['responsavel']; ?></td>
      </tr>
      <tr>
        <td colspan="2" class="secao">OBJETO DO TERMO DE PARCERIA</td>
        <td colspan="6"><?php echo $row_projeto['descricao']; ?></td>
      </tr>
      <tr>
        <td colspan="2" class="secao">EXERC&Iacute;CIO</td>
        <td colspan="6"><?php echo $ano; ?></td>
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
<?php
////////////////VERIFICA��O DE ENTRADAs (PROJETO) E MOSTRANDO PROJETO CASO EXISTA ENTRADAS NO ANO ESCOLHIDO
 $qr_notas = mysql_query("SELECT * FROM 
   (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
   INNER JOIN entrada 
   ON notas_assoc.id_entrada = entrada.id_entrada
   WHERE notas.id_projeto = '$projeto' AND notas.status = 1 AND notas.tipo_contrato = '$projeto'   AND entrada.status IN(1,2)  AND notas.tipo_contrato2 = 'projeto' AND YEAR(entrada.data_vencimento) = '$ano' ORDER BY entrada.data_vencimento") or die(mysql_error());   
  
  if(mysql_num_rows($qr_notas) != 0 or substr($row_projeto['termino'],0,4) == $ano or substr($row_projeto['inicio'],0,4) == $ano) {
?>	
	
  <tr>
    <td style="text-transform:uppercase;"><?php echo $row_projeto['tipo_contrato']; ?></td>
    <td> <?php echo 'N&ordm '.$row_projeto['numero_contrato']; ?> </td>
    <td colspan="2" align="center"><?php echo formato_brasileiro($row_projeto['data_assinatura']); ?></td>
    <td colspan="2" align="center"><?php echo abs((int)floor((strtotime($row_projeto['inicio']) - strtotime($row_projeto['termino'])) / 86400)); ?> DIAS</td>
    <td colspan="2" align="center">R$ <?php echo formato_real($row_projeto['verba_destinada']); ?></td>
  </tr>
  <?php } 	
///////////////////////////////////////////////

  
///  $total_valor = $row_projeto['verba_destinada'];  

/////////////////SUBPROJETOS
			$qr_subprojeto  = mysql_query("SELECT * FROM subprojeto WHERE id_projeto = '$projeto' AND status_reg = 1 " );
			while($row_subprojeto = mysql_fetch_assoc($qr_subprojeto)):

						 $qr_notas = mysql_query("SELECT * FROM 
						   (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
						   INNER JOIN entrada 
						   ON notas_assoc.id_entrada = entrada.id_entrada
						   WHERE notas.id_projeto = '$projeto' AND notas.status = 1 AND notas.tipo_contrato = '$row_subprojeto[id_subprojeto]'   AND entrada.status IN(1,2)  AND notas.tipo_contrato2 = 'subprojeto' AND YEAR(entrada.data_vencimento) = '$ano' ORDER BY entrada.data_vencimento") or die(mysql_error());
   
				  // if(mysql_num_rows($qr_notas) != 0 or (substr($row_subprojeto['termino'],0,4) == $ano or substr($row_subprojeto['inicio'],0,4) == $ano) ) 
				  if(mysql_num_rows($qr_notas) != 0 ) {
			
			//if($row_subprojeto['tipo_termo_aditivo'] == '2') continue;			
			///$total_valor += $row_subprojeto['verba_destinada']; ?>
            
          <tr>
            <td><?php echo $row_subprojeto['tipo_subprojeto']; ?></td>
            <td>N&ordm; <?php echo $row_subprojeto['numero_contrato']; ?></td>
            <td colspan="2" align="center"><?php echo ($row_subprojeto['inicio'] != '0000-00-00') ? formato_brasileiro($row_subprojeto['inicio']) : formato_brasileiro($row_subprojeto['data_assinatura']); ?></td>
            <td colspan="2" align="center"><?php echo abs((int)floor((strtotime($row_subprojeto['inicio']) - strtotime($row_subprojeto['termino'])) / 86400)); ?> DIAS</td>
            <td colspan="2" align="center">R$ <?php echo $row_subprojeto['verba_destinada']; ?></td>
          </tr>
	  <?php	  
          }
        endwhile; ?>
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
  
  <?php if(empty($subprojeto)) {
	  
		   $qr_notas = mysql_query("SELECT * FROM notas WHERE id_projeto = '$projeto' AND ((YEAR(data_emissao) = '$ano') OR (YEAR(data_emissao) = '".($ano-1)."' )) AND status = '1' ORDER BY data_emissao ASC");
		   while($row_nota = mysql_fetch_assoc($qr_notas)) { 
		   
		   // buscando subtipo da entrada
                	//entrada.id_entrada, entrada.subtipo, entrada.n_subtipo
                	$qr_nota = mysql_query("SELECT * FROM 
                	(notas INNER JOIN notas_assoc USING(id_notas)) 
                	INNER JOIN entrada ON notas_assoc.id_entrada = entrada.id_entrada 
                	WHERE notas.id_notas = '$row_nota[id_notas]' AND entrada.status = 2 AND YEAR(entrada.data_vencimento) = '$ano'");
					$num_rows = mysql_num_rows($qr_nota);
					
						if(empty($num_rows)) continue;
					
					
		   ?>
       
              <tr>
                <td>MUNICIPAL</td>
                <td align="center">R$ <?php echo formato_real($row_nota['valor']); $totalizador_repasse += (float) str_replace(',','.',$row_nota['valor']); ?></td>
                <td colspan="3" style="margin:0px; padding:0px;">
                	<table width="100%" height="100%" cellpadding="7" cellspacing="0">
                		<?php while($rw_nota = mysql_fetch_assoc($qr_nota)):?>
                			<tr align="center">
                				<td  width="40%" style="border-right: 1px #000 solid; ">
                					<?php 
                					switch ($rw_nota['subtipo']) {
										case '1':
											$documento_conta = 'DOC';
											break;
										case '2':
											$documento_conta = 'TED';
											break;
										case '3':
											$documento_conta = 'Cheque';
											break;
										case '4':
											$documento_conta = 'Dinheiro';
											break;
										case '5' : 
											$documento_conta = 'Transfer�ncia';
											break;
										default:
											$documento_conta = 'N�o especificado';
											break;
										}
                					echo $documento_conta;
                					echo ($rw_nota['n_subtipo'] != 0) ? ' - ' . $rw_nota['n_subtipo'] : '';
						  			?>
                				</td>
                				<td  width="20%" style="border-right: 1px #000 solid; ">
                					<?php echo formato_brasileiro($rw_nota['data_vencimento']); ?>
                				</td>
                				<td  width="40%">
                					R$ <?php 
									
                					$valor_entrada = (float) str_replace(',','.',$rw_nota['valor']); 
									$totalizador_entrada += $valor_entrada;
									//$totalizador_repasse += $valor_entrada;
									echo number_format($valor_entrada,2,',','.');
									?>
                				</td>
                			</tr>
                		<?php endwhile;?>
                		</table>		
						
					
					</td>
              </tr>
  
  <?php $total_notas += $row_nota['valor']; } } ?>
  
  <tr class="secao">
    <td colspan="4" align="right">RECEITA COM APLICA&Ccedil;&Otilde;ES FINANCEIRAS DOS REPASSES P&Uacute;BLICOS</td>
    <td>R$ <?php 

   //echo formato_real($totalizador_repasse); 
   echo formato_real($totalizador_entrada);
   ?></td>
  </tr>
  <tr>
    <td colspan="4" align="right" class="secao">TOTAL:</td>
    <td align="center">R$ <?php echo formato_real($totalizador_entrada); //echo formato_real($totalizador_repasse); ?></td>
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
      <span style="text-transform:uppercase"><?php echo $row_master['responsavel']; ?></span><br />
      <br />
      _____________________________________
    </td>
  </tr>
  <tr>
    <td colspan="8"><span style="text-transform:uppercase"><?php echo $row_master['razao']; ?></span></td>
  </tr>
  <tr>
    <td colspan="8">
      <br />
      <br />
      <br />
      Vem indicar, na forma abaixo detalhada, a aplica&ccedil;&atilde;o dos recursos recebidos no    exerc&iacute;cio supra mencionado, na import&acirc;ncia total de R$  
      R$ <?php echo formato_real($totalizador_entrada); ?>
&nbsp;(<?php echo htmlentities(valorPorExtenso($totalizador_entrada),ENT_COMPAT,'UTF-8'); unset($totalizador_entrada);?>)
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
  
  
  
  


  
   for($a=0; $a<=12; $a++) {
	
	  		
/////VERIFICACAO DE EXISTENCIA DE NOTAS NO M�S DE DEZEMBRO DO ANTERIOR A COMPET�NCIA			
/*			if($a == 0){
						
					$qr_despesas = mysql_query("SELECT * FROM notas WHERE id_projeto = '$projeto' AND  YEAR(data_emissao) = '".($ano-1)."' AND MONTH(data_emissao) = '12' AND status = '1' ORDER BY data_emissao ASC");
					$verifica_12 = mysql_num_rows($qr_despesas);
			
					if($verifica_12 == 0) {
					
					continue;
					
					}
						
				} else {*/
				
				
				$mes   = sprintf('%02d',$a);				
				$qr_despesas = mysql_query("SELECT  notas.id_notas, notas.data_emissao, notas.id_projeto, notas.numero,notas.valor,notas.tipo, notas_assoc.id_entrada,entrada.data_pg, entrada.data_vencimento 
											FROM notas
											INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas
											INNER JOIN entrada ON notas_assoc.id_entrada= entrada.id_entrada
											WHERE notas.id_projeto = '$projeto' AND (MONTH(entrada.data_vencimento) = '$mes' AND YEAR(entrada.data_vencimento) = '$ano')  AND notas.status = '1'  AND entrada.status = 2 ORDER BY entrada.data_vencimento ASC");
		
				//}
	  
//////////////////////////////////////////////////////////////////////	

			settype($ids_notas,'array');
			settype($ids_numeros,'array');
			
			while($row_despesa = mysql_fetch_assoc($qr_despesas)) {
				
				//verificao tipo de nota
				if($row_despesa['tipo'] == 1){
					$tipo_nota = 'Nota fiscal ';
				} else {
					$tipo_nota = 'Carta de medi��o ';	
				}
				
				$ids_notas[] = "'$row_despesa[id_notas]'";
				$ids_numeros[] = $tipo_nota.$row_despesa['numero']; 
				
			}
			
			$ids_notas = implode(',',$ids_notas);
			$ids_numeros = implode(', ',array_unique($ids_numeros));
			
			
		
			
			$qr_total = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
									(notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
									INNER JOIN entrada 
									ON notas_assoc.id_entrada = entrada.id_entrada
									WHERE notas.id_notas IN(".$ids_notas.")  AND entrada.status = 2 AND YEAR(entrada.data_vencimento) = '$ano' AND notas.status = 1 AND notas.id_projeto = '$projeto' AND (MONTH(entrada.data_vencimento) = '$mes' AND YEAR(entrada.data_vencimento) = '$ano')
									");
					
		
									
			$total_entrada = (float) @mysql_result($qr_total,0); 

	
			
			
		if($a == 0) {
				$qnt_dias_mes = cal_days_in_month(CAL_GREGORIAN,12,$ano-1);
		
		}else {
			
			$qnt_dias_mes = cal_days_in_month(CAL_GREGORIAN,$mes,$ano);
		
		}
			
			
			
			
			if(!empty($total_entrada)) { ?>
			<tr>
			  <td colspan="2" style="text-transform:uppercase">SERVI&Ccedil;O DE TERCEIROS- <?php echo $ids_numeros; ?> </td>
			  <td colspan="2" align="center"><?php
			  
			  
			  /*if($ano_add ==($ano -1 )) {
			  		 echo "01/$dezembro_anterior/$ano_add a $qnt_dias_mes/$dezembro_anterior/$ano_add"; 
					 
					 	unset($dezembro_anterior);
			  }else{*/
			  
			  
			  if($a == 0) {
			  
			   echo "01/12/".($ano-1)." a $qnt_dias_mes/12/".($ano-1); 
			  
			  } else {
				  
				   echo "01/$mes/$ano a $qnt_dias_mes/$mes/$ano"; 
			  }
				  
			  //}
				 ?></td>
			  <td colspan="2" align="center">MUNICIPAL</td>
			  <td colspan="2" align="center">R$ <?php echo formato_real($total_entrada); ?></td>
			</tr>
                
  <?php 	$total_despesas += $total_entrada; } unset($ids_notas,$ids_numeros,$total_entrada); }
  
  		/* for($a=1; $a<13; $a++) {
	  
			$mes   = sprintf('%02d',$a);
			$tipos = array("'132'", "'30','31','32'", "'62'", "'50'", "'55'", "'88'", "'78'");
			
			foreach($tipos as $tipo) {
	  		
				$qr_despesas = mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) AS valor, data_vencimento FROM saida WHERE tipo IN(".$tipo.") AND id_projeto = '".$projeto."' AND MONTH(data_vencimento) = '".$mes."' AND YEAR(data_vencimento) = '".$ano."' AND status = '2' ORDER BY data_vencimento ASC");          
				$row_despesa = mysql_fetch_assoc($qr_despesas);
				
				if($tipo == "'30','31','32'") {
					$nome = 'Folha de Pagamento';
				} else {
					$nome = @mysql_result(mysql_query("SELECT nome FROM entradaesaida WHERE id_entradasaida = ".$tipo.""),0);
				}
				
				if(!empty($row_despesa['valor'])) { ?>
                
                <tr>
                  <td colspan="2"><?php echo $nome; ?></td>
                  <td colspan="2" align="center"><?php echo formato_brasileiro($row_despesa['data_vencimento']); ?></td>
                  <td colspan="2" align="center">MUNICIPAL</td>
                  <td colspan="2" align="center">R$ <?php echo formato_real($row_despesa['valor']); ?></td>
                </tr>
                
  <?php $total_despesas += $row_despesa['valor']; } } } */ ?>
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
    <td colspan="7"><?php echo $row_regiao['regiao']; ?>, <?php echo $data_dia.' de '.$meses_pt[(int)$data_mes].' de '.$data_ano; ?>
	 &nbsp;
      ____________________________________
    </td>
  </tr>
  <tr>
    <td class="secao">RESPONS&Aacute;VEL:</td>
    <td colspan="7">LUIZ CARLOS MANDIA PRESIDENTE</td>
  </tr>
  <tr>
    <td colspan="2" class="secao">
      &nbsp;
     
      MEMBROS DO CONSELHO FISCAL:
    </td>
    <td colspan="6">VIT�RIO TRENTI
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
    <td colspan="6">F&Aacute;BIO SOUZA</td>
  </tr>
</table>

</td>
</tr>
</table>
</body>
</html>