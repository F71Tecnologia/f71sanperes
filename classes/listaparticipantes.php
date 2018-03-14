<?php
//CLASSE cooperado 04.08.2009
class participantes{

	public function __construct() {
		$user = $_COOKIE['logado'];
	}

	function MostraParticipante($parti){
		
		$RE = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$parti'");
		$Row = mysql_fetch_array($RE);
		
		$this->id_autonomo		= $Row['id_autonomo'];
		$this->id_bolsista		= $Row['id_bolsista'];
		$this->id_projeto		= $Row['id_projeto'];
		$this->id_regiao		= $Row['id_regiao'];
		$this->atividade		= $Row['atividade'];
		$this->salario			= $Row['salario'];
		$this->localpagamento	= $Row['localpagamento'];
		$this->locacao			= $Row['locacao'];
		$this->unidade			= $Row['unidade'];
		$this->nome				= $Row['nome'];
		$this->sexo				= $Row['sexo'];
		$this->endereco			= $Row['endereco'];
		$this->bairro			= $Row['bairro'];
		$this->cidade			= $Row['cidade'];
		$this->uf				= $Row['uf'];
		$this->cep				= $Row['cep'];
		$this->tel_fixo			= $Row['tel_fixo'];
		$this->tel_cel			= $Row['tel_cel'];
		$this->tel_rec			= $Row['tel_rec'];
		$this->data_nasci		= $Row['data_nasci'];
		$this->naturalidade		= $Row['naturalidade'];
		$this->nacionalidade	= $Row['nacionalidade'];
		$this->civil			= $Row['civil'];
		$this->rg				= $Row['rg'];
		$this->orgao			= $Row['orgao'];
		$this->data_rg			= $Row['data_rg'];
		$this->cpf				= $Row['cpf'];
		$this->conselho			= $Row['conselho'];
		$this->titulo			= $Row['titulo'];
		$this->zona				= $Row['zona'];
		$this->secao			= $Row['secao'];
		$this->pai				= $Row['pai'];
		$this->nacionalidade_pai= $Row['nacionalidade_pai'];
		$this->mae				= $Row['mae'];
		$this->nacionalidade_mae= $Row['nacionalidade_mae'];
		$this->estuda			= $Row['estuda'];
		$this->data_escola		= $Row['data_escola'];
		$this->escolaridade		= $Row['escolaridade'];
		$this->instituicao		= $Row['instituicao'];
		$this->curso			= $Row['curso'];
		$this->tipo_contratacao	= $Row['tipo_contratacao'];
		$this->tvsorrindo		= $Row['tvsorrindo'];
		$this->banco			= $Row['banco'];
		$this->agencia			= $Row['agencia'];
		$this->conta			= $Row['conta'];
		$this->tipo_conta		= $Row['tipo_conta'];
		$this->id_curso			= $Row['id_curso'];
		$this->id_psicologia	= $Row['id_psicologia'];
		$this->psicologia		= $Row['psicologia'];
		$this->obs				= $Row['obs'];
		$this->apolice			= $Row['apolice'];
		$this->status			= $Row['status'];
		$this->data_entrada		= $Row['data_entrada'];
		$this->data_saida		= $Row['data_saida'];
		$this->campo1			= $Row['campo1'];
		$this->campo2			= $Row['campo2'];
		$this->campo3			= $Row['campo3'];
		$this->data_exame		= $Row['data_exame'];
		$this->data_exame2		= $Row['data_exame2	'];
		$this->reservista		= $Row['reservista'];
		$this->cabelos			= $Row['cabelos'];
		$this->altura			= $Row['altura'];
		$this->olhos			= $Row['olhos'];
		$this->peso				= $Row['peso'];
		$this->defeito			= $Row['defeito'];
		$this->cipa				= $Row['cipa'];
		$this->ad_noturno		= $Row['ad_noturno'];
		$this->plano			= $Row['plano'];
		$this->assinatura		= $Row['assinatura'];
		$this->distrato			= $Row['distrato'];
		$this->outros			= $Row['outros'];
		$this->pis				= $Row['pis'];
		$this->dada_pis			= $Row['dada_pis'];
		$this->data_ctps		= $Row['data_ctps'];
		$this->serie_ctps		= $Row['serie_ctps'];
		$this->uf_ctps			= $Row['uf_ctps'];
		$this->uf_rg			= $Row['uf_rg'];
		$this->fgts				= $Row['fgts'];
		$this->insalubridade	= $Row['insalubridade'];
		$this->transporte		= $Row['transporte'];
		$this->adicional		= $Row['adicional'];
		$this->terceiro			= $Row['terceiro'];
		$this->num_par			= $Row['num_par'];
		$this->data_ini			= $Row['data_ini'];
		$this->medica			= $Row['medica'];
		$this->tipo_pagamento	= $Row['tipo_pagamento'];
		$this->nome_banco		= $Row['nome_banco'];
		$this->num_filhos		= $Row['num_filhos'];
		$this->nome_filhos		= $Row['nome_filhos'];
		$this->observacao		= $Row['observacao'];
		$this->impressos		= $Row['impressos'];
		$this->campo4			= $Row['campo4'];
		$this->sis_user			= $Row['sis_user'];
		$this->data_cad			= $Row['data_cad'];
		$this->foto				= $Row['foto'];
		$this->id_cooperativa	= $Row['id_cooperativa'];
		$this->c_nome			= $Row['c_nome'];
		$this->c_cpf			= $Row['c_cpf'];
		$this->c_nascimento		= $Row['c_nascimento'];
		$this->c_profissao		= $Row['c_profissao'];
		$this->e_empresa		= $Row['e_empresa'];
		$this->e_cnpj			= $Row['e_cnpj'];
		$this->e_ramo			= $Row['e_ramo'];
		$this->e_endereco		= $Row['e_endereco'];
		$this->e_bairro			= $Row['e_bairro'];
		$this->e_cidade			= $Row['e_cidade'];
		$this->e_estado			= $Row['e_estado'];
		$this->e_cep			= $Row['e_cep'];
		$this->e_tel			= $Row['e_tel'];
		$this->e_ramal			= $Row['e_ramal'];
		$this->e_fax			= $Row['e_fax'];
		$this->e_email			= $Row['e_email'];
		$this->e_tempo			= $Row['e_tempo'];
		$this->e_profissao		= $Row['e_profissao'];
		$this->e_cargo			= $Row['e_cargo'];
		$this->e_renda			= $Row['e_renda'];
		$this->e_dataemissao	= $Row['e_dataemissao'];
		$this->e_referencia		= $Row['e_referencia'];
		$this->r_nome			= $Row['r_nome'];
		$this->r_endereco		= $Row['r_endereco'];
		$this->r_bairro			= $Row['r_bairro'];
		$this->r_cidade			= $Row['r_cidade'];
		$this->r_estado			= $Row['r_estado'];
		$this->r_cep			= $Row['r_cep'];
		$this->r_tel			= $Row['r_tel'];
		$this->r_ramal			= $Row['r_ramal'];
		$this->r_fax			= $Row['r_fax'];
		$this->r_email			= $Row['r_email'];
		$this->dataalter		= $Row['dataalter'];
		$this->useralter		= $Row['useralter'];
		$this->vale				= $Row['vale'];
		$this->senhatv			= $Row['senhatv'];
		$this->documento		= $Row['documento'];
		$this->rh_vale			= $Row['rh_vale'];
		$this->rh_vinculo		= $Row['rh_vinculo'];
		$this->rh_status		= $Row['rh_status'];
		$this->rh_horario		= $Row['rh_horario'];
		$this->rh_sindicato		= $Row['rh_sindicato'];
		$this->rh_cbo			= $Row['rh_cbo'];
		$this->status_reg		= $Row['status_reg'];
	}
	
	function CursoParticipante($parti){
		
		$RE1 = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$parti'");
		$Row1 = mysql_fetch_array($RE1);
		
		$RE = mysql_query("SELECT * FROM curso WHERE id_curso = '$Row1[id_curso]'");
		$Row = mysql_fetch_array($RE);
		
		$this->id_curso			= $Row['0'];
		$this->nome				= $Row['nome'];
		$this->area				= $Row['area'];
		$this->id_regiao		= $Row['id_regiao'];
		$this->local			= $Row['local'];
		$this->inicio			= $Row['inicio'];
		$this->termino			= $Row['termino'];
		$this->descricao		= $Row['descricao'];
		$this->valor			= $Row['valor'];
		$this->parcelas			= $Row['parcelas'];
		$this->campo1			= $Row['campo1'];
		$this->campo2			= $Row['campo2'];
		$this->campo3			= $Row['campo3'];
		$this->cbo_nome			= $Row['cbo_nome'];
		$this->cbo_codigo		= $Row['cbo_codigo'];
		$this->id_horario		= $Row['id_horario'];
		$this->salario			= $Row['salario'];
		$this->ir				= $Row['ir'];
		$this->mes_abono		= $Row['mes_abono'];
		$this->id_user			= $Row['id_user'];
		$this->data_cad			= $Row['data_cad'];
		$this->tipo				= $Row['tipo'];
		$this->hora_semana		= $Row['hora_semana'];
		$this->hora_mes			= $Row['hora_mes'];
		$this->quota			= $Row['quota'];
		$this->num_quota		= $Row['num_quota'];
		$this->status			= $Row['status'];
		$this->status_reg		= $Row['status_reg'];
	}
	
	
	function ParticipantesporUnidadeCheqbox($projeto,$unidade,$status,$campos,$titulos,$contratacao,$alinhamentos){
		include "curso.php";
		$Curso = new tabcurso();
		
//SELECT * FROM autonomo WHERE id_projeto = '10' and locacao = 'CRAS VILA ESPERANÇA - URBANO - Secretaria Municipal de Gov' limit 30
		$RE = mysql_query("SELECT * FROM autonomo WHERE id_projeto = '$projeto' and locacao = '$unidade' and status = '$status' limit 10") or die($unidade);
		
		$numTi = count($titulos);
		$numCa = count($campos);
		
		if($numTi != $numCa){
			echo "Quantidade de campos diferente de Quantidade de Titulos, Impossivel continuar";
			exit;
		}
		
		//COMEÇANDO IMPRESSAO DA TABELA
		
		$retorno = "<table width='97%' align='center' cellspacing='1' class='bordaescura1px'>\n";
		$retorno .= "<tr bgcolor='#CCCCCC'>\n";
		
		for($a=0 ; $a < $numTi ; $a ++){			////IMPRIMINDO AS COLUNAS
			$retorno .= "<th>$titulos[$a]</th>\n";
		}
		$retorno .= "</tr>\n";						// AKI TERMINA DE IMPRIMIR AS COLUNAS
		
		
		//IMPRESSAO DAS LINHAS COM OS DADOS DA CONSULTA
		while($Row = mysql_fetch_array($RE)){
			$retorno .= "<tr>\n";
			for($b=0 ; $b < $numCa ; $b ++){							////IMPRIMINDO AS COLUNAS DOS REGISTROS
				$ago = $campos[$b];
				$alinha = $alinhamentos[$b];
				if($ago == "id_autonomo"){
					$retorno .= "<td align='$alinha' class='mostraregistronormal'><input type='checkbox' value='$Row[$ago]' 
					id='parti[]' name='parti[]'></td>\n";
				}elseif($ago == "id_curso"){
					$Curso -> MostraCurso($Row[$ago]);
					$nomeCurso = $Curso -> nome;
					$retorno .= "<td align='$alinha' class='mostraregistronormal'>$nomeCurso</td>\n";
				}else{
					$retorno .= "<td align='$alinha' class='mostraregistronormal'>$Row[$ago]</td>\n";
				}
			}
			$retorno .= "</tr>\n";
		}
		
		$retorno .= "</table>\n";
		echo $retorno;
		
	}

}
/* ARQUIVOS EXECUTANDO ESTA ROTINA
- ESCALA/AJAX.PHP (ESCALA.PHP)
*/
?>