<?php 
include('../include/restricoes.php');
include('../../funcoes.php');
include('../include/criptografia.php');
include('../../classes/formato_data.php');
include('../../classes/formato_valor.php');

$projeto = $_GET['id'];

$query = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row   = mysql_fetch_assoc($query);
$total = mysql_num_rows($query);

list($numero_clts, $numero_autonomos, $numero_cooperados, $numero_autonomos_pj) = explode(' / ', $row['total_participantes']);
?>
<html>
<head>
<title>Administra&ccedil;&atilde;o de Projetos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="corpo">
    <div id="menu" class="projeto">
    	<?php include('include/menu.php'); ?>
    </div>
    <p>&nbsp;</p>
       <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <th colspan="6" bgcolor="#EDEDED">&nbsp;</th>
          </tr>
          <tr>
            <th colspan="6" bgcolor="#CCCCCC">LIVRO CAIXA - <?php echo date('Y'); ?></th>
          </tr>
          <tr>
            <th colspan="6" bgcolor="#EDEDED"><?php echo $row['nome'].' - '.$row['tema']; ?></th>
          </tr>
        </table>
        <p>&nbsp;</p>
        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <th height="30" bgcolor="#EDEDED">EVENTO:</th>
            <th colspan="5">&nbsp;</th>
          </tr>
          <tr>
            <th width="16%" height="30" bgcolor="#EDEDED">Data de In&iacute;cio:</th>
            <th width="24%"><?php echo formato_brasileiro($row['inicio']); ?></th>
            <th width="11%" bgcolor="#EDEDED">Validade:</th>
            <th width="24%"><?php echo formato_brasileiro($row['termino']); ?></th>
            <th width="12%" bgcolor="#EDEDED">Renova&ccedil;&atilde;o:</th>
            <th width="13%"><?php echo formato_brasileiro($row['prazo_renovacao']); ?></th>
          </tr>
        </table>
        <p>&nbsp;</p>
        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <th colspan="6" bgcolor="#EDEDED">DOCUMENTA&Ccedil;&Atilde;O DO COTRATO</th>
          </tr>
        </table>
        <p>&nbsp;</p>
        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <th height="30" bgcolor="#EDEDED">Documento:</th>
            <th width="12%" bgcolor="#EDEDED">SIM / N&Atilde;O</th>
            <th bgcolor="#EDEDED">DATA</th>
            <th bgcolor="#EDEDED">VALIDADE</th>
            <th bgcolor="#EDEDED">&nbsp;</th>
          </tr>
          <tr>
            <th width="29%" height="30" bgcolor="#EDEDED">Proposta de Parceria</th>
            <th>&nbsp;</th>
            <th width="15%" bgcolor="#FFFFFF">&nbsp;</th>
            <th width="22%">&nbsp;</th>
            <th width="22%">&nbsp;</th>
          </tr>
          <tr>
            <th height="30" bgcolor="#EDEDED">Termo de Parceria Assinado</th>
            <th>&nbsp;</th>
            <th bgcolor="#FFFFFF">&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th height="30" bgcolor="#EDEDED">Prorroga&ccedil;&atilde;o do Termo</th>
            <th>&nbsp;</th>
            <th bgcolor="#FFFFFF">&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th height="30" bgcolor="#EDEDED">Nova Licita&ccedil;&atilde;o</th>
            <th>&nbsp;</th>
            <th bgcolor="#FFFFFF">&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th height="30" bgcolor="#EDEDED">Novo Termo de Parceria</th>
            <th>&nbsp;</th>
            <th bgcolor="#FFFFFF">&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th height="30" bgcolor="#EDEDED">Prorroga&ccedil;&atilde;o</th>
            <th>&nbsp;</th>
            <th bgcolor="#FFFFFF">&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th height="30" bgcolor="#EDEDED">Aditivo</th>
            <th>&nbsp;</th>
            <th bgcolor="#FFFFFF">&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th height="30" bgcolor="#EDEDED">Renova&ccedil;&atilde;o</th>
            <th>&nbsp;</th>
            <th bgcolor="#FFFFFF">&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
        </table>
        <p>&nbsp;</p>
        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td colspan="6" bgcolor="#EDEDED">OBJETO DO PROJETO</td>
          </tr>
        </table>
        <p>&nbsp;</p>
        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <th height="30" bgcolor="#EDEDED">OBJETO:</th>
            <th colspan="5"><?php echo $row['tipo_contrato']; ?></th>
          </tr>
          <tr>
            <th width="19%" height="30" bgcolor="#EDEDED">PROGRAMA DE TRABALHO:</th>
            <th colspan="5"><?php echo $row['descricao']; ?></th>
          </tr>
          <tr>
            <th height="30" bgcolor="#EDEDED">CAPACITA&Ccedil;&Atilde;O</th>
            <th width="11%"><input type="checkbox" name="checkbox" id="checkbox"><label for="checkbox"></label></th>
            <th width="29%" bgcolor="#EDEDED">PROGRAMA DE TRABALHO</th>
            <th width="11%"><input type="checkbox" name="checkbox3" id="checkbox3"></th>
            <th width="22%" bgcolor="#EDEDED">FOLHA DE PAGAMENTO</th>
            <th width="8%"><input type="checkbox" name="checkbox5" id="checkbox5"></th>
          </tr>
          <tr>
            <th height="30" bgcolor="#EDEDED">AUDITORIA</th>
            <th><input type="checkbox" name="checkbox2" id="checkbox2"></th>
            <th bgcolor="#EDEDED">CONTROLE DE PONTO</th>
            <th><input type="checkbox" name="checkbox4" id="checkbox4"></th>
            <th bgcolor="#EDEDED">TREINAMENTO</th>
            <th><input type="checkbox" name="checkbox6" id="checkbox6"></th>
          </tr>
        </table>
    	<p>&nbsp;</p>
        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <th colspan="6" bgcolor="#EDEDED">VERBAS DO PROJETO</th>
          </tr>
        </table>
        <p>&nbsp;</p>
        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <th width="20%" height="30" bgcolor="#EDEDED">Valor Mensal:</th>
            <th colspan="4">&nbsp;</th>
            <th width="17%" bgcolor="#EDEDED">Valor Anual:</th>
            <th colspan="2">&nbsp;</th>
          </tr>
          <tr>
            <th height="30" colspan="8" bgcolor="#EDEDED">Percentual de Taxas:</th>
          </tr>
          <tr>
            <th height="30" bgcolor="#EDEDED">Administrativa:</th>
            <th width="9%" height="30"><?php echo $row['taxa_adm']; ?> %</th>
            <th width="10%" bgcolor="#EDEDED">Parceiro:</th>
            <th width="8%" height="30"><?php echo $row['taxa_parceiro']; ?> %</th>
            <th width="8%" bgcolor="#EDEDED">Outra:</th>
            <th height="30"><?php echo $row['taxa_outra1']; ?> %</th>
            <th width="11%" height="30" bgcolor="#EDEDED">Outra:</th>
            <th width="17%"><?php echo $row['taxa_outra2']; ?> %</th>
          </tr>
          <tr>
            <th height="30" colspan="2" bgcolor="#EDEDED">Tipo de Contrata&ccedil;&atilde;o</th>
            <th bgcolor="#EDEDED">CLT</th>
            <th height="30"><?php echo $numero_clts; ?></th>
            <th bgcolor="#EDEDED">Autonomos</th>
            <th height="30"><?php echo $numero_autonomos; ?></th>
            <th height="30" bgcolor="#EDEDED">Cooperados</th>
            <th><?php echo $numero_cooperados ; ?></th>
          </tr>
          <tr>
            <th height="30" colspan="3" bgcolor="#EDEDED">Provis&atilde;o de Encargos Trabalhistas</th>
            <th height="30" colspan="5">R$ <?php echo formato_real($row['provisao_encargos']); ?></th>
            </tr>
        </table>
    	<p>&nbsp;</p>
        
        <?php $campos = array($row['id_parceiro'],$row['id_parceiro1'],$row['id_parceiro2']);
			  foreach($campos as $campo) {
				  
				  if(!empty($campo)) { 
				  
				   		$qr_parceiro  = mysql_query("SELECT * FROM parceiros WHERE parceiro_id = '$campo'");
						$row_parceiro = mysql_fetch_assoc($qr_parceiro); ?>
					  
                        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <th colspan="6" bgcolor="#EDEDED">PARCEIRO</th>
                          </tr>
                        </table>
                        <p>&nbsp;</p>
                        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
                          <tr>
                            <th height="30" bgcolor="#EDEDED">Nome:</th>
                            <th colspan="5"><?php echo $row_parceiro['parceiro_nome']; ?></th>
                          </tr>
                          <tr>
                            <th width="19%" height="30" bgcolor="#EDEDED">Endere&ccedil;o:</th>
                            <th colspan="5"><?php echo $row_parceiro['parceiro_endereco']; ?></th>
                          </tr>
                          <tr>
                            <th height="30" bgcolor="#EDEDED">Bairro:</th>
                            <th colspan="3"><?php echo $row_parceiro['parceiro_bairro']; ?></th>
                            <th width="13%" bgcolor="#EDEDED">Cidade:</th>
                            <th width="22%"><?php echo $row_parceiro['parceiro_cidade']; ?></th>
                          </tr>
                          <tr>
                            <th height="30" bgcolor="#EDEDED">Estado:</th>
                            <th width="14%"><?php echo $row_parceiro['parceiro_estado']; ?></th>
                            <th width="10%" bgcolor="#EDEDED">Telefone:</th>
                            <th width="22%"><?php echo $row_parceiro['parceiro_telefone']; ?></th>
                            <th bgcolor="#EDEDED">Celular:</th>
                            <th><?php echo $row_parceiro['parceiro_celular']; ?></th>
                          </tr>
                          <tr>
                            <th height="30" bgcolor="#EDEDED">e-mail:</th>
                            <th colspan="5"><?php echo $row_parceiro['parceiro_email']; ?></th>
                          </tr>
                          <tr>
                            <th height="30" bgcolor="#EDEDED">Banco:</th>
                            <th><?php echo $row_parceiro['parceiro_banco']; ?></th>
                            <th bgcolor="#EDEDED">Agência:</th>
                            <th><?php echo $row_parceiro['parceiro_agencia']; ?></th>
                            <th bgcolor="#EDEDED">Conta:</th>
                            <th><?php echo $row_parceiro['parceiro_conta']; ?></th>
                          </tr>
                        </table>
                        <p>&nbsp;</p>        	  
				        
			<?php }
				  
			  } ?>

        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <th colspan="6" bgcolor="#EDEDED">PRESTA&Ccedil;&Atilde;O DE CONTAS COM O MUNIC&Iacute;PIO</th>
          </tr>
        </table>
    	<p>&nbsp;</p>
        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <th width="34%" bgcolor="#EDEDED">Documento</th>
            <th width="22%" bgcolor="#EDEDED">Trimestral</th>
            <th width="22%" bgcolor="#EDEDED">Semestral</th>
            <th width="22%" bgcolor="#EDEDED">Anual</th>
          </tr>
          <?php $qr_obrigacao = mysql_query("SELECT * FROM obrigacoes WHERE obrigacao_projeto = '$projeto' AND obrigacao_status = '1'");
		  	    while($row_obrigacao = mysql_fetch_assoc($qr_obrigacao)) { ?>
          <tr>
            <th style="text-transform:uppercase;"><?php echo $row_obrigacao['obrigacao_nome']; ?></th>
            <th><?php if($row_obrigacao['obrigacao_periodicidade'] == 'mensal') { echo ''; } ?></th>
            <th><?php if($row_obrigacao['obrigacao_periodicidade'] == 'trimestral') { echo ''; } ?></th>
            <th><?php if($row_obrigacao['obrigacao_periodicidade'] == 'anual') { echo ''; } ?></th>
          </tr>
          <?php } ?>
        </table>
    	<p>&nbsp;</p>
        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <th colspan="6" bgcolor="#EDEDED">EXECU&Ccedil;&Atilde;O F&Iacute;SICO FINANCEIRO</th>
          </tr>
        </table>
        <p>&nbsp;</p>
        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <th width="12%" bgcolor="#EDEDED">M&ecirc;s</th>
            <th width="12%" bgcolor="#EDEDED">Nota Fiscal</th>
            <th width="11%" bgcolor="#EDEDED">Valor do Repasse</th>
            <th width="14%" bgcolor="#EDEDED">Taxa Administrativa</th>
            <th width="11%" bgcolor="#EDEDED">Taxa Parceiro</th>
            <th width="11%" bgcolor="#EDEDED">Sal&aacute;rios</th>
            <th width="11%" bgcolor="#EDEDED">Provis&atilde;o</th>
            <th width="9%"  bgcolor="#EDEDED">Extras</th>
            <th width="9%"  bgcolor="#EDEDED">Total</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">janeiro</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">fevereiro</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">mar&ccedil;o</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">abril</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">maio</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">junho</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">julho</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">agosto</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">setembro</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">outubro</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">novembro</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">dezembro</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">abonos</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th colspan="8" align="right" bgcolor="#EDEDED">Fechamento anual:</th>
            <th bgcolor="#EDEDED">&nbsp;</th>
          </tr>
        </table>
        <p>&nbsp;</p>
        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <th colspan="6" bgcolor="#EDEDED">OBRIGA&Ccedil;&Otilde;ES E RESPONSABILIDADES</th>
          </tr>
        </table>
        <p>&nbsp;</p>
        <table width="99%" border="1" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <th width="15%" bgcolor="#EDEDED">M&ecirc;s</th>
            <th width="17%" bgcolor="#EDEDED">FINANCEIRO</th>
            <th width="17%" bgcolor="#EDEDED">ADMINISTRATIVO</th>
            <th width="17%" bgcolor="#EDEDED">RECURSOS HUMANOS</th>
            <th width="17%" bgcolor="#EDEDED">EQUIPE T&Eacute;CNICA</th>
            <th width="17%" bgcolor="#EDEDED">DIRETORIA</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">janeiro</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">fevereiro</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">mar&ccedil;o</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">abril</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">maio</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">junho</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">julho</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">agosto</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">setembro</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">outubro</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">novembro</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
          <tr>
            <th align="right" bgcolor="#EDEDED">dezembro</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
          </tr>
        </table>
        <p>&nbsp;</p>
        <div id="rodape">
            <?php include('include/rodape.php'); ?>
        </div>
	</div>
</body>
</html>