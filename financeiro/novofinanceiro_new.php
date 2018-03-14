<?php
if(empty($_COOKIE['logado2'])) {
	print "<h1>Desculpe!</h1><br>Você não tem permissão para ver está página.";
	exit;
}

include('../conn.php');
include('../classes/funcionario.php');

$nFunc   = new funcionario();
$regiao  = $_REQUEST['regiao'];
$userlog = $_COOKIE['logado'];

if(!empty($_REQUEST['apro'])) {

	$id_user = $_COOKIE['logado2'];
	$apro    = $_REQUEST['apro'];
	$vale    = $_REQUEST['vale'];
	$valor   = $_REQUEST['valor'];
	$regiao  = $_REQUEST['regiao'];
	$idComb  = $_REQUEST['idcomb'];
	$dataCad = date('Y-m-d');

	if($apro == 1) {		

		mysql_query("UPDATE fr_combustivel SET status_reg = '2', data_libe = '$dataCad', numero='$vale', user_libe = '$id_user' WHERE id_combustivel = '$idComb'");
		$link = "../frota/printcombustivel.php?com=$idComb&regiao=$regiao";

	} else {

		mysql_query("UPDATE fr_combustivel SET status_reg = '0', data_libe = '$dataCad', user_libe = '$id_user' WHERE id_combustivel = '$idComb'");
		$link = "novofinanceiro.php?regiao=$regiao";

	}

	print "<script>location.href=\"$link\";</script>";
	exit;
}

// VARIAVEIS NECESSÁRIAS PARA AS CONSULTAS
$mes2     = date('F');
$dia_h    = date('d');
$mes_h    = date('m');
$ano      = date('Y');
$dtHojeYm = date('Y-m');

// DEFININDO QUANDO FOI O MES PASSADO PROXIMO MES E OUTROS
$mes_passadoano	= date('Y-m', mktime(0,0,0, $mes_h - 3, $dia_h, $ano));
$mes_q_vem 		= date('m',   mktime(0,0,0, $mes_h + 1, $dia_h, $ano));
$MesqVemYm 		= date('Y-m', mktime(0,0,0, $mes_h + 1, $dia_h, $ano));
$ano_passado 	= date('Y',   mktime(0,0,0, $mes_h , $dia_h, $ano - 1));

$data_hoje  = "$dia_h/$mes_h/$ano";
$dia_amanha = "$dia_h" + "1";

$meses  = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$MesInt = (int)$mes_h;
$mes    = $meses[$MesInt];

// VERIVICANDO AS CONTAS PARA HOJE
$result_jr       = mysql_query("SELECT * FROM saida WHERE id_regiao = '$regiao' AND status = '1' AND data_vencimento = '$ano-$mes_h-$dia_h' ORDER BY data_vencimento ASC");
$result_banco_jr = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$regiao' AND saldo LIKE '-%' AND status_reg = '1'");
$linha_jr        = mysql_num_rows($result_jr);
$linha_banco_jr  = mysql_num_rows($result_banco_jr);

if($linha_jr > '0') {
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $linha_jr CONTA(S) A PAGAR HOJE');</script>";
}

if($linha_banco_jr > '0') {
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $linha_banco_jr SALDO(S) NEGATIVO(S)');</script>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type"  content="text/html; charset=iso-8859-1">
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma"        content="No-Cache">
<meta http-equiv="Expires"       content="0">
<title>::: Intranet :::</title>
<link rel="stylesheet" type="text/css" href="../js/highslide.css" />
<link rel="stylesheet" type="text/css" href="css/estrutura.css" >
<link rel="stylesheet" type="text/css" href="../jquery/tools/css/estilo.css"  >
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../jquery/accordion/jquery.accordion.js"></script>
<script type="text/javascript" src="../jquery/jquery.tools.min.js"></script>
<script type="text/javascript" src="../js/highslide-with-html.js"></script>
<script type="text/javascript">
$().ready(function(){
	$('.ano').parent().next().hide();
	$(".ano").click(function(){
		$(this).parent().next().slideToggle();
		$('.ano').parent().next().hide();
	});
	$('.dataautonomos').parent().next().hide();
	$('.dataautonomos').click(function(){
		$(this).parent().next().slideToggle();
		$('.dataautonomos').parent().next().hide();
	});
});

function confirmacao(url,mensagem) {
	if(window.confirm(mensagem)) {
		location.href = url;
	}
}

hs.graphicsDir = '../images-box/graphics/';
hs.outlineType = 'rounded-white';

function abrir(URL,w,h,NOMEZINHO) {

	var width  = w;
  	var height = h;

	var left = 99;
	var top  = 99;

window.open(URL,NOMEZINHO, 'width='+width+', height='+height+', top='+top+', left='+left+', scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=yes, fullscreen=no');

}

function CorFundo(campo,cor){

	var d = document;

	if(cor == 1) {
		var color = "#F2F2E3";
	} else {
		var color = "#FFFFFF";
	}

	d.getElementById(campo).style.background=color;

}

function MM_jumpMenu(targ,selObj,restore) {
    eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
    if (restore) selObj.selectedIndex=0;
}
</script>
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" id="corpo">
  <tr>
    <td>
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><a href="javascript:abrir('saidas.php?regiao=<?=$regiao?>','750','550','Saída');">Cadastrar Sa&iacute;das</a></td>
        <td><a href="javascript:abrir('entradas.php?regiao=<?=$regiao?>','750','550','Entrada');">Cadastrar Entradas</a></td>
        <td><a href="javascript:abrir('login_adm2.php?regiao=<?=$regiao;?>','600','400','Rel');">Relat&oacute;rios</a></td>
        <td><a href="javascript:abrir('saidacaixinha.php?regiao=<?=$regiao?>','680','280','Caixa');">Cadastrar Sa&iacute;das de Caixa</a></td>
        <td><a href="javascript:abrir('../calculadora/caculadora.html','560','370','Calculadora');">Calculadora</a></td>
	    <?php // Permissão da provisão
              $permissao = array('5','28','9','75','77','64');
              if(in_array($userlog,$permissao)) { ?>
        <td><a href="javascript:abrir('cadastro.provisao.php?regiao=<?=$regiao?>','700','500','Cadastro de provisão')">Cadastro de provisão</a></td>
		<?php } ?>
      </tr>
    </table>
    
  </td>
</tr>
<tr>
  <td>
    
<?php // Controle de Região
	  $usuarios_permitidos = array('75','5','9','27','64','77','68');
	  if(in_array($userlog,$usuarios_permitidos)) {

	      $qr_id_master = mysql_query("SELECT id_master FROM funcionario WHERE id_funcionario = '$userlog'");
		  $id_master    = @mysql_result($qr_id_master,0); ?>
     
          <form name="formRegiao" id="formRegiao" method="get">
          <table>
            <tr>
              <td>Região</td>
              <td><select name="regiao" onchange="MM_jumpMenu('parent',this,0)">

              <?php $qr_selecao_regiao = mysql_query("SELECT * FROM regioes WHERE status = '1' AND id_master = '$id_master'"); 
		  	        while($row_selecao_regiao = mysql_fetch_assoc($qr_selecao_regiao)) {

						if($_GET['regiao'] == $row_selecao_regiao['id_regiao']) { 
							$selected = 'selected="selected"';
						} else {
							$selected = NULL;
						} ?>
                    
                    	<option <?=$selected?> value="?regiao=<?=$row_selecao_regiao['id_regiao']?>"><?=$row_selecao_regiao['id_regiao']?> - <?=$row_selecao_regiao['regiao']?></option>

		      <?php } ?>

             </select>
            </td>
          </tr>
        </table>
        </form>

       <!--Controle de regiao -->

<?php } ?>

  </td>
</tr>
<tr>
  <td>

    <table width="97%" border="0" cellspacing="0" cellpadding="0" align="center">
      <tr>
        <td>

        <!-- INICIO DO CONTROLE DE COMBUSTIVEL -->

       <?php $pre_users = array('27','52','5','1','65','9','64','77','68'); // filtro de usuarios
	   		 if(in_array($userlog,$pre_users)) { ?>

          <table width="100%" border="0" cellpadding="0" cellspacing="0" id="TBcombustivel">
            <tr>
              <td class="secao">CONTROLE DE COMBUST&Iacute;VEL:</td>
            </tr>
          </table>

		<table width="100%" border="0" cellspacing="1" cellpadding="0" id="TabelaCombustivel">
        
  <?php $REComb = mysql_query("SELECT *,date_format(data_cad, '%d/%m/%Y')as data_cad FROM fr_combustivel WHERE status_reg='1'");
        $cont = '0';

	while($RowComb = mysql_fetch_array($REComb)) {
		
		// FUNCIONARIO EXTERNO (NÂO ESTA CADASTRADO NA TABELA FUNCIONARIOS)
		if($RowComb['funcionario'] == 2) { 

			$REFuncionario  = mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$RowComb[id_user]'");
			$RowFuncionario = mysql_fetch_array($REFuncionario);
			$NOME           = $RowComb['nome'];
			$RG             = $RowComb['rg'];
			
		// FUNCIONARIO INTERNO (SELECIONAMOS O NOME E O CPF DELE CADASTRADO NA BASE DE DADOS)
		} else {
			
			$REUser  = mysql_query("SELECT nome,rg FROM funcionario WHERE id_funcionario = '$RowComb[id_user]'");
			$RowUser = mysql_fetch_array($REUser);
			$NOME    = $RowUser['nome'];
			$RG      = $RowUser['rg'];

		}

		$REREG  = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$RowComb[id_regiao]'");
		$RowREG = mysql_fetch_array($REREG);
		$NOME   = explode(' ',$NOME);
		$codigo = sprintf("%04d",$RowComb['0']); ?>
                
      <tr>
        <td><?=$NOME[0]?></td>
        <td><?=$RowREG['regiao']?></td>
        <td><?=$RowComb['destino']?></td>
        <td><?=$RowComb['data_cad']?></td>
        <td><a href="#" onclick="return hs.htmlExpand(this, { outlineType: 'rounded-white', wrapperClassName: 'draggable-header',headingText: 'Liberar' } )" class="highslide">Liberar</a></td>
		<td>
        
	<div class="highslide-maincontent">
    
    <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form">
	<table width="526" border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td align="center" colspan="2">
			<label><input type="radio" name="apro" id="apro" value="1"> Aprovar</label>
			<label><input type="radio" name="apro" id="apro" value="2"> Recusar</label>
			</td>
		</tr>
		<tr>
			<td align="right">N&uacute;mero do Vale:</td>
			<td><input name="vale" type="text" size="20" id="vale" value="<?=$codigo?>"/>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Valor do Vale:</td>
			<td><input name="valor" type="text" size="13" id="valor" OnKeyDown="FormataValor(this,event,17,2)"/></td>
		</tr>
		<tr>
			<td align="center" colspan="2"><input type="submit" value="Enviar" /></td>
		</tr>
	</table>
	<input type="hidden" id="regiao" name="regiao" value="<?=$regiao?>"/>
	<input type="hidden" id="idcomb" name="idcomb" value="<?=$RowComb[0]?>"/>
  </form>
  
  </div>

      </td>
	</tr>

	<?php } ?>
    
  </table>

<?php } ?>
        
<!-- FINALIZANDO A DIV DO CONTROLE DE COMBUSTIVEL -->

<!-- TOTALIZADOR -->

<?php $users = array('75','9','27','5','64','77','68');
      if(in_array($userlog,$users)) { ?>

          <table width="100%" border="0" cellpadding="0" cellspacing="0" id="TBcombustivel">
            <tr>
              <td class="secao">Contas vencidas:</td>
            </tr>
          </table>

        <table width='100%' border='0' cellspacing='1' cellpadding='0' bgcolor='#CCCCCC' id='TabelaCombustivel'>
          <tr>
            <td>Regiao</td>
            <td align="center">A vencer</td>
            <td align="center">Vencimento hoje</td>
            <td align="center">Vencidas</td>
            <td >&nbsp;</td>
          </tr>

   <?php $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '1' AND id_master = '1'");
		 while($row_regioes = mysql_fetch_assoc($qr_regioes)) {

		 $qr_cont_hoje     = mysql_query("SELECT * 
										FROM saida
									   WHERE id_regiao = '$row_regioes[id_regiao]'
										 AND STATUS = '1'
										 AND data_vencimento = CURDATE()");

		 $qr_cont_vencidas = mysql_query("SELECT * 
											FROM saida
										   WHERE id_regiao = '$row_regioes[id_regiao]'
											 AND status = '1'
											 AND data_vencimento < CURDATE()
											 AND data_vencimento != '0000-00-00'
											 AND YEAR(data_vencimento) = '".date('Y')."'");

		 $qr_cont_avencer  = mysql_query("SELECT * 
										   FROM saida
										  WHERE id_regiao = '$row_regioes[id_regiao]'
										    AND status = '1'
										    AND data_vencimento > CURDATE()");

		 $num_hoje        = mysql_num_rows($qr_cont_hoje);
		 $num_vencimento  = mysql_num_rows($qr_cont_vencidas);
		 $num_avencer     = mysql_num_rows($qr_cont_avencer);

	if(!empty($num_hoje) or !empty($num_vencimento) or !empty($num_avencer)) { ?>

    <tr bgcolor="<?php if($alternate++%2==0) { ?>#EEEEEE<?php } else { ?>#FFFFFF<?php } ?>">
    	<td><?=$row_regioes['id_regiao'].' - '.$row_regioes['regiao']?></td>
        <td><?=$num_avencer?></td>
        <td><?=$num_hoje?></td>
        <td><?=$num_vencimento?></td>
        <td><a href="">ver contas</a></td>
    </tr>   

    <?php } } ?>

	</table>

        <?php } ?>

        <!-- TOTALIZADOR -->  

        </td>
        <td width="61%">

          <table width='100%' border='0' cellpadding='0' cellspacing='0' class='titulosTab' id='TBreembolso'>
            <tr class="secao">
              <td>CONTROLE DE REEMBOLSO:</td>
            </tr>
          </table>

	<table width='100%' border='0' cellspacing='1' cellpadding='0' id='TabelaReembolso'>

<?php $REReem = mysql_query("SELECT *,date_format(data, '%d/%m/%Y') AS data FROM fr_reembolso WHERE status = '1'");
	  while($RowReem = mysql_fetch_array($REReem)) {

	      if($RowReem['funcionario'] == '1') {

			  $result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$RowReem[id_user]'");
			  $row_user    = mysql_fetch_array($result_user);
			  $NOME        = $row_user['nome1'];
		
	      } else {

	  	      $NOME = $RowReem['nome']; 

	      }

		  $pagar_imagem = '-';	  
		  $codigo       = sprintf('%05d',$RowReem['0']);
		  $valor        = $RowReem['valor'];	  
		  $valorF       = number_format($valor,2,',','.'); ?>

        <tr>
          <td><?=$codigo?></td>
          <td><?=$RowReem['data']?></td>
          <td><?=$NOME?></td>
          <td>R$ <?=$valorF?></td>
          <td><a href="../frota/ver_reembolso.php?id=1&reembolso=<?=$RowReem[0]?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )"><img src="imagensfinanceiro/editar.gif" alt="Editar" border="0"> </a></td>
          <td>&nbsp;</td>
        </tr>

<?php $soma += $valor;
      $cont++;

	}
	
    $soma_f = number_format($soma,2,',','.'); ?>

	<tr>
      <td colspan='6' align='center' style="font-size:14px; font-weight:bold; background-color:#CCC;">
		TOTAL DE REEMBOLSO: R$ <?=$soma_f?>
      </td>
    </tr>

    <?php unset($soma_f,
				$cont,
				$soma,
				$valor); ?> 

    	 </table>

       </td>
      </tr>
      <tr>
        <td colspan='2'>

         <table width='100%' border='0' cellpadding='0' cellspacing='0' id='TBsaidas'>
            <tr>
              <td>RELA&Ccedil;&Atilde;O DE SA&Iacute;DAS CADASTRADAS POR DATA:</td>
            </tr>
            
    <table width='100%' border='0' cellpadding='0' cellspacing='0' id='TabelaSaida'>
    
  	<?php $soma = '0';
	
  	// MOSTRANDO SAÍDAS DO MES ANTERIOR NÃO PAGAS
	$result_saidas_a = mysql_query("SELECT *, date_format(data_vencimento, '%d/%m/%Y') AS data_vencimento2, 
											  date_format(data_proc, '%d/%m/%Y - %h:%m:%s') AS data_proc 
									     FROM saida 
										WHERE id_regiao = '$regiao' 
										  AND status = '1'
										  AND data_vencimento 
								      BETWEEN '$mes_passadoano-01' 
									      AND '$dtHojeYm-00' 
								     ORDER BY data_vencimento ASC");
    $row_linhas = mysql_num_rows($result_saidas_a);

	while($row_saidas_a = mysql_fetch_array($result_saidas_a)) {

	  $result_banco_saida_a = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_saidas_a[id_banco]'");
	  $row_banco_saida_a    = mysql_fetch_array($result_banco_saida_a);

	  if($row_saidas_a['id_banco'] == '0') {
	  	  $pagar_imagem_a = "<a href=../edit_saidas.php?idsaida=$row_saidas_a[0]&tabela=saida&regiao=$regiao><img src=imagensfinanceiro/editar.gif alt='Editar' border=0>";
	  } else {
	  	  $pagar_imagem_a = "<a href=../ver_tudo.php?id=17&pro=$row_saidas_a[0]&tipo=pagar&tabela=saida&regiao=$regiao&idtarefa=1><img src=imagensfinanceiro/pagar.gif alt='Pagar' border=0>";
	  }

	  if($row_saidas_a['comprovante'] == '0') {
	  	  $anexo_a = NULL;
	  } else {
	  	  $anexo_a = "<img src=imagensfinanceiro/anexo.gif alt='Anexo'>";
	  }

	  $valor1_a      = $row_saidas_a['valor'];
	  $adicional1_a  = $row_saidas_a['adicional'];
	  $valor_a       = str_replace(',', '.', $valor1_a);
	  $adicional_a   = str_replace(',', '.', $adicional1_a);
	  $valor_final_a = $valor_a + $adicional_a;
	  $valor_f_a     = number_format($valor_final_a,2,',','.');

	  $nFunc -> MostraUser($row_saidas_a['id_user']);
	  $Nome   = $nFunc -> nome1; ?>

       <tr>
         <td><?=$row_saidas_a[0]?></td>
         <td><?=$row_saidas_a['data_vencimento2']?></b></td>
         <td><a href="../ver_tudo.php?regiao=$regiao&id=16&saida=$row_saidas_a[0]&entradasaida=1" target="_blank"><?=$row_saidas_a['nome']?></a></td>
         <td>Banco: <?=$row_banco_saida_a['nome']?> / Agência: <?=$row_banco_saida_a['agencia']?> / Conta: <?=$row_banco_saida_a['conta']?></td>
         <td><?=$Nome?></td>
         <td><?=$row_saidas['data_proc']?></td>
         <td>R$ <?=$valor_f_a?></td>
         <td><?=$anexo_a?></td>
         <td><?=$pagar_imagem_a?></a></td>
         <td><a href="../ver_tudo.php?id=17&pro=<?=$row_saidas_a[0]?>&tipo=deletar&tabela=saida&regiao=<?=$regiao?>"><img src="imagensfinanceiro/deletar.gif" alt="Deletar" border="0"></a></td>
       </tr>

	<?php $soma_a += $valor_final_a;
		  } ?>

   <td colspan="8">
   </td>

<?php // MOSTRANDO SAÍDAS DO MES ATUAL NÃO PAGAS
	  $result_saidas = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y') AS data_vencimento2, date_format(data_proc, '%d/%m/%Y - %h:%m:%s') AS data_proc FROM saida WHERE id_regiao = '$regiao' AND status = '1' AND data_vencimento BETWEEN '$dtHojeYm-01' AND '$MesqVemYm-31' ORDER BY data_vencimento ASC");
	  $cont = '0';

	  while($row_saidas = mysql_fetch_array($result_saidas)) {

		  $result_banco_saida = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_saidas[id_banco]'");
		  $row_banco_saida    = mysql_fetch_array($result_banco_saida);

		  if($row_saidas['id_banco'] == '0') {
			   $pagar_imagem = "<a href=../edit_saidas.php?idsaida=$row_saidas[0]&tabela=saida&regiao=$regiao><img src=imagensfinanceiro/editar.gif alt='Editar' border=0></a>";
		  } else {
			   $pagar_imagem = "<a href=../ver_tudo.php?id=17&pro=$row_saidas[0]&tipo=pagar&tabela=saida&regiao=$regiao&idtarefa=1><img src=imagensfinanceiro/pagar.gif alt='Pagar' border=0></a>";
		  }
	
		  if($row_saidas['comprovante'] == '0') {
			  $anexo = NULL;
		  } else {
			  $anexo = "<img src=imagensfinanceiro/anexo.gif alt='Anexo'>";
		  }
	
		  if('20/04/2008' <= '12/04/2008') {
			  $cor = "#FF9598";
		  } else {
			  $cor = NULL;
		  }
	
		  $valor1      = $row_saidas['valor'];
		  $adicional1  = $row_saidas['adicional'];
		  $valor       = str_replace(',', '.', $valor1);
		  $adicional   = str_replace(',', '.', $adicional1);
		  $valor_final = $valor + $adicional;
		  $valor_f     = number_format($valor_final,2,',','.');
		  $nFunc      -> MostraUser($row_saidas['id_user']);
		  $Nome        = $nFunc -> nome1; ?>

            <tr>
                <td><?=$row_saidas[0]?></td>
                <td><?=$row_saidas['data_vencimento2']?></td>
                <td align='left'><a href="../ver_tudo.php?regiao=$regiao&id=16&saida=<?=$row_saidas[0]?>&entradasaida=1" target="_blank"><?=$row_saidas['nome']?></a></td>
                <td align='left'><?=$row_banco_saida['nome']?> / Agência: <?=$row_banco_saida['agencia']?> / Conta: <?=$row_banco_saida['conta']?></td>
                <td><?=$Nome?></td>
                <td><?=$row_saidas['data_proc']?></td>
                <td>R$ <?=$valor_f?></td>
                <td><?=$anexo?></td>
                <td><?=$pagar_imagem?></a></td>
                <td><a href="../ver_tudo.php?id=17&pro=<?=$row_saidas[0]?>&tipo=deletar&tabela=saida&regiao=<?=$regiao?>"><img src="imagensfinanceiro/deletar.gif" alt="Deletar" border="0"></a></td>
            </tr>
  
	<?php $soma += $valor_final;

	}

    $soma_f = number_format($soma,2,',','.'); ?>

	<tr>
      <td colspan="8" style="font-size:14px; font-weight:bold; background-color:#CCC;">
	    	TOTAL DE SA&Iacute;DAS - <?=$mes?>: R$ <?=$soma_f?>
	  </td>
    </tr>
  </table>

  <?php unset($soma_f,
			  $soma,
			  $valor); ?>
          
          </td>
        </tr>
        <tr>
          <td colspan="2">

            <table width="100%" border="0" cellpadding="0" cellspacing="0" id="TBsaidas2">
              <tr>
                <td>RELA&Ccedil;&Atilde;O DE ENTRADAS CADASTRADAS POR DATA:</td>
              </tr>
            </table>

    <?php $soma2 = '0';
		  $result_entradas = mysql_query("SELECT *, date_format(data_vencimento, '%d/%m/%Y') AS data_vencimento2, date_format(data_proc, '%d/%m/%Y - %h:%m:%s') AS data_proc FROM entrada WHERE id_regiao = '$regiao' AND status='1' ORDER BY data_vencimento ASC");		  
		  
	while($row_entradas = mysql_fetch_array($result_entradas)) {

	  $result_banco_entradas = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_entradas[id_banco]'");
	  $row_banco_entradas    = mysql_fetch_array($result_banco_entradas);

	  $valor2       = str_replace(',', '.', $row_entradas['valor']);
	  $adicional2   = str_replace(',', '.', $row_entradas['adicional']);
	  $valor2_f     = number_format($valor2,2,',','.');
	  $adicional2_f = number_format($adicional2,2,',','.');
	  $nFunc       -> MostraUser($row_entradas['id_user']);
	  $Nome         = $nFunc -> nome1; ?>
      
    <tr>
        <td><?=$row_entradas[0]?></td>
        <td><?=$row_entradas['data_vencimento2']?></td>
        <td><a href='../ver_tudo.php?regiao=$regiao&id=16&saida=$row_entradas[0]&entradasaida=2' target='_blank'><?=$row_entradas['nome']?></a></td>
        <td>Banco: <?=$row_banco_entradas['nome']?> / Agência: <?=$row_banco_entradas['agencia']?> / Conta: <?=$row_banco_entradas['conta']?></td>
        <td><?=$Nome?></td>
        <td><?=$row_entradas['data_proc']?></td>
        <td>Adicional: R$ <?=$adicional2_f?></td>
        <td>Valor: R$ <?=$valor2_f?></td>
        <td><a href="../ver_tudo.php?id=17&pro=<?=$row_entradas[0]?>&tipo=pagar&tabela=entrada&regiao=<?=$regiao?>&idtarefa=2"><img src="imagensfinanceiro/pagar.gif" alt="Confirmar" border="0"></a></td>
        <td><a href="../ver_tudo.php?id=17&pro=<?=$row_entradas[0]?>&tipo=deletar&tabela=entrada&regiao=<?=$regiao?>"><img src="imagensfinanceiro/deletar.gif" alt="Deletar" border="0"></a></td>
    </tr>
    
    <?php $valor_soma2  = $valor2 + $adicional2;
          $soma2 	   += $valor_soma2;
		} ?>
    
    <tr>
      <td colspan="8" align="center" style="font-size:14px; font-weight:bold; background-color:#CCC;">
		  TOTAL DE SA&Iacute;DAS - <?=$mes?>: R$ <?=number_format($soma2,2,',','.')?>
	  </td>
    </tr>
  </table>
    
    <?php unset($soma_f,
				$soma,
				$valor); ?>

       </td>
      </tr>
      <tr>
        <td colspan="2">

        <table width="100%" border="0" cellpadding="0" cellspacing="0" id="TBCaixinha">
          <tr>
            <td>RELA&Ccedil;&Atilde;O DE SA&Iacute;DAS DO CAIXA:</td>
          </tr>
        </table>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" id="TabelaCaixinha">

	<?php $somaCA = '0';
		  $result_caixa = mysql_query("SELECT *, date_format(data_vencimento, '%d/%m/%Y') AS data_vencimento2 , date_format(data_proc, '%d/%m/%Y') AS data_proc FROM caixa WHERE id_regiao = '$regiao' AND status = '1' AND data_proc >= '$ano-$mes_h-01'");
		  while($row_caixa = mysql_fetch_array($result_caixa)) {
		
			  $valorCA      = $row_caixa['valor'];
			  $adicionalCA  = $row_caixa['adicional'];
			  $valorCA      = str_replace('.', '', $valorCA);
			  $valorCA      = str_replace(',', '.', $valorCA);
			  $adicionalCA  = str_replace('.', '', $adicionalCA);
			  $adicionalCA  = str_replace(',', '.', $adicionalCA);
			  $valor_finaCA = $valorCA + $adicionalCA;
			  $valor_fCA    = number_format($valor_finaCA, 2, ',', '.');
			  $valor2_fCA   = number_format($valorCA, 2, ',', '.'); ?>

        <tr>
            <td><?=$row_caixa['data_proc']?> - Nome: <?=$row_caixa['nome']?></td>
            <td>Valor: R$ <?=$valor2_fCA?></td>
            <td>Adicional: R$ <?=$adicionalCA?></td>
        </tr>

	<?php $somaCA += $valor_finaCA;
		  }

    $somaCA_F 		 = number_format($somaCA, 2, ',', '.');
	$result_caixinha = mysql_query("SELECT saldo FROM caixinha WHERE id_regiao = '$regiao'");

	while($row_caixinha = mysql_fetch_array($result_caixinha)) {
		  $saldo_caixinha            = str_replace(',', '.', $row_caixinha['saldo']);
		  $saldo_caixinha_formatado  = number_format($saldo_caixinha,2,',','.');
		  $soma_saldo               += $saldo_caixinha;
	}

	$saldo_caixinha     = number_format($soma_saldo,2,',','.');
	$calculo_caixinha   = $soma_saldo - $soma2;
	$calculo_caixinha_f = number_format($calculo_caixinha,2,',','.'); ?>

    <tr>
      <td colspan="3" align="center">
      
        <table width="100%">
          <tr>
            <td width="50%">TOTAL DE SA&Iacute;DAS DO CAIXA</td>
            <td width="50%">SALDO DO CAIXA</td>
	      </tr>
	      <tr>
	        <td>R$ <?=$somaCA_F?></td>
	        <td>R$ <?=$saldo_caixinha_formatado?></td>
	      </tr>
	    </table>
        
	   </td>
     </tr>
   </table>
  
   <?php unset($soma_f,
			   $soma,
			   $valor);
		 
		 // Provisões
		 if(in_array($userlog,$permissao)) { ?>

<div id="provisoes">

	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
	  <tr>
	     <td colspan="5">PROVIS&Otilde;ES AUTONOMOS</td>
	  </tr>
	  <tr>
	    <td colspan="5">

        <?php $query_provisao    = mysql_query("SELECT p.id_provisao, p.id_projeto, p.ano_provisao
												  FROM provisao AS p  
										     LEFT JOIN projeto  AS pr 
												    ON pr.id_projeto = p.id_projeto 
											     WHERE p.status_provisao = '1' 
												   AND pr.id_regiao = '$regiao' 
											  ORDER BY p.id_provisao ASC;");
		      $provisao_autonomo = array();

		while($row_provisao = mysql_fetch_assoc($query_provisao)) {

			$projeto = $row_provisao['id_projeto'];
			$ano 	 = $row_provisao['ano_provisao'];

			$provisao_autonomo[$projeto][$ano][] = $row_provisao['id_provisao'];
			ksort($provisao_autonomo[$projeto]);

		} ?>

              <table width="100%" border="0" cellspacing="0" cellpadding="0">

              <?php foreach($provisao_autonomo as $projeto => $anos){?>

                <tr bgcolor="<?php if($alternate++%2==0) { echo '#D2D2D2'; } else { echo '#FFFFFF'; } ?>">
                  <td colspan="5">
                  	<?php if($projeto_anterior != $projeto) {
							  $qr_projeto = mysql_query("SELECT nome,id_projeto FROM projeto WHERE id_projeto = '$projeto'");
							  print mysql_result($qr_projeto,0);
						  } ?>
                  </td>
               	</tr>

              <?php foreach($anos as $ano => $provisoes) { ?>

                <tr>
                  <td class="dataautonomos" colspan="5" align="center" style="cursor:pointer;">
                    <?php if($ano_anterior != $ano) {
							  echo $ano;
						  } ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="5">
                  
             		<table width="100%" class="autonomos">
                      <tr>
                        <td>Provisão</td>
                        <td>Mês</td>
                        <td>&nbsp;</td>
                        <td>Valor</td>
                        <td>&nbsp;</td>
                      </tr>

	   <?php foreach($provisoes as $provisao) {
		   
		   		$qr_provisao = mysql_query("SELECT * FROM provisao WHERE id_provisao = '$provisao'");
                $rw_provisao = mysql_fetch_assoc($qr_provisao);
				
				$qr_mes = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$rw_provisao[mes_provisao]'");
				
				$valor_total += $rw_provisao['valor_provisao'];  ?>

             <tr bgcolor="<?php if($alternate++%2==0) { echo '#D2D2D2'; } else { echo '#FFFFFF'; } ?>">
                  <td><?=$rw_provisao['id_provisao']?></td>
                  <td><?=@mysql_result($qr_mes,0)?></td>
                  <td>&nbsp;</td>
                  <td><?='R$ '.number_format($rw_provisao['valor_provisao'], 2, ',', ' ')?></td>
                  <td><a href="javascript:abrir('cadastro.provisao.php?ID=<?=$rw_provisao['id_provisao']?>&regiao=<?=$regiao?>','700','500','Cadastro de provisão')" ><img  width="20px" height="20px"  src="../imagensmenu2/Edit.png" /></a>
                      <a onclick="confirmacao('actions/cadastro.provisao.php?regiao=<?=$regiao?>&log=3&id=<?=$rw_provisao['id_provisao']?>','Tem certeza que deseja deletar esta provisao?')" href="#"><img width="20px" height="20px" src="../imagensmenu2/Symbol-Delete.png" /></a></td>
             </tr>

                  <?php } ?>	   

                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><b>Total:</b></td>
                        <td>R$ <?=number_format($valor_total, 2, ',', ' ')?></td>
                      </tr>
                      </table>
                    </td>
                  </tr>

                  <?php $valor_total  = 0;
				  	    $ano_anterior = $ano;	

				   		}
						
					$ano_anterior     = NULL;
					$projeto_anterior = $projeto;

				} ?>

              </table>

          </td>
	    </tr>
	  </table>
      
    <!-- ///////////////////// CLT ///////////////////////-->

  <?php $projeto     = array();
		$query_folha = mysql_query("SELECT f.rendi_final, p.nome, f.mes, f.projeto, f.id_folha, f.ano, f.terceiro, f.tipo_terceiro
									  FROM rh_folha f
							     LEFT JOIN projeto p ON p.id_projeto = f.projeto
								     WHERE p.id_regiao = '$regiao'
									   AND f.status = '3' 
									   AND p.status_reg != 0 
								  ORDER BY f.mes ASC");

		while($row_folha = mysql_fetch_assoc($query_folha)) {
			$chave_projeto = $row_folha['projeto'];
			$ano_folha     = $row_folha['ano'];
			$projetos[$chave_projeto][$ano_folha][] =  $row_folha['id_folha'];
			ksort($projetos[$chave_projeto][$ano_folha]);
		} ?>

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td>PROVIS&Otilde;ES CLT</td>
    </tr>
    <tr>

   <?php if(!empty($projetos)){
			foreach($projetos as $projeto => $anos) {
				foreach($anos as $ano => $folhas) {
					if($projeto != $ultimo_projeto) { ?>

     <tr>
       <td>
		<?php $query_projetos = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$projeto'");
			  print @mysql_result($query_projetos,0); ?>
       </td>
     </tr>

	<?php } if($ano != $ultimo_ano) { ?>

     <tr>
       <td class="ano" bgcolor="<?php if($alternate++%2==0) { ?>#EEEEEE<?php } else { ?>#FFFFFF<?php } ?>">
             <?=$ano?>
       </td>
     </tr>

    <?php } ?>

    <tr>
      <td>
          
        <table style="clear:both; width:100%; text-align:center;">
            <tr>
              <td>Folha</td>
              <td>Mês</td>
              <td>Valor total da folha</td>
              <td>Valor da provis&atilde;o</td>
            </tr>

		  <?php foreach($folhas as $folha) { 
				
                $qr_folha = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$folha';");
                $rw_folha = mysql_fetch_assoc($qr_folha);
					
				$query_meses = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$rw_folha[mes]';");
                $print_mes   = mysql_result($query_meses,0); ?>

            <tr bgcolor="<?php if($alternate++%2==0) { ?>#EEEEEE<? } else { ?>#FFFFFF<? } ?>">
              <td><?=$rw_folha['id_folha']?></td>
              <td>

		  <?php if($rw_folha['terceiro'] == '1') {

                    if($rw_folha['tipo_terceiro'] == 3) {
                        $print_mes .= "13ª integral";
                    } else {
                        $print_mes .=" 13ª ($rw_folha[tipo_terceiro]ª) Parcela";
                    }

                }

                echo $print_mes;
				
				// se for folha de 13° salario pegar o valor da valor_dt
				if($rw_folha['terceiro'] == '1') {
					$total = $rw_folha['valor_dt'];
				} else {
					$total = $rw_folha['rendi_final'];
				}

                echo 'R$ '.number_format ($total, 2, ',', ' ');
				
				$totalF     = ($total* 33.93) / 100;
				$somatorio += $totalF;
				echo 'R$ '.number_format ($totalF, 2, ',', ' '); ?>
				
                </td>&nbsp;<td>
                </td>&nbsp;<td>
             </tr>

        <?php } ?>

            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td align="right">Total:</td>
              <td>R$ <?=number_format($somatorio, 2, ',', ' ')?></td>
            </tr>
        </table>

      </td>
    </tr>

	 <?php $ultimo_projeto = $projeto;
		   $ultimo_ano	   = $ano;
		   $somatorio 	   = 0;

				}

			$ultimo_ano = 0;

			}

		}

	} ?>

		</table>

       </td>
      </tr>
    </table>

  <tr>
    <td></td>
  </tr>
</table>
</body>
</html>