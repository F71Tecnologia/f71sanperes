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

//$bancos_opt = ['-1' => 'Selecione', '0' => 'Todos os Bancos'];
$bancos_opt = ['0' => 'Todos os Bancos'];
$query = "SELECT * FROM bancos WHERE id_regiao IN(" . implode(', ', $regioes_usuario) . ")";
$result = mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {
    $bancos_opt[$row['id_banco']] = $row['id_banco'] . ' - ' . $row['nome'];
}

if (isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && isset($_REQUEST['projeto']) && (isset($_REQUEST['filtrar']))) {

    $data_ini_bd = implode('-', array_reverse(explode('/', $data_ini)));
    $data_fim_bd = implode('-', array_reverse(explode('/', $data_fim)));
    
    $auxProjeto = ($_REQUEST['projeto']) ? " AND A.id_projeto = '{$_REQUEST['projeto']}' " : '';
    $auxBanco = ($_REQUEST['id_banco']) ? " AND A.id_banco = '{$_REQUEST['id_banco']}' " : '';
    
    $auxDocumento = ($_REQUEST['n_documento']) ? " AND A.id_saida IN (SELECT B1.id_saida_pai FROM saida A1 INNER JOIN saida_agrupamento_assoc B1 ON (A1.id_saida = B1.id_saida) WHERE A1.n_documento = '{$_REQUEST['n_documento']}')" : null;
    
    $sql = "
    SELECT 
        A.id_saida, A.nome, A.data_vencimento, REPLACE(A.valor, ',', '.') AS valor, A.n_documento,
        G.id_saida AS id_saida2, G.nome AS nome2, G.data_vencimento AS data_vencimento2, REPLACE(G.valor, ',', '.') AS valor2, G.n_documento AS n_documento2,
        B.nome AS nomeProjeto, D.nome AS nomeDespesa, E.c_razao
    FROM saida AS A
    LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
    LEFT JOIN entradaesaida D ON (A.tipo = D.id_entradasaida)
    LEFT JOIN prestadorservico E ON (A.id_prestador = E.id_prestador)
    LEFT JOIN saida_agrupamento_assoc F ON (A.id_saida = F.id_saida_pai)
    INNER JOIN saida G ON (F.id_saida = G.id_saida)
    WHERE A.agrupada > 0 AND A.status IN (1,2) {$auxProjeto} {$auxBanco} AND A.data_vencimento BETWEEN '$data_ini_bd' AND '$data_fim_bd'
    $auxDocumento";
//    print_array($sql);
    $qry = mysql_query($sql) or die(mysql_error());
    while ($row = mysql_fetch_assoc($qry)) {
        $arrayLancamentos[$row['id_saida']]['id'] = $row['id_saida'];
        $arrayLancamentos[$row['id_saida']]['nome'] = $row['nome'];
        $arrayLancamentos[$row['id_saida']]['data_vencimento'] = $row['data_vencimento'];
        $arrayLancamentos[$row['id_saida']]['valor'] = $row['valor'];
        if($row['id_saida2']) { 
            $arrayLancamentos[$row['id_saida']]['saidas'][$row['id_saida2']]['id'] = $row['id_saida2'];
            $arrayLancamentos[$row['id_saida']]['saidas'][$row['id_saida2']]['nome'] = $row['nome2'];
            $arrayLancamentos[$row['id_saida']]['saidas'][$row['id_saida2']]['data_vencimento'] = $row['data_vencimento2'];
            $arrayLancamentos[$row['id_saida']]['saidas'][$row['id_saida2']]['valor'] = $row['valor2'];
            $arrayLancamentos[$row['id_saida']]['saidas'][$row['id_saida2']]['n_documento'] = $row['n_documento2'];
        }
    }
}

$nome_pagina = "Relátorio de Agrupamento";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "4", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Principal" => "/intranet/finan");
?>
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
                                        <label for="projeto" class="control-label">Projeto</label>
                                        <?=montaSelect($global->carregaProjetosByMaster($usuario['id_master'], array("0" => "Todos os Projetos")), $id_projeto, "id='projeto' name='projeto' class='form-control input-sm validate[required,custom[select]]'") ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="id_banco" class="control-label">Banco</label>
                                        <?= montaSelect($bancos_opt, $id_banco, "id='id_banco' name='id_banco' class='form-control input-sm validate[required,custom[select]]'") ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <label for="" class="control-label">Período</label>
                                        <div class="input-group">
                                            <input type="text" id='data_ini' name='data_ini' class='text-center data validate[required,custom[select]] form-control' value="<?= $data_ini ?>">
                                            <div class="input-group-addon">até</div>
                                            <input type="text" id='data_fim' name='data_fim' class='text-center data validate[required,custom[select]] form-control' value="<?= $data_fim ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="" class="control-label">Nº Documento</label>
                                        <input type="text" id='n_documento' name='n_documento' class='form-control' value="<?= $_REQUEST['n_documento'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <?php if (count($qtd_lanc) > 0) { ?><button type="submit" name="excel" value="Excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Exportar Excel</button><?php } ?>
                                <button type="submit" id="criar" name="filtrar" value="Filtrar" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
                    <?php if (isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && isset($_REQUEST['projeto']) && (isset($_REQUEST['filtrar']))) { ?>
                        <table id="tbRelatorio" class="table table-condensed valign-middle table-bordered table-striped">
                            <tr>
                                <td class="text-left text-bold">CÓD</td>
                                <td class="text-left text-bold">DATA</td>
                                <td class="text-left text-bold">DESCRIÇÃO</td>
                                <td class="text-left text-bold">Nº NOTA</td>
                                <td class="text-left text-bold">VALOR</td>
                            </tr>
                            <?php foreach ($arrayLancamentos as $key => $row_lanc) { ?>
                                <tr class="info">
                                    <td class="text-left"><?= $row_lanc['id'] ?></td>
                                    <td class="text-left"><?= implode('/', array_reverse(explode('-', $row_lanc['data_vencimento']))) ?></td>
                                    <td class="text-left"><?= $row_lanc['nome'] ?></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"><?= number_format($row_lanc['valor'], 2, ',', '.') ?></td>
                                </tr>
                                <?php foreach ($row_lanc['saidas'] as $k => $v) { ?>
                                    <tr class="">
                                        <td class="text-left"><?= $v['id'] ?></td>
                                        <td class="text-left"><?= implode('/', array_reverse(explode('-', $v['data_vencimento']))) ?></td>
                                        <td class="text-left"><?= $v['nome'] ?></td>
                                        <td class="text-left"><?= $v['n_documento'] ?></td>
                                        <td class="text-right"><?= number_format($v['valor'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </table>
                    <?php } else { ?>
                        <div class="alert alert-info">Nenhuma informação encontrada neste filtro!</div>
                    <?php } ?>
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