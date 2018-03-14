<?php
// Verificando se o usuário está logado
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}

// Incluindo Arquivos
require('../conn.php');
include('../classes/abreviacao.php');
include('../classes/formato_valor.php');
include('../classes/formato_data.php');
include('../funcoes.php');
include('../classes_permissoes/acoes.class.php');

$ACOES = new Acoes();
// Id da Folha
list($nulo,$folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

// Consulta da Folha
$qr_folha    = mysql_query("SELECT *, date_format(data_inicio, '%d/%m/%Y') AS data_inicio_br,
									  date_format(data_fim, '%d/%m/%Y') AS data_fim_br,
									  date_format(data_proc, '%d/%m/%Y') AS data_proc_br 
							     FROM folhas WHERE id_folha = '$folha' AND status = '3'");
$row_folha   = mysql_fetch_array($qr_folha);
$data_inicio = $row_folha['data_inicio'];
$data_fim    = $row_folha['data_fim'];
$ano         = $row_folha['ano'];
$mes         = $row_folha['mes'];
$mes_int     = (int)$mes;

// Consulta do Usuário que gerou a Folha
$qr_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_folha[user]'");

// Redefinindo Variáveis de Décimo Terceiro
if($row_folha['terceiro'] != 1) {
	$decimo_terceiro = NULL;
} else {
	$decimo_terceiro = 1;
	$tipo_terceiro   = $row_folha['tipo_terceiro'];
}

// Consulta da Região
$qr_regiao = mysql_query("SELECT id_regiao, regiao FROM regioes WHERE id_regiao = '$row_folha[regiao]'");
$regiao    = mysql_result($qr_regiao, 0, 0);

// Consulta do Projeto
$qr_projeto = mysql_query("SELECT id_projeto, nome, id_master FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
$projeto    = mysql_result($qr_projeto, 0, 0);

// Consulta dos Participantes da Folha
$qr_participantes    = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' AND status IN(3,4) ORDER BY nome ASC");
$total_participantes = mysql_num_rows($qr_participantes);

// Tipos de Pagamentos e Cheque
$qr_tipo_pagamento  = mysql_query("SELECT id_tipopg, tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' AND campo1 = '1' LIMIT 1");
$row_tipo_pagamento = mysql_fetch_array($qr_tipo_pagamento);

$qr_tipo_cheque  = mysql_query("SELECT id_tipopg, tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' AND campo1 = '2' LIMIT 1");
$row_tipo_cheque = mysql_fetch_array($qr_tipo_cheque);

// Definindo Mês da Folha
$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

if(!empty($decimo_terceiro)) {
	switch($tipo_terceiro) {
		case 1:
		$mes_folha = '13º Primeira parcela';
		break;
		case 2:
		$mes_folha = '13º Segunda parcela';
		break;
		case 3:
		$mes_folha = '13º Integral';
		break;
	}
} else {
	$mes_folha = "$meses[$mes_int] / $ano";
}

// Encriptografando a variável
$enc2        = str_replace('+', '--', $_REQUEST['enc']);
$link_voltar = str_replace('+', '--', encrypt("$regiao&$regiao"));

// Bloqueio Administração
echo bloqueio_administracao($regiao);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: Folha Finalizada de Aut&ocirc;nomo (<?=$folha?>)</title>
<link href="../favicon.ico" rel="shortcut icon">
<link href="sintetica/folha.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="corpo">

	<table cellspacing="4" cellpadding="0" id="topo">
      <tr>
        <td width="15%" rowspan="3" valign="middle" align="center">
          <img src="../imagens/logomaster<?=mysql_result($qr_projeto, 0, 2)?>.gif" width="110" height="79">
        </td>
        <td colspan="3" style="font-size:12px;">
        	<b><?=mysql_result($qr_projeto, 0, 1).' ('.$mes_folha.')'?></b>
        </td>
      </tr>
      <tr>
        <td width="35%"><b>Data da Folha:</b> <?=$row_folha['data_inicio_br'].' &agrave; '.$row_folha['data_fim_br']?></td>
        <td width="30%"><b>Região:</b> <?=$regiao.' - '.mysql_result($qr_regiao, 0, 1)?></td>
        <td width="20%"><b>Participantes:</b> <?=$total_participantes?></td>
      </tr>
      <tr>
        <td><b>Data de Processamento:</b> <?=$row_folha['data_proc_br']?></td>
        <td><b>Gerado por:</b> <?=abreviacao(mysql_result($qr_usuario, 0), 2)?></td>
        <td><b>Folha:</b> <?=$folha?></td>
      </tr>
    </table>
    
   <table cellpadding="0" cellspacing="1" class="folha">
        <tr>
          <td colspan="2"><a href="folha.php?id=9&enc=<?=$link_voltar?>" class="voltar">Voltar</a></td>
          <td colspan="8">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="10">
          
              <table cellspacing="0" cellpadding="0" width="100%">
                 <tr class="secao">
                  <td width="4%">COD</td>
                  <td width="24%" align="left" style="padding-left:5px;">NOME</td>
                  <td width="8%">SALÁRIO</td>
                  <td width="8%">DIÁRIA</td>
                  <td width="8%"><?php if(!empty($decimo_terceiro)) { echo 'MESES'; } else { echo 'DIAS'; } ?></td>
                  <td width="8%">FALTAS</td>
                  <td width="10%">BASE</td>
                  <td width="10%">RENDIMENTOS</td>
                  <td width="10%">DESCONTOS</td>
                  <td width="10%">L&Iacute;QUIDO</td>
                 </tr>
              </table>
                
          </td>
        </tr>
        
      <?php while($row_participante = mysql_fetch_array($qr_participantes)) {
		  
		  		$qr_autonomo  = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$row_participante[id_autonomo]'");
				$row_autonomo = mysql_fetch_array($qr_autonomo);
				  
				$qr_curso  = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_autonomo[id_curso]'");
				$row_curso = mysql_fetch_array($qr_curso);
				
				$salario_total += $row_participante['salario_limpo'];
				$diaria_total  += $row_participante['diaria'];
		
				$tipoR = $row_autonomo['tipo_conta'];
				
				if($tipoR == 'salario') {
					$checkedSalario = 'checked';	
				} elseif ($tipoR == 'corrente') {
					$checkedCorrente = 'checked';
				} ?>
      
        <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?> destaque">
          <td width="4%"><?php echo $row_participante['id_autonomo']; ?></td>
          <td width="24%" align="left" style="padding-left:5px;"><?php echo abreviacao($row_participante['nome'], 4, 1); ?></td>
          <td width="8%"><?php echo formato_real($row_participante['salario_limpo']); ?></td>
          <td width="8%"><?php echo formato_real($row_participante['diaria']); ?></td>
          <td width="8%"><?php echo $row_participante['dias_trab']; ?></td>
          <td width="8%"><?php echo $row_participante['faltas']; ?></td>
          <td width="10%"><?php echo formato_real($row_participante['salario']); ?></td>
          <td width="10%"><?php echo formato_real($row_participante['adicional']); ?></td>
          <td width="10%"><?php echo formato_real($row_participante['desconto']); ?></td>
          <td width="10%"><?php echo formato_real($row_participante['salario_liq']); ?></td>
		</tr>
      
      <?php } // Fim do Loop de Participantes ?>
      
        <tr class="totais">
           <td colspan="6">
		       <?php if($total_participantes > 10) { ?>
          	      <a href="#corpo" class="ancora left">Subir ao topo</a>
               <?php } ?>
               <div class="right">TOTAIS:</div>
           </td>
           <td><?php echo formato_real($row_folha['total_bruto']); ?></td>
           <td><?php echo formato_real($row_folha['rendimentos']); ?></td>
           <td><?php echo formato_real($row_folha['descontos']); ?></td>
           <td><?php echo formato_real($row_folha['total_liqui']); ?></td>
        </tr>
    </table>
    
    <div id="estastisticas" style="margin-top:20px;">

       <?php // Totalizadores
		     $totalizadores_nome  = array('BASE', 'RENDIMENTOS', 'DESCONTOS', 'L&Iacute;QUIDO');
		     $totalizadores_valor = array($row_folha['total_bruto'], $row_folha['rendimentos'], $row_folha['descontos'], $row_folha['total_liqui']); ?>
        
        <div id="totalizadores" style="margin-bottom:20px;">
          <table cellspacing="1">
            <tr>
              <td class="secao_pai" colspan="2">Totalizadores</td>
            </tr>
            <tr class="linha_um">
              <td class="secao">PARTICIPANTES:</td>
              <td class="valor"><?=$total_participantes?></td>
            </tr>
            <?php foreach($totalizadores_valor as $chave => $valor) { ?>
  		    <tr class="linha_<?php if($linha2++%2==0) { echo 'dois'; } else { echo 'um'; } ?>">
              <td class="secao"><?=$totalizadores_nome[$chave]?>:</td>
              <td class="valor"><?=formato_real($valor)?></td>
            </tr>
            <?php } ?>
          </table>
        </div>

        <div id="resumo" style="width:98%; margin:0 auto; float:none;">
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
		  
		  <?php $total_finalizados = mysql_num_rows(mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' AND status = '4' AND banco = '$row_banco[id_banco]'"));
				
				if(!empty($total_finalizados)) { ?>
					
					<td>&nbsp;</td>
					<td><a href="finalizados.php?regiao=<?=$regiao?>&folha=<?=$folha?>&projeto=<?=$projeto?>&banco=<?=$row_banco['id_banco']?>">FINALIZADO</a></td>
					<td align="center"><?=$total_finalizados?> Participantes</td>
                        					
		  <?php } else {
			  
					$qr_banco    = mysql_query("SELECT * FROM folha_autonomo folha INNER JOIN tipopg tipo ON folha.tipo_pg = tipo.id_tipopg WHERE folha.banco = '$row_bancos[0]' AND folha.id_folha = '$folha' AND folha.status = '3' AND tipo.tipopg = 'Depósito em Conta Corrente' AND tipo.id_regiao = '$regiao' AND tipo.id_projeto = '$projeto'");
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
	  
				$qr_cheque    = mysql_query("SELECT * FROM folha_autonomo folha INNER JOIN tipopg tipo ON folha.tipo_pg = tipo.id_tipopg WHERE folha.id_folha = '$folha' AND folha.status = '3' AND tipo.tipopg = 'Cheque' AND tipo.id_regiao = '$regiao' AND tipo.id_projeto = '$projeto' AND tipo.campo1 = '2'");
				$total_cheque = mysql_num_rows($qr_cheque);
				$linkcheque   = str_replace('+', '--', encrypt("$regiao&$folha&$row_tipo_cheque[0]&$row_tipo_pagamento[0]")); ?>
	  
               <tr class="linha_<?php if($linha4++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
                  <td style="width:7%;"><img src="../imagens/bancos/cheque.jpg" width="25" height="25" border="0"></td>
                  <td style="width:35%; text-align:left; padding-left:5px;">Cheque</td>
                  <td style="width:30%;">&nbsp;</td>
                  <td style="width:8%;"><a href="ver_cheque.php?enc=<?=$linkcheque?>"><img src="imagens/ver_banc.png" border="0" alt="Visualizar Funcionários por Cheque"></a></td>
                  <td style="width:20%; text-align:center; padding-right:5px;"><?=$total_cheque?> Participantes</td>
               </tr>
               <tr>
                  <td colspan="5">
                     <?php if($ACOES->verifica_permissoes(76)){ ?>
                  <a href="pg_lote.php?enc=<?=$enc2?>" style="font-weight:bold; padding-left:5px;">Pagamento em lote</a> | 
                  <?php } ?>
                      <a href="ver_lista_banco.php?enc=<?=$enc2?>" style="font-weight:bold; padding-left:5px;">Ver Lista por Banco</a> |                 
                      <a href="confere_banco.php?enc=<?=$enc2?>&tp_contrato=1" style="font-weight:bold; padding-left:5px;">Atualizar dados bancários</a>
               
                  </td>
               </tr>
            </table>

        </div>
    </div>
</div>
</body>
</html>