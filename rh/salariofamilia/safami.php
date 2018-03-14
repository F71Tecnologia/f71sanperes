<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

//PEGANDO O ID DO CADASTRO
include "../../conn.php";

$id_clt = $_REQUEST['clt'];
$id_user = $_COOKIE['logado'];
$id_projeto = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];

$result = mysql_query(" SELECT *, date_format(data_entrada, '%d/%m/%Y')as nova_data, date_format(data_saida, '%d/%m/%Y')as data_saida2, date_format(dataalter, '%d/%m/%Y')as dataalter2 FROM rh_clt where id_clt = $id_clt ", $conn);
$row = mysql_fetch_array($result);

$qr_regiao  = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row[id_regiao]'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);
 



$result_dependente = mysql_query("SELECT * , CURDATE(),
DATE_ADD(data1, INTERVAL '14' YEAR) AS data_baixa1,
DATE_ADD(data2, INTERVAL '14' YEAR) AS data_baixa2,
DATE_ADD(data3, INTERVAL '14' YEAR) AS data_baixa3,
DATE_ADD(data4, INTERVAL '14' YEAR) AS data_baixa4,
DATE_ADD(data5, INTERVAL '14' YEAR) AS data_baixa5,
(YEAR(CURDATE())-YEAR(data1)) - (RIGHT(CURDATE(),5)<RIGHT(data1,5)) AS idade1,
(YEAR(CURDATE())-YEAR(data2)) - (RIGHT(CURDATE(),5)<RIGHT(data2,5)) AS idade2,
(YEAR(CURDATE())-YEAR(data3)) - (RIGHT(CURDATE(),5)<RIGHT(data3,5)) AS idade3,
(YEAR(CURDATE())-YEAR(data4)) - (RIGHT(CURDATE(),5)<RIGHT(data4,5)) AS idade4,
(YEAR(CURDATE())-YEAR(data5)) - (RIGHT(CURDATE(),5)<RIGHT(data5,5)) AS idade5
FROM dependentes WHERE id_bolsista = '$id_clt' AND id_projeto = '$id_projeto'");


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SAL&Aacute;RIO FAM&Iacute;LIA</title>

<style type="text/css">
<!--
body {
 background-color: #CCC;
}
-->
</style>

<link href="../../net1.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="80%" border="0" align="center"  bgcolor="#FFFFFF" cellpadding="5" cellspacing="5" class="bordaescura1px">
  <tr>
    <td width="100%" align="center" valign="middle"><p class="linha">
<img src="../../imagens/logomaster<?php echo $row_master['id_master'];?>.gif"/></td>
  </tr>
  <tr>
    <td align="center" valign="middle"><strong> <span class="style2">
	<?php
		$row_dependente = mysql_fetch_array($result_dependente);
		//Condição para andar pelos campos data1, nome1 ... data2, nome2.
		for($cont = 1;$cont<=5;$cont++){
			$nome = $row_dependente['nome'.$cont];
			$data = $row_dependente['data_baixa'.$cont];
			$idade =$row_dependente['idade'.$cont];
			//Caso alguma das variáveis sejam vazias, não irá exibir o dependente.
			if (($nome != '') or ($data != '0000-00-00')){
				//Formatando a data retirada do banco de dadose já com sua data de baixa calculada.
				if($idade < 14){
					$data = explode("-",$data); 
					$d = $data[2];
					$m = $data[1];
					$a = $data[0];

					$data_baixa = date("d/m/Y", mktime (0, 0, 0, $m  , $d , $a));
				
					echo '<span style="color:black">NOME DO DEPENDENTE: </span>'.$nome.'<span style="color:black"> - IDADE: </span>'.$idade.'<span style="color:black"> - DATA DE BAIXA: </span>'.$data_baixa.'<br>';
				}
			}
		}
	?>
    </span></strong><br>
    <br>      &nbsp;<a href="fichasafami.php?pro=<?=$id_projeto?>&clt=<?=$row['0']?>" target="_blank"><img src="imagens/fichas.gif" width="190" height="31" border="0" target="_blank"></a>&nbsp;&nbsp;&nbsp;<a href="solicitasafami.php?pro=<?=$id_projeto?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" target="_blank"><img src="imagens/solicita.gif" width="190" height="31" border="0"></a></td>
  </tr>
  <tr>
    <td align="center" valign="middle">&nbsp;</td>
  </tr>
</table>
<p class="linha">&nbsp;</p>
</body>
</html>
