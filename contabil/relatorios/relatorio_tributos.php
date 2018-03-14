<?php
include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");
include("../../classes/c_classificacaoClass.php");
//include("../../classes/c_planodecontasClass.php");
include("../../classes/ContabilLoteClass.php");
require_once("../../classes/pdf/fpdf.php");
require_once("PDFClass.php");


$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$id_master = $usuario['id_master'];


if (isset($_REQUEST['method']) && ($_REQUEST['method'] == 'consultar' || $_REQUEST['method'] == 'pdf')) {
    $id_projeto = addslashes($_REQUEST['projeto']);
    $data_ini = ConverteData($_REQUEST['data_ini'], 'Y-m-d');
    $data_fim = ConverteData($_REQUEST['data_fim'], 'Y-m-d');
    $query = "SELECT a.*, cofins.valor AS ret_cofins,csll.valor AS ret_csll, inss.valor AS ret_inss, `ir`.valor AS ret_ir, iss.valor AS ret_iss,  `pis`.valor AS ret_pis, pcc.valor AS ret_pis_cofins_csll
                FROM prestadorservico a
                LEFT JOIN retencao cofins ON a.id_prestador = cofins.id_prestador AND cofins.id_retencao_tipo = 1
                LEFT JOIN retencao csll ON a.id_prestador = csll.id_prestador AND csll.id_retencao_tipo = 2
                LEFT JOIN retencao inss ON a.id_prestador = inss.id_prestador AND inss.id_retencao_tipo = 3
                LEFT JOIN retencao `ir` ON a.id_prestador = `ir`.id_prestador AND `ir`.id_retencao_tipo = 4
                LEFT JOIN retencao iss ON a.id_prestador = iss.id_prestador AND iss.id_retencao_tipo = 5
                LEFT JOIN retencao `pis` ON a.id_prestador = `pis`.id_prestador AND `pis`.id_retencao_tipo = 6
                LEFT JOIN retencao `pcc` ON a.id_prestador = `pcc`.id_prestador AND `pcc`.id_retencao_tipo = 7
                WHERE a.id_projeto = {$id_projeto}";
    $r = mysql_query($query);

    while ($row = mysql_fetch_assoc($r)) {
        $prestador[$row['id_prestador']]['dados'] = $row;

        switch ($_REQUEST['tipo_data']) {
            case 1:
                $data = "a.DataEmissao BETWEEN '$data_ini' AND '$data_fim'";
                break;
            case 2:
                $data = "liq.data_pg BETWEEN '$data_ini' AND '$data_fim'";
                break;
        }
                
        $query = "SELECT a.*, liq.*, cs.descricao AS historico, liq.data_vencimento, liq.data_pg, lanc.data_lancamento, a.Discriminacao AS historico
                    FROM nfse a
                    INNER JOIN nfse_codigo_servico cs ON a.CodigoTributacaoMunicipio = cs.codigo
                    INNER JOIN nfse_saidas b ON a.id_nfse = b.id_nfse
                    INNER JOIN saida liq ON b.id_saida = liq.id_saida AND liq.status > 0 AND liq.tipo_nf = 0
                    LEFT JOIN nfse_lancamentos_assoc c ON a.id_nfse = c.id_nfse
                    LEFT JOIN contabil_lancamento lanc ON c.id_lancamento = lanc.id_lancamento
                    /*LEFT JOIN saida `ir` ON b.id_saida = ir.id_saida AND ir.status = 2 AND ir.tipo_nf = 1
                    LEFT JOIN saida iss ON b.id_saida = iss.id_saida AND iss.status = 2 AND iss.tipo_nf = 2
                    LEFT JOIN saida `pis` ON b.id_saida = pis.id_saida AND pis.status = 2 AND pis.tipo_nf = 3
                    LEFT JOIN saida inss ON b.id_saida = inss.id_saida AND inss.status = 2 AND inss.tipo_nf = 4*/
                    WHERE a.PrestadorServico = {$row['id_prestador']} AND a.status > 0 AND $data;";
        $r2 = mysql_query($query) or die(mysq_error($r2));
        while ($row2 = mysql_fetch_assoc($r2)) {
            $prestador[$row['id_prestador']]['nf'][$row2['id_nfse']] = $row2;
        }
    }
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'pdf') {
    require_once 'relatorio_tributos_pdf.php';
    exit();
}


// -----------------------------------------------------------------------------

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$nome_pagina = "Relatório de Classificação de Fornecedores";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Relatórios Contábeis" => "index.php");

$id_projeto_selected = isset($_REQUEST['projeto']) ? $_REQUEST['projeto'] : NULL;
$dt_ini_selected = isset($_REQUEST['data_ini']) ? $_REQUEST['data_ini'] : NULL;
$dt_fim_selected = isset($_REQUEST['data_fim']) ? $_REQUEST['data_fim'] : NULL;
$tipo_data = isset($_REQUEST['tipo_data']) ? $_REQUEST['tipo_data'] : NULL;

$optProjetos = GlobalClass::carregaProjetosByRegiao($id_regiao);
$optPrestadores = $id_prestador_selected > 0 ? GlobalClass::carregaPrestadorByProjeto($id_prestador_selected) : ['-1' => 'Selecione o Projeto'];
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link rel="shortcut icon" href="/intranet/favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-contabil-header hidden-print">
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- <?= $nome_pagina ?></small></h2>
                    </div>
                    <form action="" method="post" name="form" id="form" class="form-horizontal top-margin hidden-print" enctype="multipart/form-data">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Projeto</label>
                                    <div class="col-sm-9">
                                        <?= montaSelect($optProjetos, $id_projeto_selected, 'class="form-control validate[required,custom[select]]" name="projeto" id="projeto"') ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Período</label>
                                    <div class="col-sm-7">
                                        <div class="input-group">
                                            <input name="data_ini" id="data_ini" class="form-control data validate[required]" value="<?= $dt_ini_selected ?>">
                                            <span class="input-group-addon">à</span>
                                            <input name="data_fim" id="data_fim" class="form-control data validate[required]" value="<?= $dt_fim_selected ?>">
                                            <span class="input-group-addon"> - </span>
                                            <?= montaSelect([1 => 'Por Data de Emissão', 2 => 'Por Data de Pagamento'], $tipo_data, 'class="form-control data validate[required]" name="tipo_data" id="tipo_data"'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <?php if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'consultar') { ?>
                                    <button type="submit" class="btn btn-default" name="method" value="pdf"><i class="fa fa-file-pdf-o text-danger"></i> PDF</button>
                                <?php } ?>
                                <button type="submit" class="btn btn-primary" name="method" value="consultar"><i class="fa fa-search"></i> Consultar</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>

        <?php if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'consultar') { ?>
            <table class="table table-condensed table-bordered text-sm">
                <thead>
                    <tr class="primary">
                        <th rowspan="2" class="text-center">Item</th>
                        <th rowspan="2" class="text-center">Nº Registro</th>
                        <th rowspan="2" class="text-center">Nº Contrato</th>
                        <th colspan="4" class="text-center">Data</th>
                        <th rowspan="2" class="text-center">Nota Fiscal</th>
                        <th rowspan="2" class="text-center">Codigo Serviço Lei 116/03</th>
                        <th rowspan="2" class="text-center">Valor Bruto</th>
                        <th rowspan="2" class="text-center">Iss Retido Emissao</th>
                        <th rowspan="2" class="text-center">Iss Retido Baixa</th>
                        <th rowspan="2" class="text-center">IRF-1708</th>
                        <th rowspan="2" class="text-center">PIS-Ret</th>
                        <th rowspan="2" class="text-center">COFINS-Ret</th>
                        <th rowspan="2" class="text-center">CSLL-Ret</th>
                        <th rowspan="2" class="text-center">Código 5952</th>
                        <th rowspan="2" class="text-center">INSS-2100, etc</th>
                        <th rowspan="2" class="text-center">INSS-2631, etc</th>
                        <th rowspan="2" class="text-center">Soma Retenções</th>
                        <th rowspan="2" class="text-center">Valor Líquido</th>
                    </tr>
                    <tr  class="primary">
                        <th>Emissão</th>
                        <th>Fiscal/Lancto. Contábil</th>
                        <th>Vencimento</th>
                        <th>Pagamento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($prestador as $k => $v) {
                        if (count($v['nf']) > 0) {
                            ?>
                            <tr class="info text-bold">
                                <td colspan="10"><?= $v['dados']['c_razao'] ?> - <?= $v['dados']['c_cnpj'] ?> - <?= $v['dados']['c_cidade'] ?> - <?= $v['dados']['c_uf'] ?></td>
                                <td class="text-right"><?= ($v['dados']['c_uf'] != $mun) ? number_format($v['dados']['ret_iss'], 2, ',', '.') : '-' ?></td>
                                <td class="text-right"><?= ($v['dados']['c_uf'] == $mun) ? number_format($v['dados']['ret_iss'], 2, ',', '.') : '-' ?></td>
                                <td class="text-right"><?= number_format($v['dados']['ret_ir'], 2, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($v['dados']['ret_pis'], 2, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($v['dados']['ret_cofins'], 2, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($v['dados']['ret_csll'], 2, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($v['dados']['ret_pis_cofins_csll'], 2, ',', '.') ?></td>
                                <td class="text-right"><?= '' ?></td>
                                <td class="text-right"><?= number_format($v['dados']['ret_inss'], 2, ',', '.') ?></td>
                                <td colspan="10"></td>
                            </tr>

                            <?php
                            $mun = 'GO';
                            foreach ($v['nf'] as $k_nf => $nfse) {
                                $soma_retencoes = $nfse['ValorIss'] + $nfse['ValorIr'] + $nfse['ValorPis'] + $nfse['ValorCofins'] + $nfse['ValorCsll'] + $nfse['ValorPisCofinsCsll'] + $nfse['ValorInss'];
                                ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= $nfse['id_nfse'] ?></td>
                                    <td><?= $v['dados']['numero'] ?></td>
                                    <td><?= (!empty($nfse['DataEmissao'])) ? converteData($nfse['DataEmissao'], 'd/m/Y') : '-' ?></td>
                                    <td><?= (!empty($nfse['data_lancamento'])) ? converteData($nfse['data_lancamento'], 'd/m/Y') : '-' ?></td>
                                    <td><?= (!empty($nfse['data_vencimento'])) ? converteData($nfse['data_vencimento'], 'd/m/Y') : '-' ?></td>
                                    <td><?= (!empty($nfse['data_pg'])) ? converteData($nfse['data_pg'], 'd/m/Y') : '-' ?></td>
                                    <td><?= $nfse['Numero'] ?></td>
                                    <td><?= $nfse['CodigoTributacaoMunicipio'] //. ' - ' . $nfse['descricao']         ?></td>
                                    <td class="text-right"><?= number_format($nfse['ValorServicos'], 2, ',', '.') ?></td>
                                    <td class="text-right"><?= ($v['dados']['c_uf'] != $mun) ? number_format($nfse['ValorIss'], 2, ',', '.') : '-' ?></td>
                                    <td class="text-right"><?= ($v['dados']['c_uf'] == $mun) ? number_format($nfse['ValorIss'], 2, ',', '.') : '-' ?></td>
                                    <td class="text-right"><?= number_format($nfse['ValorIr'], 2, ',', '.') ?></td>
                                    <td class="text-right"><?= number_format($nfse['ValorPis'], 2, ',', '.') ?></td>
                                    <td class="text-right"><?= number_format($nfse['ValorCofins'], 2, ',', '.') ?></td>
                                    <td class="text-right"><?= number_format($nfse['ValorCsll'], 2, ',', '.') ?></td>
                                    <td class="text-right"><?= number_format($nfse['ValorPisCofinsCsll'], 2, ',', '.') ?></td>
                                    <td class="text-right"><?= number_format(0, 2, ',', '.') ?></td>
                                    <td class="text-right"><?= number_format($nfse['ValorInss'], 2, ',', '.') ?></td>
                                    <td class="text-right"><?= number_format($soma_retencoes, 2, ',', '.') ?></td>
                                    <td class="text-right"><?= number_format($nfse['ValorLiquidoNfse'], 2, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td>Histórico:</td>
                                    <td colspan="20"><?= $nfse['historico'] ?></td>
                                </tr>
                                <?php
                                $i++;
                            }
                        }
                    }
                    ?>

                </tbody>
            </table>
        <?php } ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <?php include_once '../../template/footer.php'; ?>
                </div>
            </div>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>

        <script>
            $(document).ready(function () {
                $('#form').validationEngine();

                $('#projeto').ajaxGetJson('../../methods.php', {method: 'carregaPrestadores'}, null, 'id_prestador');
            });
        </script>
    </body>
</html>