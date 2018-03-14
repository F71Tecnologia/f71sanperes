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
$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM folhas where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mesINT = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mesINT];

//SELECIONANDO TODOS OS QUE JA ESTÃO MARCADOS PARAR RECEBER EM CHEQUE E AQUELES QUE ESTÃO PARA RECEBER PELO BANCO POREM ESTÃO SEM BANCO CADASTRADO
$result_folha_pro = mysql_query("SELECT * FROM folha_autonomo where id_folha = '$folha' and status = '3' and tipo_pg = '$cheq' or 
id_folha = '$folha' and status = '3' and banco = '0' and tipo_pg = '$depo'");

//CONTANDO AQUELES QUE ESTÃO MARCADOS PARA RECEBER PELO BANCO POREM ESTÃO SEM BANCO CADASTRADO
$RE_SemBanc = mysql_query("SELECT * FROM folha_autonomo where id_folha = '$folha' and status = '3' and banco = '0' and tipo_pg = '$depo'");
$Num_SemBanc = mysql_num_rows($RE_SemBanc);

if($Num_SemBanc >= 1){
	//RESOLVENDO O NOME DO ARQUIVO QUE LISTA TODOS OS CADASTROS QUE ESTÃO COMO DEPOSITO MAS ESTÃO RECEBENDO EM CHEQUE POR ERRO NO CADASTRO
	$nome_arquivo_download = "errobanc.txt";
	$arquivo = "/home/ispv/public_html/intranet/arquivos/folhaautonomo/".$nome_arquivo_download;
	$LinkArqDown = "../arquivos/folhaautonomo/".$nome_arquivo_download;

	while($row_SemBanc = mysql_fetch_array($RE_SemBanc)){
		$PARTE1 = $PARTE1."$row_SemBanc[nome]\r\n";
	}
	
	$MsgBanc = "<div style='color=#FF0000'>Atenção, existem $Num_SemBanc funcionarios listados acima que estão cadastrados para receber em 
	Depósito Bancario, porem falta escolher o banco para pagamento! </div><a href='$LinkArqDown' style='text-decoration:none'>Veja a Lista</a><br>";
}

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
<link href="../../net1.css" rel="stylesheet" type="text/css" />
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
      <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td width="10%" height="25" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">C&oacute;digo</td>
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
		  $sal_base = number_format($row['salario'],2,".","");
		  $sal_liq = number_format($row['salario_liq'],2,".","");
		  $rendi = number_format($row['adicional'],2,".","");
		  $desco = number_format($row['desconto'],2,".","");
		  

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
		  $TOsal_baseF = number_format($TOsal_base,2,",",".");
		  $TOsal_liqF = number_format($TOsal_liq,2,",",".");
		  $TOrendiF = number_format($TOrendi,2,",",".");
		  $TOdescoF = number_format($TOdesco,2,",",".");
		
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
      <?=$MsgBanc?>
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
      <?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvolt = encrypt("$regiao&$folha"); 
$linkvolt = str_replace("+","--",$linkvolt);
// -----------------------------

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