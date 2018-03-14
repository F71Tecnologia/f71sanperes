<?php 
include "../../conn.php";

$mes = $_GET['mes'];
$ano = $_GET['ano'];
$clt = $_GET['id_clt'];
$tipo = $_GET['tipo']; // 1 - FÉRIAS, 2 - RECISÂO

$projeto = $_GET['projeto'];
$regiao = $_GET['regiao'];
$ferias = $_GET['ferias'];


$query_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_assoc($query_projeto);
$query_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$projeto'");
$row_regiao = mysql_fetch_assoc($query_regiao);
$query_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$clt'");
$row_clt = mysql_fetch_assoc($query_clt);
$query_mes = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$mes'");
$mes_nome = @mysql_result($query_mes,0);


// Montando o nome da saida
if($tipo == 1){
//	$tipo_id = "156";
	$tipo_id = "8";
	$tipo_nome = "FÉRIAS";
        $subgrupo = 1;
	$qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE MONTH(data_ini) = '$mes' AND YEAR(data_ini) = '$ano' AND regiao = '$regiao' AND projeto = '$projeto' AND id_clt = '$clt' AND status = '1'");
	$query = $qr_ferias;
	$sql = "SELECT * FROM rh_ferias WHERE MONTH(data_ini) = '$mes' AND YEAR(data_ini) = '$ano' AND regiao = '$regiao' AND projeto = '$projeto' AND id_clt = '$clt' AND status = '1'";
	
}else{
//	$tipo_id = "170";
	$tipo_id = "31";
	$tipo_nome = "RESCISÃO";
        $subgrupo = 4;
        $qr_recisao = mysql_query("SELECT * FROM rh_recisao WHERE MONTH(data_demi) = '$mes' AND YEAR(data_demi) = '$ano' AND id_regiao = '$regiao' AND id_projeto = '$projeto' AND id_clt = '$clt' AND status = '1'");

        $query = $qr_recisao;
	$sql = "SELECT * FROM rh_recisao WHERE MONTH(data_demi) = '$mes' AND YEAR(data_demi) = '$ano' AND id_regiao = '$regiao' AND id_projeto = '$projeto' AND id_clt = '$clt' AND status = '1'";
}

$row = mysql_fetch_assoc($query);
$nome_saida = "$row_clt[id_clt] - $row_clt[nome], $tipo_nome $mes_nome/$ano $row_projeto[id_projeto] -  PROJETO: $row_projeto[nome]";



foreach($_GET as $chave => $valor){
	$string[] = $chave.'='.$valor;
}
$link = implode("&",$string);


//LISTANDO SAÍDAS 

$query_saida = mysql_query("SELECT PG.id_saida,B.nome, DATE_FORMAT(B.data_vencimento, '%d/%m/%Y')  as data_vencimento, IF(B.data_pg = NULL,'',DATE_FORMAT(B.data_pg, '%d/%m/%Y' )) as data_pg,
C.nome as nome_banco, C.conta, C.agencia,B.status
FROM pagamentos_especifico AS PG
INNER JOIN saida as B 
 ON PG.id_saida = B.id_saida
 INNER JOIN bancos as C
 ON C.id_banco = B.id_banco
WHERE B.status != '0' AND PG.mes = '$mes' AND PG.ano = '$ano' AND PG.id_clt = '$clt' AND (B.tipo = '51' OR B.tipo = '170')") or die(mysql_error());
$num_saida = mysql_num_rows($query_saida);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Detalhes da Rescis&atilde;o</title>

<link rel="stylesheet" type="text/css" href="../../novoFinanceiro/style/form.css"/>
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../jquery/priceFormat.js"></script>

<!-- datepiker -->
<script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css"/>
<!-- datepiker -->

<script type="text/javascript">
$(function(){
        $('.multa_valor').priceFormat({
            prefix: '',
            centsSeparator: ',',
            thousandsSeparator: '.'
        });
    
    
	$('.date').keyup(function(){
		var valor = $(this).val();
		if(valor.length == 2 || valor.length == 5 ){
			$('.date').val(valor+"/");
		}
		
		var matriz = valor.split('/');
		if(matriz[0] > 31){
			alert("Digite um dia válido!");
			$(this).val('');
			return false;
		}
		if(matriz[1] > 12){
			alert("Digite um mes válido!");
			$(this).val(matriz[0]+'/');
			return false;
		}
		if(matriz[2] > 2050){
			alert("Digite um ano válido!");
			$(this).val(matriz[0]+'/'+matriz[1]+'/');
			return false;
		}
		
	});
	// Datepicker
	$('.date, .multa_dt').datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		changeYear: true
	});
	
	$('form[name*=form]').submit(function(){
		var index = 0;
		$(this).find('input[type*=text],select').each(function(){
                    
                    console.log($(this).attr('name'));
			if($(this).val() == '') index ++;
		});
		if(index == 0) { 
			$(this).submit(); 
		}else{
			//alert('Preencha todos os campo!');
			//return false;
		}
		 
	});
        
        
 $('input[name=multa]').change(function(){
     
     var valor = $(this).val();
     
     if(valor == 1){
         
         $('.multa').show();
         
     } else {
         $('.multa').hide();
         
     }
     
 })      
        
	
});
</script>
<style type="text/css">
body{
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}
.linha_um {
 background-color:#f5f5f5;
}
.linha_dois {
 background-color:#ebebeb;
}
.linha_um td, .linha_dois td {
 border-bottom:1px solid #ccc;
}

.tabela{
    width: 800px;
    margin: 10px;
    
}

.campos{
    background-color:    #c8c8c8;
    text-align: center;
}

.titulo{
    background-color:  #c9c9c9;
    width:100px;
    padding:3px;
    text-align: center;
    margin:10px;
}

</style>
</head>
<body>
<form action="actions/cadastra2.php" name="form" method="post" enctype="multipart/form-data" >
<input type="hidden" name="link" value="<?=$link?>" />


<table width="100%">
	<tr class="linha_um">
    	<td width="20%">Nome da sa&iacute;da:</td>
        <td width="80%"><?=$nome_saida ?><input type="hidden" value="<?=$nome_saida?>" name="nome" /></td>
    </tr>
    
    
    
    <tr class="linha_um">
    	<td>CLT</td>
        <td><?= $row_clt['id_clt'] .' - '. $row_clt['nome'];?>
        <input type="hidden" value="<?= $row_clt['id_clt']?>" name="id_clt" />
        </td>
    </tr >
    <tr class="linha_um">
    	<td>GRUPO</td>
        <td>10 - PESSOAL</td>
    </tr>
    <tr class="linha_um">
    	<td>SUBGRUPO</td>
        <td>
            <?php
            $qr_subgrupo = mysql_query("SELECT * FROM entradaesaida_subgrupo  WHERE id = '$subgrupo'");
            $row_subgrupo = mysqL_fetch_assoc($qr_subgrupo);            
            echo $row_subgrupo['id_subgrupo'].' - '.$row_subgrupo['nome'];            
            ?>
        </td>
    </tr>
    <tr class="linha_um">
    	<td>TIPO</td>
        <td><?=$tipo_id.' - '.$tipo_nome?>
        <input type="hidden" value="<?=$tipo_id?>" name="tipo" />
        </td>
    </tr>
 
    
    <tr class="linha_um">
    	<td>Valor</td>
        <td>R$ <?=number_format($row['total_liquido'],2,',','.');?>
         <input type="hidden" value="<?=$row['total_liquido']?>" name="valor" />
        </td>
    </tr>
    <tr class="linha_um">
      <td>Banco</td>
      <td><label for="select"></label>
       <select name="bancos" id="bancos">
            <?php 
			$qr_bancos = mysql_query("SELECT * FROM bancos WHERE status_reg = '1'");
			while($row_bancos = mysql_fetch_assoc($qr_bancos)):
			if($grupo != $row_bancos['id_regiao']){
				$qr_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_bancos[id_regiao]'");
				echo '<optgroup label="'.$row_bancos['id_regiao']. ' - ' .@mysql_result($qr_regiao,0).'">';
			}
			$grupo = $row_bancos['id_regiao'];
			?>
			<option value="<?=$row_bancos['id_banco']?>"><?=$row_bancos['id_banco'] . ' - ' . $row_bancos['nome']?></option>
			<?php
			if($grupo != $row_bancos['id_regiao'] && !empty($grupo)) {echo '</optgroup>';}
			$grupo = $row_bancos['id_regiao'];                        
			endwhile;
			?>
          </select></td>
    </tr>
    <tr class="linha_um">
    	<td>Vencimento</td>
        <td><input type="text" name="data" class="date" /></td>
    </tr>
    
     
    
     <tr class="linha_um">
        <td colspan="2" align="center">
        	
        	<input type="hidden" name="id_folha" value="<?=$folha?>" />
            <input type="hidden" name="mes" value="<?=$mes?>" />
            <input type="hidden" name="ano" value="<?=$ano?>" />
            <input type="hidden" name="regiao" value="<?=$regiao?>" />
            <input type="hidden" name="projeto" value="<?=$projeto?>" />
            <input type="hidden" name="ferias" value="<?=$ferias?>" />
            <input type="hidden" name="id_clt" value="<?php echo $clt;?>"/>
            <input type="hidden" name="subgrupo" value="<?php echo $subgrupo;?>"/>
            
        	<input type="submit" value="  Gerar  " class="submit-go" />
        </td>
    </tr>
</table>

<?php
/*
    
    if($num_saida !=0){
        ?>

   <div class="titulo"> SAÍDAS</div>
        
     <table class="tabela">           
         <tr class="campos">
             <td width="40">COD.</td>          
             <td  width="130" >BANCO</td>
             <td width="50">AGÊNCIA</td>
             <td width="50">CONTA</td>
             <td width="70">DATA DE VENC.</td>
             <td  width="70">DATA DE PG</td>
             <td width="50">STATUS</td>
         </tr>
        
        <?php
        while($row_saida = mysql_fetch_assoc($query_saida)){
            $cor = ($i++ %2 == 0)?'linha_um': 'linha_dois';
        ?>    
         <tr style="text-align: center;" class="<?php echo $cor; ?>">
             <td><?php echo $row_saida['id_saida'];?></td>
           
             <td><?php echo $row_saida['nome_banco'];?></td>             
             <td><?php echo $row_saida['agencia'];?></td>
             <td><?php echo $row_saida['conta'];?></td>
             <td><?php echo $row_saida['data_vencimento'];?></td>
             <td><?php echo $row_saida['data_pg'];?></td>
             <td><img src="../../imagens/bolha<?php echo $row_saida['status'];?>.png" width="15" height="15"/></td> 
         </tr>
       
      <?php 
        }
        
        echo '</table>';
    } else {
        
       echo '<div>Nenhuma saída cadastrada.</div>';
    }
*/

?>


</form>
</body>
</html>