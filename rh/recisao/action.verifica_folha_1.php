<?php
if(empty($_COOKIE['logado'])) {
	print "<script>location.href = '../login.php?entre=true';</script>";
	exit;
}

include('../../conn.php');
include('../../funcoes.php');


if(isset($_GET['regiao'])){
	
$regiao 		 = $_GET['regiao'];
$data_rescisao   = explode('/', $_GET['data']);
$projeto 		 = $_GET['projeto'];
$data_rescisao   = mktime(0,0,0,$data_rescisao[1], $data_rescisao[0], $data_rescisao[2]);


if($projeto == 'todos') {
		$qr_folha = mysql_query("SELECT data_fim FROM rh_folha WHERE regiao = '$regiao' ORDER BY data_fim DESC") or die(mysql_error());
} else {
		$qr_folha = mysql_query("SELECT data_fim FROM rh_folha WHERE regiao = '$regiao'  AND projeto = '$projeto' ORDER BY data_fim DESC") or die(mysql_error());
}


if(mysql_num_rows($qr_folha) !=0) {

		$data_folha =  mysql_result($qr_folha, 0);
		
		$data_fim = explode('-',$data_folha);
		$data_fim = mktime(0,0,0,$data_fim[1],$data_fim[2], $data_fim[0]);
		
		 
		$diferenca = $data_rescisao - $data_fim;
		$diferenca = ($diferenca / 86400) ;
		
		if(($diferenca>31) or ($diferenca <=0)) {
		
			$json['verifica'] = '0';
			$json['data_ult_folha'] = implode('/',array_reverse(explode('-',$data_folha)));
			
		} else {
		
			$json['verifica'] = '1';
		
		}
} else {
	
$json['verifica'] = 2; ///não exite folha para o projeto
	
}

echo json_encode($json);




	
}

?>