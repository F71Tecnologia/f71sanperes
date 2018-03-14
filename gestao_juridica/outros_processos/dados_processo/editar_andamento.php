<?php
include ("../../include/restricoes.php");
include('../../../conn.php');
include('../../../funcoes.php');
//include "../funcoes.php";
include "../../include/criptografia.php";



if(isset($_POST['enviar'])){
	
$id_andamento = $_POST['id_andamento'];
$data_andamento = implode('-', array_reverse(explode('/', $_POST['data_andamento'])));
$id_processo    = $_POST['id_processo'];

$inserir = mysql_query("UPDATE proc_trab_andamento SET andamento_data_movi = '$data_andamento' WHERE andamento_id = '$id_andamento' LIMIT 1");


	if($inserir) {
		header("Location: anexar_doc_andamentos.php?id_andamento=$id_andamento&id_processo=$id_processo ");
	}
	
}




$id_andamento = mysql_real_escape_string($_GET['id']);
$id_processo  =  mysql_real_escape_string($_GET['id_processo']);
$qr_andamento  = mysql_query("SELECT * FROM proc_trab_andamento WHERE andamento_id = '$id_andamento' ");
$row_andamento =  mysql_fetch_assoc($qr_andamento);


$nome = mysql_result(mysql_query("SELECT proc_status_nome FROM processo_status WHERE proc_status_id = '$row_andamento[proc_status_id]' "),0) or die(mysql_error());
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<link href="../../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../../js/ramon.js"></script>
<script type="text/javascript" src="../../../js/jquery-1.3.2.js"></script>

<script type="text/javascript" src="../../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
<script type="text/javascript" src="../../../jquery/validationEngine/jquery.validationEngine.js" ></script>
<link href="../../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript">
$(function(){
	$('#data').mask('99/99/9999');
	
})

</script>
</head>
<body>
    <div id="corpo">
    	<form name="form" method="post" action="">
        <table width="50%" align="center">
        <tr><td colspan="2" align="center" style="text-align:center;">EDITAR ANDAMENTO </td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        
        <tr>
            <td align="center" class="secao_pai" colspan="2"><?php echo $nome;?></td>
        </tr>
     
        <tr>
        	<td class="secao">Data do andamento:</td>
            <td><input name="data_andamento" type="text" value="<?php echo implode('/', array_reverse(explode('-',$row_andamento['andamento_data_movi']))); ?>" class="validate[required]" id="data"/></td>
        </tr>
        <tr>
        	<td colspan="2" align="center" style="text-align:center;">
            <input type="hidden" name="id_andamento" value="<?php echo $id_andamento;?>"/>	
             <input type="hidden" name="id_processo" value="<?php echo $id_processo;?>"/>	
            <input name="enviar" type="submit" value="Enviar" /></td>
        </tr>
        
        </table>
    	</form>
    </div>

</body>
</html>
