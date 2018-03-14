<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../conn.php";


$regiao = $_REQUEST["regiao"];
$projeto = $_REQUEST["projeto"];
$banco = $_REQUEST["banco"];
$id_user = $_COOKIE['logado'];
$nome = $_REQUEST["nome"];
$especificacao = $_REQUEST["especificacao"];
$valor_parcela = $_REQUEST["valor_parcela"];
$tipo = "66";
$id_compra = $_REQUEST["compra"];




$data_pg = $_REQUEST["data_pg"];
$n_parcelas = $_REQUEST["n_parcelas"];


//print_r($_REQUEST);



$i= 0;

while ($i < $n_parcelas){
	
$data = substr($data_pg[$i] , 6 , 4)."-".substr($data_pg[$i] , 3 , 2)."-".substr($data_pg[$i] , 0 , 2); 
	
	
mysql_query("INSERT INTO saida_autorizacao(id_regiao,id_projeto,id_banco,id_user,nome,especifica,tipo,valor,data_proc,data_vencimento) values 
('$regiao','$projeto','$banco','$id_user','$nome','$especificacao','$tipo','$valor_parcela[$i]',NOW(),'$data' )") or die ("O servidor não respondeu conforme deveria, tente novamente mais tarde, Obrigado!<br><hr>".mysql_error());

$i += 1;

$id_saida = mysql_insert_id();

//mysql_query("INSERT INTO compra_saida_assoc VALUES ('$id_compra','$id_saida')")or die ("O servidor não respondeu conforme deveria, tente novamente mais tarde, Obrigado!<br><hr>".mysql_error());

}

mysql_query("UPDATE compra2 SET acompanhamento=10 WHERE id_compra=$id_compra");




$link = "../gestaocompras2.php"; 
echo "<script>location.href='".$link."';</script>";

}


?>