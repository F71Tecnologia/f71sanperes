<?php 
include "../../../conn.php";



$id_projeto = $_REQUEST['id_projeto'];
$contrato = $_REQUEST['contrato'];

$array_response = array ();


$qr_projeto=mysql_query("SELECT id_projeto,tipo_contrato,inicio, termino, numero_contrato FROM projeto WHERE id_projeto='$id_projeto'");
while($row_projeto=mysql_fetch_assoc($qr_projeto)){
	$tipo = utf8_encode($row_projeto['tipo_contrato']);
	if($contrato==$row_projeto['id_projeto']) {
		$selecionado=1;
	} else {
		$selecionado=0;
	}
	
	$array_response[$tipo][] =   array(
								'selecionado' => $selecionado ,
								'tipo' =>'projeto',
							'id_subprojeto' => $row_projeto['id_projeto'],
							'numero_contrato' => htmlentities($row_projeto['numero_contrato']),
							'inicio' 		=> implode('/',array_reverse(explode('-',$row_projeto['inicio']))),
							'termino' 		=>implode('/',array_reverse(explode('-',$row_projeto['termino']))),
							);
		
}




$qr_subprojeto = mysql_query("SELECT id_subprojeto,numero_contrato,inicio,termino,tipo_subprojeto FROM subprojeto WHERE id_projeto = '$id_projeto' AND status_reg = 1");






while($row_subprojeto = mysql_fetch_assoc($qr_subprojeto)){
	if($contrato==$row_subprojeto['id_subprojeto']) {
		$selecionado=1;
	} else {
		$selecionado=0;
	}
	
	
	
	
	$tipo = utf8_encode($row_subprojeto['tipo_subprojeto']);
	
	$array_response[$tipo][] = array(
	'selecionado' => $selecionado ,
								'tipo' =>'subprojeto',
							'id_subprojeto' => $row_subprojeto['id_subprojeto'],
							'numero_contrato' => htmlentities($row_subprojeto['numero_contrato']),
							'inicio' 		=> implode('/',array_reverse(explode('-',$row_subprojeto['inicio']))),
							'termino' 		=> implode('/',array_reverse(explode('-',$row_subprojeto['termino']))),
							);
}


echo json_encode($array_response);
?>