<?
$id_clt = $_REQUEST['clt'];
$id_user = $_COOKIE['logado'];
$id_pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];

include "../../conn.php";

/*echo $id_clt.'<br>';
echo $id_user.'<br>';
echo $id_pro.'<br>';
echo $id_reg.'<br>';
*/
$resultNome = mysql_query("SELECT nome FROM rh_clt WHERE id_clt = '$id_clt'");
$rowNome = mysql_fetch_array($resultNome);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>RECIBO DE VT</title>
<link href="../../net.css" rel="stylesheet" type="text/css" />

<link href="../../net1.css" rel="stylesheet" type="text/css">
<body>
<div align="center">
	<div align="center" style="width:80%; background:#FFF">
<?php
include "../../empresa.php";
$img= new empresa();
$img -> imagemCNPJ();
?>
    	<div><span class="style2">Recibo de  Vale - Transporte</span></div>
        <br>
        <div><span class="campotexto">RECIBOS GERADOS PARA O FUNCIONÁRIO: </span><span class="style2"><? echo $rowNome['nome']; ?></span></div>
	<!-- Início da recuperção colewtiva do banco -->



 <? 

	$ANO = date('Y');
	
	$result_protocolo = mysql_query("SELECT *, date_format(data, '%d/%m/%Y')AS dataF FROM rh_vale_relatorio WHERE ano='$ANO' AND id_reg='$id_reg' AND status='GRAVADO'");
	$cont=0;
	echo "<br>";
	echo '<div style="width:25%; height:20px; float:left; background:#CCFFCC" class="linha"> MÊS REFERÊNCIA</div>';
	echo '<div style="width:25%; height:20px; float:left; background:#CCFFCC" class="linha"> GERADO POR</div>';
	echo '<div style="width:25%; height:20px; float:left; background:#CCFFCC" class="linha"> DATA</div>';
	echo '<div style="width:24.5%; height:20px; float:left; background:#CCFFCC" class="linha"></div>';
	while($row_protocolo= @mysql_fetch_array($result_protocolo)){
		
		//Exibe o nome do usuário que gerou o protocolo baseado no numero do campo user da tabela rh_vale_relatorio
		$result_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_protocolo[user]'");
		
		$row_usuario= mysql_fetch_array($result_usuario);
		
		if($cont%2){$corLinha='background:#FFFFFF';}else{$corLinha='background:#ECF2EC';}
		echo "<br>"; 			
		echo '<div style="float:center;'.$corLinha.'" class="igreja">';
		//Exibe o nome do mes baseado no valor do campo mes da tabela rh_vale_relatorio
		$mes = $row_protocolo['mes'];	 
		$result_mes=mysql_query("SELECT * FROM ano_meses WHERE num_mes = '$mes'");
		$row_mes=mysql_fetch_array($result_mes);
		
		echo '<div style="width:25%; float:left; $corLinha" class="igreja">'.$row_mes['nome_mes'].'</div>';
		echo '<div style="width:25%; float:left; $corLinha" class="igreja">'.$row_usuario['nome'].'</div>';
		echo '<div style="width:25%; float:left; $corLinha" class="igreja">'.$row_protocolo['dataF'].'</div>'; 
		
		echo "<input type='button' name='' value='Imprimir' onclick='window.open(\"recibo.php?id_protocolo=$row_protocolo[id_protocolo]&mes_referencia=$row_protocolo[mes]&id_reg=$row_protocolo[id_reg]&ano=$row_protocolo[ano]&clt=$id_clt\")' style='border:1px solid #000;background:#ECF2EC'/>";
		echo "</div>";

		$cont = $cont+1;

	}
	
	
?>
</div>
</body>
</html>