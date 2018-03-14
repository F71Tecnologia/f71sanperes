<?php
//CLASSE regiao 30.07.2009
class regiao{

public function __construct() {
	$id_user = $_COOKIE['logado'];
	
	$r = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
	$row_user = mysql_fetch_array($r);
	
	$this->id_userlocado= $row_user['id_master'];
	$this->regiaologado= $row_user['regiao'];
	$this->id_regiaologado= $row_user['id_regiao'];
	
}

function MostraRegiao($regiao){
		
		$RE = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
		$Row = mysql_fetch_array($RE);
		
		$this->id_regiao		= $Row['id_regiao'];
		$this->id_master		= $Row['id_master'];
		$this->regiao			= $Row['regiao'];
		$this->sigla			= $Row['sigla'];
		$this->criador			= $Row['criador'];
		$this->status			= $Row['status'];

}

function SelectRegioes(){
	
	//CRIANDO UM ARRAY COM TODAS AS REGIÕES COM SEUS RESPECTIVOS ID's
	$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao' and id_master = '$master'");
	$row_local = mysql_fetch_array($result_local);
	
	$REReg = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE status = 1 and id_master = '$master'");
	while ($row_regiao = mysql_fetch_array($REReg)){
		$idReg = $row_regiao['0'];
		$REGIOES[$idReg] = $row_regiao['1'];
	}

}

function SelectMaster($nome,$outros,$user){
	
	//SELECIONANDO O MASTER
	$REMaster = mysql_query("SELECT * FROM master WHERE status = '1'");
	
	//PEGANDO O USUÁRIO
	include "funcionario.php";
	$fun = new funcionario();
	$fun -> MostraUser($user);
	
	$MasterLog = $fun -> id_master;
	
	$retorno = "<select name='$nome' id='$nome' $outros>\n";
	while($RowMas = mysql_fetch_array($REMaster)){
		if($MasterLog == $RowMas['0']){
			$retorno .= "<option value='$RowMas[0]' selected>$RowMas[0] - $RowMas[nome]</option>\n";
		}else{
			$retorno .= "<option value='$RowMas[0]'>$RowMas[0] - $RowMas[nome]</option>\n";
		}
	}
	$retorno .= "</select>\n";
	
	echo $retorno;
}


function MostraMes($mes){
	
	$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
	$MesInt = (int)$mes;
	return $meses[$MesInt];

}

function MostraDataCompleta($data){		// DIA, MES, ANO
	if(strstr($data, "/")){
		$d = explode ("/", $data);
		$dia = $d[0];
		$mes = $d[1];
		$ano = $d[2];
	}elseif(strstr($data, "-")){
		$d = explode ("-", $data);
		$dia = $d[2];
		$mes = $d[1];
		$ano = $d[0];
	}
	
	
	
	$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
	$MesInt = (int)$mes;
	$mesNome = $meses[$MesInt];
	
	return $dia." de ".$mesNome." de ".$ano;
	
}

function RegiaoLogado(){
	
	$id_regiao = $this->id_regiaologado;
	$result_local = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_regiao'");
	$row_local = mysql_fetch_array($result_local);
	
	return $row_local['regiao'];
}

function DadosRegiaoLogado(){
	
	$id_regiao = $this->id_regiaologado;
	$result_local = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_regiao'");
	$row_local = mysql_fetch_array($result_local);
	
	$this->id_regiao		= $row_local['id_regiao'];
	$this->id_master		= $row_local['id_master'];
	$this->regiao			= $row_local['regiao'];
	$this->sigla			= $row_local['sigla'];
	$this->criador			= $row_local['criador'];
	$this->status			= $row_local['status'];
	
	/*CÓDIGO PARAR POR NA SUA PÁGINA
	$regiao = new regiao();
	$regiao -> DadosRegiaoLogado();

	$id_regiao = $regiao -> id_regiao;*/
	
}

function EmpresaRegiaoLogado(){
	
	$id_regiao = $this->id_regiaologado;
	$result_local = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$id_regiao'");
	$row_local = mysql_fetch_array($result_local);
	
	$this->razao			= $row_local['razao'];
	$this->nome				= $row_local['nome'];
	$this->endereco			= $row_local['endereco'];
	$this->cnpj				= $row_local['cnpj'];

}

function SelectUFajax($campo,$complemento){
	
	$retorno = '<select name="'.$campo.'" id="'.$campo.'" '.$complemento.'>'."\n";
	$retorno .= '<option value="">Selecione</option>'."\n";
	$qr_estados = mysql_query("SELECT DISTINCT(estado), sigla FROM municipios ORDER BY estado");   
    while ($estado = mysql_fetch_assoc($qr_estados)) {
		 $retorno .= '<option value="'.$estado['sigla'].'">'.$estado['estado'].'</option>'."\n";
	}
	
	$retorno .= '</select>'."\n";
	
	echo $retorno;

}

/**
 * 
 * @param type $master
 */
public function getRegioesByMaster($master){
    
    $array = array();
    
    $qry = "SELECT id_regiao, regiao FROM regioes WHERE status = 1 and id_master = '$master'";
    $sql = mysql_query($qry) or die('Erro ao selecionar regiões por master');
    while($rows = mysql_fetch_assoc($sql)){
        $array[$rows['id_regiao']] = $rows['regiao'];
    }
    
    return $array;
}
/**
 * Funcao para pegar as regioes ativas do projeto
 */
public function getRegioesAtivas($Master){
    $array = array();
    
    $qry = "SELECT regioes.id_regiao, regioes.regiao,regioes.status FROM parceiros 
            INNER JOIN regioes ON parceiros.id_regiao = regioes.id_regiao 
            AND parceiros.parceiro_status='1'
            INNER JOIN funcionario_regiao_assoc 
            ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao 
            WHERE regioes.id_master = '$Master'
            AND regioes.status='1' AND  regioes.status_reg ='1'
            AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]' 
            GROUP BY regioes.id_regiao 
            ORDER BY regioes.id_regiao";
    $sql = mysql_query($qry) or die('Erro ao selecionar regiões ativas por master');
    while($rows = mysql_fetch_assoc($sql)){
        $array[$rows['id_regiao']] = $rows['regiao'];
    }
    
    return $array;
    
}

/**
 * Funcao para pegar os parceiros das regioes com o Id selecionado
 */

public function getParceiros($id_regiao)
{
    $array = array();
    
    $qry = "SELECT * FROM parceiros WHERE id_regiao = '$id_regiao'";
    $sql = mysql_query($qry) or die('Erro ao selecionar parceiro selecionado ');
    while($rows = mysql_fetch_assoc($sql)){
        $array[$rows['parceiro_id']] = $rows['parceiro_nome'];
    }
    
    return $array;
    
}

public function getRegioesInativas($Master){
    $array = array();
    
    $qry = "SELECT regioes.id_regiao, regioes.regiao,regioes.status FROM parceiros 
            INNER JOIN regioes ON parceiros.id_regiao = regioes.id_regiao 
            AND parceiros.parceiro_status='1'
            INNER JOIN funcionario_regiao_assoc 
            ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao 
            WHERE regioes.id_master = '$Master'
            AND (regioes.status='0' OR  regioes.status_reg ='0')
            AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]' ORDER BY regioes.id_regiao";
    $sql = mysql_query($qry) or die('Erro ao selecionar regiões Inativas por master');
    while($rows = mysql_fetch_assoc($sql)){
        $array[$rows['id_regiao']] = $rows['regiao'];
    }
    
    return $array;
    
}



}
/* ARQUIVOS EXECUTANDO ESTA ROTINA
- ESCALA.PHP
- COOPERATIVAS/TVSORRINO.PHP
- COOPERATIVAS/CONTRATO.PHP
- COOPERATIVAS/QUOTA.PHP
- ESCALA/AJAX.PHP
*/
?>