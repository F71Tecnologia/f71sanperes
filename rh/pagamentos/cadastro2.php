<?php 
include "../../conn.php";

$tipo = $_GET['tipo']; // 1 - Férias; 2 - Recição;
$ano = $_GET['ano'];
$mes = $_GET['mes'];
$projeto = $_GET['projeto'];
$regiao = $_GET['regiao'];

$qr_recisao = mysql_query("SELECT * FROM rh_recisao WHERE MONTH(data_demi) = '$mes' AND YEAR(data_demi) = '$ano' AND id_regiao = '$regiao' AND id_projeto = '$projeto' status = '1'");


$qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE MONTH(data_ini) = '$mes' AND YEAR(data_ini) = '$ano' AND regiao = '$regiao' AND projeto = '$projeto' status = '1'");

if($tipo == '1'){
	$query = $qr_ferias;
}else{
	$query = $qr_recisao;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cadastro de reci&ccedil;&atilde;o</title>
<style type="text/css">
body {
	margin:0px;
	text-align:center;
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}
#conteiner{
	margin:10px auto;
	width:450px;
	text-align:left;
}
legend{
	text-align:center;
}

.linha_um {
 background-color:#f5f5f5;
}
.linha_dois {
 background-color:#ebebeb;
}
.linha_um td, .linha_dois td {
 border-bottom:1px solid #ccc;
}

</style>
</head>
<body>
<div id="conteiner">
<form method="post" name="Form">
<input type="hidden" name="mes" value="<?=$mes?>" />
<input type="hidden" name="ano" value="<?=$ano?>" />
<fieldset>
	<legend>Clt(s) em <?php if($tipo == 1){ echo "Férias";}else{ echo "Rescisão";} ?> da folha (<?=$id_folha?>)</legend>
    <table width="100%" cellpadding="3" cellspacing="1">
    	<tr class="linha_um">
        	<td width="10%"><b>ID Clt</b></td>
            <td width="60"><b>Nome</b></td>
            <td width="20%"><b>Valor</b></td>
            <td width="10%">&nbsp;</td>
        </tr>
        <?php while($row = mysql_fetch_assoc($query)): ?>
        <?php 
		// CONFERINDO SE JÁ FOI GERADO O PAGAMENTO
		$query_pagamentos = mysql_query("
		SELECT id_saida FROM pagamentos_especifico
		WHERE 
		mes = '$mes'
		AND ano = '$ano'
		AND id_clt = '$row[id_clt]'");
		$id_saida = @mysql_result($query_pagamentos,0);
		$query_saida = mysql_query("SELECT * FROM saida WHERE id_saida = '$id_saida' AND status != '0'");
		$num_saida = mysql_num_rows($query_saida);
		$row_saida = mysql_fetch_assoc($query_saida);
		?>
        <?php $totalizador += $row['total_liquido']; ?>
        <?php 
			// BUSCANDO O NOME NA TABELA DE CLT, POIS NA TABELA DE FERIAS OU RECIÇÃO TEM REGISTRO SEM NOME
			$query_clt = mysql_query("SELECT nome FROM rh_clt WHERE id_clt = '$row[id_clt]'");
			$nome = @mysql_result($query_clt,0);
                        echo $nome;
		?>
        <?php 

			if($row_saida['status'] == '1'){
				$linha_pg = 'bgcolor=#FF5353';
			}elseif($row_saida['status'] == '2'){
				$linha_pg = 'bgcolor=#43E958';
			}else{
				$linha_pg = '';
			}

		?>
        <tr <?=$linha_pg?>>
        	<td><?=$row['id_clt']?></td>
            <td><?=$nome?></td>
            <td>R$ <?=number_format($row['total_liquido'],2,',','.');?></td>
            <?php 			
			$query_string['id_folha'] =  $id_folha;
			$query_string['mes'] =  $mes;
			$query_string['ano'] =  $ano;
			$query_string['id_clt'] =  $row['id_clt'];
			$query_string['regiao'] =  $regiao;
			$query_string['projeto'] = $projeto;
			if($tipo == '1'){
				$query_string['tipo'] =  '1';
			}else{
				$query_string['tipo'] =  '2';
			}

			foreach($query_string as $chave => $str){
				$string[] = $chave.'='.$str;
			}
			$link = implode('&',$string);

			?>
            <td>
            	<?php if(empty($num_saida)): ?>
            	<a href="detalhes.php?<?=$link?>">
                	<img border="0px" src="imagens/saida-32.png" width="18" height="18" />
                </a>
				<?php endif;?>
            </td>
        </tr>
        <?php endwhile;?>
        <tr>
        	<td colspan="2" align="right">Total:</td>
        	<td>R$ <?= number_format($totalizador,2,',','.');?></td>
        </tr>
    </table>
</fieldset>
</form>
</div>
</body>
</html>