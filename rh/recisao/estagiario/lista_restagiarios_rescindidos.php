<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../../../login.php'>Logar</a>";
    exit;
}
include("../../../conn.php");
include("../../../classes/regiao.php");
include("../../../classes/projeto.php");
include("../../../classes/funcionario.php");
include("../../../classes_permissoes/regioes.class.php");
include("../../../classes_permissoes/acoes.class.php");
include("../../../wfunction.php");
include("../../../classes/global.php");

if (!empty($_REQUEST['data_xls'])) {

    $dados = $_REQUEST['data_xls'];

    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
//    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
//    header("Content-type: application/xls");
    header("Content-Disposition: attachment; filename=relatorio.xls");


    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>RELATÓRIO DE PROVISÃO DE GASTOS</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo utf8_encode("      $dados");
    echo "  </body>";
    echo "</html>";
    exit;
}
$global = new GlobalClass();

$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$id_regiao = $Fun->id_regiao;
$REGIOES = new Regioes();

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$ACOES = new Acoes();

$opt_mes = date('m');
$opt_ano = date('Y');
If (isset($_REQUEST['gerar'])) {
    $ano = addslashes($_REQUEST['ano']);
    $mes = str_pad(addslashes($_REQUEST['mes']), 2, '0', STR_PAD_LEFT);
    $id_projeto = $_REQUEST['projeto'];
    $query = "SELECT a.id_estagiario,a.nome,a.data_fim, b.cpf, d.nome AS nome_projeto 
                FROM rh_rescisao_estagiario a 
                INNER JOIN estagiario b ON a.id_estagiario = b.id_estagiario 
                INNER JOIN projeto d ON a.id_projeto = d.id_projeto 
                WHERE DATE_FORMAT(a.data_fim,'%Y-%m') = '$ano-$mes' AND a.id_projeto = '{$id_projeto}'
                ORDER BY a.id_projeto,a.nome;";
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
        $arr_desligados[] = $row;
    }

    $opt_mes = isset($_REQUEST['mes']) ? $_REQUEST['mes'] : date('m');
    $opt_ano = isset($_REQUEST['ano']) ? $_REQUEST['ano'] : date('Y');
    $opt_projeto = isset($_REQUEST['projeto']) ? $_REQUEST['projeto'] : -1;
}

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form-lista", "ativo" => "Rescisão");
$breadcrumb_pages = array("Gestão de RH" => "/intranet/rh/principalrh.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: <?= $nome_relatorio ?></title>

        <link href="../../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">


    </head>
    <body>
        <?php include("../../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Rescisão de Estagiários</small></h2></div>

            <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
                <li role="presentation">
                    <a href="/intranet/rh_novaintra/recisao/nova_rescisao.php">Lista de Funcionários</a>
                </li>
                <li role="presentation" class="active">
                    <a href="#">Rescisão de Estagiários</a>
                </li>

            </ul>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <input type="hidden" name="data_xls" id="data_xls">
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <!--                        <div class="form-group" >
                                                    <label for="select" class="col-sm-3 control-label hidden-print" >Região</label>
                                                    <div class="col-sm-3">
                        <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                                                    </div>-->
                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print">Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect(getProjetos($id_regiao), $opt_projeto, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>

                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label hidden-print">Período</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <?= montaSelect(mesesArray(), $opt_mes, 'name="mes" class="validate[required] form-control"'); ?>
                                    <span class="input-group-addon" id="basic-addon1">/</span>
                                    <?= montaSelect(anosArray(2016, date('Y')), $opt_ano, 'name="ano" class="validate[required] form-control"'); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="panel-footer text-right hidden-print">

                        <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                    </div>  
                </div>
                <?php if (count($arr_desligados) > 0 and isset($_POST['gerar'])) { ?>
                    <button type="button" class="btn btn-success" id="exportarExcel"><i class="fa fa-file-excel-o"></i> Gerar Excel</button>
                    <div id="relatorio_exp">
                        <table class="table table-striped table-bordered text-sm valign-middle" id="">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>NOME</th>
                                    <th class="text-center">PROJETO</th>
                                    <th class="text-center">CPF</th>
                                    <th class="text-center">DATA DE SAÍDA</th>
                                    <th class="text-center">&emsp;</th>
                                </tr>
                            </thead><tbody>
                                <?php foreach ($arr_desligados as $row_rel) { ?>
                                    <tr>
                                        <td class="text-center"><?php echo $row_rel['id_estagiario'] ?></td>
                                        <td><?php echo $row_rel['nome'] ?></td>
                                        <td class="text-center"><?php echo $row_rel['nome_projeto']; ?></td>
                                        <td class="text-center"><?php echo $row_rel['cpf']; ?></td>    
                                        <td class="text-center"><?php echo converteData($row_rel['data_fim'], 'd/m/Y'); ?></td>       
                                        <td class="text-center"><a class="btn btn-default btn-xs" href="tre.php?id_estagiario=<?php echo $row_rel['id_estagiario']; ?>"><i class="fa fa-file-pdf-o text-danger"></i></a></td>       
                                    </tr>

                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?> 

            </form>

            <?php include('../../../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../../../js/jquery-1.10.2.min.js"></script>
        <script src="../../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../../resources/js/bootstrap.min.js"></script>
        <script src="../../../resources/js/tooltip.js"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../js/global.js"></script>

        <script>
            $(function () {


                $('#regiao').change(function () {
                    var id_regiao = $(this).val();

                    $('#projeto').next().html('<img src="../../../img_menu_principal/loader16.gif"/>');
                    $.ajax({
                        url: '../../../action.global.php?regiao=' + id_regiao,
                        success: function (resposta) {
                            $('#projeto').html(resposta);
                            $('#projeto').next().html('');


                        }
                    });


                });

                $('#regiao').trigger('change');



                $("#exportarExcel").click(function (e) {

                    var html = $("#relatorio_exp").html();
                    console.log(html);
                    $("#data_xls").val(html);
                    $("#form").submit();

                });

            });

        </script>
    </body>
</html>
