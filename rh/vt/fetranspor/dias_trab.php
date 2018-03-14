<?
class dias_trab{
function __construct(){
	include "../../../conn.php";
	$this->dias_semana;
	$this->dias_de_sabado;
	$this->dias_de_domingo;
	
	$this->FeriadosRegionaisSem;
	$this->FeriadosRegionaisSab;
	$this->FeriadosRegionaisDom;
	
	$this->FeriadosFederaisSem;
	$this->FeriadosFederaisSab;
	$this->FeriadosFederaisDom;
	
	$this -> DiasTrabCLT;
	$this -> nome;
	$this -> funcao;
}

function calcperiodo($dataInicio, $dataFim, $id_clt){
	//SELECIONANDO OS DADOS DO CLT
	$resultClt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_clt'");
	$rowClt = mysql_fetch_array($resultClt);
	//SELECIONANDO O HORARIO DO CLT
	$resultHorario = mysql_query("SELECT * FROM rh_horarios where id_horario = '$rowClt[rh_horario]'");
	$rowHorario = mysql_fetch_array($resultHorario);	
	//SELECIONANDO OS DIAS ENTRE A DATA DE INÍCIO DO PERÍODO E O FIM
	$resultDeSemana = mysql_query("SELECT * FROM ano WHERE data >= '$dataInicio' AND data <= '$dataFim' AND fds != 1");
	$numDiasDeSemana = mysql_num_rows($resultDeSemana);
	//CONTA A QUANTIDADE DE SÁBADOS NO PERÍODO
	$resultDeSab = mysql_query("SELECT * FROM ano WHERE data >= '$dataInicio' AND data <= '$dataFim' AND nome = 'Sábado'");
	$numDiasDeSab = mysql_num_rows($resultDeSab);
	//CONTA A QUANTIDADE DE DOMINGOS NO PERÍODO
	$resultDeDom = mysql_query("SELECT * FROM ano WHERE data >= '$dataInicio' AND data <= '$dataFim' AND nome = 'Domingo'");
	$numDiasDeDom = mysql_num_rows($resultDeDom);
	
	//CRIANDO VARIAVEIS CONTAVEIS
	$conFedSem = 0;	
	$conFedSab = 0;	
	$conFedDom = 0;		

	$conRegSem = 0;	
	$conRegSab = 0;	
	$conRegDom = 0;		
	
	//VERIFICAR O DIA DA SEMANA QUE CAI O FERIADO (FERIADOS REGIONAIS)
	$resultFeriadosREGIONALfinalSemana = mysql_query("SELECT *, WEEKDAY(data) AS dias  FROM rhferiados WHERE data >='$dataInicio' and data <='$dataFim' 
	and id_regiao='$rowClt[id_regiao]' and tipo='Regional'");
	while ($rowDias = mysql_fetch_array($resultFeriadosREGIONALfinalSemana)){
		$diaSemana = $rowDias['dias'];	
		if ($diaSemana == 5){
			$conRegSab = $conRegSab + 1;
			$this->FeriadosRegionaisSab = $conRegSab;
		}else if ($diaSemana == 6){
			$contRegDom = $contRegDom + 1;
			$this->FeriadosRegionaisDom = $contRegDom;
		}else{
			$contRegSem = $contRegSem + 1;
			$this->FeriadosRegionaisSem = $contRegSem; 
		}
	}
	
	//VERIFICAR O DIA DA SEMANA QUE CAI O FERIADO (FERIADOS FEDEREAIS)
	$resultFeriadosFEDERALfinalSemana = mysql_query("SELECT *, WEEKDAY(data) AS dias  FROM rhferiados WHERE data >='$dataInicio' and data <='$dataFim' 
	and tipo='Federal'");
	while ($rowDias = mysql_fetch_array($resultFeriadosFEDERALfinalSemana)){
		$diaSemana = $rowDias['dias'];	
		if ($diaSemana == 5){
			$conFedSab = $conFedSab + 1;
			$this->FeriadosFederaisSab = $conFedSab;
		}else if ($diaSemana == 6){
			$contFedDom = $contFedDom + 1;
			$this->FeriadosFederaisDom = $contFedDom;
		}else{
			$contFedSem = $contFedSem + 1;
			$this->FeriadosFederaisSem = $contFedSem; 
		}
	}
	
	$this->dias_semana = $numDiasDeSemana;
	$this->dias_de_sabado = $numDiasDeSab;
	$this->dias_de_domingo = $numDiasDeDom;	
	
	//VERIFICANDO QUANTOS DIAS O CLT RECEBERÁ OS VALES
	if($rowHorario['folga'] == 3){										//PERIODO NORMAL
		$this -> DiasTrabCLT = $this->dias_semana - ($this->FeriadosRegionaisSem + $this->FeriadosFederaisSem);
	}else if($rowHorario['folga'] == 2){										//FOLA OS DOMINGOS
		$DiasTrabCLT = $this->dias_semana + $this->dias_de_sabado - ($this->FeriadosRegionaisSem + $this->FeriadosFederaisSem + $this->FeriadosFederaisSab + $this->FeriadosRegionaisSab);
	}else if($rowHorario['folga'] == 1){										//FOLA OS SABADOS
		$this -> DiasTrabCLT = $this->dias_semana + $this->dias_de_domingo - ($this->FeriadosRegionaisSem + $this->FeriadosFederaisSem + $this->FeriadosFederaisDom + $this->FeriadosRegionaisDom);
	}else if($rowHorario['folga'] == 0){										//SEM FOLGA
		$this -> DiasTrabCLT = $this->dias_semana + $this->dias_de_sabado + $this->dias_de_domingo - ($this->FeriadosRegionaisSem + $this->FeriadosFederaisSem + $this->FeriadosFederaisSab + $this->FeriadosRegionaisSab + $this->FeriadosFederaisDom + $this->FeriadosRegionaisDom);
	}	

$resultIdsRhVale = mysql_query("SELECT * FROM rh_vale WHERE id_regiao = '3' AND status_reg != '' AND id_clt ='$id_clt'")or die(mysql_error());
while($rowVale = mysql_fetch_array($resultIdsRhVale)){
	for($i=1; $i<=6; $i++){
		$tarifa=$rowVale['id_tarifa'.$i];
		$result_tarifas=mysql_query("SELECT * FROM rh_tarifas WHERE id_tarifas = '$tarifa'")or die(mysql_error());
		$row_tarifas=mysql_fetch_array($result_tarifas);
		//if ($row_tarifas['tipo'] == ''){break;}
		if ($row_tarifas['valor'] != ''){
			$valorTarifa=$row_tarifas['valor'];
			echo $valorTarifa.'<br>';	
		}
	}
}
	
	
	$this -> nome = $rowClt['nome'];
	$this -> funcao = $rowHorario['nome'];
}


function imprimir(){
return $this -> DiasTrabCLT;
}
}//FIM DA CLASSE
?>