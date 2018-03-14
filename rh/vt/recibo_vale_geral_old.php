<?
$id_user = $_COOKIE['logado'];
$id_reg = $_REQUEST['regiao'];
$ID_PROTOCOLO = $_REQUEST['id_protocolo'];
$MES_REFERENCIA = $_REQUEST['mes_referencia'];
$ANO = $_REQUEST['ano'];
include "../../conn.php";

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>RECIBO DE VT</title>
<link href="../../net.css" rel="stylesheet" type="text/css" />

<link href="../../net1.css" rel="stylesheet" type="text/css">
<body topmargin="0">
<div align="center">
<div align="center" style="background:#FFF; width:90%">
	<div align="center" style="width:80%; background:#FFF">
<?php
include "../../empresa.php";
$imgCNPJ = new empresa();
$imgCNPJ -> imagemCNPJ();
?>

    	<div><span class="style2">Recibo de  Vale - Transporte</span></div>
	<!-- Início da recuperção colewtiva do banco -->
    <? 
	$resultProtocolo = mysql_query("SELECT *, date_format(data_ini, '%d/%m/%Y')as data_iniF, date_format(data_fim, '%d/%m/%Y')as data_fimF FROM rh_vale_protocolo WHERE id_protocolo='$ID_PROTOCOLO' AND id_reg='$id_reg'");
	$rowProtocolo = mysql_fetch_array($resultProtocolo);
	
	$vinculoProtocoloReletorio = $rowProtocolo['id_protocolo'];
	
	$resultRelatorio = mysql_query("SELECT * FROM rh_vale_relatorio WHERE id_protocolo=$vinculoProtocoloReletorio AND id_reg='$id_reg'");
	$rowRelatorio = mysql_fetch_array($resultRelatorio);
	
	echo '<div class="style3" align="left" style="height:70px">';
	echo '<br>';
	echo 'Mês referência: '.$rowProtocolo['mes'].' / '.$rowProtocolo['ano'].'<br>';
	echo 'Período: '.$rowRelatorio['dias'].' dias<br>';
	echo 'Data referência: '.$rowProtocolo['data_iniF'].' à '.$rowProtocolo['data_fimF'].'<br>';
	echo 'Valor: '.$rowRelatorio['valor_total'];
	echo '</div>';
	?>
<?
echo '<p class="quebra-aqui"><!-- Quebra de página --></p>';
$cont = 0;
$result_r_relatorio = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo='$vinculoProtocoloReletorio' AND id_reg='$id_reg'")or die(mysql_error());
while ($row_r_relatorio = mysql_fetch_array($result_r_relatorio)){
		
	$ID_FUNC = $row_r_relatorio['id_func'];
	//Seleciona os dados de cadastro do funcionário baseado no id da variável $ID_FUNC
	$resultRh_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$ID_FUNC'");
	$row_Rh_clt = mysql_fetch_array($resultRh_clt);
		
	$vinculo_tb_rh_clt_e_rhempresa = $row_Rh_clt['rh_vinculo'];

	$result_empresa= mysql_query("SELECT nome FROM rhempresa WHERE id_empresa = $vinculo_tb_rh_clt_e_rhempresa");
	$row_empresa = mysql_fetch_array($result_empresa);
		
	$ID_FUN[] = $row_r_relatorio['id_func'];
	
	$cont = $cont+1;
}
//-----------------------------------------------------------------
//Retira os ids repetidos
$result = array_unique($ID_FUN);
//Conta os restantes
$quant = count($result);

for($i=0; $i<$quant; $i++){
	$ID[$i] = current($result);
	next($result);
}


//-----------------------------------------------------------------
$inicial = $_REQUEST['inicialrhvale'];
$final = $_REQUEST['finalrhvale'];

if($final>=$quant){
	$final = $quant;	
}

$inicial=$_REQUEST['inicial'];
if ($_REQUEST['finalrhvale']==''){ //Variável enviada pela página anterior dese estar zerada.
	$final=$_REQUEST['final'];
}
//Mostra os dados dos funcionários
for($i2=0; $i2<$quant; $i2++){
//for($i2=$inicial; $i2<$final; $i2++){	
	$RESULT_R_RELATORIO = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo='$vinculoProtocoloReletorio' AND id_func=".$ID[$i2]." AND id_reg='$id_reg'")or die(mysql_error());
	
	$ROW_R_RELATORIO = mysql_fetch_array($RESULT_R_RELATORIO);
	$ID_FUN = $ROW_R_RELATORIO['id_func'];
	$NOME = $ROW_R_RELATORIO['nome'];
	$ITINERARIO= $ROW_R_RELATORIO['itinerario'];
	$VALOR = $ROW_R_RELATORIO['valor'];
	$QUANTIDADE = $ROW_R_RELATORIO['quantidade'];
	 
 
	$resultCLT = mysql_query("SELECT * FROM rh_clt WHERE id_clt =".$ID[$i2]."");
	$row_clt = mysql_fetch_array($resultCLT);
	
	$ENDERECO = $row_clt['endereco'];
	$MUNICIPIO = $row_clt['cidade'];
	$BAIRRO = $row_clt['bairro'];
	$CEP = $row_clt['cep'];
	$FOTO = $row_clt['foto'];	
	$id_pro = $row_clt['id_projeto'];
	$id_reg = $row_clt['id_regiao'];
	$EMPRESA = $row_empresa['nome'];
	
	$nome_para_arquivo = $row_clt['1'];
	if($FOTO == "1"){
	
	if($nome_para_arquivo == "0"){
		$nome_imagem = $id_reg."_".$id_pro."_".$ID[$i2].".gif";
	}else{
		$nome_imagem = $id_reg."_".$id_pro."_".$nome_para_arquivo.".gif";
	}
	
	}else{
		$nome_imagem = "semimagem.gif";
	}
	echo '<div>';
	echo '<div align="center"><strong class="igreja"><img src="../../fotos/'.$nome_imagem.'" border="1" width="70" height="95"></strong></div>';
	echo '<br>';

	//Nome
	echo '<div align="left" style="height:20px;border: 3px; border-style:solid ;border-color:#FFF">';
	echo '<div style="width:20%; height:30px; float:left; background:#CCFFCC">
	<span class="linha" style="font-size:10px; line-height:30px">Nome do Funcionário:</span></div>';
	echo '<span class="igreja" style="line-height:30px">'.$NOME.'</span>';
	echo '</div>';
	//Endereço
	echo '<div align="left" style="height:20px;border: 3px; border-style:solid ;border-color:#FFF">';
	echo '<div style="width:20%; height:30px; float:left; background:#CCFFCC">
	<span class="linha" style="font-size:10px; line-height:30px">Endereço Residencial:</span></div>';
	echo '<span class="igreja" style="line-height:30px">'.$ENDERECO.'</span></div>';
		
	//Município
	echo '<div align="left" style="height:20px;border: 3px; border-style:solid ;border-color:#FFF">';
	echo '<div style="width:20%; height:30px; float:left; background:#CCFFCC">
	<span class="linha" style="font-size:10px; line-height:30px">Municipio:</span></div>';
	echo '<span class="igreja" style="line-height:30px">'.$MUNICIPIO.'</span></div>';

	//Bairro
	echo '<div align="left" style="height:20px;border: 3px; border-style:solid ;border-color:#FFF">';
	echo '<div style="width:20%; height:30px; float:left; background:#CCFFCC">
	<span class="linha" style="font-size:10px; line-height:30px">Bairro:</span></div>';
	echo '<span class="igreja" style="line-height:30px">'.$BAIRRO.'</span></div>';
	
	//CEP
	echo '<div align="left" style="height:20px;border: 3px; border-style:solid ;border-color:#FFF">';
	echo '<div style="width:20%; height:30px; float:left; background:#CCFFCC">
	<span class="linha" style="font-size:10px; line-height:30px">CEP:</span></div>';
	echo '<span class="igreja" style="line-height:30px">'.$CEP.'</span></div>';
		
	//Empresa
	echo '<div align="left" style="height:20px;border: 3px; border-style:solid ;border-color:#FFF">';
	echo '<div style="width:20%; height:30px; float:left; background:#CCFFCC">
	<span class="linha" style="font-size:10px; line-height:30px">Empresa:</span></div>';
	echo '<span class="igreja" style="line-height:30px">'.$EMPRESA.'</span></div>';
	echo '<br><br>';		

	//---------------------------------------------------------------	
	
	//Titulo da tabela com as informações detalhadas sobre os vales
	echo '<div style="background:#003300"><strong class="style1">TRANSPORTES UTILIZADOS</strong></div>';
		
	echo '<div style="width:25%; height:40px; float:left; background:#F0F0F0 ; border: 1px; border-style:solid ;border-color:#FFF"><span class="linha" style="line-height:40px">TIPO DE VALE TRANSPORTE UTILIZADO</span></div>';
		
	echo '<div style="width:30%; height:40px; float:left; background:#F0F0F0; border: 1px; border-style:solid ;border-color:#FFF"><span class="linha" style="line-height:40px">INTINERÁRIO</span></div>';
		
	echo '<div style="width:20%; height:40px; float:left; background:#F0F0F0; border: 1px; border-style:solid ;border-color:#FFF"><span class="linha" style="line-height:40px">PREÇO DA PASSAGEM</span></div>';
		
	echo '<div style="width:25%; height:40px; float:left; background:#F0F0F0; border: 1px; border-style:solid ;border-color:#FFF"><span class="linha" style="line-height:40px">QUANTIDADE</span></div>';
	echo '<br>';
	
	//Dados da tabela 
	$RESULT_R_RELATORIO2=mysql_query("SELECT * FROM rh_vale_r_relatorio where id_func='$ID[$i2]' AND id_reg='$id_reg' AND mes='$MES_REFERENCIA'");
	$cont2 = 0;
	while($ROW_R_RELATORIO2 = mysql_fetch_array($RESULT_R_RELATORIO2)){
	
				$ID_FUN = $ROW_R_RELATORIO2['id_func'];
				$TIPO = $ROW_R_RELATORIO2['tipo'];
				$ITINERARIO = $ROW_R_RELATORIO2['itinerario'];
				$VALOR = $ROW_R_RELATORIO2['valor'];
				$QUANTIDADE = $ROW_R_RELATORIO2['quantidade'];
				
				$VALOR_TOTAL = $ROW_R_RELATORIO2['valor_total_func'];
				if ($VALOR_TOTAL != 0){
					$valarPorFuncionario = $VALOR_TOTAL;	  
	  				$valarPorFuncionario = number_format($valarPorFuncionario,2,",",".");
				}
				//Multiplica a quantidade por 2, pois é um vale para ida e 1 para a volta.
				$QUANTIDADE = $QUANTIDADE*2;
				if ($cont2%2){$cor = 'background:#FDFDFD';}else{$cor = 'background:#ECF2EC';}
				echo '<div style="height:20px">';
				echo '<div style="width:25%; height:25px; line-height:25px; float:left; '.$cor.'; border: 1px; border-style:solid ;border-color:#FFF"><span class="igreja">'.$TIPO.'</span></div>';

				echo '<div style="width:30%; height:25px; line-height:25px; float:left; '.$cor.'; border: 1px; border-style:solid ;border-color:#FFF"><span class="igreja">'.$ITINERARIO.'</span></div>';
				
				$VALOR = number_format($VALOR,2,",",".");
				echo '<div style="width:20%; height:25px; line-height:25px; float:left; '.$cor.'; border: 1px; border-style:solid ;border-color:#FFF"><span class="igreja">'.$VALOR.'</span></div>';
		
				echo '<div style="width:25%; height:25px; line-height:25px; float:left; '.$cor.'; border: 1px; border-style:solid ;border-color:#FFF"><span class="igreja">'.$QUANTIDADE.'</span></div>';
				
				echo '</div>';	
				
				$cont2 = $cont2+1;
	}
	echo '<br>';
	print "<div align='justify' class='campotexto'>&nbsp;&nbsp;&nbsp;&nbsp;Comprometo-me a utilizar o vale-transporte exclusivamente para os deslocamentos Residência - Trabalho - Residência, bem como manter atualizadas as informações acima prestadas.<br /><br /> 

&nbsp;&nbsp;&nbsp;&nbsp;Declaro, ainda, que as informações supra são a expressão da verdade, ciente de que o erro nas mesmas, ou o uso
indevido do vale-transporte, constituirá falta grave, ensejando punição, nos termos da legislação específica.<br />

Recebi de <span class='style2'>$EMPRESA</span>, <span class='style2'>$QUANTIDADE</span> vales transporte no valor total de R$<span class='style2'> $valarPorFuncionario </span> para utilização
durante o período de <span class='style2'>$rowProtocolo[data_iniF]</span> a <span class='style2'>$rowProtocolo[data_fimF]</span>.

</div>";
echo '<br><br><br>';
echo "<div align='center'>_____________________________________________________</div>";
echo "<div align='center'><span class='linha'>Assinatura</span></div>";

	echo '<br><br>';
	echo '<hr noshade="noshade" size="1px">';
	echo '<br><br>';
}
echo '</div>';

echo "<br>";
echo '<p class="quebra-aqui"><!-- Quebra de página --></p>';
/*
//A quantidade por página é definida com a diferença entre as variáveis $inicialRhVale e $finalRhVale vindas do arquivo rh\vt\rh_vale.php
$numeroPorPagina = 1;
$numeroDePagina = round ($quant/$numeroPorPagina);

echo 'Quantidade de página: '.$numeroDePagina.'<br>';
echo 'Quantidade por página: '.$numeroPorPagina.'<br>';
echo 'Quantidade de registros: '.$quant.'<br>';

$pag = 1;
for ($i=0;$i<$quant;$i++){

	$ini = $i;
	$fim = $numeroPorPagina + $i;
	
	if($fim>=$quant){
		$fim = $quant;
		break;
	}
	
	//print "($ini $fim)";
	print "<a href='recibo_vale_geral.php?inicial=$ini&final=$fim&acao=visualizar&id_protocolo=$ID_PROTOCOLO&mes_referencia=$MES_REFERENCIA&regiao=$id_reg'> $pag </a>";
	
	$i = $i + 1;
$pag = $pag + 1;
}
*/
?>

<p>


<div align="center"><a href="../rh_vale.php?regiao= /*<?=$id_reg?>"><img src="../../imagens/voltar_novo.gif" width="111" height="31" border="0"></a></div>
</div>
</div>
</div>
</body>
</html>
