<?php
require("../conn.php");
$id_documento = $_GET['id_doc'];

$query = mysql_query("SELECT * FROM documentos WHERE id_documento = '$id_documento'");
$row_documento = mysql_fetch_assoc($query);
?>
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript">
$().ready(function(){
	$(".tabela tr:even").addClass("linha_hover");
});
</script>
<style type="text/css">
.tabela {
	background-color:#F8F8F8;
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	border: 1px solid #CCC;
}
.linha_hover {
	background-color:#EBEBEB;
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
}

</style>
<table border="0" align="center" cellpadding="8" cellspacing="0" class="tabela">
	<tr>
		<td>Documento:</td>
        <td><?=$row_documento['id_documento']." - ".$row_documento['nome_documento']?></td>
	</tr>
    <tr>
   		<td>Descricao: </td>
        <td><?=nl2br($row_documento['descricao_documento'])?></td>
    	
    </tr>
    <tr>
  		<td>Vencimento:</td>
        <td><?=$row_documento['dia_documento']."/".date("m/Y")?></td>
    </tr>
    <tr>
      <td>Frequencia:</td>
        <td><?php
		switch($row_documento['frequencia_documento']){
			
			case 1:
				echo "Mensal";
				break;
			case 2:
				echo "Trimestral";
				break;
			case 3:
				echo "Semestral";
				break;
			case 4: 
				echo "Anual";
				break;
		}
		?></td>
    </tr>
    <tr>
    	<td>Mes de referencia:</td>
        <td><?php
        		$query_mes = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$row_documento[mes_referencia_documento]'");
				echo mysql_result($query_mes,0);
			?>
        </td>
    </tr>
        
</table>