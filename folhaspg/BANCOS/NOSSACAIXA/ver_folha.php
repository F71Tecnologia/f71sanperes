<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}

include "../conn.php";
include "../funcoes.php";


//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$decript = explode("&",$link);

$regiao = $decript[0];
$folha = $decript[1];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA
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
<script language='javascript'>

   function mascara_data(d){  
       var mydata = '';  
       data = d.value;  
       mydata = mydata + data;  
       if (mydata.length == 2){  
          mydata = mydata + '/';  
          d.value = mydata;  
       }  
          if (mydata.length == 5){  
          mydata = mydata + '/';  
          d.value = mydata;  
       }  
          if (mydata.length == 10){  
          verifica_data(d);  
         }  
      } 
           
         function verifica_data (d) {  

         dia = (d.value.substring(0,2));  
         mes = (d.value.substring(3,5));  
         ano = (d.value.substring(6,10));  
             

       situacao = "";  
       // verifica o dia valido para cada mes  
       if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
           situacao = "falsa";  
       }  

       // verifica se o mes e valido  
       if (mes < 01 || mes > 12 ) {  
              situacao = "falsa";  
       }  

      // verifica se e ano bissexto  
      if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
            situacao = "falsa";  
      }  
   
     if (d.value == "") {  
          situacao = "falsa";  
    }  

    if (situacao == "falsa") {  
       alert("Data digitada é inválida, digite novamente!"); 
       d.value = "";  
       d.focus();  
    }  
	
}
</script>
</head>

<body>
<table width="95%" border="0" align="center">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center">
      <tr>
        <td width="100%" height="92" align="center" valign="middle" bgcolor="#666666" class="title"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="16%" height="100" align="center"><span class="style1"><img src="../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79" align="absmiddle" ></span></td>
            <td width="62%"><span class="style1">
              <?=$row_master['razao']?><br>
              CNPJ : <?=$row_master['cnpj']?>
              <br>
            </span></td>
            <td width="22%">
            <span class="style1">
            Data de Processamento: <br>
            <?=$row_folha['data_proc2']?></span></td>
            </tr>
        </table></td>
      </tr>
    </table>
      <br />
      <span class="title">Folha de Pagamento - 
      <?=$mes_da_folha?> / <?=$ano?></span><br />
      <span class="title"><br />
    </span>
    <?
		//VERIFICA SE A FOLHA JÁ FOI FINALIZADA
		$resultStatusFolha = mysql_query("SELECT status FROM folhas WHERE id_folha = $folha");
		$rowStatusFolha = mysql_fetch_array($resultStatusFolha);
		if ($rowStatusFolha[0] == '4'){
				print "<span style='color:red; font-family:verdana, areal'> <strong>FINALIZADA</strong> </span>";
		}
	?>
    <br/>
    <br/>
      <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>          
          <td width="29%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Nome</td>
          <td width="10%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. Base</td>
          <td width="12%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Rendim.</td>
          <td width="13%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Descontos</td>
          <td width="12%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Faltas</td>
          <td width="14%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. L&iacute;q.</td>
        </tr>
        <?php
          $cont = "0";
		  while($row = mysql_fetch_array($result_folha_pro)){
		  
		  $REparti = mysql_query("SELECT * FROM autonomo where id_autonomo = '$row[id_autonomo]'");
		  $rowP = mysql_fetch_array($REparti);
		  
		  $result_curso = mysql_query("SELECT * FROM curso where id_curso = '$rowP[id_curso]'");
		  $row_curso = mysql_fetch_array($result_curso);
	  
		  //-- FORMATANDO NO FORMATO BRASILEIRO --
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
		  <tr height='20' class='style28' bgcolor=$color>
          <td align='center' valign='middle' $bord> $rowP[campo3]</td>
          <td align='lefth' valign='middle' $bord>$nomeT</td>
          <td align='right' valign='middle' $bord>$sal_baseF</td>
          <td align='right' valign='middle' $bord>$rendiF</td>
          <td align='right' valign='middle' $bord>$descoF</td>
          <td align='right' valign='middle' $bord>$row[faltas]</td>
          <td align='right' valign='middle' $bord>$sal_liqF</td>
		  </tr>";
		 
		  /*
		  print $row['id_autonomo'].'<br>';
		  print $row['conta'].'<br>';
		  print $row['agencia'].'<br>';
		  print $rowP['tipo_conta'].'<br>';
		  print $row['tipo_pg'].'<br>';
		  print $folha.'<br>';
		  print '( '.$row[0].' )';		  		  
		  */
		  
	  
		 $tiposDePagamentos = mysql_query("SELECT * FROM tipopg WHERE id_regiao = '$regiao' and campo1 = '2' and id_projeto = '$row_projeto[0]'");
	     $rowTipoPg = mysql_fetch_array($tiposDePagamentos);
	  	  $pgEmCheque = $rowTipoPg[0];		  
		  
		  if (($row['conta'] == '') or ($row['conta'] == '0')){
		  		mysql_query("UPDATE folha_autonomo SET tipo_pg = '$pgEmCheque' WHERE id_folha = '$folha' and id_autonomo = $row[id_autonomo]");
		  }
		  if (($row['agencia'] == '') or ($row['agencia'] == '0')){
		  		mysql_query("UPDATE folha_autonomo SET tipo_pg = '$pgEmCheque' WHERE id_folha = '$folha' and id_autonomo = $row[id_autonomo]");
		  }if ($rowP['tipo_conta'] == ''){
		  			  		mysql_query("UPDATE folha_autonomo SET tipo_pg = '$pgEmCheque' WHERE id_folha = '$folha' and id_autonomo = $row[id_autonomo]");
		  }		  

		  $PARTE1 = $PARTE1."UPDATE folha_autonomo SET salario_liq = '$sal_liqT' WHERE id_folha_pro = '$row[0]';\r\n";
		  $cont ++;
		  //-- SOMANDO VARIAVEIS PARA OS TOTAIS --//
		  $TOsal_base = $TOsal_base + $sal_base;
		  $TOsal_liq = $TOsal_liq + $sal_liq;
		  $TOrendi = $TOrendi + $rendi;
		  $TOdesco = $TOdesco + $desco;
		  
		  //-- LIMPANDO VARIAVEIS
		  $sal_base = "";
		  $sal_liq = "";
		  $rendi = "";
		  $desco = "";
		  $faltas = "";
		  $dias_trab = "";
		  $diaria = "";
		  
		  }
		
		
		//-- FORMATANDO OS TOTAIS FORMATO BRASILEIRO--//
		  $TOsal_baseF = number_format($row_folha['total_bruto'],2,",",".");
		  $TOsal_liqF = number_format($row_folha['total_liqui'],2,",",".");
		  $TOrendiF = number_format($row_folha['rendimentos'],2,",",".");
		  $TOdescoF = number_format($row_folha['descontos'],2,",",".");
		
		?>
        <tr>
          <td height="20" align="center" valign="middle" class="style23">&nbsp;</td>
          <td height="20" align="right" valign="bottom" class="style23">TOTAIS:</td>
          <td align="right" valign="bottom" class="style23"><?=$TOsal_baseF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOrendiF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOdescoF?></td>
          <td align="right" valign="bottom" class="style23">&nbsp;</td>
          <td align="right" valign="bottom" class="style23"><?=$TOsal_liqF?></td>
        </tr>
      </table>
      <br />
      <br>
      <table width="30%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="24" colspan="2" align="center" valign="middle" bgcolor="#CCCCCC" class="title"><span class="linha">TOTALIZADORES</span></td>
        </tr>
        <tr>
          <td width="46%" height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha"> Sal&aacute;rio L&iacute;quido:</span></td>
          <td width="54%" height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha"> &nbsp;&nbsp;<span class="style23">
            <?=$TOsal_liqF?>
          </span></span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Sal&aacute;rio Base:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha"><span class="style23"> &nbsp;&nbsp;
            <?=$TOsal_baseF?>
          </span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Desconto:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;<span class="style23">
            <?=$TOdescoF?>
          </span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Rendimento:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;<span class="style23">
            <?=$TOrendiF?>
          </span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Funcion&aacute;rios Listados:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;
            <?=$cont?></td>
        </tr>
      </table>
      <br>
      <br>
      
<?php
//VERIFICA SE A FOLHA JÁ FOI FINALIZADA
if ($rowStatusFolha[0] != '4'){
		//VERIFICANDO QUAIS BANCOS ESTÃO ENVOLVIDOS COM ESSA FOLHA DE PAGAMENTO
		$RE_Bancs = mysql_query("SELECT banco FROM folha_autonomo where id_folha = '$folha' and status = '3' and banco != '0' group by banco");
		$num_Bancs = mysql_num_rows($RE_Bancs);
		echo "<table border='0' width='50%' border='0' cellpadding='0' cellspacing='0'>";
		echo "<tr><td colspan=5 $bord align='center'><div style='font-size: 17px;'><b>Lista de Bancos</b></div></td></tr>";
		$contCol = 0;
		while($row_Bancs = mysql_fetch_array($RE_Bancs)){		  
				$RE_ToBancs = mysql_query("SELECT banco FROM folha_autonomo where id_folha = '$folha' and status = '3' and banco = '$row_Bancs[0]' and tipo_pg = '$row_TipoDepo[0]'");			 
		  		$num_ToBancs = mysql_num_rows($RE_ToBancs);		  
		  		$RE_Bancos = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_Bancs[0]'");
		  		$row_Bancos = mysql_fetch_array($RE_Bancos);		  	  
		  		//-- ENCRIPTOGRAFANDO A VARIAVEL
				$linkBanc = encrypt("$regiao&$row_Bancos[0]&$folha"); 
				$linkBanc = str_replace("+","--",$linkBanc);
		  		// -----------------------------
		  		$linkBank = "folha_banco.php?enc=$linkBanc";		  
		  		echo "<tr>";
		 		echo "<td align='center' valign='middle' width='30' $bord>";
		  		echo "<img src=../imagens/bancos/$row_Bancos[id_nacional].jpg  width='25' height='25' align='absmiddle' border='0'></td>";
          		echo "<td valign='middle' $bord><div style='font-size: 15px;'>&nbsp;&nbsp;".$row_Bancos['nome']."</div></a></td>";		  
		  		echo "<td valign='center' $bord> <form id='form$contCol' name='form$contCol' method='post' action='$linkBank'>&nbsp;<label id='data_pag$contCol' style='display:none'><input name='data' type='text' id='data[]' size='10' class='campotexto' onKeyUp='mascara_data(this)' maxlength='10' onFocus=\"this.style.background='#CCFFCC'\"
		  onBlur=\"this.style.background='#FFFFFF'\" style='background:#FFFFFF' > <input name='enviar' id='Enviar[]' type='submit' value='Gerar'/> </label></td>";
		  		echo "</form>";		  
		  		echo "<td align='center' valign='middle' width='5%' $bord><a style='TEXT-DECORATION: none;'>		  <img src='../rh/folha/imagens/ver_banc.png' border='0' alt='Visualizar Funcionarios por Banco' style='cursor:hand' onClick=\"document.all.data_pag$contCol.style.display = (document.all.data_pag$contCol.style.display == 'none') ? '' : 'none' ;\"></a></td>";		  
		  		echo "<td align='center' valign='middle' width='10%' $bord>$num_ToBancs Participantes</td>";  		  		
		  		$contCol ++ ;
		}
	  
	   	//VERIFICA OS TIPOS DE PAGAMENTOS DA REGIÃO E PROJETO ATUAL
		$tiposDePagamentos = mysql_query("SELECT * FROM tipopg WHERE id_regiao = '$regiao' and campo1 = '2' and id_projeto = '$row_projeto[0]'");
		$rowTipoPg = mysql_fetch_array($tiposDePagamentos);
		$RE_ToCheq = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' and tipo_pg = '$rowTipoPg[id_tipopg]' and status = '3' or (id_folha = '$folha' and banco = '0' and status = '3') or (id_folha = '$folha' and agencia = '' and status = '3') or (id_folha = '$folha' and conta = '' and status = '3')");	 
	  	$num_ToCheq = mysql_num_rows($RE_ToCheq);	  
	  	//-- ENCRIPTOGRAFANDO A VARIAVEL
		$linkcheque = encrypt("$regiao&$folha&$row_TIpoCheq[0]&$row_TipoDepo[0]");
		$linkcheque = str_replace("+","--",$linkcheque);
	  	// -----------------------------	  
		echo "<tr>";
		echo "<td align='center' valign='middle' width='30' $bord>";
		echo "<img src=../imagens/bancos/cheque.jpg  width='25' height='25' align='absmiddle' border='0'></td>";
        echo "<td valign='middle' $bord><div style='font-size: 15px;'>&nbsp;&nbsp;Cheque</div></a></td>";
		echo "<td valign='center' $bord>&nbsp;</td>";
		echo "<td align='center' valign='middle' width='15%' $bord><a href='ver_cheque.php?enc=$linkcheque'><img src='../rh/folha/imagens/ver_banc.png' border='0' alt='Visualizar Funcionarios por Cheque'></a></td>";
		echo "<td align='center' valign='middle' width='10%' $bord>$num_ToCheq Participantes</td>"; 
	  	echo "</tr></table>";
}

		print "<a href=finalizados.php?regiao=$regiao&folha=$folha&projeto=$row_projeto[0]> FINALIZADAS </a>";
	  ?>
       
      
      <br>
<br>
<?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvolt = encrypt("$regiao&$regiao"); 
$linkvolt = str_replace("+","--",$linkvolt);
// -----------------------------

?>
<br></td>
  </tr>
  <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC">
    <b><a href='folha.php?id=9&<?="enc=".$linkvolt."&tela=1"?>' style="text-decoration:none; color:#000">VOLTAR</a></b>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>