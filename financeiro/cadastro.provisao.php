<?php 
include ("include/restricoes.php");

include "../conn.php";
$regiao = $_REQUEST['regiao'];

$meses = array();
$qr_meses = mysql_query("SELECT * FROM  ano_meses ORDER BY num_mes ASC");
while($row_meses = mysql_fetch_assoc($qr_meses)){
	$meses[$row_meses['num_mes']] = $row_meses['nome_mes'];
}

$anos =  array("2010"=>2010,"2011"=>2011,"2012"=>2012,"2013"=>2013);


if(isset($_GET['ID'])){
	$query_provisao = mysql_query("SELECT * FROM provisao WHERE id_provisao = '$_GET[ID]'");
	$row_provisao = mysql_fetch_assoc($query_provisao);
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Cadastro de provis&atilde;o</title>
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
p.concluir1 {	padding:10px;
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
<form id="form1" name="form1" method="post" action="actions/cadastro.provisao.php">
  <table width="284"  border="0" bordercolor="#FFFFFF" align="center" cellpadding="5" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">
    <tr>
      <td height="25" colspan="4" align="center" valign="middle" background="imagensfinanceiro/barra3.gif"><strong>CADASTRO DE PROVI&Otilde;ES</strong></td>
    </tr>
    <tr>
      <td width="10%" >PROJETO:</td>
      <td  ><label for="nome"></label>
        <label for="projeto"></label>
        <select name="projeto" id="projeto" <?php if(isset($_GET['ID'])){ echo "disabled"; }?> >
        <?php
		if(isset($_GET['ID'])){
			$query_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_provisao[id_projeto]';");
			$rw_projeto = mysql_fetch_assoc($query_projeto);
			print "<option value='$rw_projeto[id_projeto]'> $rw_projeto[id_projeto] - $rw_projeto[nome]</option>";			
		
		}else{
			$qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1'");
			while($row_projeto = mysql_fetch_assoc($qr_projetos)){
				print "<option value='$row_projeto[id_projeto]'> $row_projeto[id_projeto] - $row_projeto[nome]</option>";			
			}
		}
		?>
      </select></td>
    </tr>
    <tr>
      <td >DATA: </td>
      <td  ><label for="descricao">
        <select name="mes" id="mes">
          <?php
		  $mes = date('m');
		  if(isset($_GET['ID'])){
			  $mes = $row_provisao['mes_provisao'];
		  }
				foreach($meses as $numero => $nome){
					if($mes == $numero){
						print  "<option value=\"$numero\" selected=\"selected\" >$nome</option>";
					}else{
						print  "<option value=\"$numero\" >$nome</option>";
					}
					
					
				}
				?>
        </select>
        <select name="ano" id="ano">
          <?php
		  	$ano_selecao = date("Y");
			if(isset($_GET['ID'])){
				$ano_selecao = $row_provisao['ano_provisao'];
			}
				foreach($anos as $ano) {
					if($ano_selecao == $ano) {
						print "<option  selected=\"selected\" value=\"$ano\">$ano</option>";
					}else{
						print "<option value=\"$ano\">$ano</option>";
					}
				}
			?>
        </select>
      </label></td>
    </tr>
    <tr>
      <td >Valor:</td>
      <td  >R$ 
        <label for="valor"></label>
      <input name="valor" type="text" id="valor" size="15" <?php if(isset($_GET['ID'])){ echo "value=\"".number_format($row_provisao['valor_provisao'], 2, ',', '.')."\"";} ?> />
      <input type="hidden" name="id" id="id" value="<?=$_GET['ID']?>">
      <input type="hidden" name="log" id="log" value="<?php if(isset($_GET['ID'])){ echo "2";}else{echo "1";};?>"></td>
    </tr>
    <tr>
      	<td colspan="2" align="center" >
      		<p class="concluir">CONCLUIR</p>
       	</td>
    </tr>
  </table>
</form>
</body>
</html>