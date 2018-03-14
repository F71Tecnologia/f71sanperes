<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";
/*
$id_clt = $_REQUEST['clt'];
$tab = $_REQUEST['tab'];
$pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];
*/
//$id_clt = $_REQUEST['clt'];
//$tab = $_REQUEST['tab'];
$pro = '1';
$id_reg = '4';

//IMPRIME O NOME DA REGIÃO ATUAL
$result_reg = mysql_query("Select * from regioes where id_regiao = '$id_reg'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$dia = date('d');
$mes = date('n');
$ano = date('Y');

switch ($mes) {
case 1:
$mes = "Janeiro";
break;
case 2:
$mes = "Fevereiro";
break;
case 3:
$mes = "Março";
break;
case 4:
$mes = "Abril";
break;
case 5:
$mes = "Maio";
break;
case 6:
$mes = "Junho";
break;
case 7:
$mes = "Julho";
break;
case 8:
$mes = "Agosto";
break;
case 9:
$mes = "Setembro";
break;
case 10:
$mes = "Outubro";
break;
case 11:
$mes = "Novembro";
break;
case 12:
$mes = "Dezembro";
break;
}

$result_reg = mysql_query("Select * from regioes where id_regiao = $id_reg", $conn);
$row_reg = mysql_fetch_array($result_reg);

$result_pro = mysql_query("Select * from projeto where id_projeto = $pro", $conn);
$row_pro = mysql_fetch_array($result_pro);
?>

<!--
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

//mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('12','$row[0]','$data_cad', '$user_cad')");

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
-->


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>COMUNICADO DE DISPENSA COLETIVA</title>
<style type="text/css">
<!--
div.MsoHeader {font-size:12.0pt;
	font-family:"Arial","sans-serif";}
li.MsoHeader {font-size:12.0pt;
	font-family:"Arial","sans-serif";}
p.MsoHeader {font-size:12.0pt;
	font-family:"Arial","sans-serif";}
.style1 {color: #003300}
.style3 {
	font-size: 12px;
	font-family: Arial, Helvetica, sans-serif;
}
.style4 {font-family: Arial, Helvetica, sans-serif}
.style5 {color: red}
.style9 {font-size: 14}
.style13 {font-size: 14px}
.style14 {
	font-size: 13px;
	font-weight: bold;
}
.style15 {
	font-family: "Univers 45 Light", "sans-serif";
	font-size: 14.0pt;
	color: red;
}
P.quebra-aqui {page-break-before: always}
-->
</style>

</head>

<body>
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="21%"><p class="MsoHeader" align="center" style='text-align:center'><strong><span class="style5">
<?php
include "empresa.php";
?><!--<img src='imagens/certificadosrecebidos.gif' width='120' height='86' />--><br />
    </span></strong></p>    </td>
    <td width="58%"><p class="MsoHeader" align="center" style='text-align:center'><b><span
  style='font-size:12.0pt;color:red'><?php /*print "$row_pro[nome] / $row_pro[regiao] <br><br> $row[locacao]"; */?></span></b></p>    </td>

    <td width="21%">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3">
      <?
	  //NÃO ESQUECER DE APAGAR O LIMIT
	  $result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada,date_format(data_saida, '%d/%m/%Y')as data_saida FROM rh_clt where id_regiao = '$id_reg' AND status = '10' AND id_projeto = '$pro' ORDER BY nome", $conn);
	  
	 while ($row = mysql_fetch_array($result_bol)){	
		
		$img= new empresa();
		$img -> imagem();
		print "<div class='MsoHeader' align='center' style='text-align:center'><b><span
  style='font-size:12.0pt;color:red'>$row_pro[nome] / $row_pro[regiao] <br><br> $row[locacao]</span></b></div>";
		print "<br>";
		print "<p align='cente' style='text-align:right'><span class='style4'>$row_reg[regiao], $dia de $mes de $ano</span></p>";
		print "<br><br>";
		$result_curso = mysql_query("Select * from curso where id_curso = $row[id_curso]", $conn);
		$row_curso = mysql_fetch_array($result_curso);
		
		//DATA DE SAÍDA DO FUNCIONÁRIO		
	  	$result_data_saida = mysql_query("SELECT * , DATE_ADD(data_saida, INTERVAL '29' DAY) AS data_saida30 FROM rh_clt where id_clt = '$row[id_clt]'", $conn) or die(mysql_error());
	  	$row_data_saida = mysql_fetch_array($result_data_saida)or die(mysql_error());

       	print " A(o) Sr(a),<br />
        <b><span style='font-size:11.0pt;line-height:150%;font-family:&quot;Univers 45 Light&quot;,&quot;sans-serif&quot;;
color:red'>$row[nome] </span></b><br />
      </p>
      <p class='style3'>CPTS: <b><span style='font-size:11.0pt;line-height:150%;font-family:&quot;Univers 45 Light&quot;,&quot;sans-serif&quot;;

color:red'>$row[campo1]</span></b></p>

      <p class='style3'><span class='style14'>Ref: COMUNICADO DE DISPENSA.</span></p>

      <p class='style3'><br />

        Vimos pela presente, comunicar-lhe que a partir dessa data <b><span style='font-size:11.0pt;line-height:150%;font-family:&quot;Univers 45 Light&quot;,&quot;sans-serif&quot;;

color:red'>$row[data_saida]</span></b>,  rescindimos seu contrato de trabalho, conforme artigo 477 da CLT.";  

if ($id_reg == 11){
	$data = $row_data_saida['data_saida30']; 
	$data30 =explode('-',$data);
	$d = $data30[2];
	$m = $data30[1];
	$a = $data30[0];
	print "Cumprindo aviso prévio até o dia <span style='font-size:11.0pt;line-height:150%;font-family:&quot;Univers 45 Light&quot;,&quot;sans-serif&quot;;color:red'>$d/$m/$a</span> quando encerrara suas atividades.";
}
		print "<br />

        Por gentileza compare&ccedil;a ao Departamento Pessoal do ";  
		$nomEmp= new empresa();
		$nomEmp -> nomeEmpresa2(); 
		print ", para recebimento das verbas rescis&oacute;rias.<br />

      	</p>

      	<p class='style3'>Sem mais,  agradecemos.<br />

      	</p>

      	<p class='style3'>Atenciosamente,</p>

      	<p align='center' class='style3'>__________________________________________<br />";

		$nomEmp2= new empresa();
		$nomEmp2 -> nomeEmpresa2();

		print "</p>

      	<p class='style3'>Ciente, <span class='style3'>$row_reg[regiao], $dia de $mes de $ano</span>.</p>

      	<p align='center' class='style3'>_________________________________________.<br />

      	<b><span style='font-size:9.0pt;line-height:150%;font-family:&quot;Univers 45 Light&quot;,&quot;sans-serif&quot;;

color:red'>$row[nome]</span></b></p>";
		$end = new empresa();
		$end = $end -> endereco('black','11px');
		
		//QUEBRA A PÁGINA
		echo '<p class="quebra-aqui"><!-- Quebra de página --></p>';
		
    	/*print "<p align='center' class='style3'>&nbsp;</p>

    	<p align='center' class='style3'>&nbsp;</p></td>

  		</tr>

  		<tr>

    	<td colspan='7'><div align='center'>

      	<p>";  

		print "<span class='style13 style3 style4'>&nbsp;</span>

        <span class='style13 style3 style4'>&nbsp;</span>    <span class='style13'></p>

      <p>&nbsp;</p>*/
    print "</div>    
      </tr>

";
	 }
}
//print "</table>";	 


?>
</table>
</body>

</html>
