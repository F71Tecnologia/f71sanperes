<?php
include "conn.php";

//default-character-set=cp1251;
mysql_query ('SET character_set_client=utf8');
mysql_query ('SET character_set_connection=utf8');
mysql_query ('SET character_set_results=utf8');

$id = $_REQUEST['id'];
$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];

if($id <= 2 ){
	$nomeid = "tabGE1";
}else{
	$nomeid = "tabGE2";
}

?>
<htm>
<head>
<title> :: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="net.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor='#D7E6D5'>
<div align="center" id='<?=$nomeid?>'><a href="#" onClick="<?=$nomeid?>.style.display='none';" style="font-weight:bold; text-decoration:none">Ocultar</a>
<table width='90%' height="25" border="0" align='center' cellpadding="2" cellspacing="0" bgcolor='#666666'>
  <tr class='linha' bgcolor=#CCCCCC>
   <td width="2%" align=center>--</td>
   <td width="2%" align=center>C&oacute;d</td>
   <td width="19%" align=center>Nome</td>
   <td width="4%" align=center>Tipo</td>
   <td width="7%" align=center>Sal&aacute;rio</td>
   <td width="4%" align=center>Status</td> 
   <td width="47%" align=center>Loca&ccedil;&atilde;o</td>
   <td width="15%" align=center>Entrada - Sa&iacute;da</td>
   </tr>
	<?
	
	switch($id){
	 case 1:
	 
	// --------------------------------------------------------------------------------------------
	// CLTS NÃO AVALIADOS
	// --------------------------------------------------------------------------------------------
	
	$result_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida 
	FROM rh_clt WHERE id_projeto = '$projeto' and id_psicologia = '0' ORDER BY locacao,nome");

	$cont = 0;
	while ($row_clt = mysql_fetch_array($result_clt)){
	
   $result_curso = mysql_query("Select * from curso where id_regiao = '$regiao' and id_curso = $row_clt[id_curso]");
   $row_curso = mysql_fetch_array($result_curso);
   
   //
   if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
   $bord = "style='border-bottom:#000 solid 1px;'";
   //
   $curso = $row_curso['0']; 
   
   if ($row['status'] >= "60"){
   $status = "<font color=red>Inativo</font>";
   }else{
   $status = "Ativo";
   }
   
  
   $valor_curso = number_format($row_curso['salario'],2,",",".");
   
print "
   <tr bgcolor=$color>
   <td align=center $bord><input name='id_parti' id='id_parti' type='checkbox' value='$row_clt[id_clt]'></td>
   <td $bord><span class='style3'>$row_clt[campo3]</span></td>
   <td $bord><a href=avalicao.php?bol=$row_clt[0]&tab=$id_projeto&regiao=$id_regiao&tipo=clt class=link>$row_clt[nome]</a></td>
   <td $bord align=center><span class='style3'>CLT</span></td>
   <td $bord><span class='style3'>$valor_curso</span></td>
   <td $bord align=center><span class='style3'>$status</span></td>
   <td $bord><span class='style3'>$row_clt[locacao]</span></td>
   <td $bord align=center><span class='style3'>$row_clt[data_entrada] - $row_clt[data_saida]</span></td>
   </tr>";
   
   $cont ++;

}

	print "<input type='hidden' name='tipo' value='1'/>";
	
	
	 break;
	 
	 
	// --------------------------------------------------------------------------------------------
	// AUTONOMOS NÃO AVALIADOS
	// --------------------------------------------------------------------------------------------
	 case 2:
	 
	 
	 $result = mysql_query("SELECT * , date_format(data_entrada, '%d/%m/%Y')as data_entrada,date_format(data_saida, '%d/%m/%Y')as data_saida 
	 FROM autonomo WHERE id_psicologia = '0' and id_projeto = '$projeto' ORDER BY locacao,nome");

     $cont = 0;
   while ($row = mysql_fetch_array($result)){
   
   $result_curso = mysql_query("Select * from curso where id_regiao = '$regiao' and id_curso = $row[id_curso]");
   $row_curso = mysql_fetch_array($result_curso);
   $curso = $row_curso['0']; 
   
   $valor_curso = number_format($row_curso['salario'],2,",",".");
   
   if ($row['status'] == "0"){
   $status = "<font color=red>Inativo</font>";
   }else{
   $status = "Ativo";
   }
   
   if ($row['tipo_contratacao'] == "1"){
   $tipo = "Autonomo";
   }else{
   $tipo = "Cooperado";
   }
   
   //
   if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
   $bord = "style='border-bottom:#000 solid 1px;'";
   //
   
   print "
   <tr bgcolor=$color>
   <td $bord align=center><input name='id_parti[]' id='id_parti' type='checkbox' value='$row[id_autonomo]'></td>
   <td $bord><span class='style3'>$row[campo3]</span></td>
   <td $bord><a href=avalicao.php?bol=$row[id_autonomo]&tab=$id_projeto&regiao=$id_regiao class=link>$row[nome]</a></td>
   <td $bord align=center><span class='style3'>$tipo</span></td>
   <td $bord><span class='style3'>$valor_curso</span></td>
   <td $bord align=center><span class='style3'>$status</span></td>
   <td $bord><span class='style3'>$row[locacao]</span></td>
   <td $bord align=center><span class='style3'>$row[data_entrada] - $row[data_saida]</span></td>
   </tr>";
   
   $cont ++;
}
   
   print "<input type='hidden' name='tipo' value='2'/>";
   
   break;
   
   // --------------------------------------------------------------------------------------------
   // GERANDO CLT AVALIADOS
   // --------------------------------------------------------------------------------------------
   case 3:
   
   
	$result_cltAV = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida 
	FROM rh_clt WHERE id_projeto = '$projeto' and id_psicologia = '1' ORDER BY locacao,nome");

	$cont = 0;
	while ($row_cltAV = mysql_fetch_array($result_cltAV)){
	
   $result_curso = mysql_query("Select * from curso where id_regiao = '$regiao' and id_curso = $row_cltAV[id_curso]");
   $row_curso = mysql_fetch_array($result_curso);
   
   
   $curso = $row_curso['0']; 
   
   if ($row['status'] >= "60"){
   $status = "<font color=red>Inativo</font>";
   }else{
   $status = "Ativo";
   }
   
  
   $valor_curso = number_format($row_curso['salario'],2,",",".");
   
    //
   if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
   $bord = "style='border-bottom:#000 solid 1px;'";
   //
   
print "
   <tr bgcolor=$color>
   <td $bord align=center><input name='id_parti' id='id_parti' type='checkbox' value='$row_cltAV[id_clt]'></td>
   <td $bord><span class='style3'>$row_cltAV[campo3]</span></td>
   <td $bord><a href=avaliacao2.php?bol=$row_cltAV[0]&tab=$projeto&regiao=$regiao&tipo=clt class=link>$row_cltAV[nome]</a></td>
   <td $bord align=center><span class='style3'>CLT</span></td>
   <td $bord><span class='style3'>$valor_curso</span></td>
   <td $bord align=center><span class='style3'>$status</span></td>
   <td $bord align=center><span class='style3'>$row_cltAV[locacao]</span></td>
   <td $bord align=center><span class='style3'>$row_cltAV[data_entrada] - $row_cltAV[data_saida]</span></td>
   </tr>";
	$cont ++;
}

	print "<input type='hidden' name='tipo' value='1'/>";
   
   
   break;
   

	// --------------------------------------------------------------------------------------------
	// AUTONOMOS AVALIADOS
	// --------------------------------------------------------------------------------------------
	
	case 4:
	 
	 $result = mysql_query("SELECT * , date_format(data_entrada, '%d/%m/%Y')as data_entrada,date_format(data_saida, '%d/%m/%Y')as data_saida 
	 FROM autonomo WHERE id_psicologia = '1' and id_projeto = '$projeto' ORDER BY locacao,nome");

     $cont = 0;
   while ($row = mysql_fetch_array($result)){
   
   $result_curso = mysql_query("Select * from curso where id_regiao = '$regiao' and id_curso = $row[id_curso]");
   $row_curso = mysql_fetch_array($result_curso);
   $curso = $row_curso['0']; 
   
   $valor_curso = number_format($row_curso['salario'],2,",",".");
   
   if ($row['status'] == "0"){
   $status = "<font color=red>Inativo</font>";
   }else{
   $status = "Ativo";
   }
   
   if ($row['tipo_contratacao'] == "1"){
   $tipo = "Autonomo";
   }else{
   $tipo = "Cooperado";
   }
   
    //
   if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
   $bord = "style='border-bottom:#000 solid 1px;'";
   //
   
   print "
   <tr bgcolor=$color>
   <td $bord align=center><input name='id_parti[]' id='id_parti' type='checkbox' value='$row[id_autonomo]'></td>
   <td $bord><span class='style3'>$row[campo3]</span></td>
   <td $bord><a href=avaliacao2.php?bol=$row[id_autonomo]&tab=$projeto&regiao=$regiao class=link>$row[nome]</a></td>
   <td $bord align=center><span class='style3'>$tipo</span></td>
   <td $bord><span class='style3'>$valor_curso</span></td>
   <td $bord align=center><span class='style3'>$status</span></td>
   <td $bord align=center><span class='style3'>$row[locacao]</span></td>
   <td $bord align=center><span class='style3'>$row[data_entrada] - $row[data_saida]</span></td>
   </tr>";
   
   $cont ++;
}
   
   print "<input type='hidden' name='tipo' value='2'/>";
   
   
   
   break;
   
	}
	
	print "</table><br>";
	if($id <=2){
		print "<center><input type='submit' value='Avaliar em LOTE'></center>";
	}else{
		print "<center><input type='submit' value='Reavaliar em LOTE'></center>";
	}
	
	
   ?>
   
</table>
</div>
</body>
</html>   
