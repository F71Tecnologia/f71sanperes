<?php
include "../conn.php";

$documento 	= $_REQUEST['documento'];
$regiao 	= $_REQUEST['regiao'];
$mes 		= $_REQUEST['mes'];

$query = mysql_query("SELECT * FROM doc_files as f,documentos as d WHERE f.id_documento  = d.id_documento AND f.id_documento = '$documento' AND f.id_regiao = '$regiao' AND f.mes_file = '$mes';");
while($row_ids = mysql_fetch_assoc($query)){
	$ids[] = $row_ids['id_file'];
}
$query = mysql_query("SELECT * FROM doc_files as f,documentos as d WHERE f.id_documento  = d.id_documento AND f.id_documento = '$documento' AND f.id_regiao = '$regiao' AND f.mes_file = '$mes';");
?>
<table width="0" border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td>Arquivos</td>
    <td></td>
  </tr>
  <?php 
  		$cont = 0;
  		while($row = mysql_fetch_assoc($query)){
			$cont++
	?>
  <tr>
    <td><?php
	  if(empty($row['recebimento_file'])){
	 		print $row['nome_documento'].$cont;
	  }else{
	  		print "<span style=\"color: #ccc;\" >".$row['nome_documento'].$cont."</span>";
	  }
	 ?></td>
    <td>
    	<?php
        if(empty($row['recebimento_file'])){
			print "<a href=\"actions/recebimento.documentos.php?id_file=$row[id_file]\" target=\"_blank\"> Baixar </a>";
		}else{
			print "<a href=\"actions/recebimento.documentos.php?id_file=$row[id_file]\" target=\"_blank\"> Visualizar </a>";
		}
		?>
    </td>
    <td>
	
    </td>
  </tr>
  <?php }?>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>