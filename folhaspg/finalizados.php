<?
include "../conn.php";
include "../funcoes.php";

$regiao = $_GET['regiao']; 
$folha = $_GET['folha'];
$projeto = $_GET['projeto'];
$banco = $_GET['banco'];
?>

<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}
//MASTER
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
//MASTER

// FOLHA
$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM folhas where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mesINT = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mesINT];

$result_folha_pro = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' and status = '3'");

$titulo = "Folha Sintética: Projeto $row_projeto[nome] mês de $mes_da_folha";

$ano = date("Y");
$mes = date("m");
$dia = date("d");

$data = date("d/m/Y");

$RE_TipoDepo = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '1'");
$row_TipoDepo = mysql_fetch_array($RE_TipoDepo);

$RE_TIpoCheq = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '2'");
$row_TIpoCheq = mysql_fetch_array($RE_TIpoCheq);


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$titulo?></title>
<link href="../net.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="95%" border="0" align="center">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center">
      <tr>
        <td width="100%" height="92" align="center" valign="middle" bgcolor="#666666" class="title"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="16%" height="100" align="center" bgcolor="e2e2e2"><span class="style1"><img src="../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79" align="absmiddle" ></span></td>
            <td width="62%" bgcolor="e2e2e2"><span class="style3">
              <?=$row_master['razao']?><br>
              CNPJ : <?=$row_master['cnpj']?>
              <br>
            </span></td>
            <td width="22%" bgcolor="e2e2e2">
            <span class="style3">
            Data de Processamento: <br>
            <?=$row_folha['data_proc2']?></span></td>
            </tr>
        </table></td>
      </tr>
    </table>
      <br />
      <span class="title">Folha de Pagamento Finalizada - 
      <?=$mes_da_folha?> / <?=$row_folha['ano']?></span><br />
      <span class="title"><br />
    </span>

<body>

<?
$resultFinalizados = mysql_query("SELECT * FROM folha_autonomo WHERE status = '4' and regiao = '$regiao' and projeto = '$projeto' and id_folha = '$folha' and banco=$banco") or die(mysql_error());
$quantRetornado = mysql_affected_rows();
?>
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="0" style="font-size:12px; line-height:24px;">
<tr height="20px" style="font-weight:bold;">
   <?
     if ($quantRetornado != 0 ){
			 print '<td width="29%" align="left"   valign="middle" bgcolor="#CCCCCC" class="style23">Nome</td>';
			 print '<td width="10%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Salário Base</td>';
			 print '<td width="12%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Rendimentos</td>';
			 print '<td width="13%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Descontos</td>';
			 print '<td width="12%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Faltas</td>';
			 print '<td width="14%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Salário L&iacute;quido</td>';
			 print '</tr>';
			 $cont = 0;
			 while ($row = mysql_fetch_array($resultFinalizados)){
					$sal_baseF = number_format($row['salario'],2,",",".");
					$rendiF = number_format($row['adicional'],2,",",".");
					$descoF = number_format($row['desconto'],2,",",".");
					$sal_liqF = number_format($row['salario_liq'],2,",",".");
					//-- FORMATO USA
					$sal_liqT = number_format($sal_liq,2,".","");
					//---- EMBELEZAMENTO DA PAGINA ----------------------------------
					if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
					$nome = str_split($row['nome'], 30);
					$nomeT = sprintf("% -30s", $nome[0]);
					$bord = "style='border-bottom:#000 solid 1px;'";
					//-----------------
					print"
					<tr>
					<tr height='20' class='style28' bgcolor=$color>
					<td align='left'  valign='middle' $bord>$nomeT</td>
					<td align='center' valign='middle' $bord>$sal_baseF</td>
					<td align='center' valign='middle' $bord>$rendiF</td>
					<td align='center' valign='middle' $bord>$descoF</td>
					<td align='center' valign='middle' $bord>$row[faltas]</td>
					<td align='center' valign='middle' $bord>$sal_liqF</td>
					</tr>";
					$sal_base01[] = $row['salario'];
					$sal_liq01[] = $row['salario_liq'];
					$rendi01[] = $row['adicional'];
					$desco01[] = $row['desconto'];
					

					//-- LIMPANDO VARIAVEIS
					$sal_base = "";
					$sal_liq = "";
					$rendi = "";
					$desco = "";
					$faltas = "";
					$dias_trab = "";
					$diaria = "";
					$cont++;
		  
		   }
		   
		   //-- SOMANDO VARIAVEIS PARA OS TOTAIS --//
		   $TOsal_base = array_sum($sal_base01);
		   $TOsal_liq = array_sum($sal_liq01);
		   $TOrendi = array_sum($rendi01);
		   $TOdesco = array_sum($desco01);
		  		   
		   //-- FORMATANDO OS TOTAIS FORMATO BRASILEIRO--//
		   $TOsal_baseF = number_format($TOsal_base,2,",",".");
		   $TOsal_liqF = number_format($TOsal_liq,2,",",".");
		   $TOrendiF = number_format($TOrendi,2,",",".");
		   $TOdescoF = number_format($TOdesco,2,",",".");
		   print '<tr style="font-weight:bold;">';
           print '<td height="20" align="right" valign="bottom" class="style23">TOTAIS:</td>';
           print '<td align="center" valign="bottom" class="style23">'.$TOsal_baseF.'</td>';
           print '<td align="center" valign="bottom" class="style23">'.$TOrendiF.'</td>';
           print '<td align="center" valign="bottom" class="style23">'.$TOdescoF.'</td>';
           print '<td align="center" valign="bottom" class="style23">&nbsp;</td>';
           print '<td align="center" valign="bottom" class="style23">'.$TOsal_liqF.'</td>';		   
           print '</tr>';
		   
		   //TOTALIZADORES
		   print '<br/>';
		   print '<table width="30%" border="0" align="center" cellpadding="0" cellspacing="0">';
           print '<tr>';
           print '<td height="24" colspan="2" align="center" valign="middle" bgcolor="#CCCCCC" class="title"><span class="linha">TOTALIZADORES</span></td>';
           print '</tr>';
           print '<tr>';
           print '<td width="46%" height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha"> Sal&aacute;rio L&iacute;quido:</span></td>';
           print '<td width="54%" height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha"> &nbsp;&nbsp;<span class="style23">'.$TOsal_liqF.'</span></span></td>';
           print '</tr>';
           print '<tr>';
           print '<td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Sal&aacute;rio Base:</span></td>';
           print '<td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha"><span class="style23"> &nbsp;&nbsp;'.$TOsal_baseF.'</span></td>';
           print '</tr>';
           print '<tr>';
           print '<td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Desconto:</span></td>';
           print '<td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;<span class="style23">'.$TOdescoF.'</span></td>';
           print '</tr>';
           print '<tr>';
           print '<td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Rendimento:</span></td>';
           print '<td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;<span class="style23">'.$TOrendiF.'</span></td>';
           print '</tr>';
           print '<tr>';
           print '<td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Funcion&aacute;rios Listados:</span></td>';
           print '<td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;'.$cont.'</td>';
           print '</tr>';
           print '</table>';
		   print '<br/>';	
	 }else{
	 		print '<tr align=center><td><span style="color:red"> <b>NÃO HÁ FUNCIONÁRIO FINALIZADO</b> </span></td></tr>';
	 }
    ?>
      </table>
	<?php
    
    //-- ENCRIPTOGRAFANDO A VARIAVEL
    $linkvolt = encrypt("$regiao&$regiao"); 
    $linkvolt = str_replace("+","--",$linkvolt);
    // -----------------------------
    
    ?>
	
    </table>
    <br/>
    <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
  		<tr>
    		<td align="center" valign="middle" bgcolor="#CCCCCC"><b><a href='folha.php?id=9&<?="enc=".$linkvolt."&tela=1"?>' style="text-decoration:none; color:#000">VOLTAR</a></b></td>
  		</tr>
	</table>

</body>
</html>