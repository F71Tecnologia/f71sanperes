<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$id = $_REQUEST['id'];
$projeto = $_REQUEST['projeto'];
$regiao = $_REQUEST['regiao'];
$banco = $_REQUEST['banco'];
print "
<html><head><title>Intranet</title>
<script type=\"text/javascript\" src=\"js/prototype.js\"></script>
<script type=\"text/javascript\" src=\"js/scriptaculous.js?load=effects,builder\"></script>
<script type=\"text/javascript\" src=\"js/lightbox.js\"></script>
<link rel=\"stylesheet\" href=\"js/lightbox.css\" type=\"text/css\" media=\"screen\" />
<script src=\"js/jquery-1.8.3.min.js\" type=\"text/javascript\"></script>
<script src=\"js/jquery-ui-1.9.2.custom.min.js\" type=\"text/javascript\"></script>
<script src=\"js/global.js\" type=\"text/javascript\"></script>

<link href='net1.css' rel='stylesheet' type='text/css'>
</head>
<body>";


switch ($id){
case 1:
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];

$d = explode ("/", $data_ini);            
$data_ini_f = "$d[2]-$d[1]-$d[0]";        
$data_ini_f = $data_ini_f." 00:00:00";

$d = explode ("/", $data_fim);            
$data_fim_f = "$d[2]-$d[1]-$d[0]";        
$data_fim_f = $data_fim_f." 23:59:59";

$linkEnc = $_GET['enc'];

if($banco == 'todos'){
	$result = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM entrada where data_proc >= '$data_ini_f' and data_proc <= '$data_fim_f' and id_projeto = '$projeto' and status = '2' ORDER BY data_vencimento");
} else {
	$result = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM entrada where data_proc >= '$data_ini_f' and data_proc <= '$data_fim_f' and id_projeto = '$projeto' and status = '2' AND id_banco = '$banco' ORDER BY data_vencimento");

}

if($_COOKIE['logado'] == 256){
    echo "SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM entrada where data_proc >= '$data_ini_f' and data_proc <= '$data_fim_f' and id_projeto = '$projeto' and status = '2' ORDER BY data_vencimento";
    echo "SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM entrada where data_proc >= '$data_ini_f' and data_proc <= '$data_fim_f' and id_projeto = '$projeto' and status = '2' AND id_banco = '$banco' ORDER BY data_vencimento";
}


print "
<p id=\"excel\" style=\"text-align: right; margin-top: 20px\"><input type=\"button\" onclick=\"tableToExcel('tbRelatorio', 'Relatório')\" value=\"Exportar para Excel\" class=\"exportarExcel\"></p>
<table id='tbRelatorio' width='750' border='0' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
  <tr>
    <td colspan='4'><img src='layout/topo.gif' alt='topo' width='750' height='38' /></td>
  </tr>
  <tr>
    <td width='21' rowspan='6' background='layout/esquerdo.gif'>&nbsp;</td>
    <td colspan='2'><div align='center' class='style7'></div></td>
    <td width='26' rowspan='6' background='layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan='2' bgcolor='#CCFFCC'><div align='center'>
	<img src='imagensfinanceiro/entradas.gif' alt='fornecedor' width='25' height='25' align='absmiddle'>
	<font color='red' size=4><b>CONTROLE DE ENTRADAS DA OSCIP</b></font></div></td>
  </tr>
  <tr>
    <td colspan='2'><table align='center'>
      <tr>
	   <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CÓDIGO ENTRADA</b></font></div>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>DATA DE RECEBIMENTO</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>NOME DO CRÉDITO</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CONTA CREDITADA</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>TIPO DE ENTRADA</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>DESCRIÇÃO</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CADASTRADA POR</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CONFIRMADA POR</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>VALOR ADICIONAL</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>VALOR TOTAL</b></font></div></td>
      </tr>";
	  
$cont1 = "1";
while($row = mysql_fetch_array($result)){

if($banco == 'todos') {	
	$nome_banco = mysql_result(mysql_query("SELECT nome FROM bancos WHERE id_banco = '$row[id_banco]'"),0);	
} else {
		$nome_banco = mysql_result(mysql_query("SELECT nome FROM bancos WHERE id_banco = '$banco'"),0);	
}


if($cont1 % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[id_user]'");
$row_user = mysql_fetch_array($result_user);

$result_user_pg = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[id_userpg]'");
$row_user_pg = mysql_fetch_array($result_user_pg);

$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row[id_banco]' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);

$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row[tipo]'");
$row_tipo = mysql_fetch_array($result_tipo);

if($row['especifica'] == ""){ $descricao = "&nbsp;"; }else{ $descricao = $row['descricao']; }
if($row['adicional'] == ""){ $adicional = "&nbsp;"; }else{ $adicional = $row['adicional']; }

print "
<tr bgcolor=$color> 
        <td class=border2><font size=1 face=Arial>&nbsp;$row[0]</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$row[data2]</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$row[nome]</td>
        <td class=border2><font size=1 face=Arial>$nome_banco -  AG: $row_banco[agencia] / C: $row_banco[conta]</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$row_tipo[nome]</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$descricao</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$row_user[0]</td>
		<td class=border2><font size=1 face=Arial>&nbsp;$row_user_pg[0]</td>
        <td class=border2><font size=1 face=Arial>$adicional</td>
        <td class=border2><font size=1 face=Arial>$row[valor]</td>
      </tr>

";
$valor_soma = str_replace(",",".",$row['valor']);
$adicional = str_replace(",",".",$row['adicional']);

$valor_total1 = $valor_total1 + $valor_soma + $adicional;
$cont1 ++;
}

$valor_total1 = number_format($valor_total1,2,",",".");
print "
      <tr>
        <td colspan='6' bgcolor='#CCFFCC'><div align='right' class='style12'>TOTAL DE ENTRADAS $data_ini a $data_fim:</div></td>
        <td colspan='3' bgcolor='#CCFFCC'><b>R$ $valor_total1</b></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan='2' height='15' align='center' valign='top' bgcolor='#FFFFFF' class='style3'><a href='novoFinanceiro/relatorios/rel_entrada.php?enc=$linkEnc'>voltar</a></td>
  </tr>

  
  <tr>
    <td width='532' height='15' align='right' valign='top' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
    <td align='center' valign='middle' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
  </tr>
  <tr>
    <td align='center' valign='middle' class='style3'></td>
    <td width='171' align='center' valign='middle' class='style3'></td>
  </tr>
  <tr valign='top'>
   <td height='37' colspan='4' bgcolor='#5C7E59'><img src='layout/baixo.gif' alt='baixo' width='750' height='38' />
   </td>
 </tr>
</table>
</body>
</html>";

break;

case 2:

$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];

$d = explode ("/", $data_ini);            
$data_ini_f = "$d[2]-$d[1]-$d[0]";        
$data_ini_f = $data_ini_f." 00:00:00";

$d = explode ("/", $data_fim);            
$data_fim_f = "$d[2]-$d[1]-$d[0]";        
$data_fim_f = $data_fim_f." 23:59:59";
$banco = $_REQUEST['banco'];

//ENCRIPTOGRAFANDO
$linkEnc = $_GET['enc'];


if($banco == 'todos'){
	$result = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM saida where data_proc >= '$data_ini_f' and data_proc <= '$data_fim_f' and id_projeto = '$projeto' and status = '2' ORDER BY data_vencimento");
} else {
	$result = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM saida where data_proc >= '$data_ini_f' and data_proc <= '$data_fim_f' and id_projeto = '$projeto' and status = '2' AND id_banco = '$banco' ORDER BY data_vencimento");
	
}
print "
<p id=\"excel\" style=\"text-align: right; margin-top: 20px\"><input type=\"button\" onclick=\"tableToExcel('tbRelatorio', 'Relatório')\" value=\"Exportar para Excel\" class=\"exportarExcel\"></p>
<table id='tbRelatorio' width='750' border='0' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
  <tr>
    <td colspan='4'><img src='layout/topo.gif' alt='topo' width='750' height='38' /></td>
  </tr>
  <tr>
    <td width='21' rowspan='6' background='layout/esquerdo.gif'>&nbsp;</td>
    <td colspan='2'><div align='center' class='style7'></div></td>
    <td width='26' rowspan='6' background='layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan='2' bgcolor='#CCFFCC'><div align='center'>
	<img src='imagensfinanceiro/saidas.gif' alt='fornecedor' width='25' height='25' align='absmiddle'>
	<font color='red' size=4><b>CONTROLE DE SAI&#769;DAS DA OSCIP</b></font></div></td>
  </tr>
  <tr>
    <td colspan='2'><table align='center'>
      <tr>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1><b>CÓDIGO ENTRADA</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1><b>DATA DE RECEBIMENTO</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1><b>NOME DO CRÉDITO</b></font></div></td>
		<td bgcolor='#FFFFCC'><div align='center'><font size=1><b>ESPECIFICAÇÃO</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1><b>CONTA CREDITADA</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1><b>TIPO DE ENTRADA</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1><b>CADASTRADA POR</b></font></div></td>
		<td bgcolor='#FFFFCC'><div align='center'><font size=1><b>PAGO POR</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1><b>VALOR ADICIONAL</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1><b>VALOR TOTAL</b></font></div></td>
      </tr>";
	  
$cont1 = "1";
while($row = mysql_fetch_array($result)){
	
if($banco == 'todos') {	
	$nome_banco = mysql_result(mysql_query("SELECT nome FROM bancos WHERE id_banco = '$row[id_banco]'"),0);	
} else {
	$nome_banco = mysql_result(mysql_query("SELECT nome FROM bancos WHERE id_banco = '$banco'"),0);	
}

if($cont1 % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[id_user]'");
$row_user = mysql_fetch_array($result_user);

$result_user_pg = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[id_userpg]'");
$row_user_pg = mysql_fetch_array($result_user_pg);

$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row[id_banco]' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);

$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row[tipo]'");
$row_tipo = mysql_fetch_array($result_tipo);

if($row['especifica'] == ""){ $descricao = "&nbsp;"; }else{ $descricao = $row['descricao']; }
if($row['adicional'] == ""){ $adicional = "&nbsp;"; }else{ $adicional = $row['adicional']; }

print "
<tr bgcolor=$color> 
        <td class=border2><font size=1 face=Arial>&nbsp;$row[0]</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$row[data2]</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$row[nome]</td>
		<td class=border2><font size=1 face=Arial>&nbsp;$row[especifica]</td>
        <td class=border2><font size=1 face=Arial>$nome_banco - AG: $row_banco[agencia] / C: $row_banco[conta]</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$row_tipo[nome]</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$row_user[0]</td>
		<td class=border2><font size=1 face=Arial>&nbsp;$row_user_pg[0]</td>
        <td class=border2><font size=1 face=Arial>$adicional</td>
        <td class=border2><font size=1 face=Arial>$row[valor]</td>
      </tr>

";

$adicional = str_replace(",",".",$row['adicional']);
$valor_soma = str_replace(",",".",$row['valor']);

$valor_total2 = $valor_total2 + $valor_soma + $adicional;
$cont1 ++;
}

$valor_total2 = number_format($valor_total2,2,",",".");

print "
      <tr>
        <td colspan='6' bgcolor='#CCFFCC'><div align='right' class='style12'>TOTAL DE ENTRADAS $data_ini a $data_fim:</div></td>
        <td colspan='3' bgcolor='#CCFFCC'>R$ $valor_total2</td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan='2' height='15' align='center' valign='top' bgcolor='#FFFFFF' class='style3'><a href='novoFinanceiro/relatorios/rel_saida.php?enc=$linkEnc'>voltar</a></td>
  </tr>

  
  <tr>
    <td width='532' height='15' align='right' valign='top' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
    <td align='center' valign='middle' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
  </tr>
  <tr>
    <td align='center' valign='middle' class='style3'></td>
    <td width='171' align='center' valign='middle' class='style3'></td>
  </tr>
  <tr valign='top'>
   <td height='37' colspan='4' bgcolor='#5C7E59'><img src='layout/baixo.gif' alt='baixo' width='750' height='38' />
   </td>
 </tr>
</table>
</body>
</html>";

break;

case 3:
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];

$d = explode ("/", $data_ini);            
$data_ini_f = "$d[2]-$d[1]-$d[0]";        
$data_ini_f = $data_ini_f." 00:00:00";

$d = explode ("/", $data_fim);            
$data_fim_f = "$d[2]-$d[1]-$d[0]";        
$data_fim_f = $data_fim_f." 23:59:59";

$result = mysql_query("SELECT *,date_format(data_proc,'%d/%m/%Y') as data3 FROM caixa where data_proc >= '$data_ini_f' and data_proc <= '$data_fim_f' and id_projeto = '$projeto' ORDER BY data_vencimento");

print "
<p id=\"excel\" style=\"text-align: right; margin-top: 20px\"><input type=\"button\" onclick=\"tableToExcel('tbRelatorio', 'Relatório')\" value=\"Exportar para Excel\" class=\"exportarExcel\"></p>
<table id='tbRelatorio' width='750' border='0' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
  <tr>
    <td colspan='4'><img src='layout/topo.gif' alt='topo' width='750' height='38' /></td>
  </tr>
  <tr>
    <td width='21' rowspan='6' background='layout/esquerdo.gif'>&nbsp;</td>
    <td colspan='2'><div align='center' class='style7'></div></td>
    <td width='26' rowspan='6' background='layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan='2' bgcolor='#CCFFCC'><div align='center'><font color='red' size=4><b>CONTROLE DE SAÍDAS DE CAIXA DA OSCIP</b></font></div></td>
  </tr>
  <tr>
    <td colspan='2'><table align='center'>
      <tr>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CÓDIGO SAÍDA</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>DATA</b></font></div></td>
		<td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>NOME DO DÉBITO</b></font></div></td>
		<td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CADASTRADA POR</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>ESPECIFICAÇÃO</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>VALOR ADICIONAL</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>VALOR TOTAL</b></font></div></td>
      </tr>";
	  
$cont1 = "1";
while($row = mysql_fetch_array($result)){
if($cont1 % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[id_user]'");
$row_user = mysql_fetch_array($result_user);

$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row[id_banco]' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);

$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row[tipo]'");
$row_tipo = mysql_fetch_array($result_tipo);

$valor_soma = str_replace(",",".",$row['valor']);

if($row['adicional'] == ""){ $adicional = "&nbsp;"; }else{ $adicional = $row['adicional']; }



print "
<tr bgcolor=$color> 
        <td class=border2><font size=1 face=Arial>&nbsp;$row[0]</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$row[data3]</td>
		<td class=border2><font size=1 face=Arial>&nbsp;$row[nome]</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$row_user[0]</td>
		<td class=border2><font size=1 face=Arial>&nbsp;$row[descricao]</td>
        <td class=border2><font size=1 face=Arial>$adicional</td>
        <td class=border2><font size=1 face=Arial>$row[valor]</td>
      </tr>

";

$adicional = str_replace(",",".", $adicional);

$valor_total3 = $valor_total3 + $valor_soma + $adicional;
$cont1 ++;
}

$valor_total3 = number_format($valor_total3,2,",",".");

print "
      <tr>
        <td colspan='4' bgcolor='#CCFFCC'><div align='right' class='style12'>TOTAL DE ENTRADAS $data_ini a $data_fim:</div></td>
        <td colspan='3' bgcolor='#CCFFCC'><b>R$ $valor_total3</b></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan='2' height='15' align='center' valign='top' bgcolor='#FFFFFF' class='style3'><a href='relfinanceiro.php?regiao=$regiao'>voltar</a></td>
  </tr>

  
  <tr>
    <td width='532' height='15' align='right' valign='top' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
    <td align='center' valign='middle' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
  </tr>
  <tr>
    <td align='center' valign='middle' class='style3'></td>
    <td width='171' align='center' valign='middle' class='style3'></td>
  </tr>
  <tr valign='top'>
   <td height='37' colspan='4' bgcolor='#5C7E59'><img src='layout/baixo.gif' alt='baixo' width='750' height='38' />
   </td>
 </tr>
</table>
</body>
</html>";

break;

case 4: //                                    RELATÓRIO DE ENTRADAS E SAIDAS

$banco = $_REQUEST['banco'];
$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];

if(empty($_REQUEST['todas_contas'])){
$todas_contas = "0";
}else{
$todas_contas = $_REQUEST['todas_contas'];
}

$data_ini_f = $ano."-".$mes."-01";
$data_fim_f = $ano."-".$mes."-31"." 23:59:59";

if(empty($_REQUEST['todas_contas'])){
$result1 = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM entrada where data_proc >= '$data_ini_f' and data_proc <= '$data_fim_f' and id_banco = '$banco' and status = '2' order by data_vencimento, data_proc");

$result2 = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM saida where data_pg >= '$data_ini_f' and data_pg <= '$data_fim_f' and id_banco = '$banco' and status = '2' order by data_vencimento, data_proc");

}else{

$result1 = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM entrada 
where data_proc >= '$data_ini_f' and data_proc <= '$data_fim_f' and id_regiao = '$regiao' and status = '2' order by data_vencimento, data_proc, id_banco ASC");

$result2 = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM saida 
where data_pg >= '$data_ini_f' and data_pg <= '$data_fim_f' and id_regiao = '$regiao' and status = '2' order by data_vencimento, data_proc, id_banco ASC");

}

print "
<p id=\"excel\" style=\"text-align: right; margin-top: 20px\"><input type=\"button\" onclick=\"tableToExcel('tbRelatorio', 'Relatório')\" value=\"Exportar para Excel\" class=\"exportarExcel\"></p>
<table id='tbRelatorio' width='750' border='0' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
  <tr>
    <td colspan='4'><img src='layout/topo.gif' alt='topo' width='750' height='38' /></td>
  </tr>
  <tr>
    <td width='21' rowspan='8' background='layout/esquerdo.gif'>&nbsp;</td>
    <td colspan='2'><div align='center' class='style7'></div></td>
    <td width='26' rowspan='8' background='layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan='2' bgcolor='#CCFFCC'><div align='center'>
	<img src='imagensfinanceiro/entradas.gif' alt='fornecedor' width='25' height='25' align='absmiddle'>
	<font color='red' size=4><b>CONTROLE DE ENTRADAS DA OSCIP</b></font></div></td>
  </tr>
  <tr>
    <td colspan='2'>
	<table align='center'>
      <tr>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CÓDIGO ENTRADA</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>DATA DE RECEBIMENTO</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>NOME DO CRÉDITO</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CONTA CREDITADA</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>TIPO DE ENTRADA</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>DESCRIÇÃO</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CADASTRADA POR</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>VALOR ADICIONAL</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>VALOR TOTAL</b></font></div></td>
      </tr>";
	  
$cont1 = "1";
while($row1 = mysql_fetch_array($result1)){
if($cont1 % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row1[id_user]'");
$row_user = mysql_fetch_array($result_user);

$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row1[id_banco]' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);

$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row1[tipo]'");
$row_tipo = mysql_fetch_array($result_tipo);

$valor_soma = str_replace(",",".",$row1['valor']);
$adicional = str_replace(",",".",$row1['adicional']);

print "
<tr bgcolor=$color> 
        <td class=border2><font size='1' face=Arial>&nbsp;$row1[0]</td>
        <td class=border2><font size='1' face=Arial>&nbsp;$row1[data2]</td>
        <td class=border2><font size='1' face=Arial>&nbsp;$row1[nome]</td>
        <td class=border2><font size='1' face=Arial>AG: $row_banco[agencia] / C: $row_banco[conta]</td>
        <td class=border2><font size='1' face=Arial>&nbsp;$row_tipo[nome]</td>
        <td class=border2><font size='1' face=Arial>&nbsp;$row1[especifica]</td>
        <td class=border2><font size='1' face=Arial>&nbsp;$row_user[0]</td>
        <td class=border2><font size='1' face=Arial>$row1[adicional]</td>
        <td class=border2><font size='1' face=Arial>$row1[valor]</td>
      </tr>

";


$valor_total_banco1 = $valor_total_banco1 + $valor_soma + $adicional;
$cont1 ++;
}

$valor_total_banco1_f = number_format($valor_total_banco1,2,",",".");

print "
      <tr>
        <td colspan='7' bgcolor='#CCFFCC'><div align='right' class='style12'>TOTAL DE ENTRADAS $data_ini a $data_fim:</div></td>
        <td colspan='2' bgcolor='#CCFFCC'><b>R$ $valor_total_banco1_f </b></td>
        </tr>
    </table>
	
	<br><hr><br>
	
	<table align='center'>
	  <tr>
    <td colspan='10' bgcolor='#CCFFCC'><div align='center'>
	<img src='imagensfinanceiro/saidas.gif' alt='fornecedor' width='25' height='25' align='absmiddle'>
	<font color='red' size=4><b>CONTROLE DE SAIDA DA OSCIP</b></font></div></td>
  </tr>

      <tr>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CÓDIGO SAÍDA</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>DATA DE RECEBIMENTO</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>NOME DO CRÉDITO</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CONTA DEBITADA</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>TIPO DE SAÍDA</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>DESCRIÇÃO</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CADASTRADA POR</b></font></div></td>
	   <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>PAGO POR</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>VALOR ADICIONAL</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>VALOR TOTAL</b></font></div></td>
      </tr>";
	  
$cont2 = "1";
while($row2 = mysql_fetch_array($result2)){
if($cont2 % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row2[id_user]'");
$row_user = mysql_fetch_array($result_user);

$result_userpg = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row2[id_userpg]'");
$row_userpg = mysql_fetch_array($result_userpg);

$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row2[id_banco]' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);

$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row2[tipo]'");
$row_tipo = mysql_fetch_array($result_tipo);

$valor_soma = str_replace(",",".",$row2['valor']);
$adicional = str_replace(",",".",$row2['adicional']);

if($row2['comprovante'] == "1"){
$link_ima = "<a href='comprovantes/$row2[0]$row2[tipo_arquivo]' target='_blanck' rel='lightbox' title='Anexo'>$row2[nome]</a>";
}else{
$link_ima = "$row2[nome]";
}


print "
<tr bgcolor=$color> 
        <td class=border2><font size='1' face=Arial>&nbsp;$row2[0]</td>
        <td class=border2><font size='1' face=Arial>&nbsp;$row2[data2]</td>
        <td class=border2><font size='1' face=Arial>$link_ima</td>
        <td class=border2><font size='1' face=Arial>AG: $row_banco[agencia] / C: $row_banco[conta]</td>
        <td class=border2><font size='1' face=Arial>&nbsp;$row_tipo[nome]</td>
        <td class=border2><font size='1' face=Arial>&nbsp;$row2[especifica]</td>
        <td class=border2><font size='1' face=Arial>&nbsp;$row_user[0]</td>
		<td class=border2><font size='1' face=Arial>&nbsp;$row_userpg[0]</td>
        <td class=border2><font size='1' face=Arial>$row2[adicional]</td>
        <td class=border2><font size='1' face=Arial>$row2[valor]</td>
      </tr>

";


$valor_total_banco2 = $valor_total_banco2 + $valor_soma + $adicional;
$cont2 ++;
}

$valor_total_banco2_f = number_format($valor_total_banco2,2,",",".");

$valor_total_banco = "$valor_total_banco1" - "$valor_total_banco2";
$valor_total_banco_f = number_format($valor_total_banco,2,",",".");

print "
      <tr>
        <td colspan='7' bgcolor='#CCFFCC'><div align='right' class='style12'>TOTAL DE ENTRADAS</div></td>
        <td colspan='3' bgcolor='#CCFFCC'>R$ $valor_total_banco2_f </td>
        </tr>
    </table>
	
	
	
	</td>
  </tr>
  <tr>
    <td colspan='2' height='15' align='center' valign='top' bgcolor='#FFFFFF' class='style3'>
	<br>
	<font color=#000000 size=3>
	Total: R$ $valor_total_banco_f
	</font>
	</td>
  </tr>
  <tr>
    <td colspan='2' height='15' align='center' valign='top' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan='2' height='15' align='center' valign='top' bgcolor='#FFFFFF' class='style3'><a href='relfinanceiro.php?regiao=$regiao'>voltar</a></td>
  </tr>

  <tr>
    <td width='532' height='15' align='right' valign='top' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
    <td align='center' valign='middle' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
  </tr>
  <tr>
    <td align='center' valign='middle' class='style3'></td>
    <td width='171' align='center' valign='middle' class='style3'></td>
  </tr>
  <tr valign='top'>
   <td height='37' colspan='4' bgcolor='#5C7E59'><img src='layout/baixo.gif' alt='baixo' width='750' height='38' />
   </td>
 </tr>
</table>
</body>
</html>";

break;

case 5:

$data_now = date('Y-m-d');

$tipo_select = $_REQUEST['select'];
if($tipo_select == "1"){            //Visualizar lançamentos não pagos
$result = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM saida where id_projeto = '$projeto' and status = '1' ORDER BY data_vencimento") or die ("Erro de digitação na query1" . mysql_error());
}else{                              //Visualizar lançamentos futuros
$result = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM saida where id_projeto = '$projeto' and status = '1' and data_vencimento > '$data_now' ORDER BY data_vencimento") or die ("Erro de digitação na query2" . mysql_error());
}

print "
<p id=\"excel\" style=\"text-align: right; margin-top: 20px\"><input type=\"button\" onclick=\"tableToExcel('tbRelatorio', 'Relatório')\" value=\"Exportar para Excel\" class=\"exportarExcel\"></p>
<table id='tbRelatorio' width='750' border='0' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
  <tr>
    <td colspan='4'><img src='layout/topo.gif' alt='topo' width='750' height='38' /></td>
  </tr>
  <tr>
    <td width='21' rowspan='5' background='layout/esquerdo.gif'>&nbsp;</td>
    <td colspan='2'><div align='center' class='style7'></div></td>
    <td width='26' rowspan='5' background='layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan='2' bgcolor='#CCFFCC'><div align='center'>
	<img src='imagensfinanceiro/saidas.gif' alt='fornecedor' width='25' height='25' align='absmiddle'>
	<font color='red' size=4><b>CONTROLE DE SAI&#769;DAS DA OSCIP</b></font></div></td>
  </tr>
  <tr>
    <td colspan='2'><table align='center'>
      <tr>
      <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CÓDIGO ENTRADA</b></font></div></td>
       <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>DATA DE RECEBIMENTO</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>NOME DO CRÉDITO</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CONTA CREDITADA</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>TIPO DE ENTRADA</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>DESCRIÇÃO</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>CADASTRADA POR</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>VALOR ADICIONAL</b></font></div></td>
        <td bgcolor='#FFFFCC'><div align='center'><font size=1 face=Arial><b>VALOR TOTAL</b></font></div></td>
      </tr>";
	  
$cont1 = "1";
while($row = mysql_fetch_array($result)){
if($cont1 % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[id_user]'");
$row_user = mysql_fetch_array($result_user);

$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row[id_banco]' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);

$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row[tipo]'");
$row_tipo = mysql_fetch_array($result_tipo);

if($row['especifica'] == ""){ $descricao = "&nbsp;"; }else{ $descricao = $row['descricao']; }
if($row['adicional'] == ""){ $adicional = "&nbsp;"; }else{ $adicional = $row['adicional']; }

print "
<tr bgcolor=$color> 
        <td class=border2><font size=1 face=Arial>&nbsp;$row[0]</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$row[data2]</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$row[nome]</td>
        <td class=border2><font size=1 face=Arial>AG: $row_banco[agencia] / C: $row_banco[conta]</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$row_tipo[nome]</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$descricao</td>
        <td class=border2><font size=1 face=Arial>&nbsp;$row_user[0]</td>
        <td class=border2><font size=1 face=Arial>$adicional</td>
        <td class=border2><font size=1 face=Arial>$row[valor]</td>
      </tr>

";

$valor_soma = str_replace(",",".",$row['valor']);
$adicional = str_replace(",",".",$row['adicional']);

$valor_total2 = $valor_total2 + $valor_soma + $adicional;
$cont1 ++;
}

$valor_total2 = number_format($valor_total2,2,",",".");

print "
      <tr>
        <td colspan='6' bgcolor='#CCFFCC'><div align='right' class='style12'>TOTAL DE ENTRADAS $data_ini a $data_fim:</div></td>
        <td colspan='2' bgcolor='#CCFFCC'>$valor_total2</td>
        </tr>
    </table></td>
  </tr>
  
  <tr>
    <td width='532' height='15' align='right' valign='top' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
    <td align='center' valign='middle' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
  </tr>

  <tr>
    <td align='center' valign='middle' class='style3'></td>
    <td width='171' align='center' valign='middle' class='style3'></td>
  </tr>
  <tr valign='top'>
   <td height='37' colspan='4' bgcolor='#5C7E59'><img src='layout/baixo.gif' alt='baixo' width='750' height='38' />
   </td>
 </tr>
</table>
</body>
</html>";

break;


}

/* Liberando o resultado */


/* Fechando a conexão */
mysql_close($conn);

}
?>