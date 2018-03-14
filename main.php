<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../../../conn.php";

$ACAO = $_REQUEST['acao'];
$ID_PROTOCOLO = $_REQUEST['id_protocolo'];
$MES_REFERENCIA = $_REQUEST['mes_referencia'];
$REGIAO = $_REQUEST['regiao'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>::Vale Express::</title>
<link href="../../../net1.css" rel="stylesheet" type="text/css" />
</head>

<body class="">

<div align="center">
	<div align="center" style="width:700px; background:#FFF">
		<div align="center">
    		<?php
					include "../../../empresa.php";
					$imgCNPJ = new empresa();
					$imgCNPJ -> imagemCNPJ();
			?>  
            <div>
            	<br>VALE EXPRESS<br><br>
            </div>       
         </div>
         <?php
		 		//RETIRA DA TABELA, O CÓDIGO DOS FUNCIOÁRIOS QUE SERÃO EMITIDOS VALE
		 		$resultRhValeRRelatorio = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo='$ID_PROTOCOLO' AND id_reg = '$REGIAO' AND mes= '$MES_REFERENCIA'");				
				
				//RETIRA OS CÓDIGO DUPLICADOS
		 		while ($rowFuncionarios = mysql_fetch_array($resultRhValeRRelatorio)){
						$arrayId_func[] = $rowFuncionarios['id_func'];
		 		}

				//RETIRA OS IDS REPETIDOS
				$result = array_unique($arrayId_func);
				//CONTA OS RESTANTES
				$quant = count($result);

				//BUSCA O ANO EXATO DE QUANDO O PROTOCOLO DA TABELA rh_vale_relatorio FOI CRIADO E A REGIAO
				$resultAno = mysql_query("SELECT * FROM rh_vale_relatorio WHERE id_protocolo='$ID_PROTOCOLO' AND id_reg = '$REGIAO' AND mes= '$MES_REFERENCIA'");
				$rowAnoRegiao = mysql_fetch_array($resultAno);
				$ANO = $rowAnoRegiao['ano'];
				$REGIAO = $rowAnoRegiao['id_reg'];
				
				for($i=0; $i<$quant; $i++){
					$arrayId_func[$i] = current($result);
					next($result);
				}
				
				//OBTEM O CNPJ DA EMPRESA QUE ESTÁ NA CLASSE EMPRESA
				$CNPJ = new empresa();				
				$cnpj = $CNPJ -> cnpjEmpresa3();
				
				$data = date('dmY');
				
				$remover01 = array(".", "-", "/");
				$cnpj = str_replace($remover01, "", $cnpj);
					
				//GRAVANDO DADOS NO ARQUIVO TEXTO
				$handle = fopen ("ve".$REGIAO.$MES_REFERENCIA.$ANO.".txt", "a");
						
				//DADOS DA EMPRESA DO FUNCIOÁRIO
				fwrite($handle, "00", 2); //PRODUTO
				fwrite($handle, $cnpj, 14); //CNPJ
				fwrite($handle, $data, 8); //DATA
				fwrite($handle, "\n");//QUEBRA DE LINHA

				echo "<div align='center'>";
				//BUSCANDO DADOS PESSOAIS DE CADA FUCNIOÁRIO
				for ($c=0; $c<$quant; $c++){
						$resultCLT = mysql_query("SELECT * FROM rh_clt WHERE id_regiao = '$R