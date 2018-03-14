<?php
require("../conn.php");
$id_file = $_GET['id_doc'];

$query = mysql_query("SELECT * FROM documentos as doc, doc_files as fil WHERE doc.id_documento = fil.id_documento AND fil.id_file = '$id_file'");
$row_file = mysql_fetch_assoc($query);

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
        <td><?=$row_file['id_documento'] - $row_file['nome_documento']?></td>
	</tr>
    <tr>
		<td>Regiao:</td>
        <td><?php 
		$query_regioes = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_file[id_regiao]'");
		echo mysql_result($query_regioes,0);
		?></td>
	</tr>
    <tr>
    	<td>Dia de vencimento</td>
        <td><?=$row_file['dia_documento']?></td>
    </tr>
    <tr>
    	<td>Frequencia</td>
        <td><?php
        
		switch($row_file['frequencia_documento']){
			
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
    	<td>Mes de referencia</td>
        <td><?php
        		$query_mes = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$row_file[mes_referencia_documento]'");
				echo mysql_result($query_mes,0);
			?>
        </td>
    </tr>
    <tr>
    	<td>Usuario que cadastrou</td>
        <td>
		<?php 
			$query_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_file[id_funcionario]'");
			echo mysql_result($query_funcionario,0);
		?>
        </td>
    </tr>
    <tr>
    	<td>Data de cadastro</td>
        <td><?=implode("/",array_reverse(explode('-',$row_file['data_documento'])))?></td>
    </tr>
    <tr>
    	<td>Data envio</td>
        <td><?=implode("/",array_reverse(explode('-',$row_file['data_file'])))?></td>
    </tr>
    <tr>
    	<td>Data de recebimento</td>
        <td><?=implode("/",array_reverse(explode('-',$row_file['data_recebimento_file'])))?></td>
    </tr>
</table>