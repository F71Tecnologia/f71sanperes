<?php
if(empty($_COOKIE['logado'])) {
	print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a>";
	exit;
}

include('../../conn.php');

$meses = array('01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Mar&ccedil;o', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SEFIP</title>
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript">
$(function(){
	/*var indice = 0;
	$('.final').each(function(){
		indice++;
	});
	if(indice == 0) {
		window.location.href = $('.sefip').attr("title");
	}*/
});
</script>
<style type="text/css">
body {
	margin:50px; text-align:center;
}
h1 {
	font-size:18px;
}
h2 {
	font-size:13px; color:#C30;
}
</style>
</head>
<body>
<?php
// Variáveis
$mes   		 = $_GET['mes'];
$ano   		 = $_GET['ano'];
$tipo_sefip  = $_GET['tipo_sefip'];
$parte 		 = $_GET['parte'];
$parte_sefip = $_GET['parte_sefip'];

$data_comparacao = implode('-', array_reverse(explode('/', $_GET['data'])));
$data            = str_replace('/','',$_GET['data']);

// Buscando Ids
$qr_folha    = mysql_query("SELECT * FROM rh_folha WHERE status = '3' AND mes = '$mes' AND ano = '$ano' AND regiao != '36'  AND regiao != 4 ");
$total_folha = mysql_num_rows($qr_folha);



if(!empty($total_folha)) {

	while($row_folha = mysql_fetch_assoc($qr_folha)) {
		
		//echo $row_folha['id_folha'].'<br>';
		
		/* Alterado em 06/11/2011 na casa de Sabino
		$qr_participantes     = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$row_folha[id_folha]' AND status_clt NOT IN ('60','61','62','63','64','65','66','81','101') AND status = '3'");*/
		$qr_participantes     = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$row_folha[id_folha]' AND status = '3'");
		$total_participantes += mysql_num_rows($qr_participantes);
		while($row_participantes = mysql_fetch_assoc($qr_participantes)) {	
				
		
			
			
			$ids[]      = $row_participantes['id_folha_proc'];
			$ids_grrf[] = $row_participantes['id_clt'];
		}
	}
	
}


/* Separando Ids pela Parte
$partes	       = ceil(count($ids) / 100);
$limite_inicio = ($parte * 100) - 100;
$limite_fim	   = $limite_inicio + 100;
$ids		   = implode(',', array_splice($ids,$limite_inicio,$limite_fim)); */

$ids	  = implode(',', $ids);
$ids_grrf = implode(',', $ids_grrf);



// Buscando Arquivo
$local_arquivo = 'arquivos/'.$mes.'_'.$ano.'.re';
if($parte_sefip == 1 and file_exists($local_arquivo)) {
	unlink($local_arquivo);
}




$arquivo = fopen("$local_arquivo", "a");

 
// SEFIP
require('corpo_sefip_rpa.php');

// GRRF
//require('corpo_grrf.php');


fclose($arquivo);

/* Partes de Informações da Empresa
$partes_sefip_unicas = array('1','2','3','7');

// Variável para a Mensagem
if(in_array($parte_sefip,$partes_sefip_unicas)) {
	$partes_setor = 1;
} else {
	$partes_setor = $partes;
}

// Variáveis para a próxima parte do Sefip
// Se a Parte do Sefip for Única ou for a Última Parte de Ids passa para a Próxima Parte do Sefip
if(in_array($parte_sefip,$partes_sefip_unicas) or $parte == $partes) {
	$proxima_parte_sefip = $parte_sefip + 1;
	$proxima_parte 		 = 1;
// Senão continua na mesma Parte do Sefip e passa para a Próxima Parte de Ids
} else {
	$proxima_parte_sefip = $parte_sefip;
	$proxima_parte 		 = $parte + 1;
}

print "<h1>SEFIP $meses[$mes]/$ano<br>"; */

//if($parte_sefip == 7) {
	// Inserindo Sefip no Banco de Dados
	mysql_query("INSERT INTO sefip (mes, ano, regiao, projeto, folha, tipo_sefip, data, autor) VALUES ('$mes', '$ano', '', '', '', '1', NOW(), '$_COOKIE[logado]')");
	// Mensagem Final
	print 'Concluido!<br></h1><h2><a class="final" href="arquivos/download.php?file='.$mes.'_'.$ano.'.re">Baixar arquivo</a></h2><hr>';
/*} else {
	// Mensagens
	print '<span class="sefip" title="sefiptexto.php?mes='.$mes.'&ano='.$ano.'&parte='.$proxima_parte.'&parte_sefip='.$proxima_parte_sefip.'">Criando parte ('.$proxima_parte.'/'.$partes_setor.') da linha ('.$proxima_parte_sefip.'/7)</span></h1><h2>Este processo pode demorar alguns minutos</h2>';
} */


?>
</body>
</html>