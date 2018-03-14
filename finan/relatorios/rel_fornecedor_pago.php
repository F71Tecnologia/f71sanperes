<?php
// session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/NFSeClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objNFSe = new NFSe();
$global = new GlobalClass();

$id_projeto = $_REQUEST['projeto'];
$id_regiao = $usuario['id_regiao'];

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if (isset($_REQUEST['consultar'])) {
    $data_ini = converteData($_REQUEST['data_ini']);
    $data_fim = converteData($_REQUEST['data_fim']);
    $where = ($_REQUEST['id_fornecedor'] !== '-1') ? "AND REPLACE(REPLACE(REPLACE(b.c_cnpj, '-', ''), '.', ''), '/', '') = '{$_REQUEST['id_fornecedor']}'" : '';
    $where .= ($_REQUEST['status']) ? " AND A.status IN ({$_REQUEST['status']})" : " AND A.status > 0";
    $projeto = ($_REQUEST['id_projeto'] !== '-1') ? "AND a.id_projeto = {$_REQUEST['id_projeto']}" : '';
    $query = "SELECT a.id_projeto,a.valor_bruto,REPLACE(a.valor,',','.') AS valor,a.especifica,c.nome AS subgrupo,
                b.c_razao,REPLACE(REPLACE(REPLACE(b.c_cnpj, '-', ''), '.', ''), '/', '') AS cnpj
                FROM saida AS a
                INNER JOIN prestadorservico AS b ON a.id_prestador = b.id_prestador
                iNNER JOIN entradaesaida_subgrupo AS c on a.entradaesaida_subgrupo_id = c.id
                WHERE a.id_regiao = $id_regiao AND (data_vencimento BETWEEN '$data_ini' AND '$data_fim') AND a.status = 2 $where $projeto";
    $result = mysql_query($query);
    
            function formato_valor($valor) {
            if (strpos($valor, ',') == FALSE) {
                return number_format($valor, 2, ',', '.');
            } else {
                return $valor;
            }
        } 
    
    while ($row = mysql_fetch_assoc($result)) {
        if (isset($saidas[$row['cnpj']][$row['id_projeto']]['liquido'])) {
            $saidas[$row['cnpj']][$row['id_projeto']]['liquido'] += (float) str_replace(',', '.', $row['valor']);
        } else {
            $saidas[$row['cnpj']][$row['id_projeto']]['liquido'] = (float) str_replace(',', '.', $row['valor']);
        }

        if (isset($saidas[$row['cnpj']][$row['id_projeto']]['bruto'])) {
            $saidas[$row['cnpj']][$row['id_projeto']]['bruto'] += (float) $row['valor_bruto'];
        } else {
            $saidas[$row['cnpj']][$row['id_projeto']]['bruto'] = (float) $row['valor_bruto'];
        }

        if (isset($saidas[$row['cnpj']]['total_bruto'])) {
            $saidas[$row['cnpj']]['total_bruto'] += (float) $row['valor_bruto'];
        } else {
            $saidas[$row['cnpj']]['total_bruto'] = (float) $row['valor_bruto'];
        }

        if (isset($saidas[$row['cnpj']]['total_liquido'])) {
            $saidas[$row['cnpj']]['total_liquido'] += (float) $row['valor'];
        } else {
            $saidas[$row['cnpj']]['total_liquido'] = (float) $row['valor'];
        }

        $saidas[$row['cnpj']]['nome'] = $row['c_razao'];
        $saidas[$row['cnpj']]['cnpj'] = $row['cnpj'];
        $saidas[$row['cnpj']]['subgrupo'] = $row['subgrupo'];
    }


    $projeto2 = ($_REQUEST['id_projeto'] !== '-1') ? "AND id_projeto = {$_REQUEST['id_projeto']}" : '';
    $queryp = "SELECT id_projeto, nome FROM projeto WHERE id_regiao = $id_regiao $projeto2";
    $resultp = mysql_query($queryp);
    while ($row = mysql_fetch_assoc($resultp)) {
        $list_projetos[] = $row;
    }
}


$selectProjeto = getProjetos($id_regiao);

$query = "SELECT REPLACE(REPLACE(REPLACE(a.c_cnpj, '-', ''), '.', ''), '/', '') AS cnpj, a.c_razao 
            FROM prestadorservico AS a
            iNNER JOIN saida AS b ON a.id_prestador = b.id_prestador
            WHERE b.status = 2 AND b.id_regiao = $id_regiao
            GROUP BY REPLACE(REPLACE(REPLACE(a.c_cnpj, '-', ''), '.', ''), '/', '')
            ORDER BY a.c_razao";
$result = mysql_query($query);
$selectFornecedor[-1] = "Todos";
while ($row = mysql_fetch_assoc($result)) {
    $selectFornecedor[$row['cnpj']] = $row['c_razao'];
}

$dataIni = (isset($_REQUEST['data_ini'])) ? $_REQUEST['data_ini'] : '';
$dataFim = (isset($_REQUEST['data_fim'])) ? $_REQUEST['data_fim'] : '';
$op_fornecedor = (isset($_REQUEST['id_fornecedor'])) ? $_REQUEST['id_fornecedor'] : '';
$op_projeto = (isset($_REQUEST['id_projeto'])) ? $_REQUEST['id_projeto'] : '';


$nome_pagina = 'Relatório de Fornecedores - Saídas Pagas';
$breadcrumb_config = array("nivel" => "../", "key_btn" => "4", "area" => "Financeiro", "id_form" => "form1", "ativo" => $nome_pagina);
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />

    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <!--<div class="container-full over-x">-->
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?= $nome_pagina ?></small></h2></div>
            <?php if (isset($_GET['s'])) { ?><div class="alert alert-dismissable alert-success text-bold"><button type="button" class="close" data-dismiss="alert">×</button>Saídas Geradas com Sucesso!</div><?php } ?>
            <?php if (isset($_GET['e'])) { ?><div class="alert alert-dismissable alert-danger text-bold"><button type="button" class="close" data-dismiss="alert">×</button>Erro: <?= $_GET['e'] ?>. Entre em contato com o suporte.</div><?php } ?>
            <form action="rel_fornecedor_pago.php" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="fornecedor" class="col-lg-2 control-label">Projeto</label>
                            <div class="col-lg-9">
                                <?= montaSelect($selectProjeto, $op_projeto, 'name="id_projeto" id="projeto" class="input form-control"') ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fornecedor" class="col-lg-2 control-label">Fornecedor</label>
                            <div class="col-lg-9">
                                <?= montaSelect($selectFornecedor, $op_fornecedor, 'name="id_fornecedor" id="fornecedor" class="input form-control"') ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fornecedor" class="col-lg-2 control-label">Status</label>
                            <div class="col-lg-9">
                                <?= montaSelect([0=>'Todos', 1=>'À Pagar', 2=>'Pagas'], $_REQUEST['status'], 'name="status" id="status" class="input form-control"') ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Visualizar Saídas de</label>
                            <div class="col-lg-4">
                                <div class="input-group">
                                    <input type="text" class="input form-control data validate[required]" name="data_ini" id="data_ini" placeholder="Data Inicial" value="<?php echo $dataIni; ?>">
                                    <span class="input-group-addon">até</span>
                                    <input type="text" class="input form-control data validate[required]" name="data_fim" id="data_fim" placeholder="Data Final" value="<?php echo $dataFim; ?>">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="consultar" id="filt" value="Consultar" class="btn btn-primary" />
                    </div>
                </div>

                <?php
                if ($_REQUEST['consultar']) {
                    if (count($saidas) > 0) {
                        ?>
                        <p style="text-align: right; margin-top: 20px"><button type="button" onclick="tableToExcel('tbRelatorio', 'Pagamento de Fornecedores')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button></p>
                        <table class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle' id="tbRelatorio">
                            <thead>
                                <tr class="bg-primary">
                                    <th rowspan="2" class="text-center">Empresa</th>
                                    <th rowspan="2" class="text-center">Serviço</th>
                                    <?php foreach ($list_projetos as $value) { ?>
                                        <th colspan="2" class="text-center"><?= $value['nome'] ?></th>
                                    <?php } ?>
                                    <th rowspan="2" class="text-center">Total Bruto (R$)</th>
                                    <th rowspan="2" class="text-center">Total Liquido (R$)</th>
                                </tr>
                                <tr class="bg-primary">
                                    <?php foreach ($list_projetos as $value) { ?>
                                        <th class="text-center">Bruto (R$)</th>
                                        <th class="text-center">Liquido (R$)</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($saidas as $cnpj => $value) { ?>
                                    <tr>
                                        <td><?= $value['nome'] ?></td>
                                        <td><?= $value['subgrupo'] ?></td>

                                        <?php foreach ($list_projetos as $proj) { ?>
                                            <td class="text-right"><?= number_format($value[$proj['id_projeto']]['bruto'], 2, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($value[$proj['id_projeto']]['liquido'], 2, ',', '.') ?></td>
                                        <?php } ?>

                                        <td class="text-right"><?= number_format($value['total_bruto'], 2, ',', '.') ?></td>
                                        <td class="text-right"><?= number_format($value['total_liquido'], 2, ',', '.') ?></td>

                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>


                    <?php } else { ?>
                        <div class="alert alert-danger top30">
                            Nenhum registro encontrado
                        </div>
                        <?php
                    }
                }
                ?>
            </form>
            <?php include('../../template/footer.php'); ?>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
                $(function () {
                    $("#form1").validationEngine({promptPosition: "topRight"});
                });
        </script>
    </body>
</html>
