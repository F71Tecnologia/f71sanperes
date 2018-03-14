<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}

include "../../conn.php";
include "../../funcoes.php";


//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$decript = explode("&",$link);

$regiao = $decript[0];
$folha = $decript[1];
$cheq = $decript[2];
$depo = $decript[3];

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
//MASTER
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
//MASTER

// FOLHA
$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM rh_folha where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

$mesINT = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mesINT];

$titulo = "Folha Sintética: Projeto $row_projeto[nome] mês de $mes_da_folha";

$ano = date("Y");
$mes = date("m");
$dia = date("d");

$data = date("d/m/Y");

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$titulo?></title>

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
<link href="../../net1.css" rel="stylesheet" type="text/css">
<link href="sintetica/folha.css" rel="stylesheet" type="text/css">
</head>

<body>

<table width="95%" border="0" align="center">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="100%" border="0" align="center">
      <tr>
        <td width="100%" height="92" align="center" valign="middle" bgcolor="#003300" class="title"><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#666666">
          <tr>
              <td width="16%" height="100" align="center"><span class="style1"><img src="../../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79" align="absmiddle" ></span></td>
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
      <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td width="7%" height="25" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">C&oacute;digo</td>
          <td width="25%" align="left" valign="middle" bgcolor="#CCCCCC" class="style23">Nome </td>
         <td width="14%" align="left" valign="middle" bgcolor="#CCCCCC" class="style23">CPF</td>
         <td width="15%" align="left" valign="middle" bgcolor="#CCCCCC" class="style23">Banco </td>
          <td width="15%" align="left" valign="middle" bgcolor="#CCCCCC" class="style23">Agência </td>
          <td width="10%" align="left" valign="middle" bgcolor="#CCCCCC" class="style23">Conta </td>
          
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. Base</td>
          
          <td width="6%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Rendim.</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Descontos </td>
          <td width="4%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">INSS</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Imp. Renda</td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. Fam. </td>
          <td width="6%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. L&iacute;q.</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Pagamento</td>
        </tr>
        <?php
          $cont = "0";
	      $tiposDePagamentos = mysql_query("SELECT * FROM tipopg WHERE id_regiao = '$regiao' and campo1 = '2' and id_projeto = '$row_projeto[0]'");
		  $rowTipoPg = mysql_fetch_array($tiposDePagamentos);
	  
		  $resultClt = mysql_query("SELECT * FROM rh_folha_proc WHERE (id_folha = '$folha' and id_banco = '0' and status = '3') or (id_folha = '$folha' and agencia = '' and status = '3') or (id_folha = '$folha' and conta = '' and status = '3')or (id_folha = '$folha' and tipo_pg = '$rowTipoPg[0]' and status = '3')");
		  while($row_clt = mysql_fetch_array($resultClt)){
			  
		  $REtabCLT  = mysql_query("SELECT A.*, B.nome as nome_banco, A.nome_banco as banco_clt FROM rh_clt as A
                                            LEFT JOIN bancos as B
                                            ON A.banco = B.id_banco 
                                            WHERE id_clt = $row_clt[id_clt]");
		  $RowTabCLT = mysql_fetch_array($REtabCLT);
		  $nome_banco = ($RowTabCLT['banco'] == '9999')? $RowTabCLT['banco_clt'] : $RowTabCLT['nome_banco'];
                    
            
                  
		  // Verificando Tipo de Pagamento
		  $qr_tipo_pg = mysql_query("SELECT tipopg FROM tipopg WHERE id_tipopg = '$row_clt[tipo_pg]'");
		 @$tipo_pg    = mysql_result($qr_tipo_pg,0);
		 
		  if(strstr($tipo_pg,'Cheque')) { 
		  
		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
		  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
		  $nome = str_split($row_clt['nome'], 30);
		  $nomeT = sprintf("% -30s", $nome[0]);
		  $bord = "style='border-bottom:#000 solid 1px;'";
		  //-----------------
		
		  //----FORMATANDO OS VALORES------------------------
		  $salario_brutoF = number_format($row_clt['salbase'],2,",",".");
		  $total_rendiF = number_format($row_clt['rend'],2,",",".");
		  $total_debitoF = number_format($row_clt['desco'],2,",",".");
		  $valor_inssF = number_format($row_clt['a5020'],2,",",".");
		  $valor_IRF = number_format($row_clt['imprenda'],2,",",".");
		  $valor_familiaF = number_format($row_clt['a5022'],2,",",".");
  
		  $valor_final_individualF = number_format($row_clt['salliquido'],2,",",".");		  		  
		  //-------------------
		  //----ARMAZENANDO VALORES PARCIAIS PARA SEREM SOMADOS MASI ADIANTE------------------------
		  $array_salario_bruto[] = $row_clt['salbase'];
		  $array_total_rendi[] = $row_clt['rend'];
		  $array_total_debito[] = $row_clt['desco'];
		  $array_valor_inss[] = $row_clt['a5020'];
		  $array_valor_IRF[] = $row_clt['imprenda'];
		  $array_valor_familia[] = $row_clt['a5022'];
		  $array_valor_final_individual[] = $row_clt['salliquido'];		  		  
		  //-------------------
		  
		  
		  echo "<tr bgcolor=$color height='20' class='valor'>";
          echo "<td align='center' valign='middle' $bord>".$row_clt['cod']." </td>";
          echo "<td align='lefth' valign='middle' $bord>".$nomeT."</td>";
          echo "<td align='left' valign='middle' $bord>".$row_clt['cpf']."</td>";
          echo "<td align='left' valign='middle' $bord>".$nome_banco."</td>";
          echo "<td align='left' valign='middle' $bord>".$RowTabCLT['agencia']."</td>";
          echo "<td align='left' valign='middle' $bord>".$RowTabCLT['conta']."</td>";
          echo "<td align='right' valign='middle' $bord>".$salario_brutoF."</td>";
	
          echo "<td align='right' valign='middle' $bord>".$total_rendiF."</td>";
          echo "<td align='right' valign='middle' $bord>".$total_debitoF."</td>";
          echo "<td align='right' valign='middle' $bord>".$valor_inssF."</td>";
          echo "<td align='right' valign='middle' $bord>".$valor_IRF."</td>";
          echo "<td align='right' valign='middle' $bord>".$valor_familiaF."</td>";
          echo "<td align='right' valign='middle' $bord>".$valor_final_individualF."</td>";
 		  echo "<td align='right' valign='middle' $bord>".'Cheque'."</td></tr>";
		  
		// AQUI TERMINA O LAÇO ONDE MOSTRA E CALCULA OS VALORES REFERENTES A UM ÚNICO FUNCIONARIO
		// FORMATANDO OS DADOS FINAIS
		
		$cont ++;
		  }
		}
	  	$salario_base_finalF = @array_sum($array_salario_bruto);
	  	$rendi_indiviF = @array_sum($array_total_rendi);
	  	$final_indiviF = @array_sum($array_total_debito);
	  	$final_INSSF = @array_sum($array_valor_inss);
	  	$final_IRF = @array_sum($array_valor_IRF);
	  	$final_familiaF = @array_sum($array_valor_familia);
	  	$valor_finalF = @array_sum($array_valor_final_individual);
		
		//---- FORMATANDO OS TOTAIS GERAIS DA FOLHA -----------
		$salario_base_finalF = number_format($salario_base_finalF,2,",",".");
		$rendi_indiviF = number_format($rendi_indiviF,2,",",".");
		$final_indiviF = number_format($final_indiviF,2,",",".");
		$final_INSSF = number_format($final_INSSF,2,",",".");
		$final_IRF = number_format($final_IRF,2,",",".");
		$final_familiaF =  number_format($final_familiaF,2,",",".");;
		$valor_finalF = number_format($valor_finalF,2,",",".");
		//----------------------			
		?>
        
         <tr>
          <td height="20" align="center" valign="middle" class="style23">&nbsp;</td>
          <td height="20" align="right" valign="bottom" class="style23">TOTAIS:</td>
          <td colspan="2" align="right" valign="bottom" class="style23"><?=$salario_base_finalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$rendi_indiviF?></td>
          <td align="right" valign="bottom" class="style23"><?=$final_indiviF?></td>
          <td align="right" valign="bottom" class="style23"><?=$final_INSSF?></td>
          <td align="right" valign="bottom" class="style23"><?=$final_IRF?></td>
          <td align="right" valign="bottom" class="style23"><?=$final_familiaF?></td>
          <td align="right" valign="bottom" class="style23"><?=$valor_finalF?></td>
        </tr>
      </table>
      <br>
      <br>
      <?=$cont." Participantes<br/>"?>
      <br />
      <?=$MsgBanc?>
      <br>
      <br>
      <?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvolt = encrypt("$regiao&$folha"); 
$linkvolt = str_replace("+","--",$linkvolt);
// -----------------------------
/*
//TENTA ABRIR O ARQUIVO TXT
if (!$abrir = fopen($arquivo, "wa+")) {
echo "Erro abrindo arquivo ($arquivo)";
exit;
}

//ESCREVE NO ARQUIVO TXT
if (!fwrite($abrir, $PARTE1)) {
print "Erro escrevendo no arquivo ($arquivo)";
exit;
}

//FECHA O ARQUIVO
fclose($abrir);
*/
?>
<br></td>
  </tr>
  <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC">
    <b><a href='ver_folha.php?enc=<?=$linkvolt?>' style="text-decoration:none; color:#000">VOLTAR</a></b>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>