<?php
include('../include/restricoes.php');
include('../../../conn.php');
include('../../../classes/formato_valor.php');
include('../../../classes/formato_data.php');
include('../../../classes/valor_extenso.php');

$projeto = $_POST['projeto'];
$master  = $_POST['master'];
$ano	 = $_POST['ano'];
$obrigacao_id = $_POST['obrigacao_id'];
$entregue_id = $_POST['entregue_id'];




// Consulta do Projeto
$qr_projeto  = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_assoc($qr_projeto);

// Consulta de Região
$qr_regiao  = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_projeto[id_regiao]'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

// Consulta da Empresa
$qr_empresa  = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '$row_projeto[id_regiao]'");
$row_empresa = mysql_fetch_assoc($qr_empresa);

// Consulta do Master
$qr_master  = mysql_query("SELECT * FROM master WHERE id_master = '$master'");
$row_master = mysql_fetch_assoc($qr_master);

// Consulta do Banco
$qr_banco  = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_projeto[id_banco_principal]'");
$row_banco = mysql_fetch_assoc($qr_banco);

// Data
$data = '31/12/'.$ano;

// Consulta de Totais do Financeiro
/*$total_repasse = @mysql_result(mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) FROM entrada WHERE tipo IN('12') AND id_projeto = '$projeto' AND data_vencimento BETWEEN '$row_projeto[inicio]' AND '$row_projeto[termino]'"),0);

$total_folha = @mysql_result(mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) FROM saida WHERE tipo IN('30','31','32') AND id_projeto = '$projeto' AND data_vencimento BETWEEN '$row_projeto[inicio]' AND '$row_projeto[termino]'"),0);

$total_gps = @mysql_result(mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) FROM saida WHERE tipo IN('62') AND id_projeto = '$projeto' AND data_vencimento BETWEEN '$row_projeto[inicio]' AND '$row_projeto[termino]'"),0);

$total_fgts = @mysql_result(mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) FROM saida WHERE tipo IN('50') AND id_projeto = '$projeto' AND data_vencimento BETWEEN '$row_projeto[inicio]' AND '$row_projeto[termino]'"),0);

$total_irrf = @mysql_result(mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) FROM saida WHERE tipo IN('55') AND id_projeto = '$projeto' AND data_vencimento BETWEEN '$row_projeto[inicio]' AND '$row_projeto[termino]'"),0);

$total_pis = @mysql_result(mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) FROM saida WHERE tipo IN('88') AND id_projeto = '$projeto' AND data_vencimento BETWEEN '$row_projeto[inicio]' AND '$row_projeto[termino]'"),0);

$total_provisao = @mysql_result(mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) FROM saida WHERE tipo IN('78') AND id_projeto = '$projeto' AND data_vencimento BETWEEN '$row_projeto[inicio]' AND '$row_projeto[termino]'"),0);

*/

$total_repasse = @mysql_result(mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) FROM entrada WHERE tipo IN('12') AND id_projeto = '$projeto' AND MONTH(data_vencimento) = '12' AND YEAR(data_vencimento) = '$ano'"),0);

$total_rendimento = @mysql_result(mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) FROM entrada WHERE tipo IN('113') AND id_projeto = '$projeto' AND MONTH(data_vencimento) = '12' AND YEAR(data_vencimento) = '$ano'"),0);

$total_folha = @mysql_result(mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) FROM saida WHERE tipo IN('30','31','32') AND id_projeto = '$projeto' AND MONTH(data_vencimento) = '12' AND YEAR(data_vencimento )= '$ano'"),0);

$total_gps =@mysql_result(mysql_query("SELECT SUM(REPLACE(saida.valor, ',', '.'))
											FROM 
											(saida INNER JOIN pagamentos 
											ON saida.id_saida = pagamentos.id_saida)
											INNER JOIN rh_folha ON rh_folha.id_folha = pagamentos.id_folha
											WHERE pagamentos.tipo_pg = '1' AND saida.status = '2' 
											AND rh_folha.projeto = '$projeto'
											AND pagamentos.mes_pg = '12' AND pagamentos.ano_pg = '$ano'
											"),0);


$total_fgts = @mysql_result(mysql_query("SELECT SUM(REPLACE(saida.valor, ',', '.'))
											FROM 
											(saida INNER JOIN pagamentos 
											ON saida.id_saida = pagamentos.id_saida)
											INNER JOIN rh_folha ON rh_folha.id_folha = pagamentos.id_folha
											WHERE pagamentos.tipo_pg = '2' AND saida.status = '2'
											AND rh_folha.projeto = '$projeto'
											AND pagamentos.mes_pg = '12' AND pagamentos.ano_pg = '$ano'
											"),0);

$total_irrf = @mysql_result(mysql_query("SELECT SUM(REPLACE(saida.valor, ',', '.'))
											FROM 
											(saida INNER JOIN pagamentos 
											ON saida.id_saida = pagamentos.id_saida)
											INNER JOIN rh_folha ON rh_folha.id_folha = pagamentos.id_folha
											WHERE pagamentos.tipo_pg = '4' AND saida.status = '2'
											AND rh_folha.projeto = '$projeto'
											AND pagamentos.mes_pg = '12' AND pagamentos.ano_pg = '$ano'
											"),0);

$total_pis = @mysql_result(mysql_query("SELECT SUM(REPLACE(saida.valor, ',', '.'))
												FROM 
												(saida INNER JOIN pagamentos 
												ON saida.id_saida = pagamentos.id_saida)
												INNER JOIN rh_folha ON rh_folha.id_folha = pagamentos.id_folha
												WHERE pagamentos.tipo_pg = '3' AND saida.status = '2'
												AND rh_folha.projeto = '$projeto'
												AND pagamentos.mes_pg = '12' AND pagamentos.ano_pg = '$ano'
												"),0);
											
$total_provisao = @mysql_result(mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) FROM saida WHERE tipo IN('78') AND id_projeto = '$projeto' AND MONTH(data_vencimento) = '12' AND YEAR(data_vencimento)='$ano'  "),0);

$total_tarifa = @mysql_result(mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) FROM saida WHERE tipo IN('58','97','98','119') AND id_projeto = '$projeto' AND MONTH(data_vencimento) = '12' AND YEAR(data_vencimento)='$ano' "),0);

$total_taxa_adm = @mysql_result(mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) FROM saida WHERE tipo IN('131') AND id_projeto = '$projeto' AND MONTH(data_vencimento) = '12' AND YEAR(data_vencimento)='$ano' "),0);

$total_prestador = @mysql_result(mysql_query("SELECT SUM(REPLACE(valor, ',', '.')) FROM saida WHERE tipo IN('132') AND id_projeto = '$projeto' AND MONTH(data_vencimento) = '12' AND YEAR(data_vencimento)='$ano' "),0);
	
///Consulta para alteração nos valores	
$qr_entregue = mysql_query("SELECT * FROM obrigacoes_entregues WHERE entregue_obrigacao = '$obrigacao_id' AND entregue_id = '$entregue_id' AND entregue_status='1'");
$row_entregue =  mysql_fetch_assoc($qr_entregue);
		  
		  		  
		  	
/*
SELECT SUM(REPLACE(saida.valor, ',', '.'))
FROM 
(saida INNER JOIN pagamentos 
ON saida.id_saida = pagamentos.id_saida)
INNER JOIN rh_folha ON rh_folha.id_folha = pagamentos.id_folha
WHERE pagamentos.tipo_pg = '2' AND saida.status = '2'
AND pagamentos.mes_pg = '12' AND pagamentos.ano_pg = '$ano'
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Concilia&ccedil;&atilde;o Banc&aacute;ria</title>
<script type="text/javascript" src="../../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript" src="../../../jquery/priceFormat.js" ></script>

<script type="text/javascript">


  function float2moeda(num) {
   
   x = 0;
 
   if(num<0) {
      num = Math.abs(num);
      x = 1;
   }
   if(isNaN(num)) num = "0";
   cents = Math.floor((num*100+0.5)%100);
 
   num = Math.floor((num*100+0.5)/100).toString();
 
   if(cents < 10) cents = "0" + cents;
      for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
         num = num.substring(0,num.length-(4*i+3))+'.'
               +num.substring(num.length-(4*i+3));
   ret = num + ',' + cents;
   if (x == 1) ret = ' - ' + ret;return ret;
}


$(function(){

$('.valores').priceFormat({
		prefix: '',
		centsSeparator: ',',
		thousandsSeparator: '.'
		
	});	

	
	
	
	
	
	$(".valores").blur(function () {
	
		var valor_repasse 		= $('#total_repasse').val();
		var valor_rendimento  	= $('#total_rendimento').val();
		var valor_folha    		= $('#total_folha').val();
		var valor_gps      		= $('#total_gps').val();
		var valor_fgts     		= $('#total_fgts').val();
		var valor_irrf     		= $('#total_irrf').val();
		var valor_pis      		= $('#total_pis').val();
		var valor_provisao 		= $('#total_provisao').val();
		var valor_tarifa   		= $('#total_tarifa').val();
		var valor_taxa_adm 		= $('#total_taxa_adm').val();
		var valor_prestador		= $('#total_prestador').val();
		
		
		var entregue_id = $('#entregue_id').val();
	
		$.ajax({
			type: "GET",

			url: "../action.mudavalor.php",
	
			data: 
			"total_repasse="+valor_repasse + 
			"&total_rendimento="+valor_rendimento + 
			"&total_folha="+valor_folha + 
			"&total_gps="+valor_gps + 
			"&total_fgts="+valor_fgts + 
			"&total_irrf="+valor_irrf + 
			"&total_pis="+valor_pis +
			"&total_provisao="+valor_provisao +
			"&total_tarifa="+valor_tarifa + 
			"&total_taxa_adm="+valor_taxa_adm + 
			"&total_prestador="+valor_prestador +
			"&mudar="+2+"&id="+entregue_id,
				  

			success: function(response) {
																
											eval('var resposta = '+ response);
											if(resposta.erro){
												alert('Ocorreu um erro');
											} else {
												
											//CALCULO DO TOTAL DO DÈBITO E O SALDO ATUAL
											var total_debito = 
											parseFloat(resposta.folha) + 
											parseFloat(resposta.gps) + 
											parseFloat(resposta.fgts) + 
											parseFloat(resposta.irrf) + 
											parseFloat(resposta.pis) + 
											parseFloat(resposta.provisao) + 
											parseFloat(resposta.tarifa) + 
											parseFloat(resposta.taxa_adm) + 
											parseFloat(resposta.prestador);
											var saldo_atual = 
											parseFloat(resposta.repasse) + 
											parseFloat(resposta.rendimento) - total_debito;
											
											
											//TRANSFORMANDO PARA O FORMATO REAL
											$('#saldo_atual').html('<strong>'+ float2moeda(saldo_atual) +'</strong>');
											
											$('#total_folha').val( float2moeda(resposta.folha));
											$('#total_gps').val( float2moeda(resposta.gps));
											$('#total_fgts').val( float2moeda(resposta.fgts));
											$('#total_irrf').val( float2moeda(resposta.irrf));
											$('#total_pis').val( float2moeda(resposta.pis));
											$('#total_provisao').val( float2moeda(resposta.provisao));
											$('#total_tarifa').val( float2moeda(resposta.tarifa));
											$('#total_taxa_adm').val( float2moeda(resposta.taxa_adm));
											$('#total_prestador').val( float2moeda(resposta.prestador));
											
											
											
											console.log(saldo_atual);
				}
					
				
			}
				
		});//fim ajax
		
	});//fim função
	
	
});
</script>


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
        <td colspan="5"><?php echo $row_master['razao']; ?></td>
      </tr>
      <tr>
        <td class="secao">PROJETO</td>
        <td colspan="5"><?php echo $row_regiao['regiao']; ?></td>
      </tr>
      <tr>
        <td class="secao">PROCESSO N&ordm;</td>
        <td colspan="2"><?php echo $row_projeto['numero_contrato']; ?></td>
        <td width="23%" class="secao">TERMO DE PARCERIA N&ordm;</td>
        <td width="35%" colspan="2"><?php echo $row_projeto['numero_contrato']; ?></td>
      <?php $qr_subprojetos = mysql_query("SELECT * FROM subprojeto WHERE id_projeto = '".$row_projeto['id_projeto']."' AND status_reg = '1'");
	        $total_subprojetos = mysql_num_rows($qr_subprojetos);
	  		while($row_subprojeto = mysql_fetch_assoc($qr_subprojetos)) {
				 if($i%2 == 0) {
					 echo '</tr>';
					 echo '<tr>';
        		 } $i++; ?>
        <td class="secao">TERMO ADITIVO N&ordm;</td>
        <td colspan="2"><?php echo $row_subprojeto['numero_contrato']; ?></td>
        <?php if($total_subprojetos == $i and $i%2 != 0) { ?>
        <td colspan="3">&nbsp;</td>
      <?php } } ?>
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
        <td><?php echo $row_banco['razao']; ?></td>
        <td class="secao">AG&Ecirc;NCIA:</td>
        <td><?php echo $row_banco['agencia']; ?></td>
        <td class="secao">CONTA:</td>
        <td><?php echo $row_banco['conta']; ?></td>
      </tr>
      <tr>
        <td class="secao">PER&Iacute;ODO:</td>
        <td colspan="5"><?php echo '01/12/'.$ano.' À '.'31/12/'.$ano; ?></td>
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
          <?php echo $data; ?><br />
          <?php echo $data; ?><br />
          -</p></td>
        <td align="center"><br />
        
		<?php if($row_entregue['total_repasse'] == 0){
         	
				echo 'R$<input name="total_repasse" id="total_repasse" value="'.formato_real($total_repasse).'" size="10" style="background-color:transparent;  border:0;" class="valores"/>';
			} else {
				$total_repasse = $row_entregue['total_repasse'];
				echo 'R$<input name="total_repasse" id="total_repasse" value="'.formato_real($row_entregue['total_repasse']).'" size="10" style="background-color:transparent;  border:0;" class="valores"/>';
				
			}
			?>
          
          <br />
         
		 <?php if($row_entregue['total_rendimento'] == 0){
         	
				echo 'R$<input name="total_rendimento" id="total_rendimento" value="'.formato_real($total_rendimento).'" size="10" style="background-color:transparent;  border:0;" class="valores"/>';
			} else {
				$total_rendimento = $row_entregue['total_rendimento'];
				echo 'R$<input name="total_rendimento" id="total_rendimento" value="'.formato_real($row_entregue['total_rendimento']).'" size="10" style="background-color:transparent;  border:0;" class="valores"/>';
				
			}
			?> 
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
        <span style=" line-height:20px;"><?php echo $data; ?></span><br />
        <span style=" line-height:20px;"> <?php echo $data; ?></span><br />
        <span style=" line-height:20px;"> <?php echo $data; ?></span><br />
        <span style=" line-height:20px;">  <?php echo $data; ?></span><br />
        <span style=" line-height:20px;">  <?php echo $data; ?></span><br />
        <span style=" line-height:20px;"> <?php echo $data; ?></span><br />
        <span style=" line-height:20px;">  <?php echo $data; ?></span><br />
         <span style=" line-height:20px;">  <?php echo $data; ?></span><br />
        <span style=" line-height:20px;">  <?php echo $data; ?></span></p>
         </td>
        <td align="center">
         
         &nbsp;
        <p align="center"><br />
           <span style="margin-left:-50px;"> - </span>
         <br />
          <?php 
		  
		  $total_calculado = array(
		  'total_folha' => $total_folha, 
		  'total_gps' => $total_gps,   
		  'total_fgts' => $total_fgts, 
		  'total_irrf' => $total_irrf, 
		  'total_pis' => $total_pis, 
		  'total_provisao' => $total_provisao, 
		  'total_tarifa' => $total_tarifa, 
		  'total_taxa_adm' => $total_taxa_adm , 
		  'total_prestador' => $total_prestador);
		  
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
		  foreach($total_calculado as $tipo => $valor):
		  		  
			 
			  if($row_entregue[$tipo] == 0.00){ 
			 
			 		$totais_tipo[$tipo] = $valor; 
					echo'<span style="line-height:14px;">R$</span><input name="'.$tipo.'" id="'.$tipo.'" value="'.formato_real($valor).'"  size="10" style="background-color:transparent; border:0;margin-top:0; margin:bottom;" class="valores"/><br />';
			   
				}else {
				 
					 $totais_tipo[$tipo] = $row_entregue[$tipo]; 			
					 echo '<span style="line-height:14px;">R$</span><input name="'.$tipo.'" id="'.$tipo.'" value="'.formato_real($row_entregue[$tipo]).'"  size="10" style="background-color:transparent; border:0;border:0;margin-top:0" class="valores"/><br />';
			   }
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
        
		
		//CALCULANDO O TOTAL DO DÉBITO
		foreach($totais_tipo as $tipo=>$valor ):
			$total_debito+= $valor;
		endforeach;
		
		//CALCULO DO SALDO ATUAL		
		$saldo_atual= $total_repasse + $total_rendimento - $total_debito;
			
		?>
        
        <td colspan="3"><strong>SALDO ATUAL</strong></td>
        <td>&nbsp;</td>
        <td align="center"  id="saldo_atual"><strong><?php echo  formato_real($saldo_atual);?></strong></td>
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
        <td colspan="2"><?php echo $row_master['responsavel']; ?></td>
        <td class="secao">NOME:</td>
        <td colspan="2"><?php echo $row_master['responsavel']; ?></td>
      </tr>
      <tr>
        <td class="secao">CARGO:</td>
        <td colspan="2">Presidente</td>
        <td class="secao">CARGO:</td>
        <td colspan="2">Presidente</td>
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

