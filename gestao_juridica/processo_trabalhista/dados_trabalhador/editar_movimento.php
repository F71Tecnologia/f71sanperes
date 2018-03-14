<?php
include ("../../include/restricoes.php");
include('../../../conn.php');
include('../../../funcoes.php');
//include "../funcoes.php";
include "../../include/criptografia.php";



if(isset($_POST['enviar'])){
	
$id_movimento = $_POST['id_movimento'];
$data_movimento = implode('-', array_reverse(explode('/', $_POST['data_movimento'])));
$id_processo    = $_POST['id_processo'];
$obs			= $_POST['obs'];

$inserir = mysql_query("UPDATE proc_trab_movimentos SET data_movimento = '$data_movimento',
														obs = '$obs'
															  WHERE proc_trab_mov_id = '$id_movimento' LIMIT 1");


	if($inserir) {
		header("Location: anexar_doc_movimentos.php?id_movimento=$id_movimento&id_processo=$id_processo");
	}
	
}




$id_movimento= mysql_real_escape_string($_GET['id']);
$id_processo  =  mysql_real_escape_string($_GET['id_processo']);
$qr_movimento  = mysql_query("SELECT * FROM proc_trab_movimentos WHERE proc_trab_mov_id = '$id_movimento' ");
$row_movimento =  mysql_fetch_assoc($qr_movimento);


//$nome = mysql_result(mysql_query("SELECT proc_status_nome FROM processo_status WHERE proc_status_id = '$row_andamento[proc_status_id]' "),0) or die(mysql_error());
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
<link href="../../../js/highslide.css" rel="stylesheet" type="text/css"  /> 
<script type="text/javascript" src="../../../js/highslide-with-html.js"></script> 
<script type="text/javascript" src="../../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>

<script type="text/javascript" src="../../../jquery/priceFormat.js" ></script>

<script type="text/javascript">
    hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';
	$(function(){
		$('#data').mask('99/99/9999');
		
		})
</script>
</head>
<body>
    <div id="corpo">
    	<form name="form" method="post" action="">
        <table width="50%" align="center">
        <tr><td colspan="2" align="center" style="text-align:center;">EDITAR MOVIMENTO </td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        
        <tr>
            <td align="center" class="secao_pai" colspan="2">MOVIMENTO</td>
        </tr>
     
        <tr>
        	<td class="secao">Data do andamento:</td>
            <td><input name="data_movimento" type="text" value="<?php echo implode('/', array_reverse(explode('-',$row_movimento['data_movimento']))); ?>"  id="data" size="8"/></td>
        </tr>
        <tr>
        	<td class="secao">Descrição:</td>
            <td>
            <textarea cols="30" rows="5" name="obs">
            <?php
            echo trim($row_movimento['obs']);			
			?>
            
            </textarea>
            
            </td>
        </tr>
        
        <tr>
        	<td colspan="2" align="center" style="text-align:center;">
            <input type="hidden" name="id_movimento" value="<?php echo $id_movimento;?>"/>	
             <input type="hidden" name="id_processo" value="<?php echo $id_processo;?>"/>	
            <input name="enviar" type="submit" value="Enviar" /></td>
        </tr>
        
        </table>
    	</form>
    </div>

</body>
</html>
