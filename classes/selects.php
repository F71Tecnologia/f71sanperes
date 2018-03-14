<?php
//CLASSE selects 26.08.2009
class selects{

	public function __construct() {
		$user = $_COOKIE['logado'];
	}

	function MostraBasico($tab,$where,$campos,$titulos,$alinhamentos,$tamanho){
		$RE = mysql_query("SELECT * FROM $tab $where") or die(mysql_error());
		
		$numTi = count($titulos);
		$numCa = count($campos);
		
		if($numTi != $numCa){
			echo "Quantidade de campos diferente de Quantidade de Titulos, Impossivel continuar";
			exit;
		}
		
		//----------------- COMEÇANDO IMPRESSAO DA TABELA
		
		$retorno = "<table width='$tamanho' align='center' cellspacing='0' class='bordaescura1px'>\n";
		$retorno .= "<tr bgcolor='#999999'>\n";
		
		for($a=0 ; $a < $numTi ; $a ++){			////IMPRIMINDO AS COLUNAS COM OS TITULOS
			$retorno .= "<th>$titulos[$a]</th>\n";
		}
		$retorno .= "</tr>\n";
		
		//IMPRESSAO DAS LINHAS COM OS DADOS DA CONSULTA
		$contLIN = 1;
		while($Row = mysql_fetch_array($RE)){
			// ---------- EMBELEZAMENTO DA TABELA ---------- 
			if($contLIN % 2){ $cor = "#f0f0f0"; }else{ $cor= "#dddddd"; }
			$bord = "style='border-bottom:#000 solid 1px;'";
			// ---------- EMBELEZAMENTO DA TABELA ---------- 
			$retorno .= "<tr bgcolor='$cor'>\n";
			for($b=0 ; $b < $numCa ; $b ++){							////IMPRIMINDO AS COLUNAS DOS REGISTROS
				$ago = $campos[$b];
				$alinha = $alinhamentos[$b];
				
				$retorno .= "<td align='$alinha' class='mostraregistronormal' $bord>$Row[$ago]</td>\n";
				
			}
			$retorno .= "</tr>\n";
			$contLIN ++;
		}
		
		$retorno .= "</table>\n";
		echo $retorno;
		
	}
	
	
	
#	-------------------------------------------------------------	
	
	
	function MostraTarefa($tab,$where,$campos,$titulos,$alinhamentos,$tamanho){
		$RE = mysql_query("SELECT *,date_format(data_entrega, '%d/%m/%Y')as data_entrega FROM $tab $where") or die(mysql_error());
		
		$numTi = count($titulos);
		$numCa = count($campos);
		
		if($numTi != $numCa){
			echo "Quantidade de campos diferente de Quantidade de Titulos, Impossivel continuar";
			exit;
		}
		
		//----------------- COMEÇANDO IMPRESSAO DA TABELA
		
		$retorno = "<table width='$tamanho' align='center' cellspacing='0'>\n";
		$retorno .= "<tr>\n";
		
		$bord1 = "style='border-bottom:#000 solid 1px;'";
		
		for($a=0 ; $a < $numTi ; $a ++){			////IMPRIMINDO AS COLUNAS COM OS TITULOS
			$retorno .= "<td $bord1><div style='font-size:13px; color:#000'><b>$titulos[$a]</b></div></td>\n";
		}
		$retorno .= "</tr>\n";
		
		//IMPRESSAO DAS LINHAS COM OS DADOS DA CONSULTA
		$contLIN = 1;
		while($Row = mysql_fetch_array($RE)){
			// ---------- EMBELEZAMENTO DA TABELA ---------- 
			//if($contLIN % 2){ $cor = "#f0f0f0"; }else{ $cor= "#dddddd"; }
			$bord = "style='border-bottom:#dddddd solid 1px;'";
			// ---------- EMBELEZAMENTO DA TABELA ---------- 
			$retorno .= "<tr bgcolor='$cor'>\n";
			
			for($b=0 ; $b < $numCa ; $b ++){							////IMPRIMINDO AS COLUNAS DOS REGISTROS
				$ago = $campos[$b];
				$alinha = $alinhamentos[$b];
				
				if($ago == "id_tarefa"){
					
					$link = "<a href='#' onClick=\"javascript:popup('ver_tarefa.php?id=1&tarefa=$Row[$ago]','popup','750','450','yes');\" style='text-decoration:none'>";
					
					$retorno .= "<td align='$alinha' class='mostraregistronormal' $bord height='30'>
					<input type='checkbox' name='id' value='$Row[$ago]'></td>\n";
					
				}elseif($ago == "status_tarefa"){
					if($Row[$ago] == "1"){
						$statusTarefa = "Não Lido";
						//$cor = "#F00";
					}else{
						$statusTarefa = "Lido";
						//$cor = "#00F";
					}
					
					$retorno .= "<td align='$alinha' class='mostraregistronormal' $bord height='30'>
					<img src='imagens/read$Row[$ago].gif' alt='$statusTarefa'></td>\n";
				}elseif($ago == "id_regiao"){
					$RR = $Row[$ago];
					
					$RERE = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$RR'");
					$RowRE = mysql_fetch_array($RERE);
					
					$retorno .= "<td align='$alinha' class='mostraregistronormal' $bord height='30'>$RowRE[regiao]</td>\n";
				}else{
					$retorno .= "<td align='$alinha' class='mostraregistronormal' $bord height='30'>$link $Row[$ago]</a></td>\n";
				}
				
			}
			$retorno .= "</tr>\n";
			$contLIN ++;
			unset($RR);
		}
		
		$retorno .= "</table>\n";
		echo $retorno;
		
	}
	
#	-------------------------------------------------------------
	
	function MostraEscalas($tab,$where,$campos,$titulos,$alinhamentos,$tamanho){
		$RE = mysql_query("SELECT * FROM $tab $where") or die(mysql_error());
		$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
		
		$numTi = count($titulos);
		$numCa = count($campos);
		
		if($numTi != $numCa){
			echo "Quantidade de campos diferente de Quantidade de Titulos, Impossivel continuar";
			exit;
		}
		
		//----------------- COMEÇANDO IMPRESSAO DA TABELA
		
		$bord1 = "style='border-bottom:#000 solid 1px;'";
		
		$retorno = "<br><table width='$tamanho' align='center' cellspacing='0' class='bordaescura1px'>\n";
		$retorno .= "<tr>\n";
		
		for($a=0 ; $a < $numTi ; $a ++){			////IMPRIMINDO AS COLUNAS COM OS TITULOS
			$retorno .= "<th $bord1>$titulos[$a]</th>\n";
		}
		$retorno .= "</tr>\n";
		
		//IMPRESSAO DAS LINHAS COM OS DADOS DA CONSULTA
		$contLIN = 1;
		while($Row = mysql_fetch_array($RE)){
			// ---------- EMBELEZAMENTO DA TABELA ---------- 
			if($contLIN % 2){ $cor = "#f0f0f0"; }else{ $cor= "#dddddd"; }
			$bord = "style='border-bottom:#666 solid 1px;'";
			// ---------- EMBELEZAMENTO DA TABELA ---------- 
			$retorno .= "<tr bgcolor='$cor' height='30'>\n";
			for($b=0 ; $b < $numCa ; $b ++){							////IMPRIMINDO AS COLUNAS DOS REGISTROS
				$ago = $campos[$b];
				$alinha = $alinhamentos[$b];
				
				if($ago == "nome"){
					
					$RE_escala_proc = mysql_query("SELECT id_escala_proc,mes FROM escala_proc WHERE id_escala = '$Row[0]' ORDER BY mes");
					$Num_escala_proc = mysql_num_rows($RE_escala_proc);
					
					$retorno .= "<td align='$alinha' class='mostraregistronormal' $bord><a href='escala.php?id=2&esc=$Row[0]'>$Row[$ago]</a> 
					</td>\n";
				}else{
					$retorno .= "<td align='$alinha' class='mostraregistronormal' $bord>$Row[$ago]</td>\n";
				}
				
			}
			$retorno .= "</tr>\n";
			
			if($Num_escala_proc != 0){
				while($EscalaProc = mysql_fetch_array($RE_escala_proc)){
					$MesesEscalaProc .= " [ <a href='escalapronta.php?id=".$EscalaProc['id_escala_proc']."'>".$meses[$EscalaProc['mes']]."</a> ] ";
				}
				$retorno .= "<tr bgcolor='$cor' height='25'><td align='center' $bord>
				<img src='../imagens/setinha.gif'> </td><td colspan='8' $bord>Meses: $MesesEscalaProc</td></tr>";
			}
			
			mysql_free_result($RE_escala_proc);
			$MesesEscalaProc = "";
			
			$contLIN ++;
		}
		
		$retorno .= "</table>\n";
		echo $retorno;
		
	}
	
	
}
/* ARQUIVOS EXECUTANDO ESTA ROTINA
- ESCALA.PHP
*/
?>