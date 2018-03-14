<?php
include('../include/restricoes.php');

include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_extenso.php');

if(isset($_GET['mudar_valor']) and $_GET['mudar_valor']=1){
	
$valor=str_replace(',','.',str_replace('.','',($_GET['valor'])));
$muda_valor = $_GET['mudar_valor'];
$id_entregue = $_GET['id'];

mysql_query("UPDATE  obrigacoes_entregues SET entregue_realizado='$valor' WHERE entregue_id='$id_entregue' AND entregue_status='1' LIMIT 1");

 $qr_obrigacoes_entregues=mysql_query("SELECT * FROM obrigacoes_entregues WHERE entregue_id='$id_entregue' AND entregue_status='1' ");
			  $row_entregue=mysql_fetch_assoc($qr_obrigacoes_entregues);
			  
			 echo $row_entregue['entregue_realizado'];

}



if(isset($_GET['mudar']) and $_GET['mudar'] == 2){
	
	
		$entregue_id      = $_GET['id'];	
		$total_repasse    = str_replace(',','.',str_replace('.','',($_GET['total_repasse'])));
		$total_rendimento = str_replace(',','.',str_replace('.','',($_GET['total_rendimento'])));
		$total_folha      = str_replace(',','.',str_replace('.','',($_GET['total_folha'])));
		$total_gps		  = str_replace(',','.',str_replace('.','',($_GET['total_gps'])));
		$total_fgts 	  = str_replace(',','.',str_replace('.','',($_GET['total_fgts'])));
		$total_irrf		  = str_replace(',','.',str_replace('.','',($_GET['total_irrf'])));
		$total_pis		  = str_replace(',','.',str_replace('.','',($_GET['total_pis'])));
		$total_provisao   = str_replace(',','.',str_replace('.','',($_GET['total_provisao'])));
		$total_tarifa     = str_replace(',','.',str_replace('.','',($_GET['total_tarifa'])));
		$total_taxa_adm   = str_replace(',','.',str_replace('.','',($_GET['total_taxa_adm'])));
		$total_prestador  = str_replace(',','.',str_replace('.','',($_GET['total_prestador'])));
		
		$update = mysql_query("UPDATE  obrigacoes_entregues SET 
													total_repasse   =  '$total_repasse',
													total_rendimento   =  '$total_rendimento',
													total_folha     =  '$total_folha',
													total_gps       =  '$total_gps',
													total_fgts      =  '$total_fgts',
													total_irrf      =  '$total_irrf',
													total_pis		=  '$total_pis',
													total_provisao  =  '$total_provisao',
													total_tarifa	=  '$total_tarifa',
													total_taxa_adm	=  '$total_taxa_adm',
													total_prestador =  '$total_prestador'
													
													WHERE entregue_id = '$entregue_id' AND entregue_status='1' LIMIT 1")or die(mysql_error());
		
			
			$qr_obrigacoes_entregues = mysql_query("SELECT * FROM obrigacoes_entregues WHERE entregue_id='$entregue_id' AND entregue_status='1' ");
			$row_entregue = mysql_fetch_assoc($qr_obrigacoes_entregues);
			
		if($update) {
			
				echo json_encode(array('erro' => false, 
				'repasse' =>$row_entregue['total_repasse'],
				'rendimento' =>$row_entregue['total_rendimento'],  
				'folha' =>$row_entregue['total_folha'], 
				'gps' =>$row_entregue['total_gps'], 
				'fgts' =>$row_entregue['total_fgts'], 
				'irrf' =>$row_entregue['total_irrf'], 
				'pis' =>$row_entregue['total_pis'], 
				'provisao' =>$row_entregue['total_provisao'], 
				'tarifa' =>$row_entregue['total_tarifa'], 
				'taxa_adm' =>$row_entregue['total_taxa_adm'], 
				'prestador' =>$row_entregue['total_prestador'] ));
			
					 
		} else {
			
			echo json_encode(array('erro' =>true));
		}


}



//////////////	MODELO 4 - MUDAR DATA DE TERMINO  /////////////////////////////////////
if(isset($_GET['muda_valor']) and $_GET['muda_valor']==3){

$termino = implode('-',array_reverse(explode('/',$_GET['termino'])));
$id_entregue = $_GET['id'];


mysql_query("UPDATE  obrigacoes_entregues SET projeto_termino = '$termino' WHERE entregue_id = '$id_entregue' AND entregue_status='1' LIMIT 1") or die(mysql_error());

 $qr_obrigacoes_entregues=mysql_query("SELECT * FROM obrigacoes_entregues WHERE entregue_id='$id_entregue' AND entregue_status='1' ")or die(mysql_error());
			  $row_entregue=mysql_fetch_assoc($qr_obrigacoes_entregues);
			  
			 echo  implode('/',array_reverse(explode('-',$row_entregue['projeto_termino'])));
			 
}
?>