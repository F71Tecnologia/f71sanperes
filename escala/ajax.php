<?php
header("Content-Type: text/html;  charset=ISO-8859-1",true); 
include "../conn.php";

//ESTE AJAX É ESPECIFICO PARA O ARQUIVO ESCALA.PHP

//TRABALHANDO A SOLICIATÇÃO DA PAGINA
$id = $_REQUEST['id'];

switch ($id){
	case 1:
	
	$projeto = $_REQUEST['projeto'];
	$RE = mysql_query("SELECT * FROM curso WHERE campo3 = '$projeto' ORDER BY nome");
	
	$select = "<center><b>Selecione:</b> <br><select name='id_curso' id='id_curso' size='5' multiple='multiple' 
	ondblclick=\"insertValueQuery(this.id,'sql_query','visualatividade')\">\n";
	
	while($Row = mysql_fetch_array($RE)){
		$NomeAti = str_replace("CAPACITANDO EM ","CAP. EM ",$Row['nome']);
		
		$select .= "<option value='$Row[0]'>$NomeAti</option>\n";
	}
	$select .= "</select>\n </center>";

	echo $select;
	
	

	break;
	
	case 2:
	
	$projeto = $_REQUEST['projeto'];
	$unidade = $_REQUEST['unidade'];
	
	//$projeto = 10;
	//$unidade = "CRAS VILA ESPERANÇA - URBANO - Secretaria Municipal de Governo - Itaboraí - RJ";
	
	$titulos = array("-",CÓDIGO,NOME,ATIVIDADE);
	$campos = array(id_autonomo,campo3,nome,id_curso);
	$alinhamentos = array(center,left,left,left);
	
	$contratacao = 1;
	
	include "../classes/listaparticipantes.php";
	$listagem = new participantes();
	$listagem -> ParticipantesporUnidadeCheqbox($projeto,$unidade,"1",$campos,$titulos,$contratacao,$alinhamentos);
	
	break;
	
	case 3:					//MONTANDO TABELA PARA INFORMAR OS HORARIOS DOS PERIODOS
	
	$periodos = $_REQUEST['projeto'];
	
	if($periodos >= 7){
		echo "<center><b>o valor maximo de periodos é 6 </b></center><br>";
		$periodos = 6;
	}
		
	
	echo "<table width=100% border=0>";
	for($i = 1; $i <= $periodos; $i ++){
		if($i == 1){
			$exago = "(07:00 até 13:00)";
		}elseif($i == 2){
			$exago = "(13:00 até 19:00)";
		}elseif($i == 3){
			$exago = "(19:00 até 07:00)";
		}else{
			$exago = "(00:00 até 00:00)";
		}
		echo "<tr><td><div align='center' class='titulo_opcoes'>Periodo $i&nbsp;&nbsp;&nbsp;";
		echo "horario inicial e horario final&nbsp;&nbsp;";
		echo "<input type='text' name='hora$i' id='hora$i'> $exago</div></td></tr>";
	}
	echo "</table>";
	
	break;
	
	case 4:							//MONTANDO SELECT DE UNIDADES
	
	$projeto = $_REQUEST['projeto'];
	$RE = mysql_query("SELECT * FROM unidade WHERE campo1 = '$projeto'");
	
	$select = "&nbsp;&nbsp;<select name='unidade' id='unidade'>\n";
	$select .= "<option value='0'>- Selecione -</option>\n";
	
	while($Row = mysql_fetch_array($RE)){
		$select .= "<option value='$Row[unidade]'>$Row[0] - $Row[unidade]</option>\n";
	}
	$select .= "</select>\n";

	echo $select;
	
	break;
	
	
	case 5:					//MONTANDO AS LISTAS DOS FUNCIONÁRIOS PARA OS CAMPOS DE TEXTO
	
	$recebi = $_REQUEST['procura'];
	$pro = $_REQUEST['pro'];
	$reg = $_REQUEST['reg'];
	$curso = $_REQUEST['curso'];
	$idcampo = $_REQUEST['idcampo'];
	
	$Atividades = explode(", ",$curso);
	$contAtivi = count($Atividades);
	
	if($contAtivi != 1){
		
		for($i=0 ; $i < $contAtivi; $i ++){
			
			if($i == 0){
				$STRCurso .= "( id_curso = '".$Atividades[$i]."' ";
			}else{
				$STRCurso .= " or id_curso = '".$Atividades[$i]."' ";
			}
			
		}#END FOR
		
		$STRCurso .= " ) ";
				
	}else{	#CASO SEJE APENAS UMA ATIVIDADE
		
		$STRCurso .= " id_curso = '".$curso."' ";
		
		
	}#END if($contAtivi != 1)
	
	$sql = "SELECT * FROM autonomo WHERE status = '1' and id_regiao = '$reg' and id_projeto = '$pro' and $STRCurso ORDER BY nome";
	$REAuto = mysql_query("SELECT * FROM autonomo WHERE status = '1' and id_regiao = '$reg' and id_projeto = '$pro' and $STRCurso ORDER BY nome");
	$numAut = mysql_num_rows($REAuto);
	
	
	$li = " onCLick=\"document.all.ttdiv.style.display='none' \" style='text-decoration:none; color:#FF0000;' ";
	$Devolver = "<div align='right' style='margin-right:5px; margin-top:5px; cursor:pointer' $li> fechar 
	<img src='../imagens/bot_fechar.gif' border='0' align='absmiddle'></a></div>";
	
	
	if($numAut == 0){
		$Devolver .= "<div style='color:#FF0000;' align='center'>Sua busca n&atilde;o retornou Resultado</div>";
	}else{
		//WHILE DE AUTONOMOS
		while($RowTeste = mysql_fetch_array($REAuto)){
			//http://www.netsorrindo.com.br/intranet/ver_bolsista.php?reg=3&bol=4108&pro=11
			$li = " onCLick=\"document.all.ttdiv.style.display='none'; 
			document.getElementById('$idcampo').value='$RowTeste[nome]';
			document.getElementById('$idcampo').focus();
			\" style='text-decoration:none; cursor:pointer' ";
			$Devolver .= "<div onMouseOver=\"this.style.background='#09F'\" onMouseOut=\"this.style.background=''\" $li >".$RowTeste['nome']."</div>";
		}
	}
	
	echo $Devolver;
	
	break;
}
?>
