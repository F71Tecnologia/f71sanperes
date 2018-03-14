<?php
include('../include/restricoes.php');
include('../../../conn.php');
include('../../../classes/formato_valor.php');
include('../../../classes/formato_data.php');
include('../../../classes/valor_extenso.php');
include('../../../classes_permissoes/acoes.class.php');

$ACOES = new Acoes();


$projeto = $_POST['id_projeto'];

$master  = $_POST['master'];
$ano	 = $_POST['ano_competencia'];
$obrigacao_id = $_POST['obrigacao_id'];

$ano_competencia	= $_POST['ano_competencia'];
$obrigacao_data 	= $_POST['obrigacao_data'];
$entregue_obrigacao	= $_POST['obrigacao_entrega'];
$data_referencia    = $_POST['data_referencia'];
$projeto_inicio 	= $_POST['projeto_inicio'];
$projeto_termino 	= $_POST['projeto_termino'];



$ano_base = date('Y')-1;

// Consulta do Projeto

$qr_projeto  = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_assoc($qr_projeto);


////////////////VERIFICAÇÂO
 $qr_notas = mysql_query("SELECT * FROM 
   (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
   INNER JOIN entrada 
   ON notas_assoc.id_entrada = entrada.id_entrada
   WHERE notas.id_projeto = '$projeto' AND notas.status = 1 AND notas.tipo_contrato = '$projeto'   AND entrada.status IN(1,2)  AND notas.tipo_contrato2 = 'projeto' AND YEAR(entrada.data_vencimento) = '$ano' ORDER BY entrada.data_vencimento") or die(mysql_error());
   
  
  if(mysql_num_rows($qr_notas) != 0  ) {
	
	$total_verba +=  $row_projeto['verba_destinada'];
	  
  } else if( substr($row_projeto['termino'],0,4) == $ano or substr($row_projeto['inicio'],0,4) == $ano) {
	 
	  $total_verba +=  $row_projeto['verba_destinada'];
  }
			
////////////////





//$qr_subprojeto  = mysql_query("SELECT * FROM subprojeto WHERE id_projeto = '$projeto' AND status_reg = 1"); antes
$qr_subprojeto  = mysql_query("SELECT * FROM subprojeto WHERE id_projeto = '$projeto' AND status_reg = 1 " );
while($row_subprojeto = mysql_fetch_assoc($qr_subprojeto)):

 $qr_notas = mysql_query("SELECT * FROM 
   (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
   INNER JOIN entrada 
   ON notas_assoc.id_entrada = entrada.id_entrada
   WHERE notas.id_projeto = '$projeto' AND notas.status = 1 AND notas.tipo_contrato = '$row_subprojeto[id_subprojeto]'   AND entrada.status IN(1,2)  AND notas.tipo_contrato2 = 'subprojeto' AND YEAR(entrada.data_vencimento) = '$ano' ORDER BY entrada.data_vencimento") or die(mysql_error());
   
  
  if(mysql_num_rows($qr_notas) != 0 ) {
	
	$total_verba +=  str_replace(',','.',str_replace('.','',$row_subprojeto['verba_destinada']));
	  
  }
									






endwhile;
	






//ROTINA para mostrar o valor previsto
	/*$ano_inicio_projeto = substr($row_projeto['inicio'], 0,4);
	
	if($ano_inicio_projeto < $ano) {
		
		
		
		if(mysql_num_rows($qr_subprojeto) == 0 ){
			
			$total_verba = $row_projeto['verba_destinada'];
			
		} else {
			
			$total_verba = $total_subprojeto ;
		
		}
		
		
		
	} else {
		$total_verba = $total_subprojeto + $row_projeto['verba_destinada'];
	}


$ano_projeto_termino = substr($row_projeto['termino'],0,4);
			if($ano_projeto_termino == $ano) {	   	
			
				$qr_nota = mysql_query("SELECT SUM(REPLACE(entrada.valor,',', '.')) as valor
										FROM (notas INNER JOIN notas_assoc USING(id_notas)) 
										INNER JOIN entrada ON notas_assoc.id_entrada = entrada.id_entrada
										WHERE   DATE(entrada.data_vencimento) >= '$ano-01-01' AND DATE(entrada.data_vencimento) <= '$row_projeto[termino]' AND notas.id_projeto = '$row_projeto[id_projeto]' AND notas.status = 1 ") or die(mysql_error());
						
				$row_verba =  mysql_fetch_assoc($qr_nota);
				$total_verba += $row_verba['valor'];
				
			} */


// Consulta de Região
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="../../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript" src="../../../jquery/priceFormat.js" ></script>
<title>Publica&ccedil;&atilde;o</title>
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
			  
			  

$(function () {
	
	$('#total_repasse').priceFormat({
		prefix: '',
		centsSeparator: ',',
		thousandsSeparator: '.'
		
	});
	
	
	$('#termino').mask('99/99/9999');
	
	
	$('#termino').blur(function (){
		
		var termino = $('#termino').val(); 
		var entregue_id =  $('#entregue_id').val(); 
		$.ajax({
			
			type:"GET",
			url: "../action.mudavalor.php",
			data: "termino="+termino+"&muda_valor="+3+"&id="+entregue_id,
			success: function(resposta) {
				
					console.log(resposta);	
								
				$('#termino').val(response);	
				
				
				}
		});
		
			
	});




	$("#total_repasse").blur(function () {
	
		var total_repasse = $(this).val().replace('.','').replace(',','.');				
		var previsto 	= $(".previsto").val();
	
		var diferenca =  total_repasse -  previsto;
	
		alert(total_repasse);
	
		var format_diferenca = float2moeda(diferenca);
		var format_resposta = float2moeda(total_repasse);	
	
										
								
		$('.diferenca').empty();
		$('.diferenca').append('R$ '+format_diferenca);
		$('.input_diferenca').val(format_diferenca);
		
		$('.realizado').html('R$ '+format_resposta);
		$('.input_realizado').val(format_resposta);					
					
			
		

	});
	
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


 #total_repasse{
	
		background-color:transparent;
	 border:0px;
	  margin:0;
	}
</style>

<style type="text/css" media="print">
#total_repasse{
	
		background-color:transparent;
	 border:0px;
	  margin:0;
	  font-size:11.3px;
	  letter-spacing:10	px;
	}
	

.finalizar{
background-color: #D7D7D7;
color:#000;
width:100px;
height:40px;

}
.finalizar:hover{
background-color:  #A4A4A4;
font-weight:bold;

}
</style>
</head>
<body style="text-align:center; margin:0; background-color:#efefef; font-family:Arial, Helvetica, sans-serif; font-size:13px; text-transform:uppercase;">

<form name="form" action="finalizar.php" method="post">

<table style="margin:50px auto; width:790px; border:1px solid #222; text-align:left; padding:10px; background-color:#fff;" cellpadding="4" cellspacing="1">
  <tr>
    <td colspan="6">
      <p align="center"><strong>DECRETO N&ordm; 3.100, DE  30 DE JUNHO DE 1999.</strong><br />ANEXO II</p>
      <p align="center">Prefeitura Municipal de <?php echo $row_regiao['regiao']; ?></p>
      <input type="hidden" name="prefeitura" value="Prefeitura Municipal de <?php echo $row_regiao['regiao']; ?>"/>
      </td>
  </tr>
  <tr class="secao">
    <td colspan="6">Extrato de Relat&oacute;rio de Execu&ccedil;&atilde;o F&iacute;sica e Financeira de Termo de Parceria
    				
    </td>
  </tr>
  <tr>
    <td colspan="6">&nbsp;</td>
  </tr>
  <tr>
    <td class="secao">Custo do projeto:</td>
    <td colspan="5">R$ <?php echo number_format($total_verba,2,',','.').' ('.htmlentities(valorPorExtenso($total_verba),ENT_COMPAT,'UTF-8'),')'; ?>
    <input type="hidden" name="custo_projeto" value="<?php echo $total_verba; ?>"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Local de realiza&ccedil;&atilde;o do projeto:</td>
    <td colspan="5">Munic&iacute;pio de <?php echo $row_regiao['regiao']; ?>
    				   <input type="hidden" name="local_projeto" value="Munic&iacute;pio de <?php echo $row_regiao['regiao']; ?>"/>
    </td>
  </tr>
  <tr>
    <td class="secao" width="25%">Data de assinatura do TP:</td>
    <td width="15%"><?php echo implode('/', array_reverse(explode('-', $row_projeto['inicio']))); ?>
    				<input type="hidden" name="data_assinatura" value="<?php echo  $row_projeto['inicio']; ?>"/>
    </td>
    <td class="secao" width="15%">In&iacute;cio do projeto:</td>
    <td width="15%"><?php echo implode('/', array_reverse(explode('-', $row_projeto['inicio']))); ?>
    				<input type="hidden" name="data_inicio" value="<?php echo  $row_projeto['inicio']; ?>"/>
    </td>
    <td class="secao" width="15%">T&eacute;rmino:</td>
    <td width="15%">
 		  <input name="data_termino" id="termino" value="<?php echo implode('/', array_reverse(explode('-', $row_projeto['termino'])))?>"  style=" border:0;background-color:transparent;"/>
          	
	</td>
  </tr>
  <tr>
    <td class="secao">Objetivos do projeto:</td>
    <td colspan="5"><?php echo $row_projeto['descricao']; ?>
    				<input type="hidden" name="obj_projeto" value="<?php echo  	 $row_projeto['descricao']; ?>"/>
    </td>
  </tr>
  <tr>
    <td class="secao">Resultados alcan&ccedil;ados:</td>
    <td colspan="5">
    Considerando o TERMO DE PARCERIA celebrado entre a  OSCIP e a Prefeitura Municipal de <?php echo $row_regiao['regiao']; ?> conclu&iacute;mos que o <?php echo $row_projeto['nome']; ?> teve um desempenho satisfat&oacute;rio, contribuindo efetivamente para o &ecirc;xito desta parceria. Obedecendo aos crit&eacute;rios estipulados no programa de Trabalho cumprindo os requisitos necess&aacute;rios.
    
    <textarea style="display:none;" name="resultados">   
    Considerando o TERMO DE PARCERIA celebrado entre a  OSCIP e a Prefeitura Municipal de <?php echo $row_regiao['regiao']; ?> conclu&iacute;mos que o <?php echo $row_projeto['nome']; ?> teve um desempenho satisfat&oacute;rio, contribuindo efetivamente para o &ecirc;xito desta parceria. Obedecendo aos crit&eacute;rios estipulados no programa de Trabalho cumprindo os requisitos necess&aacute;rios.
</textarea>
    
    </td>
  </tr>
  <tr>
    <td colspan="6" align="center"><p>&nbsp;</p>
      <style type="text/css">
	  table#financeiro td {
		  background-color:#fff;
	  }
	  </style>
      <table style="background-color:#000; width:75%;" cellpadding="4" cellspacing="1" id="financeiro">
        <tr>
          <td colspan="4" align="center"><strong>Custos de Implementa&ccedil;&atilde;o do Projeto</strong></td>
        </tr>
        <tr>
          <td align="right"><strong>Categorias de despesas</strong></td>
          <td>Previsto</td>
          <td>Realizado</td>
          <td>Diferen&ccedil;a</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td  align="left">R$ <?php echo formato_real($total_verba); ?>
          					<input type="hidden" name="cat_desp_previsto" value="<?php echo  $total_verba; ?>"/>
          </td>
          <td  align="left">
               
               
               <?php
			   $qr_notas = mysql_query("SELECT * FROM notas WHERE id_projeto = '$projeto' AND ((YEAR(data_emissao) = '$ano') OR (YEAR(data_emissao) = '".($ano-1)."' AND MONTH(data_emissao) = '12')) AND status = '1' ORDER BY data_emissao ASC");
					   while($row_nota = mysql_fetch_assoc($qr_notas)) :
					   
					   
					  $qr_total = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
										(notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
										INNER JOIN entrada 
										ON notas_assoc.id_entrada = entrada.id_entrada
										WHERE notas.id_notas = '$row_nota[id_notas]' AND entrada.status = 2 AND YEAR(entrada.data_vencimento) = '$ano';
										") or die(mysql_error());
									$total_entrada = (float) @mysql_result($qr_total,0); 
									$totalizador_repasse += $total_entrada;
						  
					   endwhile;	
					   ?>
               
               
            R$ <input type="text" id="total_repasse"  name="cat_desp_realizado" value="<?php echo formato_real($totalizador_repasse); ?>" size="10"  >                
               
           <input type="hidden" class="previsto" value="<?php echo $total_verba;?>">
           
                      </td>
                      <td align="left"> 
                       <div class="diferenca">
                          <?php 
                          $diferenca = $totalizador_repasse - $total_verba  ;
                          echo 'R$ '.formato_real($diferenca);
                          
                          ?>
                         
                        </div>
                           <input type="hidden" name="cat_desp_diferenca" value="<?php echo formato_real($diferenca);?>" class="input_diferenca" />
                        
                      </td>
                    </tr>
                    <tr>
                      <td align="right"><strong>TOTAIS:</strong></td>
                      <td>R$ <?php echo formato_real($total_verba); ?>
                        <input type="hidden" name="total_previsto" value="<?php echo formato_real($total_verba);?>"  />
                      </td>
                      
                      <td align="left">
                          <div class="realizado">
                          
                          R$ <?php echo formato_real($totalizador_repasse); ?>
                          
                          </div>
                             <input type="hidden" name="total_realizado" value="<?php echo formato_real($totalizador_repasse);?>" class="input_realizado" />
                      
                      </td>
                      
                      <td align="left">
                               <div class="diferenca">
                              <?php 
                              $diferenca = $totalizador_repasse - $total_verba;
                              	
								echo 'R$ '.formato_real($diferenca);
							                               
                              ?>
                             </div>
                              <input type="hidden" name="total_diferenca" value="<?php echo formato_real($diferenca);?>" class="input_diferenca" />
                      </td>
                    </tr>
                  </table>
                  <p>&nbsp;</p></td>
              </tr>
              <tr>
                <td class="secao">Nome da OSCIP:</td>
                <td colspan="5"><?php echo $row_master['razao']; ?>
                				
                </td>
              </tr>
              <tr>
                <td class="secao">Endere&ccedil;o:</td>
                <td colspan="5"><?php echo $row_master['endereco']; ?>
                
                </td>
              </tr>
              <tr>
                <td class="secao">Cidade:</td>
                <td><?php echo $row_master['municipio'] ?></td>
                <td class="secao">UF:</td>
                <td><?php echo $row_master['uf']; ?></td>
                <td class="secao">CEP:</td>
                <td><?php echo $row_master['cep']; ?></td>
              </tr>
              <tr>
                <td class="secao">Tel:</td>
                <td><?php echo $row_master['telefone']; ?></td>
                <td class="secao">Fax:</td>
                <td><?php echo $row_master['fax']; ?></td>
                <td class="secao">E-mail:</td>
                <td><?php echo $row_master['email']; ?></td>
              </tr>
              <tr>
                <td class="secao">Nome do respons&aacute;vel pelo projeto:</td>
                <td colspan="5"><?php echo $row_master['responsavel']; ?></td>
              </tr>
              <tr>
                <td class="secao">Cargo / Fun&ccedil;&atilde;o:</td>
                <td colspan="5">Presidente</td>
              </tr>
           
             
				<tr>
                	<td colspan="6" align="center">
           
           
           <input type="hidden" name="nome_oscip" 		value="<?php echo $row_master['razao'];?>" />
           <input type="hidden" name="endereco_oscip" value="<?php  echo $row_master['logradouro']; ?>" />
           <input type="hidden" name="cidade_oscip" value="<?php   echo  $row_master['municipio']; ?>"/>
           <input type="hidden" name="uf_oscip" value="<?php   echo  $row_master['uf'];?>"/>
           <input type="hidden" name="cep_oscip" value="<?php   echo $cep; ?>"/>
           <input type="hidden" name="tel_oscip" value="<?php   echo $row_master['telefone']; ?>"/>
           <input type="hidden" name="fax_oscip" value="<?php    echo $row_master['fax']; ?>"/>
           <input type="hidden" name="email_oscip" value="<?php    echo $row_master['email']; ?>"/>
           <input type="hidden" name="nome_responsavel" value="<?php    echo $row_master['responsavel']; ?>"/>
           <input type="hidden" name="cargo_responsavel" value="Presidente"/>
		
		
		<input type="hidden" name="entregue_obrigacao" 		 value="<?php echo $entregue_obrigacao?>" />
		<input type="hidden" name="entregue_dataproc" 	 	 value="<?php echo implode('-',array_reverse(explode('/',$obrigacao_data)));?>" />
		<input type="hidden" name="entregue_datareferencia"  value="<?php echo $data_referencia?>" />
		<input type="hidden" name="entregue_ano_competencia" value="<?php echo $ano_competencia; ?>" />
		           
           
           
           
                      <input name="projeto_id" type="hidden" value="<?php echo $projeto; ?>"/>
                      <input name="regiao_id" type="hidden" value="<?php echo $row_projeto['id_regiao']; ?>"/>
                      
                      <input name="master" type="hidden" value="<?php echo $master; ?>"/>
                      <input name="ano" type="hidden" value="<?php echo $ano; ?>"/>
                      <input name="obrigacao_id" type="hidden" value="<?php echo $obrigacao_id; ?>"/>                      
                      <input name="ano_base" type="hidden" value="<?php echo $ano_base; ?>"/>
                      
                      <input name="tipo" type="hidden" value="anexo_2" />     
                           
                      <?php if($ACOES->verifica_permissoes(70)) {?>	            
                 	<input type="submit" name="finalizar" value="FINALIZAR"  class="finalizar"/>
                      <?php }?>
              		</td>
              </tr>
            </table>
            </form>
  















</body>
</html>