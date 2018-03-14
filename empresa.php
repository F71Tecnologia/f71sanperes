<?php
//CLASSE empresa 03.02.2009
class empresa{
	
public function __construct() {
	//include "conn.php";
	$id_user = $_COOKIE['logado'];
	
	$r = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
	$row_user = mysql_fetch_array($r);
	
	$this->id_user= $row_user['id_master'];
	$this->regiao= $row_user['regiao'];
	
	$this->re = mysql_query("SELECT * FROM master")or die(mysql_error());
	$this->remp = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = $row_user[id_regiao]")or die(mysql_error());
}

//Método para escrever no rodapé da página qual empresa o usuário está logado atualmente.
function rodape(){	
	while ($row_master = mysql_fetch_array($this->re)){
		if ($row_master['id_master'] == $this->id_user){
			echo '<div align="center" class="rodape">';
			echo '<strong>'.$row_master['razao'].'</strong> ';
			echo '- Acesso Restrito a Funcion&aacute;rios';
			echo '</div>';
		} 
	}
}

//Metodo que imprime a logo da empresa do usuário está logado atualmente sem o CNPJ e sem uma <div>.
function imagem(){	
	print "<img src='/intranet/imagens/logomaster".$this->id_user.".gif' alt='log' width='110' height='79' />";
}

function imagem2(){	
	print "imagens/logomaster".$this->id_user.".gif'";
}

//Metodo que imprime a logo da empresa do usuário que está logado atualmente com o CNPJ.
function imagemCNPJ(){	
	while ($row_master = mysql_fetch_array($this->re)){
		if ($row_master['id_master'] == $this->id_user){
			$razao	=	$row_master['razao'];
			$cnpj	=	$row_master['cnpj'];
		}
	}
	echo '<div>';	
	print "<img src='/intranet/imagens/logomaster".$this->id_user.".gif' alt='log' width='110' height='79' />";
	echo '</div>';
	
	echo '<div>';
	echo '<span style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;color: #000000; font-weight: bold">';
	echo $razao.'</span>';
	echo '</div>';
	
	echo '<div style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 10px; font-weight: bold; color: #006600">';
	echo 'CNPJ:'.$cnpj;
	echo '</div>';	
}

function imagemCNPJ2($MA){	
	$REmaster = mysql_query("SELECT * FROM master Where id_master = $MA");
	$row_master = mysql_fetch_array($REmaster);
	$razao	=	$row_master['razao'];
	$cnpj	=	$row_master['cnpj'];

	echo '<div>';	
	print "<img src='/intranet/imagens/logomaster".$MA.".gif' alt='log' width='110' height='79' />";
	echo '</div>';
	
	echo '<div>';
	echo '<span style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px;color: #000000; font-weight: bold">';
	echo $razao.'</span>';
	echo '</div>';
	
	echo '<div style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 10px; font-weight: bold; color: #006600">';
	echo 'CNPJ:'.$cnpj;
	echo '</div>';	
}

//Imprime o endereço da empresa com a cor da fonte definido pelo programador
function endereco01($cor){
	if ($cor==''){$cor='#000';}
	$rowEndereco = mysql_fetch_array($this->remp);
	echo '<div style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;color: '.$cor.'" align="center">';
	echo '<div style="font-weight: bold"> '.$rowEndereco['razao'].' </div>';
	echo '<br>';
	echo '<div>CNPJ: '.$rowEndereco['cnpj'].'</div>';
	echo '<div> '.$rowEndereco['endereco'].' </div>';
	echo '<div> '.$rowEndereco['tel'].' </div>';
	echo '</div>';
}

//Imprime o endereço da empresa com a cor e o tamanho da fonte definidos pelo programador
function endereco($cor, $fontSize){
	$rowEndereco = mysql_fetch_array($this->remp);
	echo '<div style="font-family: Arial, Helvetica, sans-serif; font-size: '.$fontSize.';color: '.$cor.'" align="center">';
	echo '<div style="font-weight: bold"> '.$rowEndereco['razao'].' </div>';
	echo '<br>';
	echo '<div>CNPJ: '.$rowEndereco['cnpj'].'</div>';
	echo '<div> '.$rowEndereco['endereco'].' </div>';
	echo '<div> '.$rowEndereco['tel'].' </div>';
	echo '</div>';
}

//Imprime a razao da empresa no interior de uma <div>
function nomeEmpresa(){
	$rowEndereco = mysql_fetch_array($this->remp);
	echo ' '.$rowEndereco['razao'].' ';
}

//Imprime a razao social da empresa sem usar <div>
function nomeEmpresa2(){
	$rowEndereco = mysql_fetch_array($this->remp);
	echo $rowEndereco['razao'];
}

//Imprime o CNPJ da empresa no interior de uma <div>
function cnpjEmpresa(){
	$rowEndereco = mysql_fetch_array($this->remp);
	echo ' '.$rowEndereco['cnpj'].' ';
}

//Imprime o CNPJ da empresa sem usar <div>
function cnpjEmpresa2(){
	$rowEndereco = mysql_fetch_array($this->remp);
	echo $rowEndereco['cnpj'];
}
function cnpjEmpresa3(){
	$rowEndereco = mysql_fetch_array($this->remp);
	return $rowEndereco['cnpj'];
}
}
?>