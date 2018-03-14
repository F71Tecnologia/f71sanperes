<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}

include('../conn.php');
include('../funcoes.php');

// RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc     = $_REQUEST['enc'];
$enc     = str_replace('--', '+', $enc);
$link 	 = decrypt($enc); 
$decript = explode("&",$link);

$regiao = $decript[0];
$folha = $decript[1];
//

// MASTER
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
//

// SELECIONANDO INFORMAÇÃO DA FOLHA
$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM folhas WHERE id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mesINT = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mesINT];

//$result_folha_pro = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' and status = '3'");
$result_folha_pro = mysql_query("SELECT * FROM folha_autonomo WHERE (id_folha = '$folha' and status = '3') or (id_folha = '$folha' and status = '4') ORDER BY nome");

if($row_folha['terceiro'] == 1) {
	$tipo_terceiro = array('', 'Primeira Parcela', 'Segunda Parcela', 'Integral');
	$mensagem = 'Abono Natalino '.$tipo_terceiro[$row_folha['tipo_terceiro']];
} else {
	$mensagem = "$mes_da_folha / $row_folha[ano]";
}

$titulo = "Folha Autonomo: Projeto $row_projeto[nome] ".$mensagem;

$ano = date('Y');
$mes = date('m');
$dia = date('d');

$data = date('d/m/Y');

$RE_TipoDepo = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '1'");
$row_TipoDepo = mysql_fetch_array($RE_TipoDepo);

$RE_TIpoCheq = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '2'");
$row_TIpoCheq = mysql_fetch_array($RE_TIpoCheq);

// Bloqueio Administração
echo bloqueio_administracao($regiao);
?>
<html>
<head>
<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript" src="../js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="../js/lightbox.js"></script>
<script type="text/javascript" src="../js/highslide-with-html.js"></script>
<link rel="stylesheet" href="../js/lightbox.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="../js/highslide.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$titulo?></title>
<link href="../net1.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
    hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = 'rounded-white';

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
a:visited {
	font-size:10px; 
	color:#F00;
	text-decoration:none; 
	font-weight:bold; 
	font-family:Verdana, Arial, Helvetica, sans-serif;
}
a:link {
	font-size:10px; 
	color:#F00; 
	text-decoration:none; 
	font-weight:bold;
	font-family:Verdana, Arial, Helvetica, sans-serif;
}
#borda td {
	border-bottom:1px solid #000;
	padding:5px 0px;
}
.linha_um, .linha_dois {
	text-align:center;
}
.linha_um {
	background-color:#f5f5f5;
}
.linha_dois {
	background-color:#ebebeb;
}
.linha_um td, #folha .linha_dois td {
	border-bottom:1px solid #ccc;
}
.secao_pai {
	text-align:center; background-color:#999; color:#FFF; font-weight:bold; line-height:30px; font-size:16px;
}
#resumo {
	width:65%; float:left; background-color:#fff; text-align:center;
}
#resumo table {
    width:100%; border:1px solid #ddd; font-size:12px; line-height:22px; border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px; margin-bottom:10px;
}
#resumo table .secao {
	text-align:center; background-color:#555; color:#FFF; font-weight:bold; line-height:22px;
}
</style>
</head>
<body>
<table width="95%" border="0" align="center" class="bordaescura1px">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center">
      <tr>
        <td width="100%" height="92" align="center" valign="middle" bgcolor="#666666" class="bordaescura1px"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
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
            <?=$row_folha['data_proc2']?></span>
            </td>
          </tr>
        </table>
       </td>
      </tr>
    </table>
      <br />
      <span class="titulo_opcoes">
          Folha de Pagamento - <?=$mensagem?>
      </span>
      <br />
      <br />
    <?php // VERIFICA SE A FOLHA JÁ FOI FINALIZADA
			$resultStatusFolha = mysql_query("SELECT status FROM folhas WHERE id_folha = $folha");
			$rowStatusFolha    = mysql_fetch_array($resultStatusFolha);
			if($rowStatusFolha[0] == '4') {
					print "<span style='color:red; font-family:verdana, areal'> <strong>FINALIZADA</strong> </span>";
			} ?>
    <br/>
    <br/>
      <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px">
        <tr class="novo_tr_dois">
          <td width="10%" height="25" >C&oacute;digo</td>
          <td width="29%" style="text-align:left;">Nome</td>
          <td width="10%" >Sal. Base</td>
          <td width="12%" >Rendimentos</td>
          <td width="13%" >Descontos</td>
          <td width="12%" >Faltas</td>
          <td width="14%" >Sal. L&iacute;q.</td>
        </tr>
        
        <?php $cont = '0';
		
		while($row = mysql_fetch_array($result_folha_pro)) {
		  
		  $REparti = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$row[id_autonomo]'");
		  $rowP = mysql_fetch_array($REparti);
		  
		  $result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$rowP[id_curso]'");
		  $row_curso = mysql_fetch_array($result_curso);	 
		
		  // FORMATANDO NO FORMATO BRASILEIRO
		  $sal_baseF = number_format($row['salario'],2,",",".");
		  $rendiF 	 = number_format($row['adicional'],2,",",".");
		  $descoF 	 = number_format($row['desconto'],2,",",".");
		  $sal_liqF  = number_format($row['salario_liq'],2,",",".");
		  
		  // FORMATO USA
		  $sal_liqT  = number_format($row['salario_liq'],2,".","");

		  // EMBELEZAMENTO DA PAGINA
		  if($cont % 2){ $classcor="corfundo_um"; } else { $classcor="corfundo_dois"; };
		  $nome  = str_split($row['nome'], 30);
		  $nomeT = sprintf("% -30s", $nome[0]);
		  $bord  = "style='border-bottom:#000 solid 1px;'";

		  $tipoR = $rowP['tipo_conta'];
			
		  if($tipoR == 'salario') {
			  $checkedSalario = 'checked';	
		  } elseif($tipoR == 'corrente') {
			  $checkedCorrente = 'checked';
		  } ?>
                
		  <tr class="novalinha <?=$classcor?>" style="text-align:center;">
              <td><?=$rowP['campo3']?></td>
              <td style="text-align:left;"><?=$alink.$nomeT.$divTT?></td>
              <td><?=$sal_baseF?></td>
              <td><?=$rendiF?></td>
              <td ><?=$descoF?></td>
              <td><?=$row['faltas']?></td>
              <td><?=$sal_liqF?></td>
		  </tr>
		  
	<?php $PARTE1 = $PARTE1."UPDATE folha_autonomo SET salario_liq = '$sal_liqT' WHERE id_folha_pro = '$row[0]';\r\n";
		  $cont++;
		  
		  // SOMANDO VARIAVEIS PARA OS TOTAIS
		  $TOsal_base += $sal_base;
		  $TOsal_liq  += $sal_liq; 
		  $TOrendi    += $rendi;
		  $TOdesco    += $desco;
		  
		  // LIMPANDO VARIAVEIS
		  unset($sal_base,
				$sal_liq,
				$rendi,
				$desco,
				$faltas,
				$dias_trab,
				$diaria,
				$sal_liqT);
		  
		  }
		
		  // FORMATANDO OS TOTAIS FORMATO BRASILEIRO
		  $TOsal_baseF = number_format($row_folha['total_bruto'], 2, ',', '.');
		  $TOsal_liqF  = number_format($row_folha['total_liqui'], 2, ',', '.');
		  $TOrendiF    = number_format($row_folha['rendimentos'], 2, ',', '.');
		  $TOdescoF    = number_format($row_folha['descontos'], 2, ',', '.'); ?>
          
        <tr style="text-align:center; height:30px; font-weight:bold;">
          <td>&nbsp;</td>
          <td style="text-align:right;">TOTAIS:</td>
          <td><?=$TOsal_baseF?></td>
          <td><?=$TOrendiF?></td>
          <td><?=$TOdescoF?></td>
          <td>&nbsp;</td>
          <td><?=$TOsal_liqF?></td>
        </tr>
      </table>
      <br>
      <br>
      <table width="30%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px">
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

    <div id="resumo" style="width:100%; clear:both; margin-top:20px;">
        <table cellspacing="1">
	          <tr>
                <td class="secao_pai" colspan="5">Lista de Bancos</td>
              </tr>
            
      <?php // Verificando os bancos envolvidos na folha de pagamento
		    $qr_bancos = mysql_query("SELECT DISTINCT(banco) FROM folha_autonomo WHERE banco != '9999' AND banco != '0' AND id_folha = '$folha' AND status IN(3,4)");
		  	while($row_bancos = mysql_fetch_array($qr_bancos)) {
				
				$numero_banco++;
			    $qr_banco  = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_bancos[banco]'");
			    $row_banco = mysql_fetch_array($qr_banco); ?>
			  
		     <tr class="linha_<?php if($linha4++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
		          <td style="width:7%;"><img src="../imagens/bancos/<?=$row_banco['id_nacional']?>.jpg" width="25" height="25"></td>
                  <td style="width:35%; text-align:left; padding-left:5px;"><?=$row_banco['nome']?></td>
		  
		  <?php $total_finalizado = mysql_num_rows(mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' AND status = '4' AND banco = '$row_banco[id_banco]'"));
				
				if(!empty($total_finalizado)) {
											
					$total_finalizados = mysql_num_rows(mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' AND status = '4' AND banco = '$row_banco[id_banco]'")); ?>
					
					<td>&nbsp;</td>
					<td><a href="finalizados.php?regiao=<?=$regiao?>&folha=<?=$folha?>&projeto=<?=$projeto?>&banco=<?=$row_banco['id_banco']?>">FINALIZADO</a></td>
					<td align="center"><?=$total_finalizados?> Participantes</td>
                        					
		  <?php } else {
			  
					$qr_banco    = mysql_query("SELECT * FROM folha_autonomo folha INNER JOIN tipopg tipo ON folha.tipo_pg = tipo.id_tipopg WHERE folha.banco = '$row_bancos[0]' AND folha.id_folha = '$folha' AND folha.status = '3' AND tipo.tipopg = 'Depósito em Conta Corrente' AND tipo.id_regiao = '$regiao' AND tipo.id_projeto = '$row_projeto[0]'");
					$total_banco = mysql_num_rows($qr_banco); ?>
					
					<td style="width:30%; text-align:center;">
					<form id="form1" name="form1" method="post" action="folha_banco.php?enc=<?=str_replace('+', '--', encrypt("$regiao&$folha"))?>">
					  <select name="banco">
						  <?php $qr_bancos_associados = mysql_query("SELECT * FROM bancos WHERE id_nacional = '$row_banco[id_nacional]' AND status_reg = '1' AND id_regiao != ''");
								while($row_banco_associado = mysql_fetch_assoc($qr_bancos_associados)) { ?>
								<option value="<?=$row_banco_associado['id_banco']?>" <?php if($row_banco_associado['id_banco'] == $row_banco['id_banco']) { echo 'selected'; } ?>>
								<?php echo $row_banco_associado['id_banco'].' - '.$row_banco_associado['nome'].' ('.@mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_banco_associado[id_regiao]'"),0).')'; ?>
								</option>
						  <?php } ?>
					  </select>
					  <label id="data_pagamento<?=$numero_banco?>" style="display:none;"> 
						<input name="data" id="data[]" type="text" size="10" onKeyUp="mascara_data(this)" maxlength="10">
						<input name="enviar" id="enviar[]" type="submit" value="Gerar">
					  </label>
					  <input type="hidden" name="banco_participante" value="<?=$row_banco['id_banco']?>">
					</form>
					</td>
					<td style="width:8%;"><a style="cursor:pointer;"><img src="imagens/ver_banc.png" border="0" alt="Visualizar Funcionários por Banco" onClick="document.all.data_pagamento<?=$numero_banco?>.style.display = (document.all.data_pagamento<?=$numero_banco?>.style.display == 'none') ? '' : 'none' ;"></a></td>
					<td style="width:20%; text-align:center; padding-right:5px;"><?=$total_banco?> Participantes</td>
			   </tr>
                 
			   <?php }
				}
	  
				$qr_cheque    = mysql_query("SELECT * FROM folha_autonomo folha INNER JOIN tipopg tipo ON folha.tipo_pg = tipo.id_tipopg WHERE folha.id_folha = '$folha' AND folha.status = '3' AND tipo.tipopg = 'Cheque' AND tipo.id_regiao = '$regiao' AND tipo.id_projeto = '$row_projeto[0]' AND tipo.campo1 = '2'");
				$total_cheque = mysql_num_rows($qr_cheque);
				$linkcheque   = str_replace('+', '--', encrypt("$regiao&$folha&$row_TIpoCheq[0]&$row_TipoDepo[0]")); ?>
	  
               <tr class="linha_<?php if($linha4++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
                  <td style="width:7%;"><img src="../imagens/bancos/cheque.jpg" width="25" height="25" border="0"></td>
                  <td style="width:35%; text-align:left; padding-left:5px;">Cheque</td>
                  <td style="width:30%;">&nbsp;</td>
                  <td style="width:8%;"><a href="ver_cheque.php?enc=<?=$linkcheque?>"><img src="imagens/ver_banc.png" border="0" alt="Visualizar Funcionários por Cheque"></a></td>
                  <td style="width:20%; text-align:center; padding-right:5px;"><?=$total_cheque?> Participantes</td>
               </tr>
            </table>
            </div>
    
    
    
    
    
    
    
    
    
    
	<br>            
  <br>
<br>
<?php // ENCRIPTOGRAFANDO A VARIAVEL
$linkvolt = encrypt("$regiao&$regiao"); 
$linkvolt = str_replace("+","--",$linkvolt);
$enc2     = str_replace("+","--",$enc);
// ?>
<br></td>
  </tr>
  <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC">
    <b><a href='folha.php?id=9&<?="enc=".$linkvolt."&tela=1"?>' style="text-decoration:none; color:#000">VOLTAR</a></b>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <b><a href='ver_lista_banco.php?<?="enc=".$enc2?>' style="text-decoration:none; color:#000">VER LISTA POR BANCO</a></b>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>