<?php 
include ("../include/restricoes.php");
include "../../conn.php";
	$log 		= $_REQUEST['log'];
	$id 		= $_REQUEST['id']; 
	$projeto 	= $_POST['projeto'];
	$data_cad 	= 	date("Y-m-d");
	$user_cad 	= 	$_COOKIE['logado'];
	$valor 		=	str_replace(",",".",str_replace(".","",$_POST['valor']));
	$mes		= $_POST['mes'];
	$ano 		= $_POST['ano'];
	
	switch($log){
		case 1: 
			$sql = "INSERT INTO provisao (id_projeto, data_cad_provisao, user_cad_provisao, valor_provisao,  mes_provisao, ano_provisao)
						VALUES 	 		('$projeto', '$data_cad', '$user_cad', '$valor', '$mes', '$ano');";
			$query_insert = mysql_query($sql);
			if($query_insert){
				
				print "<script>
							opener.location.reload();
							window.close();
						</script>";
			}else{
				print "Erro...";
			}
			break;
		
		case 2: 
			$sql = "UPDATE provisao SET valor_provisao = '$valor', mes_provisao = '$mes', ano_provisao = '$ano' WHERE id_provisao = '$id' LIMIT 1;";
			$query_update = mysql_query($sql);
			if($query_update){
				
				print "<script>
							opener.location.reload();
							window.close();
						</script>";
			}else{
				print "Erro...";
			}
			break;
		case 3:
			$regiao = $_GET['regiao'];
			mysql_query("UPDATE provisao SET status_provisao = '0' WHERE id_provisao = '$id'");
			header("Location: ../novofinanceiro.php?regiao=$regiao");
			break;
	}
	
	
	
		
		
	
	


?>