<?php
include ("include/restricoes.php");
include "../conn.php";

$id_provisao = $_GET['provisao'];
$query_provisao = mysql_query("SELECT * FROM provisao WHERE id_provisao = '$id_provisao'");
$row_provisao = mysql_fetch_assoc($query_provisao);

$query_max_data = mysql_query("SELECT MAX(data_provisionamento) FROM provisionamento WHERE id_provisao = '$id_provisao'");
$row_max_data = mysql_result($query_max_data,0);
if(!empty($row_max_data)){
	$row_max_data = explode("/",$row_max_data);
	$row_max_data = sprintf('%2d',$row_max_data[0]+1)."/".$row_max_data[1];
	
}else{
	$row_max_data = $row_provisao['dataini_provisao']; 
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Cadastro de provisionamento</title>
<link href="../net1.css" rel="stylesheet" type="text/css" />
<script type="application/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
<script type="application/javascript" src="../jquery/priceFormat.js" ></script>
<script type="application/javascript">
$().ready(function(){
		$("p.concluir").click(function(){
			$("#form1").submit();
		});
		$("#valor").priceFormat({
			prefix: '',
			centsSeparator: ',',
			thousandsSeparator: '.'
		}); 
});
	
</script>
<style type="text/css">
strong {
	color:#FFF;
}
p.concluir {
	padding:10px;
	width:150px;
	background-color: #EAEAEA;
	border: thin solid #CECECE;
	-moz-border-radus: 5px;
	-webkit-border-radus: 5px;
	cursor:pointer;
	margin: 5px;
	font-weight: bold;
	color: #333;
}
</style>
</head>

<body>

<form id="form1" name="form1" method="post" action="actions/cadastro.provisionamento.php">
  <table width="300"  border="0" bordercolor="#FFFFFF" align="center" cellpadding="5" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">
    <tr>
      <td height="25" colspan="4" align="center" valign="middle" background="imagensfinanceiro/barra3.gif"><strong>CADASTRO DE PROVISIONAMENTO</strong></td>
    </tr>
    <tr>
      <td width="10%" >PROVIS&Atilde;O:</td>
      <td  ><label for="nome"></label>
      <input name="nome" type="text" disabled="disabled" id="nome" value="<?=$row_provisao['nome_provisao']?>" /></td>
    </tr>
    <tr>
      <td >DESCRI&Ccedil;&Atilde;O: </td>
      <td  ><label for="descricao"></label>
      <textarea name="descricao" id="descricao"></textarea></td>
    </tr>
    <tr>
      <td >VALOR:</td>
      <td  >R$
        <input name="valor" type="text" id="valor" size="15" /></td>
    </tr>
    <tr>
      <td >DATA:</td>
      <td >
      
      <input name="data" type="text" id="data" value="<?=$row_max_data?>" disabled size="9"> 
      mm/aaaa
      <input type="hidden" name="provisao" id="provisao" value="<?=$id_provisao?>">
      <input type="hidden" name="dataProvisao" id="dataProvisao" value="<?=$row_max_data?>"></td>
    </tr>
    <tr>
      <td colspan="2" align="center" ><p class="concluir">CONCLUIR</p></td>
    </tr>
  </table>
</form>

</body>
</html>