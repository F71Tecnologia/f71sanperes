<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../net1.css" rel="stylesheet" type="text/css">
</head>

<body>
<?
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
}else{

include "../../conn.php";

$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
}

$MES = $_REQUEST['mes'];
$REGIAO = $_REQUEST['regiao'];
$DATA = $_REQUEST['data'];
?>
<div align="center">
<div style="width:80%; background:#FFF" align="center"><?
include "../../empresa.php";
$imgCNPJ = new empresa();
$imgCNPJ -> imagemCNPJ();
?>
<?
//CONSTANTE DE IDENTIFICAÇÃO DO ARQUIVO
$CONSTANTE = "CADUSU";
//NÚMERO DA VERSÃO DO LAYOUT DO ARQUIVO
$VERSAO = '0301';
					
//OBTEM O CNPJ DA EMPRESA QUE ESTÁ NA CLASSE EMPRESA
$cnpj = new empresa();				
$cnpj = $cnpj -> cnpjEmpresa3();				
					
//RETIRANDO A FORMATACAO DO CNPJ
$remover01 = array(".", "-", "/");
$cnpj = str_replace($remover01, "", $cnpj);
					
//INSERE ZERO A DIRITA CASO O CONTEÚDO DA VARIÁVEL NÃO TENHA 14 DIGITOS
$CNPJ = sprintf("%014s",$cnpj);


$Nr_seq_reg01 = 1;
$Nr_seq_regF = sprintf("%05d", $Nr_seq_reg01);

$Tp_registro = '01'; //TIPO DO REGISTRO: 01 - HEADER DO ARQUIVO

$Nm_arquivo = $CONSTANTE; //CONSTANTE QUE IDENTIFICA O ARQUIVO
$Nm_arquivo = sprintf("% -6s", $Nm_arquivo);

$Nr_versao = '03.01'; //NUMERO DA VERSÃO DO LAYOUT DO ARQUIVO (VERSAO=02.00)
$Nr_versao = sprintf("% -5s", $Nr_versao);

$Nr_doc_arq = $CNPJ; //NUMERO DO CNPJ DO COMPRADOR

$dataE = explode("-", $DATA);
$d=$dataE[2];
$m=$dataE[1];
$a=$dataE[0]; 
$data = $a.$m.$d;
					
//INSERE ZERO A DIRITA CASO O CONTEÚDO DA VARIÁVEL NÃO TENHA 8 DIGITOS
$DATA = sprintf("%08d",$data);


$HORA = '1200'; 

$arquivo = "fetranspor/".$CONSTANTE."_".$VERSAO."_".$CNPJ."_".$DATA."_".$HORA.".txt";
if (file_exists($arquivo)){
	print "<br><br>";
	print "<span class='igreja'>ESTE ARQUIVO JÁ FOI GERADO!</span>";
	print '<br><br>';
	print "<span class='igreja'>DOWNLOAD DO ARQUIVO!</span>";
	print '<br>';
	$arquivo = $CONSTANTE."_".$VERSAO."_".$CNPJ."_".$DATA."_".$HORA.".txt";
	print "<a href='fetranspor/download.php?file=$arquivo'><img src='imagens/download.gif' border='0' alt='Download do Arquivo'></a>";
	print "<br><br>";
	exit;
						
}
?>
  <?
//////////////////////////////////////////////////////////////////////////////////////////
//VERIFICAR SE EXISTEM NOVOS FUNCIONÁRIO CADASTRADO ENTRE O PROTOCOLO PASSADO E O ATUAL//
////////////////////////////////////////////////////////////////////////////////////////
/*
$mes = $MES;
$mes = $mes-01;
$mes = sprintf("%02d", $mes);
*/
$ANO = date('Y');
$resultUltimoprotocolo = mysql_query("SELECT data FROM rh_vale_protocolo WHERE ano='$ANO' AND id_reg='$REGIAO' AND status ='IMPRESSO'");
$rowUltimoProtocolo01 = mysql_fetch_array($resultUltimoprotocolo);
$dataUltimoProtocolo = $rowUltimoProtocolo01['data'];
if ($dataUltimoProtocolo == 0){
	//CASO NÃO EXISTA NENHUM PROTOCOLO GERADO, O SISTEMA BUSCA O CADASTRO DOS ÚLTIMOS 365 DIAS
	$resultDat = mysql_query("SELECT SUBDATE(CURDATE(), INTERVAL 365 DAY) AS ultimoMes")or die(mysql_error());
	$rowDat = mysql_fetch_array($resultDat);
	$dataUltimoProtocolo = $rowDat['ultimoMes'];
}
$resultEntrada=mysql_query("SELECT nome, data_entrada, id_regiao, id_clt FROM rh_clt WHERE id_regiao = '$REGIAO' AND data_entrada >= '$dataUltimoProtocolo' and data_entrada <= CURDATE() and status = '10'");

$cadastrosNovos = mysql_affected_rows();
if ($cadastrosNovos !=0){
    ?>
    
	<form id='form1' name='form1' method='get' action='fetranspor/cadastro_txt_funcionario_riocard.php'>			
	<br><br>
        <div align='center' style='width:80%'>
	<div style='float:left; width:97%; height: 30px;' align='center'><span class='igreja'><strong>FUNCIONÁRIOS NOVOS NO PERÍODO</strong></span></div>
	<div style='width:100%; background:#CCC; line-height:20px'>
	<div style='float:left; width:25%; height: 20px;' align='center'><span class='igreja'><strong>CÓDIGO</strong></span></div>
	<div style='float:left; width:25%; height: 20px;' align='center'><span class='igreja'><strong>NOME</strong></span></div>
	<div style='float:left; width:25%; height: 20px;' align='center'><span class='igreja'><strong>DATA DE CONTRATAÇÃO</strong></span></div>
	<div style='float:left; width:25%; height: 20px;' align='center'><span class='igreja'><strong>CADASTRAR RIO CARD</strong></span></div>	
	</div>
	<?php
	$cont = 0;
	$cont02 = 0;
	while ($rowEntrada = mysql_fetch_array($resultEntrada)){
		$dataContrato = $rowEntrada['data_entrada'];
		$data = explode("-",$dataContrato);
		$d=$data[2];
		$m=$data[1];
		$a=$data[0];
		$dataContratoF = $d.'-'.$m.'-'.$a;

		if(($cont % 2)==0){ 
			$background = 'background:#ECF2EC';
		}else{ $background = 'background:#FFFFFF'; }
		
		//if($row['transporte'] == "1"){
			//$chek2 = "checked";
			//$disable_vale = "style='display:'";
		//}else{
				//$chek2 = "";
				$disable_vale = "style='display:none'";
			 //}

		
		print "<div style='$background;width:100%'>";
		print "<div style='float:left; width:25%; height: 20px; line-height:20px' align='center'><span class='igreja'>".$rowEntrada['id_clt']."</span></div>";		
		print "<div style='float:left; width:25%; height: 20px; line-height:20px' align='center'><span class='igreja'>".$rowEntrada['nome']."</span></div>";
		print "<div style='float:left; width:25%; height: 20px; line-height:20px' align='center'><span class='igreja'>".$dataContratoF."</span></div>";
		print "<div style='float:left; width:25%; height: 20px; line-height:20px' align='center'><span class='igreja'><input type='checkbox' name='checkbox$cont02' id='checkbox$cont02' value='$rowEntrada[id_clt]' onClick=\"document.all.tablevale$cont02.style.display = (document.all.tablevale$cont02.style.display == 'none') ? '' : 'none' ;\"/></span></div>";	

		//DADOS COMPLEMENTARES PARA O CADASTO DO FUNCIONÁRIO NO RIO CARD
		print "<table width='100%' id='tablevale$cont02' name='tablevale$cont02' border='0' align='center' cellpadding='0' cellspacing='0' $disable_vale>";
  		print "<tr>";
    	print "<td style='$background'><div align='left' class='igreja'>";
		print "Cidade de Recarga: <select id='recarga$cont02' name='recarga$cont02' style='font-family:Verdana, Geneva, sans-serif; font-size:9px'>";
		
		$resultRioCard = mysql_query("SELECT * FROM rh_vale_rio_card WHERE rede_recarga != '' OR cod_recarga != '' ORDER BY cidade");
		while ($rowRioCard = mysql_fetch_array($resultRioCard)){
			print "<option value='$rowRioCard[codigo]-$rowRioCard[cod_recarga]'>$rowRioCard[codigo] - $rowRioCard[cidade] - $rowRioCard[rede_recarga]</option>";
		}
		print "</select>";
		print "</div></td>";		
  		print "</tr>";
		print "</table>";
  
		print '</div>';
		print "<input type='hidden' name='cont02' id='cont02' value='$cont02'>";
		print "<input type='hidden' name='data' id='data' value='$DATA'>";
		print "<input type='hidden' name='regiao' id='regiao' value='$REGIAO'>";
		$cont = $cont+1;
		$cont02 = $cont02+1;
	}
print "</div>";
print "<br>";
print "<br>";
print "<input type='submit' name='button' id='button' value='Gerar Arquivo' />";
print "</form>";
}

print "<br>";
/*
/////////////////////////////////////////////////////////////////////////////////
//VERIFICA SE EXISTEM FUNCIOÁRIO EXCLUIDOS ENTRE O PROTOCOLO PASSADO E O ATUAL//
///////////////////////////////////////////////////////////////////////////////
$resultSaida=mysql_query("SELECT nome, data_saida, id_regiao, id_clt FROM rh_clt WHERE id_regiao = '$REGIAO' AND data_saida >= '$dataUltimoProtocolo' and data_entrada <= CURDATE() AND transporte = '1';");

$cadastrosExcluidos = mysql_affected_rows();
print "<div>";
if ($cadastrosExcluidos != 0){
	print "<form id='form1' name='form1' method='get' action='cadastro_txt_funcionario_riocard.php'>";	
	print "<br><br>";
	print "<div align='center' style='width:80%'>";
	print "<div style='float:left; width:97%; height: 30px;' align='center'><span class='igreja'><strong>FUNCIONÁRIOS EXCLUIDOS NO PERÍODO</strong></span></div>";
	print "<div style='width:100%; background:#CCC; line-height:20px'>";	
	print "<div style='float:left; width:25%; height: 20px;' align='center'><span class='igreja'><strong>CÓDIGO</strong></span></div>";	
	print "<div style='float:left; width:25%; height: 20px;' align='center'><span class='igreja'><strong>NOME</strong></span></div>";
	print "<div style='float:left; width:25%; height: 20px;' align='center'><span class='igreja'><strong>DATA DE EXCLUSÃO</strong></span></div>";
	print "<div style='float:left; width:25%; height: 20px;' align='center'><span class='igreja'><strong>REMOVER RIO CARD</strong></span></div>";	
	print "</div>";
	$cont = 0;
	$cont03 = 0;
	while ($rowSaida = mysql_fetch_array($resultSaida)){
		
		$dataExclusao = $rowSaida['data_saida'];
		$data = explode("-",$dataExclusao);
		$d=$data[2];
		$m=$data[1];
		$a=$data[0];
		$dataExclusaoF = $d.'-'.$m.'-'.$a;
		
		if(($cont % 2)==0){ 
			$background = 'background:#ECF2EC';
		}else{ $background = 'background:#FFFFFF'; }
		
		print "<div style='$background; height: 20px;width:100%'>";
		print "<div style='float:left; width:25%; height: 20px; line-height:20px' align='center'><span class='igreja'>".$rowSaida['id_clt']."</span></div>";		
		print "<div style='float:left; width:25%; height: 20px; line-height:20px' align='center'><span class='igreja'>".$rowSaida['nome']."</span></div>";
		print "<div style='float:left; width:25%; height: 20px; line-height:20px' align='center'><span class='igreja'>".$dataExclusaoF."</span></div>";
		print "<div style='float:left; width:25%; height: 20px; line-height:20px' align='center'><span class='igreja'><input type='checkbox' name='checkbox2$cont03' id='checkbox2$cont03' value='$rowSaida[id_clt]'/></span></div>";
		print "<input type='hidden' name='cont03' id='cont03' value='$cont03'>";		
		$cont03 = $cont03 +1;
	}
print "</div>";
print "<br>";
print "<br>";
print "<input type='submit' name='button' id='button' value='Gerar Arquivo' />";
print "</form>";
}*/
?>
</div>
</div>
</body>
</html>