<?php
//CLASSE funcionario 30.07.2009
class clt{

public function __construct() {
	$id_user = $_COOKIE['logado'];
	
	$r = mysql_query("SELECT *,date_format(data_nasci, '%d/%m/%Y') as data_nasci2 FROM funcionario where id_funcionario = '$id_user'");
	$row_user = mysql_fetch_array($r);
	
	$this->id_user= $row_user['id_funcionario'];
	$this->id_regiao= $row_user['id_regiao'];
	$this->regiao= $row_user['regiao'];
}

	function MostraClt($M_user){
				
		$REuser = mysql_query("SELECT *,date_format(data_nasci, '%d/%m/%Y')as data_nasci2, date_format(data_entrada, '%d/%m/%Y')as data_entrada2, 
		date_format(data_demi, '%d/%m/%Y')as data_demi2,
                DATE_ADD(data_entrada, INTERVAL 44 DAY) as periodo_45_dias 

                FROM rh_clt where id_clt = '$M_user'");
		$RowUser = mysql_fetch_array($REuser);
		
		$this->id_clt  				= $RowUser['id_clt'];  
		$this->id_antigo 			= $RowUser['id_antigo']; 		
		$this->id_projeto 			= $RowUser['id_projeto']; 		
		$this->id_regiao 			= $RowUser['id_regiao']; 		
		$this->atividade 			= $RowUser['atividade']; 		
		$this->salario 				= $RowUser['salario']; 			
		$this->localpagamento 		= $RowUser['localpagamento']; 		
		$this->locacao 				= $RowUser['locacao']; 			
		$this->unidade 				= $RowUser['unidade']; 			
		$this->nome					= $RowUser['nome'];			
		$this->sexo					= $RowUser['sexo'];			
		$this->endereco 			= $RowUser['endereco']; 		
		$this->complemento 			= $RowUser['complemento']; 		
		$this->numero 			= $RowUser['numero']; 		
		$this->bairro				= $RowUser['bairro'];			
		$this->cidade				= $RowUser['cidade'];			
		$this->uf 					= $RowUser['uf']; 			
		$this->cep					= $RowUser['cep'];			
		$this->tel_fixo 			= $RowUser['tel_fixo']; 		
		$this->tel_cel 				= $RowUser['tel_cel']; 			
		$this->tel_rec 				= $RowUser['tel_rec']; 			
		$this->data_nasci 			= $RowUser['data_nasci'];
		$this->data_nasci2 			= $RowUser['data_nasci2'];
		$this->naturalidade 		= $RowUser['naturalidade']; 		
		$this->nacionalidade 		= $RowUser['nacionalidade']; 		
		$this->civil				= $RowUser['civil'];			
		$this->rg 					= $RowUser['rg']; 			
		$this->orgao 				= $RowUser['orgao']; 			
		$this->data_rg 				= $RowUser['data_rg']; 			
		$this->cpf 					= $RowUser['cpf']; 			
		$this->titulo 				= $RowUser['titulo']; 			
		$this->zona 				= $RowUser['zona']; 			
		$this->secao 				= $RowUser['secao']; 			
		$this->pai 					= $RowUser['pai'];			
		$this->nacionalidade_pai	= $RowUser['nacionalidade_pai'];	
		$this->mae 					= $RowUser['mae']; 			
		$this->nacionalidade_mae	= $RowUser['nacionalidade_mae'];	
		$this->estuda 				= $RowUser['estuda']; 			
		$this->data_escola 			= $RowUser['data_escola']; 		
		$this->escolaridade 		= $RowUser['escolaridade']; 		
		$this->instituicao 			= $RowUser['instituicao']; 		
		$this->curso 				= $RowUser['curso']; 			
		$this->tipo_contratacao		= $RowUser['tipo_contratacao'];		
		$this->tvsorrindo 			= $RowUser['tvsorrindo']; 		
		$this->banco 				= $RowUser['banco']; 			
		$this->agencia 				= $RowUser['agencia']; 			
		$this->conta 				= $RowUser['conta']; 			
		$this->tipo_conta 			= $RowUser['tipo_conta']; 		
		$this->id_curso 			= $RowUser['id_curso']; 		
		$this->id_psicologia 		= $RowUser['id_psicologia']; 		
		$this->psicologia 			= $RowUser['psicologia'];		
		$this->obs	 				= $RowUser['obs'];	 		
		$this->apolice 				= $RowUser['apolice']; 			
		$this->status 				= $RowUser['status']; 			
		$this->data_entrada 		= $RowUser['data_entrada']; 		
		$this->data_entrada2 		= $RowUser['data_entrada2']; 		
		$this->data_saida 			= $RowUser['data_saida']; 		
		$this->campo1 				= $RowUser['campo1']; 			
		$this->campo2 				= $RowUser['campo2']; 			
		$this->campo3 				= $RowUser['campo3']; 			
		$this->data_exame 			= $RowUser['data_exame']; 		
		$this->data_exame2 			= $RowUser['data_exame2']; 		
		$this->reservista 			= $RowUser['reservista']; 		
		$this->cabelos 				= $RowUser['cabelos']; 			
		$this->altura 				= $RowUser['altura']; 			
		$this->olhos 				= $RowUser['olhos']; 			
		$this->peso 				= $RowUser['peso']; 			
		$this->defeito 				= $RowUser['defeito']; 			
		$this->cipa 				= $RowUser['cipa']; 			
		$this->ad_noturno 			= $RowUser['ad_noturno']; 		
		$this->plano 				= $RowUser['plano']; 			
		$this->assinatura 			= $RowUser['assinatura']; 		
		$this->distrato 			= $RowUser['distrato']; 		
		$this->outros 				= $RowUser['outros']; 			
		$this->pis 					= $RowUser['pis']; 			
		$this->dada_pis 			= $RowUser['dada_pis']; 		
		$this->data_ctps 			= $RowUser['data_ctps']; 		
		$this->serie_ctps 			= $RowUser['serie_ctps'];		
		$this->uf_ctps 				= $RowUser['uf_ctps']; 			
		$this->uf_rg 				= $RowUser['uf_rg']; 			
		$this->fgts 				= $RowUser['fgts']; 			
		$this->insalubridade 		= $RowUser['insalubridade']; 		
		$this->transporte 			= $RowUser['transporte']; 		
		$this->adicional 			= $RowUser['adicional']; 		
		$this->terceiro 			= $RowUser['terceiro']; 		
		$this->num_par 				= $RowUser['num_par']; 			
		$this->data_ini 			= $RowUser['data_ini']; 		
		$this->medica 				= $RowUser['medica']; 			
		$this->tipo_pagamento 		= $RowUser['tipo_pagamento']; 		
		$this->nome_banco 			= $RowUser['nome_banco']; 		
		$this->num_filhos 			= $RowUser['num_filhos']; 		
		$this->nome_filhos 			= $RowUser['nome_filhos']; 		
		$this->observacao 			= $RowUser['observacao']; 		
		$this->impressos 			= $RowUser['impressos']; 		
		$this->campo4 				= $RowUser['campo4']; 			
		$this->sis_user				= $RowUser['sis_user'];			
		$this->data_cad				= $RowUser['data_cad'];			
		$this->foto 				= $RowUser['foto']; 			
		$this->dataalter 			= $RowUser['dataalter']; 		
		$this->useralter 			= $RowUser['useralter']; 		
		$this->vale 	 			= $RowUser['vale']; 	 		
		$this->documento 			= $RowUser['documento']; 		
		$this->rh_vale 				= $RowUser['rh_vale']; 			
		$this->rh_vinculo 			= $RowUser['rh_vinculo']; 		
		$this->rh_status 			= $RowUser['rh_status']; 		
		$this->rh_horario 			= $RowUser['rh_horario'];		
		$this->rh_sindicato 		= $RowUser['rh_sindicato']; 		
		$this->rh_cbo 				= $RowUser['rh_cbo'];
		$this->data_aviso 			= $RowUser['data_aviso'];
		$this->data_demi 			= $RowUser['data_demi'];
		$this->data_demi2 			= $RowUser['data_demi2'];
		$this->status_reg			= $RowUser['status_reg'];
		$this->periodo_45_dias			= $RowUser['periodo_45_dias'];
                $this->tipo_contrato                    = $RowUser['tipo_contrato'];
	}


	function EmpresadoCLT($M_user){
				
		$REuser = mysql_query("SELECT * FROM rh_clt where id_clt = '$M_user'");
		$RowUser = mysql_fetch_array($REuser);
		
		$REEmp = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$RowUser[id_regiao]'");
		$Rowemp = mysql_fetch_array($REEmp);
		
		$this->id_empresa			= $Rowemp['0'];
		$this->id_regiao			= $Rowemp['id_regiao'];
		$this->id_user_cad			= $Rowemp['id_user_cad'];
		$this->data_cad				= $Rowemp['data_cad'];
		$this->nome					= $Rowemp['nome'];
		$this->razao				= $Rowemp['razao'];
		$this->endereco				= $Rowemp['endereco'];
		$this->im					= $Rowemp['im'];
		$this->ie					= $Rowemp['ie'];
		$this->cnpj					= $Rowemp['cnpj'];
		$this->tipo_cnpj			= $Rowemp['tipo_cnpj'];
		$this->tel					= $Rowemp['tel'];
		$this->fax					= $Rowemp['fax'];
		$this->email				= $Rowemp['email'];
		$this->site					= $Rowemp['site'];
		$this->responsavel			= $Rowemp['responsavel'];
		$this->cpf					= $Rowemp['cpf'];
		$this->acid_trabalho		= $Rowemp['acid_trabalho'];
		$this->atividade			= $Rowemp['atividade'];
		$this->grupo				= $Rowemp['grupo'];
		$this->proprietarios		= $Rowemp['proprietarios'];
		$this->familiares			= $Rowemp['familiares'];
		$this->tipo_pg				= $Rowemp['tipo_pg'];
		$this->municipio			= $Rowemp['municipio'];
		$this->ano					= $Rowemp['ano'];
		$this->logo					= $Rowemp['logo'];
		$this->cnpj_matriz			= $Rowemp['cnpj_matriz'];
		$this->banco				= $Rowemp['banco'];
		$this->agencia				= $Rowemp['agencia'];
		$this->conta				= $Rowemp['conta'];
		$this->cep					= $Rowemp['cep'];
		
		#OUTROS
		
	}
}
/* ARQUIVOS EXECUTANDO ESTA ROTINA
- ESCALA.PHP
- PONTO.PHP
- login_adm.php
*/
?>