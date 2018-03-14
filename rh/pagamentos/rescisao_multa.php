<?php 
include "../../conn.php";
include("../../classes/uploadfile.php");


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



$tipo_id = "170";
$tipo_nome = "MULTA FGTS";
$subgrupo = 3;
$qr_recisao = mysql_query("SELECT * FROM rh_recisao WHERE MONTH(data_demi) = '$mes' AND YEAR(data_demi) = '$ano' AND id_regiao = '$regiao' AND id_projeto = '$projeto' AND id_clt = '$clt' AND status = '1'");

$query = $qr_recisao;
$sql = "SELECT * FROM rh_recisao WHERE MONTH(data_demi) = '$mes' AND YEAR(data_demi) = '$ano' AND id_regiao = '$regiao' AND id_projeto = '$projeto' AND id_clt = '$clt' AND status = '1'";


$row = mysql_fetch_assoc($query);
$nome_saida = "$row_clt[id_clt] - $row_clt[nome], $tipo_nome $mes_nome/$ano $row_projeto[id_projeto] - $row_projeto[nome]  - $row_regiao[regiao]";


if(isset($_POST['gerar'])){
    
         $id_user = $_COOKIE['logado'];
         $folha = $_POST['id_folha'];
         $mes = $_POST['mes'];
         $ano = $_POST['ano'];
         $clt = $_POST['id_clt'];       
         $nome = $_POST['nome'];
        
         $valor = str_replace('.', ',',$_POST['valor']);
        $data = implode("-",array_reverse(explode("/",$_POST['data'])));
        $banco = $_POST['bancos'];
        $subgrupo = $_POST['subgrupo'];
        $id_clt = $_POST['id_clt'];

        $qr_banco  = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'");
        $row_banco = mysql_fetch_assoc($qr_banco);
        $regiao    = $row_banco['id_regiao'];
        $projeto   = $row_banco['id_projeto'];
    
        $nome .= 'MULTA FGTS';  
        $data_multa = $data = implode("-",array_reverse(explode("/",$_POST['multa_dt_vencimento'])));
//        $valor_multa = str_replace('.','',$_POST['multa_valor']);
        $valor_multa = str_replace(',','.',str_replace('.','',$_POST['multa_valor']));
        

        $sql_multa = "INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome,especifica,   tipo,  valor, data_proc, data_vencimento, status,comprovante, nosso_numero, tipo_boleto, cod_barra_gerais, id_referencia, id_tipo_pag_saida, entradaesaida_subgrupo_id, id_clt)
	VALUES ('$regiao', '$projeto', '$banco', '$_COOKIE[logado]', '$nome','$nome', '170', '$valor_multa',NOW(), '$data_multa',  '1', '2', '$nosso_numero', '2', '$cod_barra_gerais','1', '1', '3', '$id_clt') ";
     
        
        mysql_query($sql_multa) or die(mysql_error());
       $id_saida2 = mysql_insert_id();
       
  
        
	$diretorio = "../../comprovantes";
	$upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
        
       
        
	$upload->arquivo($_FILES['multa_anexo_saida']);
	$upload->verificaFile();
        
       
	mysql_query("INSERT INTO saida_files (tipo_saida_file, id_saida,multa_rescisao) VALUES ('.$upload->extensao', '$id_saida2',1);") or die(mysql_error());	
	$id = mysql_insert_id();	
	$upload->NomeiaFile($id.'.'.$id_saida2);
	$upload->Envia();     

       echo '<script>parent.window.location.reload();
          if (parent.window.hs) {
           var exp = parent.window.hs.getExpander();
           if (exp) {            
             exp.close();           
           }
          }</script>';
       exit;
}


foreach($_GET as $chave => $valor){
	$string[] = $chave.'='.$valor;
}
$link = implode("&",$string);
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

</style>
</head>
<body>
<form action="" name="form" method="post" enctype="multipart/form-data" >
    
<input type="hidden" name="link" value="<?=$link?>" />

<h3>MULTA FGTS</h3>
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
     
      
      
     <tr class="linha_um multa"  >
    	<td>VALOR DA MULTA</td>
        <td>
            <input type="text" name="multa_valor" class="multa_valor"/>           
        </td>
    </tr>
     <tr class="linha_um multa"  >
    	<td>DATA DE VENCIMENTO DA MULTA</td>
        <td>
            <input type="text" name="multa_dt_vencimento"  class="multa_dt"/>           
        </td>
    </tr>
     <tr class="linha_um multa">
        <td>Anexo:</td>
        <td>
            <input type="file" name="multa_anexo_saida" />
            
        </td>
    </tr>  
    
    
     <tr class="linha_um">
        <td colspan="2" align="center">
        	
            <input type="hidden" name="id_folha" value="<?=$folha?>" />
            <input type="hidden" name="mes" value="<?=$mes?>" />
            <input type="hidden" name="ano" value="<?=$ano?>" />
            <input type="hidden" name="regiao" value="<?=$regiao?>" />
            <input type="hidden" name="projeto" value="<?=$projeto?>" />
            <input type="hidden" name="id_clt" value="<?php echo $clt;?>"/>
            <input type="hidden" name="subgrupo" value="<?php echo $subgrupo;?>"/>
            
        	<input type="submit" name="gerar" value="Gerar" class="submit-go" />
        </td>
    </tr>
</table>
</form>
</body>
</html>