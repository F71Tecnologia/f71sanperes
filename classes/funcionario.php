<?php
//CLASSE funcionario 30.07.2009
class funcionario{

public function __construct() {
	$id_user = $_COOKIE['logado'];
	
	$r = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
	$row_user = mysql_fetch_array($r);
	
	$this->id_user		= $row_user['id_funcionario'];
	$this->id_regiao	= $row_user['id_regiao'];
	$this->regiao		= $row_user['regiao'];
	$this->id_master	= $row_user['id_master'];
}

	
	function MostraUser($M_user = 0){
		if($M_user == 0){
			$M_user = $this->id_user;
		}
		
		$REuser = mysql_query("SELECT * FROM funcionario where id_funcionario = '$M_user'");
		$RowUser = mysql_fetch_array($REuser);
		
		$this->id_funcionario	= $RowUser['id_funcionario'];
		$this->id_master		= $RowUser['id_master'];
		$this->tipo_usuario		= $RowUser['tipo_usuario'];
		$this->grupo_usuario	= $RowUser['grupo_usuario'];
		$this->nome				= $RowUser['nome'];
		$this->salario			= $RowUser['salario'];
		$this->id_regiao		= $RowUser['id_regiao'];
		$this->regiao			= $RowUser['regiao'];
		$this->funcao			= $RowUser['funcao'];
		$this->locacao			= $RowUser['locacao'];
		$this->endereco			= $RowUser['endereco'];
		$this->bairro			= $RowUser['bairro'];
		$this->cidade			= $RowUser['cidade'];
		$this->uf				= $RowUser['uf'];
		$this->cep				= $RowUser['cep'];
		$this->tel_fixo			= $RowUser['tel_fixo'];
		$this->tel_cel			= $RowUser['tel_cel'];
		$this->tel_rec			= $RowUser['tel_rec'];
		$this->data_nasci		= $RowUser['data_nasci'];
		$this->naturalidade		= $RowUser['naturalidade'];
		$this->nacionalidade	= $RowUser['nacionalidade'];
		$this->civil			= $RowUser['civil'];
		$this->ctps				= $RowUser['ctps'];
		$this->serie_ctps		= $RowUser['serie_ctps'];
		$this->uf_ctps			= $RowUser['uf_ctps'];
		$this->pis				= $RowUser['pis'];
		$this->rg				= $RowUser['rg'];
		$this->orgao			= $RowUser['orgao'];
		$this->data_rg			= $RowUser['data_rg'];
		$this->cpf				= $RowUser['cpf'];
		$this->titulo			= $RowUser['titulo'];
		$this->zona				= $RowUser['zona'];
		$this->secao			= $RowUser['secao'];
		$this->pai				= $RowUser['pai'];
		$this->mae				= $RowUser['mae'];
		$this->estuda			= $RowUser['estuda'];
		$this->data_escola		= $RowUser['data_escola'];
		$this->escolaridade		= $RowUser['escolaridade'];
		$this->instituicao		= $RowUser['instituicao'];
		$this->curso			= $RowUser['curso'];
		$this->foto				= $RowUser['foto'];
		$this->banco			= $RowUser['banco'];
		$this->agencia			= $RowUser['agencia'];
		$this->conta			= $RowUser['conta'];
		$this->login			= $RowUser['login'];
		$this->senha			= $RowUser['senha'];
		$this->alt_senha		= $RowUser['alt_senha'];
		$this->lisenca			= $RowUser['lisenca'];
		$this->exclusaostatus_reg = $RowUser['exclusaostatus_reg'];
		$this->user_cad			= $RowUser['user_cad'];
		$this->data_cad			= $RowUser['data_cad'];
		$this->nome1			= $RowUser['nome1'];
	}
	
	
	function MostraMaster($Master){
		if(empty($Master) or $Master == 0){
			$Master = $this->id_master;
		}
		
		$REuser = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
		$RowUser = mysql_fetch_array($REuser);
		
		$this->id_master		= $RowUser['id_master'];
		$this->razao			= $RowUser['razao'];
		$this->nome				= $RowUser['nome'];
		$this->cnpj				= $RowUser['cnpj'];
		$this->endereco			= $RowUser['endereco'];
		$this->email			= $RowUser['email'];
		$this->telefone			= $RowUser['telefone'];
		$this->responsavel		= $RowUser['responsavel'];
		$this->cpf				= $RowUser['cpf'];
		$this->rg				= $RowUser['rg'];
		$this->civil			= $RowUser['civil'];
		$this->nacionalidade	= $RowUser['nacionalidade'];
		$this->formacao			= $RowUser['formacao'];
		$this->logo				= $RowUser['logo'];
		
	}
	
	
	
	
	

	function ConverteData($Data){
		 if (strstr($Data, "/")){//verifica se tem a barra /
			$d = explode ("/", $Data);//tira a barra
			$rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
			return $rstData;
		 } elseif(strstr($Data, "-")){
		 	$d = explode ("-", $Data);
		 	$rstData = "$d[2]/$d[1]/$d[0]"; 
		 	return $rstData;
		 }else{
		 	return "0";
		 }
	}
	//$data_rg = ConverteData($data_rg);

	function preenche_select_nome($id_funcionario,$master = NULL, $regiao = NULL){
		 
		
	
	 $REFunc = mysql_query("SELECT *,UPPER(funcionario.nome1) as nome1
							FROM funcionario
							INNER JOIN funcionario_master
							ON  funcionario_master.id_funcionario = funcionario.id_funcionario
							WHERE funcionario.status_reg = '1' 
							AND  funcionario_master.id_master = '$master'	
							GROUP BY funcionario.id_funcionario
							ORDER BY funcionario.nome1");
							
					  print "<option>Selecione</option>";
					  
					  while($RowFunc = mysql_fetch_assoc($REFunc)){
						  
						  
						print "<option value='$RowFunc[id_funcionario]' >$RowFunc[nome1]</option>";
						
					  }	
		
		
		
	}








}
/* ARQUIVOS EXECUTANDO ESTA ROTINA
- ESCALA.PHP
- PONTO.PHP
- login_adm.php
*/
?>