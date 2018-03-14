<?php
if (!include_once(ROOT_LIB . 'fw.class.php'))
    die('Não foi possível incluir ' . ROOT_LIB . 'fw.class.php a partir de ' . __FILE__);

$rh = new \hub\FwClass();

$charset = mysql_set_charset('utf8');

$objCombustivel = new Abastecimento();
$dadosCombustivel = $objCombustivel->getAbastecimentoAPagar($usuario['id_regiao']);

$objReembolso = new Reembolso();
$dadosReembolso = $objReembolso->getReembolsoAPagar($usuario['id_regiao']);

$objViagem = new ViagemClass();
$dadosViagem = $objViagem->getViagemByStatus([1, 2, 3]);
$tituloViagem = [1 => 'AGUARDANDO APROVAÇÃO', 2 => 'AGUARDANDO ACERTO', 3 => 'AGUARDANDO APROVAÇÃO DO ACERTO'];

$objCaixinha = new CaixinhaClass();
$arrayItens = $objCaixinha->getItensDespesas();

$objFinanceiro = new Financeiro();
$arrayTipos = $objFinanceiro->getTiposFiltro();

$objSaida = new Saida();

$objEntrada = new Entrada();

$objNfse = new NFSe();
//$qtdNfse = $objNfse->getQtdLiberadaByRegiao($usuario['id_regiao']);
$arrayQtdNfse = $objNfse->getQtdLiberada();

//echo '<pre>';print_r($dadosReembolso);echo "</pre>";
?>
<style>
    .testesapn {
        z-index: 10; 
        float: right;
        font-size: 8px;
        padding: 1px 2px 0px 1px;
        margin-right: -4px;
    }
    .btnAcoes { width: 24px; height: 22px; }
</style>
<div class="row">
    <?php if ($acoes->verifica_permissoes(9) && count($dadosCombustivel) != 0) { ?>
        <div class="col-xs-6">
            <table class="table table-condensed table-bordered text-sm valign-middle" id='TabelaCombustivel'>
                <thead>
                    <tr class="bg-primary valign-middle">
                        <th colspan="5">CONTROLE DE COMBUST&Iacute;VEL</th>
                    </tr>
                </thead>
                <tbody>
                <span id="FimComb"></span>
                <?php
                $cont = "0";
                if (count($dadosCombustivel) != 0) {
                    foreach ($dadosCombustivel as $RowComb) {
                        $NOME = explode(" ", $RowComb['nomeFuncionario']);
                        ?>
                        <tr class="valign-middle title-combustivel" data-key="c<?= $RowComb['id_combustivel'] ?>">
                            <th><?= $RowComb['nomeRegiao'] ?></th>
                            <th><?= (!empty($RowComb['nomeFuncionario'])) ? $RowComb['nomeFuncionario'] : $RowComb['nome'] ?></th>
                            <th><?= $RowComb['destino'] ?></th>
                            <th><?= $RowComb['data'] ?></th>
                            <th class="text-center">
                                <a class="btn btn-xs btn-success liberarCombustivel" data-key="<?= $RowComb['id_combustivel'] ?>" href='javascript:;'>Liberar</a>
                            </th>
                        </tr>
                        <?php
                        $cont ++;
                    }
                } else {
                    ?>
                    <tr><td colspan="5" class="warning">Sem Registros Nesta Região.</td></tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /////PERMISSAO CONTROLE DE REEMBOLSO
    if ($acoes->verifica_permissoes(11) && count($dadosReembolso) != 0) {
        ?>
        <div class="col-xs-6">
            <table class="table table-condensed table-bordered text-sm">
                <tr class="bg-primary valign-middle">
                    <td class="titulo_tabela" colspan="5">CONTROLE DE REEMBOLSO:</td>
                </tr>
                <?php if (count($dadosReembolso) != 0) { ?>
                    <?php
                    foreach ($dadosReembolso as $RowReem) {
                        if ($RowReem['funcionario'] == '1') {
                            $NOME = $RowReem['nomeFuncionario'];
                        } else {
                            $NOME = $RowReem['nome'];
                        }
                        ?>
                        <tr class="valign-middle reembolso<?= $RowReem['id_reembolso'] ?>">
                            <td width='5%' class="text-center" class="linhaspeq"><?= $RowReem['id_reembolso'] ?></td>
                            <td width='40%' class="text-left"align='left'><?= $RowReem['nome'] ?></td>
                            <td width='30%' class="text-center"class="text-center"><?= $RowReem['data'] ?></td>
                            <td width="20%" class="text-right" ><b><?= number_format($RowReem['valor'], 2, ",", ".") ?></b></td>
                            <td width="5%" class="text-center" class="linhaspeq" >
                                <button type="button" class='btn btn-xs btn-success verReembolso' data-idreembolso="<?= $RowReem['id_reembolso'] ?>" data-toggle="tooltip" title="Confirmar Reembolso">
                                    <i class="fa fa-check" alt='Editar'></i>
                                </button>
                            </td>
                        </tr>
                        <?php
                        $cont ++;
                        $soma += $RowReem['valor'];
                    }
                    ?>
                    <tr>
                        <td colspan='3' align="right"><b>TOTAL DE REEMBOLSO:</b></td>
                        <td class="text-right"><b>R$ <?= number_format($soma, 2, ',', '.') ?></b></td>
                        <td></td>
                    </tr>
                    <?php unset($soma); ?>
                <?php } else { ?>
                    <tr><td colspan="5" class="warning">Sem Registros Nesta Região.</td></tr>
                <?php } ?>
            </table>
        </div>
    <?php } ?>
    <?php if ($acoes->verifica_permissoes(11) || 1) { ?>
        <?php if (count($dadosViagem) != 0) { ?>
            <div class="col-xs-12">
                <table class="table table-condensed table-bordered text-sm valign-middle">
                    <tr class="bg-primary valign-middle">
                        <td class="titulo_tabela" colspan="6">SOLICITAÇÕES DE VIAGENS:</td>
                    </tr>
                    <?php foreach ($dadosViagem as $RowViagem) { ?>
                        <?php if ($auxStatus != $RowViagem['status']) { ?>
                            <tr class="valign-middle info">
                                <td class="titulo_tabela" colspan="6"><?php echo $tituloViagem[$RowViagem['status']] ?></td>
                            </tr>
                            <?php $auxStatus = $RowViagem['status']; ?>
                        <?php } ?>
                        <tr class="valign-middle">
                            <td class="text-center"><?= $RowViagem['id_viagem'] ?></td>
                            <td class="text-left"><?= $RowViagem['nome'] ?></td>
                            <td class="text-left"><?= $RowViagem['destino'] ?></td>
                            <td class="text-center"><?= "{$RowViagem['data_ini']} até {$RowViagem['data_fim']}" ?></td>
                            <td class="text-right"><b><?= number_format($RowViagem['valor'], 2, ",", ".") ?></b></td>
                            <td class="text-center">
                                <button type="button" class='btn btn-xs btn-info verViagem' data-id="<?= $RowViagem['id_viagem'] ?>" data-toggle="tooltip" title="Detalhe">
                                    <i class="fa fa-search"></i>
                                </button>
                                <?php if ($RowViagem['status'] == 1) { ?>
                                    <a href='viagem/doc_solicitacao.php?id=<?php echo $RowViagem['id_viagem'] ?>' target='_blank' class='btn btn-xs btn-default' title='Formulário Acerto'><i class='fa fa-print'></i></a>
                                <?php } ?>
                                <?php if ($RowViagem['status'] == 3) { ?>
                                    <a href='viagem/doc_acerto.php?id=<?php echo $RowViagem['id_viagem'] ?>' target='_blank' class='btn btn-xs btn-default' title='Formulário Acerto'><i class='fa fa-print'></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                        $cont ++;
                        $soma += $RowViagem['valor'];
                    }
                    ?>
                    <tr>
                        <td colspan='4' align="right"><b>TOTAL:</b></td>
                        <td class="text-right"><b>R$ <?= number_format($soma, 2, ',', '.') ?></b></td>
                        <td></td>
                    </tr>
                    <?php unset($soma); ?>
                </table>
            </div>
        <?php } ?>
    <?php } ?>
</div>

<?php if (count($arrayQtdNfse) > 0) { ?>
    <div class="row">
        <div class="col-xs-8 col-xs-offset-2">
            <div class="panel panel-warning">
                <div class="panel-heading" id="headingOne">
                    <a role="button" class="text-warning" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="text-decoration:none">Notas Fiscais Liberadas em <?= count($arrayQtdNfse) ?> Regiões</a></div>
                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">
                        <!--<i class="fa fa-info-circle fa-lg"></i> <strong><?= $qtdNfse ?> Notas Fiscais de Serviço</strong> Liberadas para esta Região.-->
                        <ul>
                            <?php foreach ($arrayQtdNfse as $key => $value) { ?>
                                <li><strong><?php echo $value['qtd'] ?> Notas Fiscais de Serviço</strong> Liberadas na Região <strong><?php echo "{$value['id_projeto']} - {$value['nome']}" ?></strong>.</li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<hr>
<form action="solicitacao_pagamento.php" target="_blank" id="form" method="post">
    <div class="panel panel-success">
        <div class="panel-heading">
            <div class="col-xs-8 no-padding">
                <i class="fa fa-arrow-up text-danger"></i><i class="fa fa-arrow-down text-success"></i> CONTAS PRINCIPAIS
            </div>
            <div class="col-xs-4 no-padding text-right">
                <button type="button" class="btn btn-xs btn-info" id="group_all" data-toggle="tooltip" title="Agrupar Selecionadas"><i class="fa fa-clone"></i> Agrupar</button>
                <button type="button" class="btn btn-xs btn-default" id="print_all" data-toggle="tooltip" title="Imprimir Selecionadas"><i class="fa fa-print"></i> Imprimir</button>
                <?php if($acoes->verifica_permissoes(118)) { ?><button type="button" class="btn btn-xs btn-success" id="Pagar_all" data-toggle="tooltip" title="Pagar Selecionadas"><i class="fa fa-check"></i> Confirmar</button><?php } ?>
                <?php if($acoes->verifica_permissoes(119)) { ?><button type="button" class="btn btn-xs btn-danger" id="Deletar_all" data-toggle="tooltip" title="Deletar Selecionadas"><i class="fa fa-ban"></i> Deletar</button><?php } ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="panel-body no-padding-hr no-padding-b">
            <div class="form-group">
                <!--            <div class="col-sm-4">
                                <label class="text-sm">Tipo Despesa</label>
                <?php echo montaSelect($arrayTipos, null, 'id="filtroTipo" class="form-control input-sm"') ?>
                            </div>-->
                <div class="col-sm-4">
                    <!--<label class="text-sm">Tipo Despesa</label>-->
                    <?php echo montaSelect($global->carregaProjetosByMaster($usuario['id_master'], array("0" => "Todos os Projetos")), $_REQUEST['id_projeto'], "id='id_projeto' name='id_projeto' class='form-control input-sm validate[required,custom[select]]'") ?>
                </div>
                <div class="col-sm-4">
                    <!--<label class="text-sm">Período</label>-->
                    <div class="input-group">
                        <input type="text" class="form-control data input-sm" name="filtroDataIni" id="filtroDataIni" value="<?php echo date('01/m/Y') ?>">
                        <div class="input-group-addon"> até </div>
                        <input type="text" class="form-control data input-sm" name="filtroDataFim" id="filtroDataFim" value="<?php echo date('t/m/Y') ?>">
                    </div>
                </div>
                <div class="col-sm-4">
                    <!--<label style="display: block;">&nbsp;</label>-->
                    <button type="button" class="btn btn-sm btn-primary" id="btnFiltro"><i class="fa fa-filter"></i></button>
                </div>
            </div>
        </div>
        <div class="panel-body no-padding-hr no-padding-b">
            <div class="panel-group no-margin-b">
                <?php
                while ($row_bancos = mysql_fetch_assoc($dadosBanco)) {
                    if($row_bancos['principal'] == 0 && $auxPrincipal == 0) {
                        echo '</div></div></div>
                        <div class="panel panel-success">
                            <div class="panel-heading pointer" id="banco_secundario">
                                <div class="col-xs-12 no-padding">
                                    <i class="fa fa-arrow-up text-danger"></i><i class="fa fa-arrow-down text-success"></i> CONTAS SECUNDÁRIAS
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="panel-body no-padding-hr no-padding-b banco_secundario hide"><div class="panel-group no-margin-b">';
                        $auxPrincipal = 1;
                    }
                    
                    $dadosFinanceiro = $objFinanceiro->getSaidaEntradaBanco(null, $row_bancos['id_banco']);
                    
                    $tipo_servico = $rh->Cnab240Produto->setOperacao('C')->setBanco($row_bancos['id_nacional'])->select()->db->getRowGroupConcat('servico');
                    ?>
                    <div class="panel panel-default panel-banco">
                        <div class="panel-heading banco-title pointer load_table_financeiro" data-key="<?= $row_bancos['id_banco'] ?>" data-regiao="<?= $usuario['id_regiao'] ?>" data-id_nacional="<?= $row_bancos['id_nacional'] ?>" data-agencia="<?= $row_bancos['agencia'] ?>" data-conta="<?= $row_bancos['conta'] ?>" data-toggle="collapse" data-parent="#accordion" href="#<?= $row_bancos[id_banco] ?>">
                            <div class="col-xs-4 text-sm no-padding-l">
                                <?= "{$row_bancos['id_banco']} - {$row_bancos['nome_banco']} (CC: {$row_bancos['conta']} / Ag: {$row_bancos['agencia']})" ?>
                            </div>
                            <div class="col-xs-8 text-sm">
                                <div class="col-xs-2 no-padding-hr">Remessa: <?= count($dadosFinanceiro['11']) + count($dadosFinanceiro['12']) + count($dadosFinanceiro['13']) ?></div>
                                <div class="col-xs-2 no-padding-hr">Hoje: <?= count($dadosFinanceiro['1']) ?></div>
                                <div class="col-xs-2 no-padding-hr">Vencidas: <?= count($dadosFinanceiro['2']) ?></div>
                                <div class="col-xs-2 no-padding-hr">Próximas: <?= count($dadosFinanceiro['3']) ?></div>
                                <div class="col-xs-2 no-padding-hr">Entradas: <?= count($dadosFinanceiro['4']) ?></div>
                                <div class="col-xs-2 text-right no-padding text-bold">R$ <?= number_format($dadosFinanceiro['totalizador_saida'], 2, ',', '.'); ?></div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="panel-body banco panel-collapse collapse" id="<?= $row_bancos[id_banco] ?>">
                            <div class="panel panel-default margin_b10">
                                <div class="panel-body">
                                    <div class="col-md-3 col-lg-3 col-sm-3" style="padding:6px;">
                                        <button type="button" class="btn btn-xs btn-success group_button a" id="processar_retorno" data-toggle="tooltip" title="Processar Arquivo de Retorno Bancário"><i class="fa fa-check"></i> Retorno</button>
                                        <button type="button" class="btn btn-xs btn-success group_button b" id="gerar_remessa" data-toggle="tooltip" title="Gerar Arquivo de Remessa Bancária" disabled="disabled"><i class="fa fa-check"></i> Remessa</button>
                                        <span class="c pull-right">Serviço:</span>
                                    </div>
                                    <div class="col-md-3 col-lg-3 col-sm-3">
                                        <?= $rh->Cnab240Servico->setTipoServico($tipo_servico)->select()->cmbBox->getHtml(array('value' => 'tipo_servico', 'text' => 'descricao', 'id' => 'tipo_servico', 'class' => 'd group_cmbbox validate[required,custom[select]] form-control input-sm tipo_servico', 'name' => 'tipo_servico', 'charset' => 'iso-8859-1')) ?>            
                                    </div>
                                    <div class="e tipo_forma col-md-4 col-lg-4 col-sm-4"></div>
                                </div>
                            </div>
                            <table id="example<?php echo $row_bancos['id_banco'] ?>" class="table table-condensed table-bordered text-sm valign-middle" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th width='16%'></th>
                                        <th>Cod.</th>
                                        <th>Nome</th>
                                        <th>Nº</th>
                                        <th>Vencimento</th>
                                        <th>Valor</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th width='16%'></th>
                                        <th>Cod.</th>
                                        <th>Nome</th>
                                        <th>Nº</th>
                                        <th>Vencimento</th>
                                        <th>Valor</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <?php unset($dados); ?>
                <?php } ?>
            </div>
        </div>
    </div>
</form>