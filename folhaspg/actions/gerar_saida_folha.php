<?php
include('../../adm/include/restricoes.php');
include('../../conn.php');
include('../../classes_permissoes/regioes.class.php');

$REGIAO = new Regioes();

if(isset($_POST['confirmar'])){



$id_trab 	= $_POST['id_trab'];
$id_banco   = $_POST['banco'];
$id_folha   = $_POST['id_folha'];
$id_regiao  = $_POST['regiao'];
$id_projeto = $_POST['projeto'];
$data_vencimento = implode('-',array_reverse(explode('/',$_POST['data_vencimento'])));


$qr_trab = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$id_trab'");
$row_trab = mysql_fetch_assoc($qr_trab);

$qr_banco = mysql_query("SELECT * FROM bancos WHERE  id_banco = '$id_banco'");
$row_banco = mysql_fetch_assoc($qr_banco);



$qr_folha  = mysql_query("SELECT * FROM folhas WHERE id_folha = '$id_folha'");
$row_folha = mysql_fetch_assoc($qr_folha);

switch($row_folha['contratacao']){

	case 1: $tipo = 30;
			$result_folha_pro = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$id_folha' AND  	id_autonomo = '$id_trab' AND status IN('3','4') ORDER BY banco, nome");
			$tabela_folha = 'folha_autonomo';
	
	break;

	case 3: $tipo = 32;
			$result_folha_pro = mysql_query("SELECT * FROM folha_cooperado WHERE id_folha = '$id_folha'  AND  id_autonomo = '$id_trab' AND status IN('3','4') ORDER BY banco, nome");
			$tabela_folha = 'folha_cooperado';
	break;
	
}
$row_folha_proc = mysql_fetch_assoc($result_folha_pro);


$especifica ='COMP. '.$row_folha['mes'].'/'.$row_folha['ano'];


$qr_insert = mysql_query("INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome,  especifica, tipo, valor, data_proc, data_vencimento, status,comprovante)
	VALUES ('$id_regiao', '$id_projeto', '$id_banco', '$_COOKIE[logado]', '$row_trab[nome]',  '$especifica', '$tipo', '$row_folha_proc[salario_liq]',NOW(), '$data_vencimento',  '1', '0')") or die(mysql_error());

if($qr_insert) {
	
	mysql_query("UPDATE $tabela_folha SET vinculo_financeiro = 1 WHERE id_folha_pro = '$row_folha_proc[id_folha_pro]' LIMIT 1");
	
}
echo 'ENVIADO PARA O FINANCEIRO COM SUCESSO!';
echo 
'<script>
	parent.window.location.reload();
		if (parent.window.hs) {
			var exp = parent.window.hs.getExpander();
			if (exp) {
				exp.close();
			}
		}

</script>';

exit;
	
}




if(isset($_GET['id_trab'])){


$id_folha = mysql_real_escape_string( $_GET['folha']);	
$id_trab = mysql_real_escape_string($_GET['id_trab']);
$qr_trab = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$id_trab'");
$row_trab = mysql_fetch_assoc($qr_trab);

$regiao  = $row_trab['id_regiao'];
$projeto = $row_trab['id_projeto'];

$nome_projeto = mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$projeto'"),0);
$nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'"),0);



$qr_folha  = mysql_query("SELECT * FROM folhas WHERE id_folha = '$id_folha'");
$row_folha = mysql_fetch_assoc($qr_folha);

switch($row_folha['contratacao']){

	case 1: $tipo = 30;
			$result_folha_pro = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$id_folha' AND id_autonomo = '$id_trab' AND status IN('3','4') ORDER BY banco, nome");
			$tabela_folha = 'folha_autonomo';
	
	break;

	case 3: $tipo = 32;
			$result_folha_pro = mysql_query("SELECT * FROM folha_cooperado WHERE id_folha = '$id_folha' AND id_autonomo = '$id_trab' AND status IN('3','4') ORDER BY banco, nome");
			$tabela_folha = 'folha_cooperado';
	break;
	
}
$row_folha_proc = mysql_fetch_assoc($result_folha_pro);
}

?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Financeiro</title> 
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js" ></script>
<script src="../jquery/jquery.tools.min.js" type="text/javascript">	</script>
<script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript">	</script>
<link rel="stylesheet" type="text/css" href="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" />

<script>
$(function(){

$('#data').mask('99/99/9999');	
	
$('#regiao').change(function(){
	
	
	var id_regiao = $(this).val();
	$.ajax({
		
		url : 'dados_gera_saida.php?regiao='+id_regiao,
		success :function(resposta){
			
				$('#projeto').html(resposta);	
			}
		
		});
	
	
	});	
	
	



$('#projeto').change(function(){
	
	
	var id_projeto = $(this).val();
	$.ajax({
		
		url : 'dados_gera_saida.php?projeto='+id_projeto,
		success :function(resposta){
			
				$('#banco').html(resposta);	
			}
		
		});
	
	
	});	
	
	
});
</script>




</head>
<body>
<form name="form" method="post" action="" />
<table border="0" style="font-size:12px;height:200px;">
	<tr>
    	<td colspan="2"><strong>PAGAMENTO:</strong> <?php echo $row_trab['nome'];?></td>
    </tr>
    <tr>
    	<td> <strong>REGIÃO:</strong> </td>
		<td>
		<select name="regiao" id="regiao">
        <?php        
		$REGIAO->Preenhe_select_sem_master();
		?>        
        </select>
		</td>
        
        </tr>
        
    <tr>
        <td> <strong>PROJETO:</strong> </td>
        <td>
        <select name="projeto" id="projeto"></select>        
        </td>
    </tr> 
    <tr>
    	<td><strong>BANCO:</strong></td>
        <td>
        <select name="banco" id="banco">
	
        </select>
        </td>
    </tr>
    <tr>
    	<td><strong>DATA DE VENCIMENTO:</strong></td>
        <td><input type="text" name="data_vencimento" id="data"/></td>
    </tr>
    
    
    <tr>
    	<td><strong>VALOR:</strong></td>
        <td>R$ <?php echo $row_folha_proc['salario_liq'];?></td>
    </tr>
   
    <tr>
      <td colspan="2" align="center">
      
      <input type="hidden" name="id_trab"   value="<?php echo $id_trab;?>"/>     
      <input type="hidden" name="id_projeto"  value="<?php echo $projeto; ?>"/>
      <input type="hidden" name="id_folha"   value="<?php echo $id_folha;?>" />
      
      <input type="submit" value="CONFIRMAR" name="confirmar"/>
      </td>
    </tr>
</table>

</select>

</body>
</html>


