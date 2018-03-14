<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

include('../conn.php');
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include('../wfunction.php');
include("../classes/global.php");
require_once("../classes/FolhaClass.php");

$folha = new Folha();
$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$global = new GlobalClass();

$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form-lista", "ativo" => "Relatório por Movimentos");
$breadcrumb_pages = array("Gestão de RH" => "/intranet/rh/principalrh.php");

$projeto = $Fun->id_regiao;
$mes = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$ano = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$filtro = $_REQUEST['filtrar'];

if (!empty($_REQUEST['data_xls'])) {
    $dados = utf8_encode($_REQUEST['data_xls']);

    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=relatorio_de_movimentos.xls");

    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>Relatório de Movimentos</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}

if (isset($filtro)) {
    if ($_REQUEST['status_folha'] == 5) {
        $checkFechada = 'checked';
    } else {
        $checkAberta = 'checked';
    }
    switch ($_REQUEST['tipoFolha']) {

        case 1:
            $terceiro = 2;
            $tipoTerceiro = 3;
            break;
        case 2:
            $terceiro = 1;
            $tipoTerceiro = 3;
            break;
        case 3:
            $terceiro = 1;
            $tipoTerceiro = 1;
            break;
        case 4:
            $terceiro = 1;
            $tipoTerceiro = 2;
            break;
    }

    if (isset($_REQUEST['mov'])) {
//        $movimentosArray = $_SESSION['movimentos'];
        $movimentos = $_REQUEST['mov'];

        if ($_REQUEST['status_folha'] == 5) {
            $sqlFolha = "SELECT ids_movimentos_estatisticas
                         FROM rh_folha A
                         WHERE A.mes = $mes AND A.ano = $ano AND A.terceiro = $terceiro AND A.tipo_terceiro = $tipoTerceiro AND projeto = $projeto";
            $queryFolha = mysql_query($sqlFolha);
            $estatisticas = mysql_result($queryFolha, 0);

            $sqlMovs = "SELECT cod_movimento, nome_movimento
                        FROM rh_movimentos_clt
                        WHERE id_movimento IN ($estatisticas)
                        GROUP BY cod_movimento
                        ORDER BY nome_movimento";
            $queryMovs = mysql_query($sqlMovs);
            $movimentosArray[0] = '-- Todos --';
            while ($rowMovs = mysql_fetch_assoc($queryMovs)) {
                $movimentosArray[$rowMovs['cod_movimento']] = $rowMovs['nome_movimento'];
            }
            if ($movimentos == 0) {
                $criteriaMovimentos = '';
            } else if ($movimentos > 0) {
                $criteriaMovimentos = " AND cod_movimento = {$_REQUEST['mov']}";
            }
            $checkFechada = 'checked';

            $sqlRelatorio = "SELECT A.id_clt, B.cpf, B.nome, C.nome funcao, C.letra, C.numero, DATE_FORMAT(B.data_entrada, '%d/%m/%Y') data_entrada, CONCAT(A.mes_mov,'/',A.ano_mov) dt_lancamento, nome_movimento, A.qnt, A.qnt_horas, A.valor_movimento
                                FROM rh_movimentos_clt A
                                LEFT JOIN rh_clt B ON A.id_clt = B.id_clt
                                LEFT JOIN curso C ON B.id_curso = C.id_curso
                                WHERE id_movimento IN ({$_REQUEST['estatisticas']})
                                $criteriaMovimentos
                                ORDER BY B.nome";
            $queryRelatorio = mysql_query($sqlRelatorio);


            while ($rowRelatorio = mysql_fetch_assoc($queryRelatorio)) {
                $arrRelatorio[] = $rowRelatorio;
            }

            if ($_REQUEST['mov'] == 0) {
                $criteriaMov = '';
            } else if ($_REQUEST['mov'] > 0) {
                $criteriaMov = "AND cod_movimento = {$_REQUEST['mov']}";
            }

            $total_descontados = mysql_num_rows($queryRelatorio);
        } else {
            $sqlMovs = "SELECT cod_movimento, nome_movimento FROM ( 
                        SELECT * FROM rh_movimentos_clt
                        WHERE STATUS > 0 AND mes_mov = $mes AND ano_mov = $ano AND id_projeto = $projeto
                        UNION
                        SELECT * FROM (
                        SELECT * FROM rh_movimentos_clt
                        WHERE STATUS > 0 AND lancamento = 2 AND id_projeto = $projeto
                        ORDER BY id_movimento DESC) MOV
                        GROUP BY id_clt, cod_movimento
                        ) A
                        GROUP BY nome_movimento";
            $queryMovs = mysql_query($sqlMovs);
            $movimentosArray[0] = '-- Todos --';
            while ($rowMovs = mysql_fetch_assoc($queryMovs)) {
                $movimentosArray[$rowMovs['cod_movimento']] = $rowMovs['nome_movimento'];
            }

            if ($_REQUEST['mov'] == 0) {
                $criteriaMov = '';
            } else if ($_REQUEST['mov'] > 0) {
                $criteriaMov = "AND cod_movimento = {$_REQUEST['mov']}";
            }

            $checkAberta = 'checked';

            $sqlRelatorio = "SELECT A.id_clt, B.cpf, B.nome, C.nome funcao, C.letra, C.numero, DATE_FORMAT(B.data_entrada, '%d/%m/%Y') data_entrada, CONCAT(A.mes_mov,'/',A.ano_mov) dt_lancamento, nome_movimento, A.qnt, A.qnt_horas, A.valor_movimento FROM ( 
                             SELECT * FROM rh_movimentos_clt
                             WHERE STATUS > 0 AND mes_mov = $mes AND ano_mov = $ano  $criteriaMov AND id_projeto = $projeto
                             UNION
                             SELECT * FROM (
                             SELECT * FROM rh_movimentos_clt
                             WHERE STATUS > 0 AND lancamento = 2 $criteriaMov AND id_projeto = $projeto
                             ORDER BY id_movimento DESC) MOV
                             GROUP BY id_clt, cod_movimento
                             ) A
                             LEFT JOIN rh_clt B ON A.id_clt = B.id_clt
                             LEFT JOIN curso C ON B.id_curso = C.id_curso
                             WHERE B.status < 60 || B.status IN (67,200)
                             GROUP BY id_clt
                             ORDER BY nome";
            $queryRelatorio = mysql_query($sqlRelatorio);
            while ($rowRelatorio = mysql_fetch_assoc($queryRelatorio)) {
                $arrRelatorio[] = $rowRelatorio;
            }

            $total_descontados = mysql_num_rows($queryRelatorio);
        }
    } else {

        if ($_REQUEST['status_folha'] == 5) {

            $sqlFolha = "SELECT ids_movimentos_estatisticas
                         FROM rh_folha A
                         WHERE A.mes = $mes AND A.ano = $ano AND A.terceiro = $terceiro AND A.tipo_terceiro = $tipoTerceiro AND projeto = $projeto";
            $queryFolha = mysql_query($sqlFolha);
            $estatisticas = mysql_result($queryFolha, 0);

            $sqlMovs = "SELECT cod_movimento, nome_movimento
                        FROM rh_movimentos_clt
                        WHERE id_movimento IN ($estatisticas)
                        GROUP BY cod_movimento
                        ORDER BY nome_movimento";
            $queryMovs = mysql_query($sqlMovs);
            $movimentosArray[0] = '-- Todos --';
            while ($rowMovs = mysql_fetch_assoc($queryMovs)) {
                $movimentosArray[$rowMovs['cod_movimento']] = $rowMovs['nome_movimento'];
            }

//            $_SESSION['movimentos'] =  $movimentosArray;
        } else {
            $sqlMovs = "SELECT cod_movimento, nome_movimento FROM ( 
                        SELECT * FROM rh_movimentos_clt
                        WHERE STATUS > 0 AND mes_mov = $mes AND ano_mov = $ano AND id_projeto = $projeto
                        UNION
                        SELECT * FROM (
                        SELECT * FROM rh_movimentos_clt
                        WHERE STATUS > 0 AND lancamento = 2 AND id_projeto = $projeto
                        ORDER BY id_movimento DESC) MOV
                        GROUP BY id_clt, cod_movimento
                        ) A
                        GROUP BY nome_movimento";
            $queryMovs = mysql_query($sqlMovs);
            $movimentosArray[0] = '-- Todos --';
            while ($rowMovs = mysql_fetch_assoc($queryMovs)) {
                $movimentosArray[$rowMovs['cod_movimento']] = $rowMovs['nome_movimento'];
            }

//            $_SESSION['movimentos'] =  $movimentosArray;
        }
    }
//    $ficha = $folha->getFichaFinanceira($clt, $ano, $meses, $terceiro);
//    $itensFicha = $folha->getDadosFicha();
//    print_array($itensFicha);
} else {
    $sqlFolha = "SELECT status
                 FROM rh_folha A
                 WHERE A.mes = MONTH(NOW()) AND A.ano = YEAR(NOW()) AND A.terceiro = 2 AND projeto = $projeto";
    $queryFolha = mysql_query($sqlFolha);
    $arrFolha = mysql_fetch_assoc($queryFolha);

    if ($arrFolha['status'] == 3) {
        $checkFechada = 'checked';
    } else {
        $checkAberta = 'checked';
    }
}
?>
<!doctype html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <title>Relatório por Movimentos</title>
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">

        <script src="../js/jquery-1.10.2.min.js"></script>

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="<?= ($container_full) ? 'container-full' : 'container' ?>">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório por Movimentos</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">

                <?php if (isset($_SESSION['regiao'])) { ?>                
                    <!--resposta de algum metodo realizado-->
                    <div class="alert alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                        <button type="button" class="close" data-dismiss="alert">Ã—</button>
                        <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy();
                    ?></p>
                    </div>
                <?php } ?>

                <div class="panel panel-default hidden-print">
                    <div class="panel-heading">Relatório Detalhado</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto:</label>
                            <div class="col-lg-8">
                                <?php echo montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao']), $projeto, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Mês:</label>
                            <div class="col-lg-8">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <?php echo montaSelect(mesesArray(), $mes, "id='mes' name='mes' class='required[custom[select]] form-control'"); ?>
                                    <span class="input-group-addon">Ano</span>
                                    <?php echo montaSelect(AnosArray(null, null), $ano, "id='ano' name='ano' class='required[custom[select]] form-control'"); ?>                                
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Status da Folha:</label>
                            <div class="col-lg-2">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <input type="radio" name="status_folha" value="1" <?= $checkAberta ?> /> Folha Aberta
                                </div>
                            </div>
                            <div class="col-lg-1"></div>
                            <div class="col-lg-2">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <input type="radio" name="status_folha" value="5" <?= $checkFechada ?> /> Folha Finalizada
                                </div>
                            </div>
                            <div class="col-lg-5"><input type="hidden" id="rhFolhaStatus" name="rhFolhaStatus" value="<?= (isset($arrFolha['status_folha'])) ? $arrFolha['status_folha'] : $_REQUEST['status_folha']; ?>" /></div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Tipo da Folha:</label>
                            <div class="col-lg-3">
                                <?php echo montaSelect(['1' => "Normal", '2' => "13º Integral", '3' => "13º Primeira Parcela", '4' => "13º Segunda Parcela"], $_REQUEST['tipoFolha'], "id='tipoFolha' name='tipoFolha' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <?php if (isset($filtro)) { ?>
                            <div class="form-group">
                                <label for="select" class="col-lg-2 control-label">Movimento:</label>
                                <div class="col-lg-8">
                                    <div class="input-daterange input-group" id="bs-datepicker-range">
                                        <?php echo montaSelect($movimentosArray, $movimentos, "id='mov' name='mov' class='required[custom[select]] form-control'"); ?>
                                    </div>
                                    <input type="hidden" id="estatisticas" name="estatisticas" value="<?= $estatisticas ?>" />
                                </div>
                            </div>
                            <?php if (isset($_REQUEST['mov'])) { ?>

                            <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="panel-footer text-right controls">
                        <?php if (!empty($total_descontados) && (isset($_POST['filtrar']))) { ?>
                            <button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <input type="hidden" id="data_xls" name="data_xls" value="">
                            <button type="button" form="formPdf" name="pdf" data-title="Relatorio Por Movimentos" data-id="table_excel" id="pdf" value="Gerar PDF" class="btn btn-danger"><span class="fa fa-file-pdf-o"></span> Gerar PDF</button>
                        <?php } ?>
                        <button type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar </button>
                    </div>
                </div>


                <?php
                if ($filtro) {
                    if ($total_descontados > 0) {
                        ?>
                        <div id="relatorio_exp">
                            <table class="table table-bordered table-hover table-condensed text-sm valign-middle" id="table_excel">
                                <thead>                    
                                    <tr class="bg-primary">
                                        <th>Nome</th>
                                        <th>CPF</th>
                                        <th>Função</th>
                                        <th>Data Entrada</th>
                                        <th>Lançamento</th>
                                        <th>Movimento</th>
                                        <th>Quantidade</th>
                                        <th>Valor</th>                        
                                    </tr>
                                </thead>
                                <?php $totalDesconto = 0; ?>
                                <tbody>
                                    <?php
                                    foreach ($arrRelatorio as $value) {
                                        ?>
                                        <tr class="linhasParticipantes">
                                            <td><?php echo $value['nome']; ?></td>
                                            <td><?php echo $value['cpf']; ?></td>
                                            <td><?php echo $value['funcao'] . " - " . $value['letra'] . $value['numero']; ?></td>
                                            <td><?php echo $value['data_entrada']; ?></td>
                                            <td><?php echo $value['dt_lancamento']; ?></td>
                                            <td><?php echo $value['nome_movimento']; ?></td>
                                            <td>
                                                <?php
                                                if($value['qnt'] > 0){
                                                    echo $value['qnt']." dia(s)";
                                                }
                                                
                                                if($value['qnt_horas'] != '00:00:00'){
                                                    echo $value['qnt_horas']." hora(s)";
                                                }
                                                
//                                                echo ($value['qnt'] > 0) ? $value['qnt'] : ($value['qnt_horas'] != '00:00:00') ? $value['qnt_horas'] : null ?>
                                            </td>
                                            <td><?php echo number_format($value['valor_movimento'], 2, ',', '.'); ?></td>
                                            <?php $totalDesconto += $value['valor_movimento']; ?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr class='danger'>
                                        <td colspan="6" class='text-right'>Total Geral:</td>
                                        <td><?php echo number_format($totalDesconto, 2, ',', '.'); ?></td> 
                                    </tr>
                                </tfoot>

                            </table>
                        </div>

                    <?php } else { ?>
                        <div class="alert alert-danger top30">                    
                            Nenhum registro encontrado
                        </div>
                    <?php }
                }
                ?>

            </form>

            <?php include('../template/footer.php'); ?>


            <script src="../js/jquery-1.10.2.min.js"></script>
            <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
            <script src="../resources/js/bootstrap.min.js"></script>
            <script src="../resources/js/tooltip.js"></script>
            <script src="../resources/js/main.js"></script>
            <script src="../js/global.js"></script>
            <script>
                $(function () {

                    $('#master').change(function () {
                        var id_master = $(this).val();
                        $('#regiao').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                        $.ajax({
                            url: '../action.global.php?master=' + id_master,
                            success: function (resposta) {
                                $('#regiao').html(resposta);
                                $('#regiao').next().html('');
                            }
                        });

                        $('#regiao').trigger('change')
                    });



                    $('#regiao').change(function () {
                        var id_regiao = $(this).val();

                        $('#projeto').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                        $.ajax({
                            url: '../action.global.php?regiao=' + id_regiao,
                            success: function (resposta) {
                                $('#projeto').html(resposta);
                                $('#projeto').next().html('');
                            }
                        });


                    });

                    $('#master').trigger('change');

                    $("body").on("click", "input[name='filtroTipo']", function () {
                        var valor = $(this).val();
                        if (valor == 2) {
                            $(".linhasParticipantes").hide();
                        } else {
                            $(".linhasParticipantes").show();
                        }
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

