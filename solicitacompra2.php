<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$regiao = $_REQUEST['regiao'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - REQUISI&Ccedil;&Atilde;O DE COMPRA</title>
<link href="adm/css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<script language="javascript" src="jquery-1.3.2.js"></script>
<script type="text/javascript" src="jquery/priceFormat.js"></script> 
<script>
$(function(){
	
$('#valor_medio').priceFormat({
		prefix:'',
		centsSeparator: ',',
		thousandsSeparator: '.',
});
	
});



</script>
<style>
table tr {

height:40px;
	
}


</style>

<?php
if(empty($_REQUEST['tipo'])){



$result_cont1 = mysql_query("SELECT id_compra FROM compra");
$row_cont1 = mysql_num_rows($result_cont1);
$row_cont1 ++;
$regiao_re = sprintf("%03d",$regiao);
$n_registros = sprintf("%06d",$row_cont1);
$aleatorio = mt_rand(1,99);
$n_aleatorio = sprintf("%02d",$aleatorio);
$n_ano = date("y");

$n_requisicao = $regiao_re.".".$n_registros."-".$n_aleatorio."/".$n_ano;
?>

<body style="background-color:#FFF;">

    
    
     <form id="form1" name="form1" method="post" action="solicitacompra2.php">
<table width="750" class="relacao" border="0"> 
  <tr>
    <td height="25"  align="left" valign="middle" colspan="6"><h3><img src="imagensfinanceiro/controledecotacoes.gif" alt="cotas" width="30" height="30" align="absmiddle" /> REQUISI&Ccedil;&Atilde;O  DE COMPRA </h3></td>
  </tr>

  
  <tr>   
    <td   colspan="6" align="left"> N�. Requisi��o: <?php echo $n_requisicao?>
    </td>
   </tr>
   <tr>
    <td class="secao">Tipo:</td>
    <td align="left">
		<select name="tipo" id="tipo">
		    <option value="1">PRODUTO</option>
		    <option value="2">SERVI&Ccedil;O</option>
		</select>
   </td>
   
   	<td class="secao">Integra&ccedil;&atilde;o ao Patrim&ocirc;nio:</td>
   	<td colspan="3" align="left">
    <select name="patrimonio" id="patrimonio">
      <option value="1">BEM DE CONSUMO</option>
      <option value="2">BEM DUR&Aacute;VEL</option>
    </select>
	</td>
   </tr>
   <tr>
   	<td class="secao"> Produto:</td>
   	<td colspan="5" align="left"><input name="produto" type="text" id="produto" size="80" /></td>
   </tr>
    <tr>
    	<td class="secao">Descri&ccedil;&atilde;o do Produto:</td>
    	<td colspan="5" align="left"> <input name="descricao" type="text" id="descricao" size="84" maxlength="300" /></td>
    </tr>   
    <tr>
    	<td class="secao">Quantidade:</td>
    	<td align="left">  <input name="quantidade" type="text" id="quantidade" size="10" /></td>
        <td class="secao">Valor M&eacute;dio:</td>
        <td align="left"><input name="valor_medio" type="text" id="valor_medio" size="20"  />
		</td>
		<td align="left" class="secao">Necess&aacute;rio para:</td>
        <td>  <input name="data" type="text" id="data" size="10" maxlength="10"/></td>
    </tr>
     <tr>
     	<td class="secao">Descri&ccedil;&atilde;o da Necessidade:</td>
     	<td colspan="5" align="left"> <input name="necessidade" type="text" id="necessidade" size="77" maxlength="250" /></td>
     </tr>    
	
       <tr>
       <td colspan="6">
	  <input type="hidden" name="requisicao" id="requisicao" value="<?php echo $n_requisicao; ?>" />
        <input type="hidden" name="regiao" id="regiao" value="<?php echo $regiao; ?>" /> 
	        <input type="submit" name="solicitar" id="button" value="SOLICITAR" />
	        </label>
	       
	        <label>
	        <input type="reset" name="cancela" id="cancela" value="CANCELAR" />
	        </label>
	     
      </td>  
  </tr>
 
  
   </table>
		<?php
		include "empresa.php";
		$rod = new empresa();
		$rod -> rodape();
		?>


 </form>

</body>
</html>








<?php
}else{                   //------------------------------------GRAVANDO OS DADOS DO FORMUL�RIO -------------------

$id_user_pedido = $_COOKIE['logado'];
$tipo = $_REQUEST['tipo'];
$produto = $_REQUEST['produto'];
$descricao = $_REQUEST['descricao'];
$valor_medio = $_REQUEST['valor_medio'];
$data = $_REQUEST['data'];
$requisicao = $_REQUEST['requisicao'];
$quantidade = $_REQUEST['quantidade'];
$necessidade = $_REQUEST['necessidade'];

/* 
Fun��o para converter a data
De formato nacional para formato americano.
Muito �til para voc� inserir data no mysql e visualizar depois data do mysql.
*/


function ConverteData($Data){
 if (strstr($Data, "/"))//verifica se tem a barra /
 {
  $d = explode ("/", $Data);//tira a barra
 $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
 return $rstData;
 } elseif(strstr($Data, "-")){
 $d = explode ("-", $Data);
 $rstData = "$d[2]/$d[1]/$d[0]"; 
 return $rstData;
 }else{
 return "Data invalida";
 }
}

$data_c = ConverteData($data);
$data_hoje = date("Y-m-d");
$valor_medio = str_replace(".","",$valor_medio);


mysql_query ("INSERT INTO compra (id_regiao,id_user_pedido,num_processo,tipo,nome_produto,descricao_produto,necessidade,quantidade,valor_medio,data_produto,data_requisicao,status_requisicao,acompanhamento) 
VALUES 
('$regiao','$id_user_pedido','$requisicao','$tipo','$produto','$descricao','$necessidade','$quantidade','$valor_medio','$data_c','$data_hoje','1','1')");


echo  "
<script>

parent.window.location.reload();
if (parent.window.hs) {
	var exp = parent.window.hs.getExpander();
	if (exp) {
		exp.close();
	}
}

</script>

";


}
}

?>
<script language="javascript" src="designer_input.js"></script>