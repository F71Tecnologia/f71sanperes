<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=false';</script>";
}

include_once("../conn.php");
include_once("../wfunction.php");
include_once("../classes/BotoesClass.php");
require_once("../classes/pdf/fpdf.php");
require_once("../classes/c_planodecontasClass.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$objPlano = new c_planodecontasClass();

$botoes = new BotoesClass("../img_menu_principal/");
$icon = $botoes->iconsModulos;

$projeto = ($_REQUEST['projeto'] > 0) ? $_REQUEST['projeto'] : null;

if ($_REQUEST['method'] == 'carregaFornecedor') {
    $sqlPrestadorSemAssociassao = " 
    SELECT id_prestador, numero, c_razao 
    FROM prestadorservico A
    WHERE A.status = 1 AND (A.encerrado_em > '2016-01-01' /*CURDATE()*/ OR A.encerrado_em IS NULL) AND A.id_projeto = {$_REQUEST['projeto']}
    ORDER BY c_razao";
    $qryPrestadorSemAssociassao = mysql_query($sqlPrestadorSemAssociassao);
    echo "<option value=''>selecione</option>";
    while ($rowPrestadorSemAssociassao = mysql_fetch_assoc($qryPrestadorSemAssociassao)) {
        $selected = ($rowPrestadorSemAssociassao['id_prestador'] == $_REQUEST['prestador']) ? 'selected' : '';
        $id_str = str_pad($rowPrestadorSemAssociassao['id_prestador'],5,'0',STR_PAD_LEFT);
        $num_str = str_pad($rowPrestadorSemAssociassao['numero'],7,'0',STR_PAD_LEFT);
        echo "<option value='{$rowPrestadorSemAssociassao['id_prestador']}' $selected >" . $id_str . ' - ' . $num_str . ' - ' . utf8_encode($rowPrestadorSemAssociassao['c_razao']) . "</option>";
    }
    exit;
}

if (isset($_REQUEST['associar']) && $_REQUEST['prestador'] > 0) {

    mysql_query("DELETE FROM contabil_contas_assoc_prestador WHERE id_prestador = {$_REQUEST['prestador']}");
    foreach ($_REQUEST['imposto'] as $id_imposto => $contas) {
        if ($contas['passivo'] > 0 && $contas['dre'] > 0)
            $insert[] = "('{$_REQUEST['prestador']}','{$id_imposto}','{$contas['passivo']}','{$contas['dre']}','1')";
    }
    $insert = "INSERT INTO contabil_contas_assoc_prestador (`id_prestador`, `id_imposto`, `id_conta_passivo`, `id_conta_dre`, `status`) VALUES " . implode(', ', $insert) . ";";
    mysql_query($insert);
    $_SESSION['s'] = "Associção efetuada com sucesso!";
    header("Location: prestador_contas_assoc.php");
    exit;
}

if (isset($_REQUEST['prestador']) && isset($_REQUEST['filtrar']) && $_REQUEST['projeto'] > 0) {
    $sqlImpostos = "SELECT A.id_imposto, A.sigla, B.id_conta_passivo, B.id_conta_dre
    FROM contabil_impostos A
    LEFT JOIN contabil_contas_assoc_prestador B ON (A.id_imposto = B.id_imposto AND B.id_prestador = {$_REQUEST['prestador']} AND B.status = 1)
    WHERE A.status = 1
    ORDER BY A.sigla";
    $qryImpostos = mysql_query($sqlImpostos);

    $contaPrestador = mysql_fetch_assoc(mysql_query("SELECT id_conta_passivo, id_conta_dre FROM contabil_contas_assoc_prestador WHERE id_prestador = '{$_REQUEST['prestador']}' AND id_imposto = 0 LIMIT 1"));

    $qryPlanos = "SELECT * FROM contabil_planodecontas WHERE id_projeto = '{$_REQUEST['projeto']}' AND classificacao = 'A' ORDER BY classificador";
    $sqlPlanos = mysql_query($qryPlanos) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
    $arrayPlanosPassivo[] = $arrayPlanosDre[] = 'selecione';
    while ($rowPlanos = mysql_fetch_assoc($sqlPlanos)) {
        $n = explode('.', $rowPlanos['classificador']);
        
        if($n[0] < 3)
            $arrayPlanosPassivo[$rowPlanos['id_conta']] = $rowPlanos['acesso'] . ' - ' . $rowPlanos['classificador'] . ' - ' . $rowPlanos['descricao'];
         else if ($n[0] >= 3)
            $arrayPlanosDre[$rowPlanos['id_conta']] = $rowPlanos['acesso'] . ' - ' . $rowPlanos['classificador'] . ' - ' . $rowPlanos['descricao'];
    }
}

$nome_pagina = "Associação Prestador Impostos e Contas";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
//$breadcrumb_pages = array("Relatórios Contábeis"=>"index.php");
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link rel="shortcut icon" href="../favicon.png">
        <!-- Bootstrap -->        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-compras.css" rel="stylesheet" media="all">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-contabil-header hidden-print">
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- <?= $nome_pagina ?></small></h2>
                    </div>
                    <?php if (isset($_SESSION['s'])) { ?>
                        <div class="alert alert-success"><?= $_SESSION['s'] ?></div>
                        <?php unset($_SESSION['s']);
                    } ?>
                    <form action="" method="post"  class="form-horizontal top-margin hidden-print" enctype="multipart/form-data">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-sm-1 text-sm control-label">Projeto</label>
                                    <div class="col-sm-5"><?= montaSelect(getProjetos($usuario['id_regiao']), $projeto, "id='projeto' name='projeto' data-for='prestador' class='form-control input-sm validate[required,custom[select]]'") ?></div>
                                    <label for="" class="col-sm-1 text-sm control-label">Fornecedor</label>
                                    <div class="col-sm-5">
                                        <?php echo montaSelect($arrayPrestadorSemAssociassao, $prestador, "id='prestador' name='prestador' class='input-sm validate[required,custom[select]] form-control'"); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <button type="submit" id="criar" name="filtrar" value="Filtrar" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
                    <?php if (isset($_REQUEST['prestador']) && isset($_REQUEST['filtrar']) && $_REQUEST['projeto'] > 0) { ?>
                        <form action="" method="post">
                            <table id="tbRelatorio" class="table table-condensed table-hover text-sm valign-middle">
                                <thead>
                                    <tr>
                                        <td class="text-left text-bold"></td>
                                        <td style="width: 42%" class="text-center text-bold">DÉBITO<span class="sr-only">PASSIVO</span></td>
                                        <td style="width: 42%" class="text-center text-bold">CRÉDITO<span class="sr-only">DRE</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-left text-bold">Fornecedor</td>
                                        <td class="text-left text-bold"><?= montaSelect($arrayPlanosDre, $contaPrestador['id_conta_dre'], "id='conta' name='imposto[0][dre]' class='input-sm validate[required,custom[select]] form-control'"); ?></td>
                                        <td class="text-left text-bold"><?= montaSelect($arrayPlanosPassivo, $contaPrestador['id_conta_passivo'], "id='conta' name='imposto[0][passivo]' class='input-sm validate[required,custom[select]] form-control'"); ?></td>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($rowImpostos = mysql_fetch_assoc($qryImpostos)) { ?>
                                        <tr>
                                            <td class="text-left text-bold"><?= $rowImpostos['sigla'] ?></td>
                                            <td class=""><?= montaSelect($arrayPlanosDre, $rowImpostos['id_conta_dre'], "id='conta' name='imposto[{$rowImpostos['id_imposto']}][dre]' class='input-sm validate[required,custom[select]] form-control'"); ?></td>
                                            <td class=""><?= montaSelect($arrayPlanosPassivo, $rowImpostos['id_conta_passivo'], "id='conta' name='imposto[{$rowImpostos['id_imposto']}][passivo]' class='input-sm validate[required,custom[select]] form-control'"); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right">
                                            <input type="hidden" name="prestador" value="<?= $_REQUEST['prestador'] ?>">
                                            <button class="btn btn-primary" name="associar" value="1"><i class="fa fa-link"></i> Associar</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </form>
                        <?php } else {
                        ?>
                        <div class="alert alert-info">Nenhuma informação encontrada neste filtro!</div>
                    <?php } ?>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../js/jquery.form.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/financeiro/saida.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        <script src="../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/classificacao.js" type="text/javascript"></script>
        <script>
            $(function () {
                $('body').on('change', '#projeto', function () {
                    var destino = $(this).data('for');
                    $.post("", {method: "carregaFornecedor", projeto: $(this).val(), prestador: '<?= $_REQUEST['prestador'] ?>'}, function (data) {
                        $("#" + destino).html(data);
                    });
                });
                $('#projeto').trigger('change');

                $('body').on('change', '#prestador', function () {
                    $('#tbRelatorio').remove();
                });
            });
        </script>
    </body>
</html>
