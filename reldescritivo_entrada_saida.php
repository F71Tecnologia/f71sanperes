<?php
include "conn.php";
include "adm/include/restricoes.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Relatorio</title>
<script type="text/javascript" src="jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="jquery/tablesorte/jquery.tablesorter.min.js"></script> 
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
/*
function arredonda(num){
	return Math.round(num*10)/10;
}
*/
$(function(){
	$('.valor').each(function(){
		var valor = parseFloat($(this).val());
		var totalizador = parseFloat($('#totalizador').val());
		$(this).parent().next().text(float2moeda(valor*100/totalizador)+' %');
	});
	$('.valor2').each(function(){
		var valor = parseFloat($(this).val());
		var totalizador = parseFloat($('#totalizador2').val());
		var valor_moeda = float2moeda(valor*100/totalizador);
		valor_moeda = valor_moeda.replace(',','.');
		
		$(this).parent().next().text(String(valor_moeda)+' %');
	});
	$("#tableOrden").tablesorter({2: {sorter:"moeda"}, 3: {sorter: "percent"}, widgets: ['zebra']});
	
	
	
});
</script>
<style type="text/css">
body {
	margin: 0px;
	padding: 0px;
	background-color: #F0F0F0;
	font-family:Tahoma, Geneva, sans-serif;
	font-size:12px;
	text-transform:uppercase;
}
#base_pesquiza {
	width: 998px;
	margin-top: 10px;
	margin-right: auto;
	margin-bottom: 0px;
	margin-left: auto;
	overflow: hidden;
	background-color: #FFF;
}
.bloco_pesquiza {
	float: left;
	width: 48%;
	background-color: #FFF;
	padding: 1%;
}
.bloco_pesquiza2 {
	background-color: #FFF;
	padding: 1%;
}
.bloco_pesquiza h1{
	margin:0;
	padding:0;
	font-size:14px;
	text-align:center;
}
.bloco_pesquiza h2{
	margin:0;
	padding:0;
	font-size:14px;
	text-align:center;
	font-weight:bold;
}
#formulario {
	width: 500px;
	margin-top: 10px;
	margin-right: auto;
	margin-bottom: 0px;
	margin-left: auto;
	padding: 10px;
	background-color: #FFF;
}
.linha_um {
 background-color:#f5f5f5;
}
.linha_dois {
 background-color:#ebebeb;
}

.linha_selectd {
 background-color:#FFE7CE;
}
.linha_um td, .linha_dois td {
 border-bottom:1px solid #ccc;
}
.linha_um:hover , .linha_dois:hover{
	background-color:#575757;
	color:#FFF;
}

</style>
<link rel="stylesheet" type="text/css" href="novoFinanceiro/style/form.css"/>
<link rel="stylesheet" type="text/css" href="jquery/tablesorte/blue/style.css"/>

</head>
<body>
<?php if(!isset($_GET['mes'])):?>
    <form id="form1" name="form1" method="GET" action="">
    <div id="formulario">
      <fieldset>
        	<legend>Relatorio indicativo</legend>
       	<table width="100%" border="0">
           	  <tr>
           	    <td colspan="3">&nbsp;</td>
   	      </tr>
           	  <tr>
           	    <td colspan="2" align="right">CONTA:
                        
                  <select name="conta" id="conta">
                      <option value="">Selecione</option>
                    <?php 
            		$qr_contas = mysql_query("SELECT id_banco, nome FROM bancos WHERE id_regiao = '$_GET[regiao]' AND status_reg = '1'");
				
			
				while($row_contas = mysql_fetch_array($qr_contas)){
					$selected = ($row_contas[0]) ? 'selected="selected"' : '';
					echo "<option value=\"$row_contas[0]\" $selected>$row_contas[0] - $row_contas[1]</option>";
				}
            ?>
                </select></td>
           	    <td>&nbsp;</td>
   	      </tr>
           	  <tr>
           	    <td width="53%" align="right">Mes: 
           	      <select name="mes" id="meses3">
 <?php 
            	$qr_meses = mysql_query("SELECT * FROM ano_meses");
				while($row_meses = mysql_fetch_array($qr_meses)){
					$selected = ($row_meses[0] == date('m')) ? 'selected="selected"' : '';
					echo "<option value=\"$row_meses[0]\" $selected>$row_meses[1]</option>";
				}
            ?> 
</select>
                </td>
           	    <td width="41%">ano:
           	      <select name="ano" id="mes">
           	        <?php
				for($i = 2005; $i <= (date('Y') + 3); $i ++){
					$selected = ($i == date('Y')) ? 'selected="selected"' : '';
					echo '<option '.$selected.' value="'.$i.'" >'.$i.'</option>';
				}
				?>
       	          </select>
                <input type="hidden" name="master" value="<?= $_GET['master'];?>" /></td>
           	    <td width="6%">&nbsp;</td>
   	      </tr>
           	  <tr>
           	    <td colspan="3" align="center"><input type="submit" name="button" id="button" value="Gerar Relatorio" /></td>
   	      </tr>
   	    </table>
      </fieldset>
    </div>
	</form>
<?php else:?>
<?php 
$mes = $_GET['mes'];
$ano = $_GET['ano'];
$master = $_GET['master'];
$banco = $_GET['conta'];
?>
<div id="base_pesquiza">
  <div>
  	<?php
	$qr_mes = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$mes'");
	$nome_mes = @mysql_result($qr_mes,0);
	echo $nome_mes .'/'.$ano;
	?>
  </div>
  <div class="bloco_pesquiza">
   	  <fieldset>
       	<legend>Entradas</legend>
            <h1>Taxa administrativa (65)</h1>
            <?php
               	$qr_master = mysql_query("SELECT id_master, nome FROM master WHERE status = '1' AND id_master = '$master'");
				while($row_master = mysql_fetch_array($qr_master)):			
			?>
            
            <fieldset>
            	<legend><h2><img src="imagens/logomaster<?=$row_master[0]?>.gif" width="57" height="39" /> <?=$row_master[1]?></h2></legend>
           
          	<table width="100%">
              <?php
			  	$qr_regioes = mysql_query("SELECT id_regiao, regiao FROM regioes WHERE status = '1' AND	status_reg = '1' AND id_master  = '$row_master[0]'");
				while($row_regioes = mysql_fetch_array($qr_regioes)):	
				
				$qr_controle = mysql_query("SELECT * FROM saida WHERE 
				tipo = '65' AND status = '2'
				AND id_regiao = '$row_regioes[0]'
				AND MONTH(data_vencimento) = '$mes'
				AND YEAR(data_vencimento) = '$ano'
                                "
                                );
				$num_controle = @mysql_num_rows($qr_controle);                                
				//if(empty($num_controle)) continue;							
			  ?> 
              <tr class="<?php if($alterColor++%2){ echo "linha_dois";}else{echo "linha_um";}?>">
            	  <td width="3%"><span style="color:#CA6500; font-weight:bold;">></span></td>
            	  <td width="49%"><?=$row_regioes['regiao']?></td>
            	  <td width="32%">&nbsp;</td>
            	  <td width="15%">&nbsp;</td>
          	  </tr>
              <?php
              if(!empty($banco)) { $banco_sql = "AND id_banco = '$banco'"; }else{ $banco_sql = ''; }
			  	
			  	$qr_projeto = mysql_query("SELECT id_banco, nome FROM bancos WHERE id_regiao = '$row_regioes[0]' AND status_reg = '1' $banco_sql ");
				while($row_projeto = mysql_fetch_array($qr_projeto)):
			 ?>
             <?php 
			 	$qr_taxa_adm = mysql_query("SELECT * FROM entrada WHERE
				tipo = '131' AND status = '2' 
				AND id_regiao = '$row_regioes[0]'
				AND id_banco = '$row_projeto[0]'
				AND MONTH(data_vencimento) = '$mes'
				AND YEAR(data_vencimento) = '$ano'
                                
				");
				
				while ($row_taxa_adm = mysql_fetch_assoc($qr_taxa_adm)){
					
					$v = (float) str_replace(',','.',$row_taxa_adm['valor']);
					$v = (float) $v + str_replace(',','.',$row_taxa_adm['adicional']);
					?>
                    <tr class="<?php if($alterColor++%2){ echo "linha_dois";}else{echo "linha_um";}?>">
                        <td></td>
                        <td ><?=$row_taxa_adm['nome']?></td>
                        <td >R$ <?=number_format($v,2,',','.')?><input type="hidden" name="valor" class="valor" value="<?=$v?>" /></td>
                        <td class="indicador"></td>
                     </tr>
                    <?php
					$totalizador += $v;
				}
				//$valor = mysql_result($qr_taxa_adm,0);
				
				
				if(empty($v)) continue;
			 ?>
             
             
			 <?php endwhile; // QUERY projeto ?>
             <?php endwhile; // QUERY REGIOES?>
             </table>
        </fieldset>
  			 <?php endwhile; // QUERY MASTER?>
     		 <table width="100%">
             	<tr>
                	<td width="3%">&nbsp;</td>
                    <td width="49%" align="right">Total:</td>
               		<td width="32%"><b>R$ <?=number_format($totalizador,2,',','.');?></b><input type="hidden" name="totalzador" id="totalizador" value="<?=$totalizador?>" /></td>
                    <td width="15%"></td>
               </tr>
             </table>
             <?php 
			 $total_entrada = $totalizador;
			 ?>
             <?php 
			 unset($totalizador);
			 ?>
    </fieldset>
  </div>
  <div style="float:right;width:49%"> 
  <div class="bloco_pesquiza2">
   	<fieldset>
        	<legend>FOLHA
   	  </legend><table width="100%" border="0" id="tableOrden" class="tablesorter">
            <thead>
              <tr>
                <th width="45%">TIPO</th>
                <th width="12%">QTD</th>
                <th width="25%">VALOR</th>
                <th width="18%">INDICADOR</th>
              </tr>
        </thead>
             <tbody>
              <?php
              if(!empty($banco)){ $banco_sql = "AND S.id_banco = '$banco'"; } else { $banco_sql = ''; }
			  $tipos_saidas = array(29,62,50,51,76,56);
			  $sql_tipos = array();
			  foreach($tipos_saidas as $tip){
				  $sql_tipos[]= "S.tipo = '$tip'";
			  }
			  $sql_tipos = implode(' OR ', $sql_tipos);
			  	$qr_tipo = mysql_query("		
				SELECT T.id_entradasaida, T.nome, COUNT(S.id_saida) AS quantidade, 
						  SUM(REPLACE(S.valor,',','.') + REPLACE(S.adicional,',','.')) AS valor_total						  
						  FROM entradaesaida AS T LEFT JOIN saida AS S ON T.id_entradasaida = S.tipo
				WHERE 
				MONTH(S.data_vencimento) = '$mes'
				AND YEAR(S.data_vencimento) = '$ano'
				AND S.status = '2'
				AND ($sql_tipos) 
                                $banco_sql
				GROUP BY (S.tipo)
				ORDER BY S.valor
				");
				
				
				while($row_tipo = mysql_fetch_assoc($qr_tipo)):
			  ?>
              <tr class="<?php if($alterColor++%2){ echo "linha_dois";}else{echo "linha_um";}?>">
              <?php $totalizador += $row_tipo['valor_total'];?>
                <td><?=$row_tipo['id_entradasaida'].' - '.$row_tipo['nome'];?></td>
                <td><?=$row_tipo['quantidade']?></td>
                <td><?='R$ '.number_format($row_tipo['valor_total'],2,',','.')?><input type="hidden" name="valor2" class="valor2" value="<?=$row_tipo['valor_total']?>" /></td>
                <td class="indicador2">&nbsp;</td>
              </tr>
              <?php endwhile; ?>
             </tbody>
             <tr>
             	<td></td>
                <td align="right">Total:</td>
                <td><b>R$ <?=number_format($totalizador,2,',','.');?></b><input type="hidden" name="totalizador8" id="totalizador8" value="<?=$totalizador?>" /></td>
                <td></td>
             </tr>
             <?php
			 	$total_saida = $totalizador;
			 ?>
      </table>
   	</fieldset>
  </div>
  <div class="bloco_pesquiza2">
    <fieldset>
      <legend>CUSTOS FIXOS</legend>
      <table width="100%" border="0" id="tableOrden2" class="tablesorter">
        <thead>
          <tr>
            <th width="45%">TIPO</th>
            <th width="12%">QTD</th>
            <th width="25%">VALOR</th>
            <th width="18%">INDICADOR</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if(!empty($banco)){ $banco_sql = "AND S.id_banco = '$banco'"; } else { $banco_sql = ''; }
			  $tipos_saidas = array(134,44,100,122,105,108,101,121,19);
			  $sql_tipos = array();
			  foreach($tipos_saidas as $tip){
				  $sql_tipos[]= "S.tipo = '$tip'";
			  }
			  $sql_tipos = implode(' OR ', $sql_tipos);
			  	$qr_tipo = mysql_query("		
				SELECT T.id_entradasaida, T.nome, COUNT(S.id_saida) AS quantidade, 
						  SUM(REPLACE(S.valor,',','.') + REPLACE(S.adicional,',','.')) AS valor_total						  
						  FROM entradaesaida AS T LEFT JOIN saida AS S ON T.id_entradasaida = S.tipo
				WHERE 
				MONTH(S.data_vencimento) = '$mes'
				AND YEAR(S.data_vencimento) = '$ano'
				AND S.status = '2'
				AND ($sql_tipos)
                                $banco_sql 
				GROUP BY (S.tipo)
				ORDER BY S.valor
				");
				
				
				while($row_tipo = mysql_fetch_assoc($qr_tipo)):
			  ?>
          <tr class="<?php if($alterColor++%2){ echo "linha_dois";}else{echo "linha_um";}?>">
            <?php $totalizador2 += $row_tipo['valor_total'];?>
            <td><?=$row_tipo['id_entradasaida'].' - '.$row_tipo['nome'];?></td>
            <td><?=$row_tipo['quantidade']?></td>
            <td><?='R$ '.number_format($row_tipo['valor_total'],2,',','.')?>
              <input type="hidden" name="valor3" class="valor2" value="<?=$row_tipo['valor_total']?>" /></td>
            <td class="indicador2">&nbsp;</td>
          </tr>
          <?php endwhile; ?>
        </tbody>
        <tr>
          <td></td>
          <td align="right">Total:</td>
          <td><b>R$
            <?=number_format($totalizador2,2,',','.');?>
            </b>
            <input type="hidden" name="totalizador8" id="totalizador8" value="<?=$totalizador2?>" /></td>
          <td></td>
        </tr>
        <?php
			 	$total_saida1 = $totalizador2;
			 ?>
      </table>
    </fieldset>
  </div>
  <div class="bloco_pesquiza2">
    <fieldset>
      <legend>CUSTOS VARIAVEIS</legend>
      <table width="100%" border="0" id="tableOrden3" class="tablesorter">
        <thead>
          <tr>
            <th width="45%">TIPO</th>
            <th width="12%">QTD</th>
            <th width="25%">VALOR</th>
            <th width="18%">INDICADOR</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          if(!empty($banco)){ $banco_sql = "AND S.id_banco = '$banco'"; } else { $banco_sql = ''; }

			  $tipos_saidas = array(81,87,104,117,70,103,123,140,69,124,133,126,127,73,139,42,115,120,54,125,141,98,109);
			  $sql_tipos = array();
			  foreach($tipos_saidas as $tip){
				  $sql_tipos[]= "S.tipo = '$tip'";
			  }
			  $sql_tipos = implode(' OR ', $sql_tipos);
			  	$qr_tipo = mysql_query("		
				SELECT T.id_entradasaida, T.nome, COUNT(S.id_saida) AS quantidade, 
						  SUM(REPLACE(S.valor,',','.') + REPLACE(S.adicional,',','.')) AS valor_total						  
						  FROM entradaesaida AS T LEFT JOIN saida AS S ON T.id_entradasaida = S.tipo
				WHERE 
				MONTH(S.data_vencimento) = '$mes'
				AND YEAR(S.data_vencimento) = '$ano'
				AND S.status = '2'
				AND ($sql_tipos)
                                $banco_sql 
				GROUP BY (S.tipo)
				ORDER BY S.valor
				");
				
				
				while($row_tipo = mysql_fetch_assoc($qr_tipo)):
			  ?>
          <tr class="<?php if($alterColor++%2){ echo "linha_dois";}else{echo "linha_um";}?>">
            <?php $totalizador3 += $row_tipo['valor_total'];?>
            <td><?=$row_tipo['id_entradasaida'].' - '.$row_tipo['nome'];?></td>
            <td><?=$row_tipo['quantidade']?></td>
            <td><?='R$ '.number_format($row_tipo['valor_total'],2,',','.')?>
              <input type="hidden" name="valor4" class="valor2" value="<?=$row_tipo['valor_total']?>" /></td>
            <td class="indicador2">&nbsp;</td>
          </tr>
          <?php endwhile; ?>
        </tbody>
        <tr>
          <td></td>
          <td align="right">Total:</td>
          <td><b>R$
            <?=number_format($totalizador3,2,',','.');?>
            </b>
            <input type="hidden" name="totalizador8" id="totalizador8" value="<?=$totalizador3?>" /></td>
          <td></td>
        </tr>
        <?php
			 	$total_saida2 = $totalizador3;
			 ?>
      </table>
    </fieldset>
  </div></div>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
<div style="clear:both;"></div>
  	<fieldset >
            <legend>Saldo para o m&ecirc;s de <?=$nome_mes?></legend>
      <br />
      <table width="100%" border="0">
      <?php 
	  $total_saida = $total_saida + $total_saida1 + $total_saida2;
	  
	  $total_final = $total_entrada - $total_saida;
	  ?>
        <tr>
          <td align="right"><b>Total entradas:</b></td>
          <td><b><?='R$ '.number_format($total_entrada,2,',','.')?></b></td>
          <td align="right"><b>Total saidas:</b></td>
          <td><b><?='R$ '.number_format($total_saida,2,',','.')?><input type="hidden" name="totalizador2" id="totalizador2" value="<?=$total_saida?>" /></b></td>
          <td align="right"><b>Total:</b></td>
          <td><b><?='R$ '.number_format($total_final,2,',','.')?></b></td>
        </tr>
      </table>
    </fieldset>
    <div style="text-align:center; font-size:14px; padding:5px;">
        <a href="<?=$_SERVER['PHP_SELF']."?master=$_GET[master]&regiao=$_GET[regiao]"?>">Voltar</a>
    </div>
</div>
<?php endif;?>
</body>
</html>