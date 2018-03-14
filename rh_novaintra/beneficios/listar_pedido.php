<?php
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
}

error_reporting(E_ALL);

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/ValeAlimentacaoRefeicaoClass.php");
include("../../classes/ValeAlimentacaoRefeicaoRelatorioClass.php");
include("../../classes_permissoes/acoes.class.php");
include "../../classes/LogClass.php";

$log = new Log();

$objAcoes = new Acoes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$objAlimentaca = new ValeAlimentacaoRefeicaoClass();
$objAlimentacaItem = new ValeAlimentacaoRefeicaoRelatorioClass();

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'editar_valor_pedido') {
    $ret = $objAlimentacaItem->salvar(array('id_va_relatorio' => $_REQUEST['id_va_relatorio'], 'va_valor_mes' => $_REQUEST['va_valor_mes']));
    echo json_encode(array('status' => $ret));
}

if (!empty($_REQUEST['data_xls'])) {            
    $dados = strip_tags(utf8_encode($_REQUEST['data_xls']), '<div>, <table>, <thead>, <tr>, <th>, <td>, <tbody>');
        
    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=relatorio-de-pedido-de-vale-alinetacao.xls");

    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>PARTICIPANTES DE VA</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}


$x = $objAlimentaca->consultar(array('id_va_pedido' => $_REQUEST['id']));

$y = $objAlimentacaItem->gerForRelatorio($_REQUEST['id']);

$categoria_vale = array('1' => 'Alimentação', '2' => 'Refeição');
$vale_link = array('1' => 'alimentacao', '2' => 'refeicao');

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Participantes do Pedido");
$breadcrumb_pages = array("Gestão de RH" => "../../rh/principalrh.php", "Benefícios" => "../beneficios", "Vale " . $categoria_vale[$x[1]['categoria_vale']] => "vale_".$vale_link[$x[1]['categoria_vale']]);

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão de RH</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" type="text/css">
        <style>
            .exportButtons {
                width: 282px;
                float: right;
            }
            
            .competencia {
                margin: 6px 0;
                float: left;
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <form method="post" id="form1" action="" name="form1">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>- Vale <?= $categoria_vale[$x[1]['categoria_vale']] ?></small></h2></div>
                    <div class="alert alert-success">
                        <input type="hidden" id="data_xls" name="data_xls" value="">
                        <p class="competencia"><strong>Competência:</strong> <?= mesesArray($x[1]['mes']).' de '.$x[1]['ano'] ?></p>
                        <div class="exportButtons">
                            <button type="button" form="formPdf" name="pdf" data-title="Relatório de Pedido de Vale Alimentação" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                            <button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <?php if (count($y) > 0) { ?>
                        <div class="panel panel-default">
                            <div id="relatorio_exp">
                                <table class="table table-striped table-hover text-sm tablesorter" id="tbRelatorio">
                                    <thead>
                                        <tr>
                                            <th>Matrícula</th>
                                            <th>Nome</th>
                                            <th class="sorter-shortDate dateFormat-ddmmyyyy">Entrada</th>
                                            <th>CPF</th>
                                            <th>Unidade</th>
                                            <th>Cargo</th>
                                            <?php if ($_COOKIE['logado'] == 353) { ?>
                                                <th>Sindicato</th>
                                            <?php } ?>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($y as $key => $value) { ?>
                                            <tr>
                                                <td><?= $value['matricula_sodexo'] ?></td>
                                                <td><?= $value['nome'] ?></td>
                                                <td><?= converteData($value['data_entrada'], 'd/m/Y') ?></td>
                                                <td><?= $value['cpf'] ?></td>
                                                <td><?= $value['nome_unidade'] ?></td>
                                                <td><?= $value['nome_curso'] ?></td>
                                                <?php if ($_COOKIE['logado'] == 353) { ?>
                                                    <td><?= $value['nome_sindicato'] ?></td>
                                                <?php } ?>
                                                <td class="action_val text-right"  data-id="<?= $value['id_va_relatorio'] ?>">
                                                    <a href="javascript:;" id="<?= $value['id_va_relatorio'] ?>_span"><?= number_format($value['va_valor_mes'], 2, ',', '.') ?></a>
                                                    <?php if($_COOKIE['logado'] != 395){ ?>
                                                    <input type="hidden" name="valor[]" class="input_valor_edit valor_msk" value="<?= $value['va_valor_mes'] ?>" id="<?= $value['id_va_relatorio'] ?>_valor" data-id="<?= $value['id_va_relatorio'] ?>">
                                                    <?php } ?>
                                                </td>

                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="panel-footer">
                                <a href="controle.php?id=<?= $x[1]['id_va_pedido'] ?>&tipo=<?= $x[1]['categoria_vale'] ?>" name="download" value="download" class="btn btn-info"><i class="fa fa-download"></i> Download</a>  
                            </div>
                        </div>


                    <?php } ?>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        </form>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/rh/eventos/prorrogar_evento.js"></script>
        <script src="../../js/jquery.maskedinput.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../jquery/tablesorte/jquery.tablesorter.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <!--<script src="../../resources/js/rh/beneficios/vale_alimentacao.js" type="text/javascript"></script>-->

        <script>
            $(document).ready(function () {
                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });

            $(function () {
                $(".valor_msk").maskMoney({prefix: 'R$ ', allowNegative: true, thousands: '', decimal: '.'});

                $("table").tablesorter({
                    dateFormat: "mmddyyyy", // set the default date format

                    // or to change the format for specific columns, add the dateFormat to the headers option:
                    headers: {
                        0: {sorter: "shortDate"} //, dateFormat will parsed as the default above
                        // 1: { sorter: "shortDate", dateFormat: "ddmmyyyy" }, // set day first format; set using class names
                        // 2: { sorter: "shortDate", dateFormat: "yyyymmdd" }  // set year first format; set using data attributes (jQuery data)
                    }
                });

                $("#exportarExcel").click(function () {
                    $("#relatorio_exp img:last-child").remove();

                    var html = $("#relatorio_exp").html();

                    $("#data_xls").val(html);
                    $("#form1").submit();
                });

                $("#checkAll").click(function () {
                    if ($("#checkAll").prop("checked")) {
                        $(".chk").prop("checked", true);
                    } else {
                        $(".chk").prop("checked", false);
                    }
                });

                $(".action_val").click(function () {
                    var id = $(this).data("id");
                    var valor = $("#" + id + "_valor");
                    var valor_txt = $("#" + id + "_span");

                    $(valor).attr("type", "text");
                    $(valor_txt).attr("class", "hidden");
                });

                $('.input_valor_edit').blur(function () {
                    var id = $(this).data('id');
                    var valor_novo = $(this).val();
                    var valor = $("#" + id + "_valor");
                    var valor_txt = $("#" + id + "_span");

                    $(valor).attr("type", "hidden");
                    $(valor_txt).removeClass("hidden");
                    $(valor_txt).text(valor_novo);

                    $.post('#', {method: 'editar_valor_pedido', id_va_relatorio: id, va_valor_mes: valor_novo}, function (data) {
                        if (data.status) {
                            $(valor).attr("type", "hidden");
                            $(valor_txt).removeClass("hidden");
                            $(valor_txt).text(valor_novo);
                        }
                    }, 'json');

                });
                
                $("#exportarExcel").click(function () {
                    $("#relatorio_exp img:last-child").remove();

                    var html = $("#relatorio_exp").html();

                    $("#data_xls").val(html);
                    $("#form1").submit();
                });
            });
        </script>
    </body>
</html>