<?php
include ("../include/restricoes.php");
include('../../conn.php');
include('../../classes_permissoes/regioes.class.php');

///////// PEGANDO AS REGIÕES
if(isset($_GET['master'])and !empty($_GET['master'])){
	
$master_id = mysql_real_escape_string($_GET['master']);
$status_regiao = array(1=>'REGIÕES ATIVAS', 2=> 'REGIÕES INATIVAS');

echo '<option>Selecione a região...</option>';

foreach($status_regiao as $status => $tipo) {
		
		if($status == 1) {
			$qr_regioes = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE id_master = '$master_id' AND status= 1 AND status_reg=1 ORDER BY regiao") or die(mysql_error());
			 echo '<optgroup label="REGIÕES ATIVAS"></optgroup>';
		} else {
			$qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_master = '$master_id'  AND (status= 0 OR status_reg=0) ORDER BY regiao");
			echo '<optgroup label="REGIÕES INATIVAS"></optgroup>';
		}
	
	while($row_regiao = mysql_fetch_assoc($qr_regioes)):
	
	echo '<option value="'.$row_regiao['id_regiao'].'">'.$row_regiao['id_regiao'].' - '.htmlentities($row_regiao['regiao']).'</option>';
	
	endwhile;
}
}


/////////////// PEGANDO OS PROJETOS
if(isset($_GET['regiao'])){
	
$regiao_id =  mysql_real_escape_string($_GET['regiao']);
$status_projeto = array(1=>'PROJETOS ATIVOS', 2=>'PROJETOS INATIVOS');

$data['projeto']  .= '<option>Selecione o projeto...</option>';
$data['curso']    .= '<option>Selecione o curso...</option>';

foreach($status_projeto as $status => $nome_status){
	
	 $data['projeto']  .= '<optgroup label="'.$nome_status.'"></optgroup>';
	 
	 $qr_projeto = mysql_query("SELECT nome,id_projeto FROM projeto WHERE id_regiao = '$regiao_id' AND status_reg= '$status' ORDER BY nome");
	 while($row_projeto = mysql_fetch_assoc($qr_projeto)): 
	 
     	 $data['projeto'] .= ' <option value="'.$row_projeto['id_projeto'].'">'.$row_projeto['id_projeto'].' - '.htmlentities($row_projeto['nome']).'</option>';
	 
	 endwhile;
}


///////PEGANDO CURSOS
$qr_curso = mysql_query("SELECT  id_curso,campo2 FROM curso WHERE id_regiao = '$regiao_id'");
while($row_curso= mysql_fetch_assoc($qr_curso)):
	
	$data['curso'] .= ' <option value="'.$row_curso['id_curso'].'">'.htmlentities($row_curso['campo2']).'</option>';
	
endwhile;


echo json_encode($data);
	
}












?>