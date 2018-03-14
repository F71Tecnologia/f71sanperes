<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com/intranet/login.php'>Logar</a> ";
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

//SELECIONANDO O INSTITUTO PARAR CARREGAR A LOGO
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
//SELECIONANDO O INSTITUTO PARAR CARREGAR A LOGO


//SELECIONANDO A FOLHA
$result_folha = mysql_query("SELECT *,date_format(data_inicio, '%d/%m/%Y')as data_inicio2,date_format(data_fim, '%d/%m/%Y')as data_fim2,date_format(data_proc, '%d/%m/%Y')as data_proc2 FROM folhas where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);


//SELECIONANDO OS CLTS JA CADASTRADOS NA TAB FOLHA_PROC QUE ESTEJAM COM STATUS 2 = SELECIONADO ANTERIORMENTE
$result_folha_pro = mysql_query("SELECT * FROM folha_autonomo where id_folha = '$folha' and status = '2' ORDER BY nome ASC");
$num_clt_pro = mysql_num_rows($result_folha_pro);

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

$mes_int = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mes_int];

$titulo = "Folha Autonomo: Projeto $row_projeto[nome] mês de $mes_da_folha";

$ano = date("Y");
$mes = date("m");
$dia = date("d");

$data = date("d/m/Y");

$data_menor14 = date("Y-m-d", mktime(0,0,0, $mes,$dia,$ano - 14));
$data_menor21 = date("Y-m-d", mktime(0,0,0, $mes,$dia,$ano - 21));

// Definindo Usuários para Finalizar a Folha
$acesso_finalizacao = array('9','20','33','77');
?>
<html>
<head>
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma"        content="No-Cache">
<meta http-equiv="Expires"       content="0">

<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript" src="../js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="../js/lightbox.js"></script>
<script type="text/javascript" src="../js/highslide-with-html.js"></script>
<script type="text/javascript" src="../js/ramon.js"></script>

<script>
limpaCache('sintetica.php');
</script>

<link rel="stylesheet" href="../js/lightbox.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="../js/highslide.css" />

<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net1.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
    hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>

<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style29 {
	font-family: arial;
	font-weight: bold;
}
.style30 {color: #663300}
-->
</style>

<script language="javascript"> 

  //o parâmentro form é o formulario em questão e t é um booleano 
  function ticar(form, t) { 
    campos = form.elements; 
    for (x=0; x<campos.length; x++) 
      if (campos[x].type == "checkbox") campos[x].checked = t; 
  } 

</script> 

</head>

<body>
<table width="99%" border="0" align="center" class="bordaescura1px">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center">
      <tr>
        <td width="100%" height="81" align="center" valign="middle" class="show">
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="16%" height="98" align="center" valign="middle"><span class="style1">
              <img src="../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79" align="absmiddle" ></span></td>
            <td width="62%" align=""><span class="style3">
              <?=$row_master['razao']?><br>
              CNPJ : <?=$row_master['cnpj']?>
              <br>
            </span></td>
            <td width="22%">
            <span class="style3">
            Data de Processamento: <?=$row_folha['data_proc2']?>
            <br>
            Intervalo de: 
            <br>
            <?=$row_folha['data_inicio2']?> at&eacute; <?=$row_folha['data_fim2']?>
            </span></td>
            </tr>
        </table></td>
      </tr>
    </table>
      <br />
      <span class="title">Folha de Pagamento Autônomo -
<?=$mes_da_folha?> / <?=$ano?></span><br />
      <span class="title"><br />
    </span>
      <table width="97%" border="0" align="center" cellpadding="0" cellspacing="0" style="line-height:26px; font-size:12px;">
        <tr>
          <td width="3%" height="25" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">&nbsp;</td>
          <td width="7%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Cod</td>
          <td width="29%" align="left" valign="middle" bgcolor="#CCCCCC" class="style23">Nome</td>
          <td width="10%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. Base</td>
          <td width="12%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Rendimentos</td>
          <td width="13%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Descontos</td>
          <td width="6%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Faltas</td>
          <td width="7%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Dias Trab.</td>
          <td width="13%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. L&iacute;q.</td>
        </tr>
       
        <?php
          $cont = "1";
		  $num_total_auto = mysql_num_rows($result_folha_pro);
		  
		  //INICIO DO PROCESSAMENTO DE CADA AUTONOMO
		  while($row = mysql_fetch_array($result_folha_pro)){
			  
		  //SELECIONA TODAS AS INFORMAÇES DE CADA AUTONOMO
		  $REparti = mysql_query("SELECT * FROM autonomo where id_autonomo = '$row[id_autonomo]'");
		  $rowP = mysql_fetch_array($REparti);
		  
		  //SELECIONA O CURSO DA PESSOA PARA SABER VALOR DE SALARIO
		  $result_curso = mysql_query("SELECT * FROM curso where id_curso = '$rowP[id_curso]'");
		  $row_curso = mysql_fetch_array($result_curso);
		  
		  //FORMATA OS VALORES
		  $sal_base = number_format($row_curso['salario'],2,".","");
		  $rendi = number_format($row['adicional'],2,".","");
		  $desco = number_format($row['desconto'],2,".","");
		  
		  $faltas = $row['faltas'];
		  $dias_trab = $row['dias_trab'] - $faltas;
		  $diaria = $sal_base / $row_folha['qnt_dias'];
		  
		  $sal_liq = ($dias_trab * $diaria) + $rendi - $desco;
		  
		  //-- FORMATANDO NO FORMATO BRASILEIRO --
		  $sal_baseF = number_format($sal_base,2,",",".");
		  $rendiF = number_format($rendi,2,",",".");
		  $descoF = number_format($desco,2,",",".");
		  $sal_liqF = number_format($sal_liq,2,",",".");
		  //-- FORMATO USA
		  $sal_baseT = number_format($sal_base,2,".","");
		  $sal_liqT = number_format($sal_liq,2,".","");
		  //-- ENCRIPTOGRAFANDO A VARIAVEL
		  $linkfalt = encrypt("$row[0]&1&$folha"); 
		  $linkfalt = str_replace("+","--",$linkfalt);
		  // -----------------------------
	
		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
		  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
		  $nome = str_split($row['nome'], 30);
		  $nomeT = sprintf("% -30s", $nome[0]);
		  $bord = "style='border-bottom:#000 solid 1px;' ";
		  $ninputad = "adicional_$cont";
		  $ninputde = "desconto_$cont";
		  $ninputfa = "faltas_$cont";
		  $atributos = "onBlur=\"ajaxupdatefolha('folha_autonomo',this.value,this.id,'id_folha_pro','$row[0]','2');\"";
		  //-----------------
		  
		  print"
		  <tr height='20' class='mostraregistronormal' bgcolor=$color>
		  <td align='center' valign='middle' $bord>$cont</td>
          <td align='center' valign='middle' $bord>$rowP[campo3]</td>
          <td align='lefth' valign='middle' $bord>
		  <a href='faltas.php?enc=$linkfalt' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\"> $nomeT</a></td>
          <td align='right' valign='middle' $bord>$sal_baseF</td>
          <td align='right' valign='middle' $bord>

		  <input type=\"text\" id=\"$ninputad\" size=\"6\" value=\"$rendiF\" OnKeyDown=\"FormataValor(this,event,20,2)\" $atributos/>
		  
		  </td>
		  <td align='right' valign='middle' $bord>
		  
		  <input type=\"text\" id=\"$ninputde\" size=\"6\" value=\"$descoF\" OnKeyDown=\"FormataValor(this,event,20,2)\" $atributos/>
		  
		  </td>
          <td align='right' valign='middle' $bord>
		  
		  <input type=\"text\" id=\"$ninputfa\" size=\"1\" value=\"$faltas\" OnKeyDown=\"FormataValor(this,event,20,2)\" $atributos/>
		  
		  </td>
		  <td align='right' valign='middle' $bord>$dias_trab</td>
          <td align='right' valign='middle' $bord>$sal_liqF</td>
		  </tr>";
		  
		  $PARTE1 = $PARTE1."UPDATE folha_autonomo SET salario = '$sal_baseT', salario_liq = '$sal_liqT', terceiro = '$row_folha[terceiro]', status = '3' WHERE id_folha_pro = '$row[0]';\r\n";
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
          <td height="20" align="center" valign="middle" class="style23">&nbsp;</td>
          <td height="20" align="right" valign="bottom" class="style23">TOTAIS:</td>
          <td align="center" valign="bottom" class="style23"><?=$TOsal_baseF?></td>
          <td align="center" valign="bottom" class="style23"><?=$TOrendiF?></td>
          <td align="center" valign="bottom" class="style23"><?=$TOdescoF?></td>
          <td colspan="2" align="right" valign="bottom" class="style23">&nbsp;</td>
          <td align="center" valign="bottom" class="style23"><?=$TOsal_liqF?></td>
        </tr>
        
      </table>
      <br />
      <?=$mensagem?>
      <br>
      
      <br>
      <table width="30%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px">
        <tr>
          <td height="24" colspan="2" align="center" valign="middle" bgcolor="#CCCCCC" class="title"><span class="linha">TOTALIZADORES</span></td>
        </tr>
        <tr>
          <td width="46%" height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha"> Sal&aacute;rio L&iacute;quido:</span></td>
          <td width="54%" height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">
            &nbsp;&nbsp;<span class="style23">
            <?=$TOsal_liqF?>
            </span></span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Sal&aacute;rio Base:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha"><span class="style23">
            &nbsp;&nbsp;
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
            <?=$num_total_auto?></td>
        </tr>
      </table>
    <br>
<?php
	
	
//-- FORMATANDO OS TOTAIS FORMATO E.U.A. PARA SEREM GRAVADOS NO ARQUIVO TXT--//

		  $TOsal_baseT = number_format($TOsal_base,2,".","");
		  $TOsal_liqT = number_format($TOsal_liq,2,".","");
		  $TOrendiT = number_format($TOrendi,2,".","");
		  $TOdescoT = number_format($TOdesco,2,".","");		


$TERCEIRA_PARTE = "UPDATE folhas SET participantes = '$cont', rendimentos = '$TOrendiT ', descontos = '$TOdescoT', total_bruto = '$TOsal_baseT', total_liqui = '$TOsal_liqT', status = '3' WHERE id_folha = '$folha' LIMIT 1 ;\r\n";
	
		
		$conteudo = $PARTE1."$TERCEIRA_PARTE"."\r\n";

		
		$nome_arquivo_download = "autonomo_".$folha.".txt";
		$arquivo = "../arquivos/folhaautonomo/".$nome_arquivo_download;

		//TENTA ABRIR O ARQUIVO TXT
		if (!$abrir = fopen($arquivo, "wa+")) {
		echo "Erro abrindo arquivo ($arquivo)";
		exit;
		}

		//ESCREVE NO ARQUIVO TXT
		if (!fwrite($abrir, $conteudo)) {
		print "Erro escrevendo no arquivo ($arquivo)";
		exit;
		}

		//FECHA O ARQUIVO
		fclose($abrir);
    
	//-- ENCRIPTOGRAFANDO A VARIAVEL
	$linkvolt = encrypt("$regiao&$regiao"); 
	$linkvolt = str_replace("+","--",$linkvolt);
	
	$add = encrypt("$regiao&$folha&2"); 
	$add = str_replace("+","--",$add);
	
	$linkFim = encrypt("$regiao&$folha"); 
	$linkFim = str_replace("+","--",$linkFim);
	// -----------------------------

?>

</td>
  </tr>
  <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC"><table width="80%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center"><b><a href='javascript:window.location.reload()' class="botao">ATUALIZAR</a></b></td>
          <td align="center"><b><a href='folha2.php?<?="enc=".$add?>'  class="botao">ADICIONAR PARTICIPANTE</a></b></td>
          <td align="center"><b><a href='folha.php?id=9&<?="enc=".$linkvolt?>'  class="botao">VOLTAR</a></b></td>
        </tr>
      </table>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
    <?php if(in_array($_COOKIE['logado'], $acesso_finalizacao)) { ?>
    	<b><a href='acao_folha.php?<?="enc=".$linkFim?>' class="botao">FINALIZAR</a></b>
    <?php } ?>
    <!--
    <br /><a href='<?=_URL."arquivos/folhaautonomo/".$nome_arquivo_download?>'>texto</a>
    -->
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>