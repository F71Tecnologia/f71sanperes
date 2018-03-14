<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include("../../conn.php");
include("../../wfunction.php");
include("../../empresa.php");
include("../../classes/BotoesClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$master = mysql_fetch_assoc(mysql_query("SELECT * FROM master WHERE id_master = {$usuario['id_master']} LIMIT 1;"));
$global = new GlobalClass();

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$regioes_usuario = array_keys(getRegioes());
unset($regioes_usuario[0]);

$id_projeto = $_REQUEST['projeto'];
$id_banco = $_REQUEST['id_banco'];
$data_ini = (!empty($_REQUEST['data_ini'])) ? $_REQUEST['data_ini'] : "01/" . date('m/Y');
$data_fim = (!empty($_REQUEST['data_fim'])) ? $_REQUEST['data_fim'] : date('t/m/Y');
$auxStatus = ($_REQUEST['status_lancamento']) ? $_REQUEST['status_lancamento'] : '1,2';


//$bancos_opt = ['-1' => 'Selecione', '0' => 'Todos os Bancos'];
$bancos_opt = ['0' => 'Todos os Bancos'];
$query = "SELECT * FROM bancos WHERE id_regiao IN(" . implode(', ', $regioes_usuario) . ")";
$result = mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {
    $bancos_opt[$row['id_banco']] = $row['id_banco'] . ' - ' . $row['nome'];
}

if (isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && (isset($_REQUEST['filtrar']) || isset($_REQUEST['excel']))) {

    $data_ini_bd = implode('-', array_reverse(explode('/', $data_ini)));
    $data_fim_bd = implode('-', array_reverse(explode('/', $data_fim)));

    $auxBanco = ($_REQUEST['id_banco']) ? " AND A.id_banco = '{$_REQUEST['id_banco']}' " : '';
    
    $sql = "
    SELECT A.id_viagem, A.nome, A.destino, CONCAT(DATE_FORMAT(data_ini, '%d/%m/%Y'), ' à ', DATE_FORMAT(data_fim, '%d/%m/%Y')) AS periodo, A.descricao, A.status,
    B.tipo, SUM(REPLACE(B.valor, ',', '.')) AS valor, 
    C.nome AS despesa
    FROM viagem A 
    LEFT JOIN saida_viagem B ON (A.id_viagem = B.id_viagem)
    LEFT JOIN entradaesaida C ON (B.tipo = C.id_entradasaida)
    LEFT JOIN entradaesaida_subgrupo D ON (C.cod LIKE CONCAT(D.id_subgrupo,'%'))
    LEFT JOIN entradaesaida_grupo E ON (E.id_grupo = D.entradaesaida_grupo)
    WHERE A.status > 0 AND (A.data BETWEEN '$data_ini_bd' AND '$data_fim_bd') $auxBanco
    GROUP BY A.id_viagem, B.tipo";
//    print_array($sql);
    $qry = mysql_query($sql) or die(mysql_error());
    while ($row = mysql_fetch_assoc($qry)) {
        $despesas[$row['tipo']] = $row['despesa'];
        $arrayLancamentos[$row['id_viagem']]['id_viagem'] = $row['id_viagem'];
        $arrayLancamentos[$row['id_viagem']]['periodo'] = $row['periodo'];
        $arrayLancamentos[$row['id_viagem']]['destino'] = $row['destino'];
        $arrayLancamentos[$row['id_viagem']]['descricao'] = $row['descricao'];
        $arrayLancamentos[$row['id_viagem']]['nome'] = $row['nome'];
        $arrayLancamentos[$row['id_viagem']]['status'] = $row['status'];
        $arrayLancamentos[$row['id_viagem']]['despesas'][$row['tipo']] += $row['valor'];
    }
    $despesas = array_filter($despesas);
    //$arrayLancamentos = $objLancamentoItens->getLivroDiario($projeto, $data_ini_bd, $data_fim_bd);
    $qtd_lanc = count($arrayLancamentos);
//    print_array($arrayLancamentos);
    
    if(isset($_REQUEST['excel'])){
        $arquivo = 'Relatório de Despesas.xls';
        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cache");
        header ("Content-type: application/x-msexcel");
        header ("Content-Disposition: attachment; filename={$arquivo}" );
        header ("Content-Description: PHP Generated Data" );
    }
}

$count = 0;
$nome_pagina = "Relatório de Despesas";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Relatórios Contábeis" => "index.php");
?>
<?php if(!isset($_REQUEST['excel'])) { ?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link rel="shortcut icon" href="../../favicon.png">
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
                    <div class="page-header box-financeiro-header hidden-print">
                        <h2><?php echo $icon['4'] ?> - Financeiro <small>- <?= $nome_pagina ?></small></h2>
                    </div>
                    <form action="" method="post" name="form" id="form" class="form-horizontal top-margin hidden-print" enctype="multipart/form-data">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <label for="" class="control-label">Período</label>
                                        <div class="input-group">
                                            <input type="text" id='data_ini' name='data_ini' class='text-center data validate[required,custom[select]] form-control input-sm' value="<?= $data_ini ?>">
                                            <div class="input-group-addon">até</div>
                                            <input type="text" id='data_fim' name='data_fim' class='text-center data validate[required,custom[select]] form-control input-sm' value="<?= $data_fim ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="id_banco" class="control-label">Banco</label>
                                        <?= montaSelect($bancos_opt, $id_banco, "id='id_banco' name='id_banco' class='form-control input-sm validate[required,custom[select]]'") ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <?php if (count($qtd_lanc) > 0) { ?><button type="submit" name="excel" value="Excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Exportar Excel</button><?php } ?>
                                <button type="submit" id="criar" name="filtrar" value="Filtrar" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
<?php } ?>
                    <?php if (isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && (isset($_REQUEST['filtrar']) || isset($_REQUEST['excel']))) { ?>
                        <table id="tbRelatorio" class="table table-condensed valign-middle table-bordered table-striped">
                            <tr>
                                <td class="text-center text-bold">CÓD</td>
                                <td class="text-center text-bold">NOME</td>
                                <td class="text-center text-bold">PERIODO</td>
                                <td class="text-center text-bold">DESTINO</td>
                                <td class="text-center text-bold">DESCRIÇÃO</td>
                                <td class="text-center text-bold">STATUS</td>
                                <?php foreach ($despesas as $key => $value) { ?>
                                    <td class="text-center text-bold"><?php echo $value ?></td>
                                <?php } ?>
                            </tr>
                            <?php foreach ($arrayLancamentos as $key => $row_lanc) { ?>
                            <?php
                            if($row_lanc['status'] == 1) {
                                $status = 'Aguardando aprovação';
                            } elseif($row_lanc['status'] == 2) {
                                $status = 'Gerar Acerto';
                            } elseif($row_lanc['status'] == 3) {
                                $status = 'Aguardando aprovação acerto';
                            } elseif($row_lanc['status'] == 4) {
                                $status = 'Acertado';
                            } elseif($row_lanc['status'] == 5) {
                                $status = 'Acertado Recusado';
                            } else {
                                $status = 'Recusado';
                            }
                            ?>
                                <tr class="">
                                    <td class="text-center"><?= $row_lanc['id_viagem'] ?></td>
                                    <td class="text-left"><?= $row_lanc['nome'] ?></td>
                                    <td class="text-left"><?= $row_lanc['periodo'] ?></td>
                                    <td class="text-left"><?= $row_lanc['destino'] ?></td>
                                    <td class="text-left"><?= $row_lanc['descricao'] ?></td>
                                    <td class="text-center"><?= $status ?></td>
                                    <?php foreach ($despesas as $key => $value) { ?>
                                        <?php $total[$key] += $row_lanc['despesas'][$key]; ?>
                                        <td class="text-right"><?php echo number_format($row_lanc['despesas'][$key], 2, ',', '.') ?></td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="6" class='text-right warning text-bold'>TOTAL</td>
                                <?php foreach ($despesas as $key => $value) { ?>
                                    <td class="text-right warning text-bold"><?php echo number_format($total[$key], 2, ',', '.') ?></td>
                                <?php } ?>
                            </tr>
                        </table>
                    <?php } else { ?>
                        <div class="alert alert-info">Nenhuma informação encontrada neste filtro!</div>
                    <?php } ?>
<?php if(!isset($_REQUEST['excel'])) { ?>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/financeiro/saida.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-fileclass.min.js" type="text/javascript"></script>
        <script>
        $(function () {
            $('#form').validationEngine();
            $('body').on('change', '#projeto', function () {
                console.log($("#contas").val());
                $.post("", {bugger: Math.random(), method: 'select_contas', projeto: "'" + $(this).val() + "'", conta: '<?= $_REQUEST['contas'] ?>'}, function (resultado) {
                    $("#contas").html(resultado);
                });
            });
            $('#projeto').trigger('change');
        })
        </script>
    </body>
</html>
<?php } ?>