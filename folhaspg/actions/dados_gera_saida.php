<?php
include('../../adm/include/restricoes.php');
include('../../conn.php');
include('../../classes_permissoes/regioes.class.php');


if(isset($_GET['regiao'])){


echo '<option value="">Selecione o projeto....</option> ';

$id_regiao = mysql_real_escape_string($_GET['regiao']);

$qr_projeto = mysql_query("SELECT * FROM projeto WHERe id_regiao = $id_regiao");
while($row_projeto = mysql_fetch_assoc($qr_projeto)):
	
	echo '<option value="'.$row_projeto['id_projeto'].'">'.$row_projeto['id_projeto'].' - '.htmlentities(utf8_encode($row_projeto['nome'])).'</option> ';
endwhile;




}




if(isset($_GET['projeto'])){


echo '<option value="">Selecione o banco....</option>';

$id_projeto = mysql_real_escape_string($_GET['projeto']);

$qr_banco = mysql_query("SELECT * FROM bancos WHERe id_projeto = $id_projeto AND status_reg = 1");
while($row_banco = mysql_fetch_assoc($qr_banco)):
	
	echo '<option value="'.$row_banco['id_banco'].'">'.htmlentities(utf8_encode($row_banco['nome'])).' AG: '.$row_banco['agencia'].' C: '.$row_banco['conta'].'</option>';
endwhile;




}