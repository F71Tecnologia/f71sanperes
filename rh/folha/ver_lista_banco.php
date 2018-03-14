<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}
if(!empty($_REQUEST['agencia'])){
	include "../../conn.php";
	$nome = $_REQUEST['nome'];
	$cpf = $_REQUEST['cpf'];
	$banco = $_REQUEST['banco'];
	$ag = $_REQUEST['agencia'];
	$cc = $_REQUEST['conta'];
	
	$enc = $_REQUEST['enc'];
	$enc2 = str_replace("+","--",$enc);
	
	$clt = $_REQUEST['clt'];
	$id = $_REQUEST['id'];
	$id_folha = $_REQUEST['id_folha'];
	$tipopg = $_REQUEST['tipopg'];


	$tipo_conta = $_REQUEST['radio_tipo_conta'];
 	$RE_clt = mysql_query("SELECT * FROM rh_folha_proc where id_clt = '$id' and status = 3") or die (mysql_error());
	
	$RowCLT = mysql_fetch_array($RE_clt);
	
	mysql_query("UPDATE rh_clt SET nome='$nome', cpf='$cpf', banco='$banco',agencia='$ag', conta='$cc', tipo_conta='$tipo_conta', tipo_pagamento='$tipopg' WHERE id_clt = '$id'") or die (mysql_error());
	
	mysql_query("UPDATE rh_folha_proc SET nome='$nome', cpf='$cpf', id_banco='$banco', agencia='$ag', conta='$cc', tipo_pg='$tipopg' WHERE id_clt = '$id' and id_folha = '$id_folha'") or die (mysql_error());
	
	/*
	print "<div style='backgroud:red'>";
	echo "NOME: ".$nome."<BR>";
	echo "CPF: ".$cpf."<BR>";
	echo "Banco: ".$banco."<BR>";
	//echo "tipo: ".$radio_tipo_conta."<BR>";
	echo "AG:".$ag."<BR>";
	echo "CC:".$cc."<BR>";
	echo "CLT:".$clt."<BR>";
	echo "ID: ".$id."<BR>";
	echo "TIPO:".$tipo_conta."<BR>";
	echo "Tipo pg:".$tipopg."<BR>";
	echo "folha:".$id_folha."<BR>";
	//echo "<a href='folha_banco.php?enc=$enc'>Continuar</a>";
	print "</div>";
	
	exit;
	
	/*print"
	<script>
	location.href=\"ver_lista_banco.php?enc=$enc2\"
	</script>";
	
	
	exit;
	*/
        
        print"
	<script>
	location.href=\"ver_lista_banco.php?enc=$enc2\"
	</script>";
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
//RECEBENDO A VARIAVEL CRIPTOGRAFADA
//MASTER
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
//MASTER

// FOLHA
$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio2,date_format(data_fim, '%d/%m/%Y')as data_fim2 FROM rh_folha where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mesINT = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mesINT];

//$result_folha_pro = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' and status = '3'");
$result_folha_pro = mysql_query("SELECT A.*, B.agencia, B.agencia_dv, B.conta, B.conta_dv
                        FROM rh_folha_proc AS A
                                LEFT JOIN rh_clt AS B ON (B.id_clt = A.id_clt)
                        WHERE (A.id_folha = '67' and A.status = '3') or (A.id_folha = '67' and A.status = '4') and A.status='3' 
                        ORDER BY A.id_banco, A.nome")or die(mysql_error());

$titulo = "Folha PG: Projeto $row_projeto[nome] mês de $mes_da_folha";

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
<script type="text/javascript" src="../../js/prototype.js"></script>
<script type="text/javascript" src="../../js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="../../js/lightbox.js"></script>
<script type="text/javascript" src="../../js/highslide-with-html.js"></script>
<script src="../../js/jquery-1.10.2.min.js"></script>
<script src="../../resources/js/tooltip.js"></script>
<script src="../../resources/js/main.js" type="text/javascript"></script>
<script src="../../js/global.js"></script>
<link rel="stylesheet" href="../../js/lightbox.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" />

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$titulo?></title>

<script type="text/javascript">
    hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>
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
<style type="text/css">
a:visited {font-size: 10px; color: #F00; text-decoration: none; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif;}
a:link{font-size: 10px; color:#F00; text-decoration: none; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif;}


</style>
<link href="../../net1.css" rel="stylesheet" type="text/css">
</head>
<body>
<table  width="95%" border="0" align="center" class="bordaescura1px">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center">
        <tr>
          <td width="100%" height="101" align="center" class="show">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="16%" align="center">
              <img src="../../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79" align="absmiddle" ></td>
              <td width="62%">
                <?=$row_master['razao']?>
                <br>
                CNPJ :
                <?=$row_master['cnpj']?>
                <br>
                </td>
              <td width="22%">Data de Processamento:
                <?=$row_folha['data_proc2']?>
                <br>
                Data Inicio da folha:
                <?=$row_folha['data_inicio2']?>
              </td>
            </tr>
          </table></td>
        </tr>
      </table>
      <br />
      <span class="titulo_opcoes">Folha de Pagamento - 
      <?=$mes_da_folha?> / <?=$row_folha['ano']?></span><br />
      <br />
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
    <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="btn btn-success"> <button type="button" form="formPdf" name="pdf" data-title="Folha de Pagamento - <?=$mes_da_folha?> / <?=$row_folha['ano']?>" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button></p>    
      
    <table id="tbRelatorio" width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr class="novo_tr_dois">
          <td height="25" align="center">C&oacute;digo</td>
          <td align="center">Nome</td>
          <td align="center">Tipo Pagamento</td>
          <td align="center">Banco</td>
          <td align="center">Agência</td>
          <td align="center">Agência DV</td>
          <td align="center">Conta</td>
          <td align="center">Conta DV</td>
          <td align="center">CPF</td>
          <td align="center">Tipo de conta</td>
          <td align="center">Sal. L&iacute;q.</td>
        </tr>     
        <?php
        
        
          $cont = "0";

		  
		  while($row = mysql_fetch_array($result_folha_pro)){
		 $REparti = mysql_query("SELECT * FROM rh_clt where id_clt = '$row[id_clt]'");
		  $rowP = mysql_fetch_array($REparti);
		  $pg = $rowP['tipo_pagamento'];
		  $result_curso = mysql_query("SELECT * FROM curso where id_curso = '$rowP[id_curso]'");
		  $row_curso = mysql_fetch_array($result_curso);	 
		
                 
                  
                  
		  //-- FORMATANDO NO FORMATO BRASILEIRO --
		  $id_banco = $row['id_banco'];
		  $resultNomeBanco = mysql_query("SELECT nome FROM bancos WHERE id_banco = '$id_banco'");
		  $rowNomeBanco = mysql_fetch_array($resultNomeBanco);
		  $nomeBanco = $rowNomeBanco[0];
		  if($nomeBanco == ''){
			  $nomeBanco = $rowP['nome_banco'];
		  }
		  
		  $agencia = $row['agencia'];
                  $agDv = $row['agencia_dv'];
		  $conta = $row['conta'];
                  $conDv = $row['conta_dv'];
		  if ($rowP['tipo_conta'] == 'corrente'){
		  		$tipoConta = 'Conta Corrente';
		  }else if ($rowP['tipo_conta'] == 'salario'){
					$tipoConta = 'Conta Salario';
		  }else{
		  		$tipoConta = '&nbsp;';
		  }
		  
                   
                  
		$tipoR = $rowP['tipo_conta'];
		if ($tipoR == 'salario'){
			$checkedSalario = 'checked';	
		}else if ($tipoR == 'corrente'){
			$checkedCorrente = 'checked';
		}
		  $cpf = $row['cpf'];
		  $valor = number_format($row['salliquido'],2,".","");

		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
		  $color = ($cont++ % 2) ? "corfundo_um" : "corfundo_dois";
		  $nome = str_split($row['nome'], 30);
		  $nomeT = sprintf("% -30s", $nome[0]);
		  $bord = "style='border-bottom:#000 solid 1px;'";
		  //-----------------

		  $tiposDePagamentos = mysql_query("SELECT * FROM tipopg WHERE id_tipopg = '$pg'");
                  $rowTipoPg2 = mysql_fetch_assoc($tiposDePagamentos);
	  	  $pgEmCheque = $rowTipoPg2['id_tipopg'];		  
		  
		  $resultCheque = mysql_query("SELECT tipo_pg, id_banco FROM rh_folha_proc WHERE id_folha = '$folha' and id_clt = $row[id_clt] and status = '3'");
		  $rowTipoPg = mysql_fetch_array($resultCheque);
		  
		  if ($rowTipoPg[0] == $pgEmCheque){
			  $option ="<option value='$pgEmCheque'>Cheque</option><option value='$row_TipoDepo[0]' >Depósito</option>";		  	
		  }else{
		  		$option ="<option value='$row_TipoDepo[0]' >Depósito</option><option value='$pgEmCheque'>Cheque</option>";
		  }
		  
//			$alink = "<a href='#' onclick=\"return hs.htmlExpand(this, { outlineType: 'rounded-white', 
//			wrapperClassName: 'draggable-header',headingText: '$nomeT' } )\" class='highslide' style='color:#000;'>";
			
			$NovoBanco = "";
			$result_banco = mysql_query("SELECT * FROM bancos where id_regiao = '$regiao' and id_projeto = '$row_projeto[0]'");
			$result_bancoAUT = mysql_query("SELECT nome FROM bancos where id_banco = '$row[banco]'");
			$RowBancAUT = mysql_fetch_array($result_bancoAUT);
		
			$N = $row['id_clt'];
			$NovoBanco .="<select name='banco' id='banco$N' style='display:'>";
			$NovoBanco .= "<option value='00'>SELECIONE</option>";
			while ($row_banco = mysql_fetch_array($result_banco)){	
				if($row['id_banco'] == $row_banco[0]){
					$NovoBanco .= "<option value='$row_banco[0]' selected>".$row_banco['nome']."</option>";
				}else{
					$NovoBanco .= "<option value='$row_banco[0]'>".$row_banco['nome']."</option>";
				}
			}
			$NovoBanco .= "</select>";
			
//			$divTT = "<div class='highslide-maincontent'>
//			<form action='ver_lista_banco.php' method='post' name='form1'>
//			<table width='526' border='0' cellspacing='0' cellpadding='0'>
//			
//			   <tr>
//			    <td align='right' bgcolor='#f0f0f0'>Nome</td>
//			    <td>&nbsp;<input name='nome' type='text' size='25' id='nome' value='$rowP[nome]'/>&nbsp;</td>
//			    <td align='right' bgcolor='#f0f0f0'>CPF</td>
//			    <td>&nbsp;<input name='cpf' type='text' size='15' maxlength='14' id='cpf' value='$rowP[cpf]'/></td>
//			  </tr>
//			  
//			  <tr>
//			    <td align='right' bgcolor='#f0f0f0'>Bancos:</td>
//			    <td>&nbsp;$NovoBanco
//				</td>
//			    <td align='right' bgcolor='#f0f0f0'>Tipo de PG:</td>
//			    <td>&nbsp;
//				<select name='tipopg' id='tipopg'>
//				$option
//				</select></td>
//			  </tr>
//			  
//			  <tr>
//			    <td align='right' bgcolor='#f0f0f0'>Agencia</td>
//			    <td>&nbsp;<input name='agencia' type='text' size='15' maxlength='10' id='agencia' value='$rowP[agencia]'/>&nbsp;</td>
//			    <td align='right' bgcolor='#f0f0f0'>Conta</td>
//			    <td>&nbsp;<input name='conta' type='text' size='15' maxlength='10' id='conta' value='$rowP[conta]'/></td>
//			  </tr>
//			  
//			  <tr>
//			    <td align='right' bgcolor='#f0f0f0'>Tipo de Conta</td>
//			    <td colspan='2'>&nbsp;
//				<label><input type='radio' name='radio_tipo_conta' value='salario' $checkedSalario>Conta Salário </label>
//				&nbsp;&nbsp;
//				<label><input type='radio' name='radio_tipo_conta' value='corrente' $checkedCorrente>Conta Corrente </label></td>
//			  </tr>
//			  <tr>
//			    <td colspan='3' align='center'><input type='submit' value='Enviar' /></td>
//			  </tr>
//			  
//			</table>
//			<input type='hidden' name='enc' value='$enc'>
//			<input type='hidden' name='clt' value='$rowP[0]'>
//			<input type='hidden' name='id' value='$row[id_clt]'>
//			<input type='hidden' name='id_folha' value='$folha'>
//			</form>
//			</div>";

		  print"
		  <tr class=\"novalinha $color\">
          <td align='center'>&nbsp; $rowP[campo3]</td>
          <td align='center'>&nbsp; $alink $nomeT</a> $divTT</td>
          <td align='center'>&nbsp; {$rowTipoPg2['tipopg']} </td>
          <td align='left'>&nbsp; $nomeBanco</td>
          <td align='center'>&nbsp; $agencia</td>
          <td align='center'>&nbsp; $agDv</td>
          <td align='center'>&nbsp; $conta</td>
          <td align='center'>&nbsp; $conDv</td>
		  <td align='left'>&nbsp; $cpf</td>
          <td align='left'>&nbsp; $tipoConta</td>
          <td align='left'>&nbsp; $valor</td>
		  </tr>";
		  unset($checkedSalario);
		  unset($checkedCorrente);
		  unset($tipoConta);
		  unset($option);
	  
		  //-- SOMANDO VARIAVEIS PARA OS TOTAIS --//
		  $TOsal_liq = $TOsal_liq + $sal_liq; 
		  $sal_liqT = "";
		  
		  }
		
		
		//-- FORMATANDO OS TOTAIS FORMATO BRASILEIRO--//
		  $TOsal_liqF = number_format($row_folha['total_liqui'],2,",",".");
		?>
        <tr>
          <td height="20" align="center" valign="middle" class="style23">&nbsp;</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td align="right">TOTAIS:</td>
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
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Funcion&aacute;rios Listados:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;
            <?=mysql_num_rows($result_folha_pro)?></td>
        </tr>
      </table>
      <br>            
<?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
//$linkvolt = encrypt("$regiao&$regiao"); 
$linkvolt =encrypt("$regiao&$folha");
$linkvolt = str_replace("+","--",$linkvolt);
// -----------------------------

?>
<br></td>
  </tr>
  <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC">
    <b><a href='ver_folha.php?<?="enc=".$linkvolt."&tela=1"?>' style="text-decoration:none; color:#000">VOLTAR</a></b>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>