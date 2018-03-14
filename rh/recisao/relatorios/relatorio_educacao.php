<?
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}

include "conn.php";

$projeto = $_REQUEST['pro'];

$regiao = $_REQUEST['reg'];


$result_pro = mysql_query("SELECT * FROM projeto where id_projeto = '$projeto'");
$row_pro = mysql_fetch_array($result_pro);

// PEGA O ID DO FUNCIONÁRIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

//FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$result = mysql_query("SELECT campo3,nome, data_nasci,(YEAR(CURDATE())-YEAR(data_nasci)) - (RIGHT(CURDATE(),5)<RIGHT(data_nasci,5)) AS idade, locacao, date_format(data_nasci, '%d/%m/%Y') as data_nasci FROM autonomo WHERE id_regiao = '$regiao' and id_projeto = '$projeto' ORDER BY idade DESC");
$quantidade = mysql_affected_rows();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: INTRANET ::</title>
<link href="net1.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px">
</tr>
   <td width="21%" bgcolor="#FFFFFF"><p class="MsoHeader" align="center" style='text-align:center'><strong><span class="style5"><img src='imagens/logomaster<?=$row_user['id_master']?>.gif' width='120' height='86' /><br /></span></strong><span style='font-size:10px'><?=$row_master['razao']?></span></p></td>
   <td width="58%" bgcolor="#FFFFFF"><p class="MsoHeader" align="center" style='text-align:center'><b><span
  style='font-size:16.0pt;color:red'><?php print "$row_pro[nome] <br> $row_pro[regiao] "; ?></span></b></p></td>
   <td width="21%" bgcolor="#FFFFFF">&nbsp;</td>
</tr>
   <table align="center" width="97%" border="0" class="campotexto4" cellspacing="0" bgcolor="#FFFFFF">
      	<caption> <strong>RELATÓRIO POR IDADE</strong></caption>
		<caption>QUANTIDADE DE PARTICIPANTES: <?=$quantidade?></caption>
		<tr bgcolor="#666666" class="campotexto4">
			<th width="5%" height="20px" align="center" class="campotexto4">CÓDIGO</th>
			<th width="25%" height="20px" align="center" class="campotexto4">NOME</th>
			<th width="5%" height="20px" align="center" class="campotexto4">IDADE</th>
			<th width="10%" height="20px" align="center" class="campotexto4">DATA</th>
			<th width="30%" height="20px" align="center" class="campotexto4">LOCA&Ccedil;&Atilde;O</th>
        </tr>
		<?
		$cont = 1;
		$bord = "style='border-bottom:#000 solid 1px;'";
		while ($row = mysql_fetch_array($result)){
			if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
				print "<tr bgcolor=$color class='campotexto4' style='font-size:10px'>";
				print "<td $bord width='5%' height='20px' align='center'>$row[campo3]</td>";
				print "<td $bord width='25%' height='20px'>$row[nome]</td>";
				print "<td $bord width='5%' height='20px' align='center'>$row[idade]</td>";
				print "<td $bord width='10%' height='20px' align='center'>$row[data_nasci]</td>";
				print "<td $bord width='30%' height='20px'>$row[locacao]</td>";
				print "</tr>";;
				$cont = $cont + 1;
		}
		?>
		</table>
</tr>
</table>
</body>
</html>