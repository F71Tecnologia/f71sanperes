<?php
//CLASSE projeto 02.08.2009
class projeto{

	function MostraProjeto($projeto){
	
		$RE = mysql_query("SELECT * FROM  projeto WHERE id_projeto = '$projeto'");
		$Row = mysql_fetch_array($RE);
		
		$this->id_projeto		= $Row['id_projeto'];
		$this->id_master 		= $Row['id_master'];
		$this->nome 			= $Row['nome'];
		$this->tema 			= $Row['tema'];
		$this->area 			= $Row['area'];
		$this->local 			= $Row['local'];
		$this->id_regiao 		= $Row['id_regiao'];
		$this->regiao 			= $Row['regiao'];
		$this->inicio 			= $Row['inicio'];
		$this->termino 			= $Row['termino'];
		$this->descricao 		= $Row['descricao'];
		$this->valor_ini 		= $Row['valor_ini'];
		$this->valor_acre 		= $Row['valor_acre'];
		$this->bolsista 		= $Row['bolsista'];
		$this->sis_user 		= $Row['sis_user'];
		$this->status_reg 		= $Row['status_reg'];
		$this->entrega 			= $Row['entrega'];
		$this->data_entrega 	= $Row['data_entrega'];
		$this->trimestral 		= $Row['trimestral'];
		$this->semestral 		= $Row['semestral'];
		$this->data_semestral 	= $Row['data_semestral'];
		$this->data_trimestral 	= $Row['data_trimestral'];
		$this->capacita 		= $Row['capacita'];
		$this->data_capacita 	= $Row['data_capacita'];
		$this->desempenho 		= $Row['desempenho'];
		$this->data_desempenho 	= $Row['data_desempenho'];
		$this->gestores	      	= $Row['gestores'];
		
	}

function SelectProjetos($regiao,$nome,$ajax,$idajax){
	
	if($ajax == 1){
		$acrecenta = "onChange=\"ajaxFunctionlocal($idajax);\"";
	}
	
	$REPro = mysql_query("SELECT * FROM  projeto WHERE id_regiao = '$regiao' and status_reg = '1'");
	$select = "<select name='$nome' id='$nome' $acrecenta>\n";
	$select .= "<option value='0'>- Selecione -</option>\n";
	while ($RowPro = mysql_fetch_array($REPro)){
		$select .= "<option value='$RowPro[0]'>$RowPro[0] - $RowPro[nome]</option>\n";
	}
	$select .= "</select>\n";
	echo $select;
}





}
/* ARQUIVOS EXECUTANDO ESTA ROTINA
- ESCALA.PHP
*/
?>