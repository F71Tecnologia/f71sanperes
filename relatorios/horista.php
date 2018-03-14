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

$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form-lista", "ativo" => "Relatório de Horistas");
$breadcrumb_pages = array("Gestão de RH" => "/intranet/rh/principalrh.php");

$arrRelatorio = array();
$listaProjetos = $global->carregaProjetosByRegiao($usuario['id_regiao']);
  
$projeto = isset($_REQUEST['projeto'])?$_REQUEST['projeto']:'0'; 
$filtro = $_REQUEST['filtrar'];
$todos =  $_REQUEST['todos'];

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

/**
 * 
 */
if (isset($filtro)) {
    
    $criterio = "";
    if(isset($_REQUEST['projeto']) && $_REQUEST['projeto'] != 0){
        $criterio = " AND B.id_projeto = '{$_REQUEST['projeto']}' ";
    }
    
    $qry = "SELECT 
                    C.nome as projeto, 
                    A.nome AS funcao, 
                    COUNT(B.id_clt) AS total_participantes, 
                    SUM(A.valor) as total_valor, 
                    SUM(A.hora_mes) as total_horas_mes
            FROM curso AS A
                    LEFT JOIN rh_clt AS B ON(A.id_curso = B.id_curso)
                    LEFT JOIN projeto AS C ON(B.id_projeto = C.id_projeto)	
                    LEFT JOIN rh_recisao AS D ON(B.id_clt = D.id_clt AND D.`status` > 0)
            WHERE A.horista_plantonista > 0 {$criterio}
            GROUP BY B.id_curso
            HAVING total_participantes > 0"; 
    
    $sql = mysql_query($qry) or die('Erro ao Horistas');
    
    while($rows = mysql_fetch_assoc($sql)){
        $arrRelatorio[] = $rows;
    }
     
}

/**
 * 
 */ 
if (isset($todos)) {
    
    $qry = "SELECT 
                    C.nome as projeto, 
                    A.nome AS funcao, 
                    COUNT(B.id_clt) AS total_participantes, 
                    SUM(A.valor) as total_valor, 
                    SUM(A.hora_mes) as total_horas_mes
            FROM curso AS A
                    LEFT JOIN rh_clt AS B ON(A.id_curso = B.id_curso)
                    LEFT JOIN projeto AS C ON(B.id_projeto = C.id_projeto)	
                    LEFT JOIN rh_recisao AS D ON(B.id_clt = D.id_clt AND D.`status` > 0)
            WHERE A.horista_plantonista > 0 
            GROUP BY B.id_curso
            HAVING total_participantes > 0"; 
    
    $sql = mysql_query($qry) or die('Erro ao Horistas');
     
    while($rows = mysql_fetch_assoc($sql)){
        $arrRelatorio[] = $rows;
    }
     
}
?>
<!doctype html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <title>Relatório de Horistas</title>
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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório de Horistas</small></h2></div>
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
                                <?php echo montaSelect($listaProjetos, $projeto, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right controls">
                        <?php if (!empty($total_descontados) && (isset($_POST['filtrar']))) { ?>
                            <button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <input type="hidden" id="data_xls" name="data_xls" value="">
                            <button type="button" form="formPdf" name="pdf" data-title="Relatorio Por Movimentos" data-id="table_excel" id="pdf" value="Gerar PDF" class="btn btn-danger"><span class="fa fa-file-pdf-o"></span> Gerar PDF</button>
                        <?php } ?>
                        <button type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar </button>
                        <button type="submit" name="todos" id="todos" value="Todos Os Projetos" class="btn btn-primary"><span class="fa fa-filter"></span> Todos Os Projetos </button>
                    </div>
                </div>


                <?php
                if ($filtro || $todos) {
                    if (!empty($arrRelatorio)) {
                        ?>
                        <div id="relatorio_exp">
                            <table class="table table-bordered table-hover table-condensed text-sm valign-middle" id="table_excel">
                                <thead>                    
                                    <tr class="bg-primary">
                                        <th>Projeto</th>
                                        <th>Função</th>
                                        <th>Total de Participantes</th>
                                        <th>Total Salário</th>
                                        <th>Total Hora Mês</th>
                                    </tr>
                                </thead>
                                 
                                <tbody>
                                    <?php foreach ($arrRelatorio as $value) { ?>
                                        <?php $totalParticipantes += $value['total_participantes']; ?>
                                        <?php $totalSalario += $value['total_valor']; ?>
                                        <?php $totalHoraMes += $value['total_horas_mes']; ?>
                                        <tr class="linhasParticipantes">
                                            <td><?php echo $value['projeto']; ?></td>
                                            <td><?php echo $value['funcao']; ?></td>
                                            <td><?php echo $value['total_participantes']; ?></td>
                                            <td><?php echo "R$ " . number_format($value['total_valor'], 2, ',', '.'); ?></td>
                                            <td><?php echo "R$ " . number_format($value['total_horas_mes'], 2, ',', '.'); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr class='danger'>
                                        <td colspan="2" class='text-right'>Total Geral:</td>
                                        <td><?php echo $totalParticipantes; ?></td> 
                                        <td><?php echo "R$ " . number_format($totalSalario, 2, ',', '.'); ?></td> 
                                        <td><?php echo "R$ " . number_format($totalHoraMes, 2, ',', '.'); ?></td> 
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

