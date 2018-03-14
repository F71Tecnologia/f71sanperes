<?php
include('../../adm/include/restricoes.php');
include('../../conn.php');
include('../../classes_permissoes/regioes.class.php');

$REGIAO = new Regioes();



if(isset($_POST['confirmar'])){


$id_banco   = $_POST['banco'];
$id_folha   = $_POST['id_folha'];
$id_regiao  =  $_POST['regiao'];
$id_projeto = $_POST['projeto'];
$data_vencimento = implode('-', array_reverse(explode('/',$_POST['data_vencimento'])));



$sql = array();

$qr_folha  = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$id_folha'");
$row_folha = mysql_fetch_assoc($qr_folha);

$especifica ='COMP. '.$row_folha['mes'].'/'.$row_folha['ano'];


$result_folha_pro = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$id_folha'  AND financeiro != 1  AND status = 3  ");

while($row_folha_proc = mysql_fetch_assoc($result_folha_pro)):


$sql[] = "('$id_regiao', '$id_projeto', '$id_banco', '$_COOKIE[logado]', '$row_folha_proc[nome]',  '$especifica', '31', '$row_folha_proc[salliquido]',NOW(), '$data_vencimento',  '1', '0')";

$id_folha_proc[] = $row_folha_proc['id_folha_proc'];


endwhile;

$sql = implode(',',$sql);
$id_folha_proc = implode(',',$id_folha_proc);



$qr_insert = mysql_query("INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome,  especifica, tipo, valor, data_proc, data_vencimento, status,comprovante) VALUES $sql") or die(mysql_error());



if($qr_insert) {
	
	mysql_query("UPDATE rh_folha_proc SET financeiro = 1 WHERE id_folha_proc IN($id_folha_proc) ");
	
}


echo 'ENVIADO PARA O FINANCEIRO COM SUCESSO!<br> Aguarde..';
echo 
'<script>
	setTimeout(function() { parent.window.location.reload();
		if (parent.window.hs) {
			var exp = parent.window.hs.getExpander();
			if (exp) {
				
				
				exp.close()
			}
		}},3000);

</script>';

exit;
	
}




if(isset($_GET['folha'])){


$id_folha = mysql_real_escape_string( $_GET['folha']);	


$qr_folha  = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$id_folha'");
$row_folha = mysql_fetch_assoc($qr_folha);

$nome_projeto = mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$row_folha[projeto]'"),0);
$nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_folha[regiao]'"),0);




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
<link rel="stylesheet" type="text/css" href="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" />

<script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript">	</script>
<script>
$(function(){

$('#data').mask('99/99/9999');	
	
$('#regiao').change(function(){
	
	
	var id_regiao = $(this).val();
	$.ajax({
		
		url : '../../folhaspg/actions/dados_gera_saida.php?regiao='+id_regiao,
		success :function(resposta){
			
				$('#projeto').html(resposta);	
			}
		
		});
	
	
	});	
	
	



$('#projeto').change(function(){
	
	
	var id_projeto = $(this).val();
	$.ajax({
		
		url : '../../folhaspg/actions/dados_gera_saida.php?projeto='+id_projeto,
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
        <td>R$ <?php echo $row_folha['total_liqui'];?></td>
    </tr>
   
    <tr>
      <td colspan="2" align="center">
      
      <input type="hidden" name="id_trab"   value="<?php echo $id_trab;?>"/>     

      <input type="hidden" name="id_folha"   value="<?php echo $id_folha;?>" />
      
      <input type="submit" value="CONFIRMAR" name="confirmar"/>
      </td>
    </tr>
</table>


</body>
</html>


