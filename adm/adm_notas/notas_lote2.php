<?php 
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');




$nota = $_GET['id'];

$id_projeto = '3228';

$qr_notas  = mysql_query("SELECT *, YEAR(data_emissao) as ano, MONTH(data_emissao) as mes FROM notas WHERE id_projeto = '$id_projeto' order by data_emissao ASC") or die(mysql_error());

if(!file_exists('teste/'.$id_projeto)){

mkdir('teste/'.$id_projeto, 0755);


}
$dir = 'teste/'.$id_projeto;

?>
<html>
<head>
<title>Administra&ccedil;&atilde;o de Notas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.min.js" ></script>
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js" ></script>
<link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
<link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript" src="../../jquery/priceFormat.js" ></script>



</head>
<body>
<div id="corpo">
   
    <div id="conteudo">   
     <?php
	 
     while($row_notas = mysql_fetch_assoc($qr_notas)):
	 
	 
	  copy('include/index.html',$pasta_ano.'/index.html');
	 
	 	 $pasta_ano = $dir.'/'.$row_notas['ano'];
		$mes 	  = sprintf('%02s', $row_notas['mes']);
			$row_mes = mysql_result(mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$mes'"),0);	
			
			$pasta_mes = $dir.'/'.$row_notas['ano'].'/'.$row_mes;
		 
		 
		 if($row_notas['ano'] != $ano_anterior){
		 
				 if(!file_exists($pasta_ano)){	mkdir($pasta_ano, 0755); }
		 
		 }
	 
	 
	  if($row_notas['mes'] != $mes_anterior){		 
			 copy('include/index.html',$pasta_mes.'/index.html');
			 
				 if(!file_exists($pasta_mes)){
					 
					mkdir( $pasta_mes, 0755);	
					
					
						 
					 $qr_anexo = mysql_query("SELECT * FROM notas_files WHERE id_notas = '$row_notas[id_notas]' AND  status = 1") or die(mysql_error());
				  	 while($row_anexo = mysql_fetch_assoc($qr_anexo)):
				  	 
					 
						if(!file_exists($pasta_mes.'/'.$row_anexo['id_file'].'.'.$row_anexo['tipo'])){
					 		
								copy('notas/'.$row_anexo['id_file'].'.'.$row_anexo['tipo'],$pasta_mes.'/'.$row_anexo['id_file'].'.'.$row_anexo['tipo']);
					 
						}
						  
					 endwhile;		  
						  	
				  }
				  
				  
		 }
	 
	 
	 
	  
	  	 
		 
		 
		 
	  		 
	  	 
	 
	 $ano_anterior = $row_notas['ano'];
	 $mes_anterior = $row_notas['mes'];
	 endwhile;
	 
	 
	 echo "<a href='teste/$id_projeto'>BAIXAR</a>";
	 ?>
  </div>
    <div id="rodape">
        <?php include('../include/rodape.php'); ?>
    </div>
</div>
</body>
</html>