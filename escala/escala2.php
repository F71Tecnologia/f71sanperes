<?php
/*
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}
*/

include "../conn.php";
include "../classes/insert.php";

$escala = $_REQUEST['escala'];
$mes = $_REQUEST['mes'];
$regiao = $_REQUEST['regiao'];

$campos_reservados[] = 'Enviar';
$campos_reservados[] = 'escala';
$campos_reservados[] = 'mes';
$campos_reservados[] = 'regiao';
$campos_reservados[] = 'id';
	
$conteudo = new insert();
$conteudo -> campos_insert($HTTP_POST_VARS,$campos_reservados);
	
$Campos = $conteudo -> campos;
$Valores = $conteudo -> valores;
	
//RESOLVENDO O PROBLEMA COM A ULTIMA VIRGULA
$n_camp = strlen($Campos);						//CONTANDO A QUANTIDADE DE CARACTERS
$n_camp = $n_camp - 1;							//DIMINUINDO CARACTERS POR 4 PARA REMOVER A VIRGULA
$Campos = str_split($Campos, $n_camp);		    //EXPLODINDO D VARIAVEL, JA SEM A VIRGULA
	
//RESOLVENDO O PROBLEMA COM A ULTIMA VIRGULA
$n_val = strlen($Valores);						//CONTANDO A QUANTIDADE DE CARACTERS
$n_val = $n_val - 1;							//DIMINUINDO CARACTERS POR 4 PARA REMOVER A VIRGULA
$Valores = str_split($Valores, $n_val);		    //EXPLODINDO D VARIAVEL, JA SEM A VIRGULA

$Query = "INSERT INTO escala_proc (id_escala,mes,$Campos[0]) values ('$escala','$mes',$Valores[0])";

mysql_query($Query) or die ("Erro no Insert <br><br>".mysql_error());
	
print "
<script> 
alert(\"Dados gravados com êxito!\");
location.href = \"escala.php?id=1&id_reg=$regiao\";
</script>";

?>
