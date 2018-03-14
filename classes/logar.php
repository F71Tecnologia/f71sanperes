<?php
//CLASSE curso 30.07.2009
class logar{

public function __construct() {
	$id_user = $_COOKIE['logado'];
	
	$r = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
	$row_user = mysql_fetch_array($r);
	
	$this->id_user= $row_user['id_funcionario'];
	$this->regiao= $row_user['regiao'];
}




	function LoginFinanceiro($senha,$tela,$url){
		
	//	$RE = mysql_query("SELECT * FROM senhas where id_senha = '$tela' and senha = '$senha'");
		//$Row = mysql_num_rows($RE);
		
		//if($Row != 0){
			if($tela == 1){
			//	setcookie ("logado2", $this->id_user);
				$blz = new logar();
				$blz -> GravandoLog($this->id_user, "EFETUANDO LOGIN NO FINANCEIRO","FINANCEIRO");
			/*}else{
				setcookie ("logado3", $this->id_user);
			}*/
			
			header("Location: $url");
			
			
			//header("Location: $url"); //Fazendo O LOGIN NO NOVO FINANCEIRO
			
	/*	}else{
			print "<script>location.href = 'login_adm.php?senha_errada=true';</script>";
		*/} 
		
	}
	
	function LoginRH($senha,$tela,$url){
		
		//$RE = mysql_query("SELECT * FROM senhas where id_senha = '$tela' and senha = '$senha'");
		//$Row = mysql_num_rows($RE);
		
	//	if($Row != 0){
			if($tela == 3){
				//setcookie ("logado2", $this->id_user);
				$blz = new logar();
				$blz -> GravandoLog($this->id_user, "EFETUANDO LOGIN NA GESTÃO RH","GESTÃO RH");
		//	}else{
				//setcookie ("logado3", $this->id_user);
			}
			
			print "
			<script>
			location.href=\"$url\"
			</script>";
			
			//header("Location: $url"); //Fazendo O LOGIN NO NOVO FINANCEIRO
			
		/*}else{
			print "<script>location.href = 'login_rh.php?senha_errada=true';</script>";
		}*/
		
	}
	
	
	function LoginAdministracao($senha,$tela,$url){
		
		//$RE = mysql_query("SELECT * FROM senhas where id_senha = '$tela' and senha = '$senha'");
	//	$Row = mysql_num_rows($RE);
		
	/*	if($Row != 0){*/
			if($tela == 4){
				//setcookie ("logado2", $this->id_user);
				$blz = new logar();
				$blz -> GravandoLog($this->id_user, "EFETUANDO LOGIN NA ADMINISTRAÇÃO","ADMINISTRAÇÃO");
		/*	}else{
				//setcookie ("logado3", $this->id_user);
			*/}
			
			print "
			<script>
			location.href=\"$url\"
			</script>";
			
			
		/*}else{
			print "<script>location.href = '../adm/login.php?senha_errada=true';</script>";
		}*/
		
	}
	
	
	function LoginContabil($senha,$tela,$url){
		
		$RE = mysql_query("SELECT * FROM senhas where id_senha = '$tela' and senha = '$senha'");
		$Row = mysql_num_rows($RE);
		
	//	if($Row != 0){
			if($tela == 5){
		//		setcookie ("logado2", $this->id_user);
				$blz = new logar();
				$blz -> GravandoLog($this->id_user, "EFETUANDO LOGIN NA GESTÃO CONTÁBIL","GESTÃO CONTÁBIL");
			
				header("Location: $url");
			/*}else{
				setcookie ("logado3", $this->id_user);
			}
			
			
			print "
			<script>
			location.href=\"$url\"
			</script>";
			
			
		}else{
			print "<script>location.href = 'login3.php?senha_errada=true';</script>";
		*/}
		
	}
	
	function LoginJuridico($senha,$tela,$url){
	
		
	//	if($Row != 0){
			if($tela == 6){
			//	setcooki ("logado2", $this->id_user);
				$blz = new logar();
				$blz -> GravandoLog($this->id_user, "EFETUANDO LOGIN NA GESTÃO JURÍDICA","GESTÃO JURÍDICA");
		/*	}else{
				setcookie ("logado3", $this->id_user);
			}*/
			
			header("Location: $url");
		/*	print "
			<script>
			location.href=\"$url\"
			</script>";
			
			
		}else{
			print "<script>location.href = 'login3.php?senha_errada=true';</script>";
		*/ }
		
	}
	
	function LoginRelatorio($senha,$tela,$url){
	
		
	//	if($Row != 0){
			if($tela == 2){
			//	setcooki ("logado2", $this->id_user);
				$blz = new logar();
				$blz -> GravandoLog($this->id_user, "EFETUANDO LOGIN EM RELATÓRIOS","RELATÓRIOS");
		/*	}else{
				setcookie ("logado3", $this->id_user);
			}*/
			
			header("Location: $url");
		/*	print "
			<script>
			location.href=\"$url\"
			</script>";
			
			
		}else{
			print "<script>location.href = 'login3.php?senha_errada=true';</script>";
		*/ }
		
	}
	
	
	
	
	function GravandoLog($userA,$acao,$local){
		
		// INI -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG
		$ip = $_SERVER['REMOTE_ADDR'];  //PEGANDO O IP
		$horario = date('Y-m-d H:i:s');
		
		$RE1 = mysql_query("SELECT id_regiao,tipo_usuario,grupo_usuario FROM funcionario WHERE id_funcionario = '$userA'");
		$Ro = mysql_fetch_array($RE1);
		
		mysql_query("INSERT INTO log (id_user,id_regiao,tipo_user,grupo_user,local,horario,ip,acao) 
		VALUES ('$userA','$Ro[id_regiao]','$Ro[tipo_usuario]','$Ro[grupo_usuario]','$local','$horario','$ip','$acao')") 
		or die ("Erro Inesperado<br><br>".mysql_error());

	}
}
/* ARQUIVOS EXECUTANDO ESTA ROTINA
- login_adm.PHP
*/
?>