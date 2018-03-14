<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();

//Pegando o id_regiao da pessoa logada
$funcionarios = new funcionario();
$regiao_selecionada = $funcionarios->id_regiao;

if (!empty($_REQUEST['data_xls'])) {
    $dados = utf8_encode($_REQUEST['data_xls']);

    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=contribuicao_sindical.xls");

    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>Contribuição Sindical</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}

$arrProj = array('all' => "<< TODOS OS PROJETOS >>");
foreach ($optRegiao as $k => $proj) {
    $arrProj[$k] = $proj;
}
unset($arrProj['-1']);

//$ano = array("2013" => 2013, "2014" => 2014);
$ano = anosArray(null, null, array('' => "<< Ano >>"));

//Recebe o array para montar uma input select dos meses
$mes2 = mesesArray();
//SINDICATOS
$indice_sindicato = array();
$query_sindicato = "SELECT * FROM rhsindicato WHERE id_regiao = {$regiao_selecionada} AND status = 1 ORDER BY nome";
$sql = mysql_query($query_sindicato) or die("erro ao selecionar sindicato");

$indice_sindicato[-1] = "-- SELECIONE --";
while ($row_sindicato = mysql_fetch_assoc($sql)) {
    $indice_sindicato[$row_sindicato['id_sindicato']] = $row_sindicato['nome'];
}
//Recebe todos os nomes dos sindicatos para colocar numa input select
$sindicatoSelect = $indice_sindicato;

//CARGOS
$indice_cargo = array();
$query_cargo = "SELECT * FROM curso";
$sql_cargo = mysql_query($query_cargo) or die("erro ao selecionar cargo ou função");
while ($row_cargo = mysql_fetch_assoc($sql_cargo)) {
    $indice_cargo[$row_cargo['id_curso']] = $row_cargo['nome'];
}

$addsql = "";
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : null;
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
if ($mesSel < 10 && $mesSel > 0) {
    $mesSel = "0" . $mesSel;
//    $addsql .= " AND C.mes ='{$mesSel}' AND A.mes_mov ='{$mesSel}'";
}

$addsql .= " AND C.mes ='{$mesSel}' AND A.mes_mov ='{$mesSel}'";

if ($anoSel != "") {
    $addsql .= " AND C.ano = '{$anoSel}' AND A.ano_mov = '{$anoSel}'";
}

if ($projetoSel > 0) {
    $addsql .= " AND C.id_projeto = '{$projetoSel}'";
}

if ($_REQUEST['sindicato'] == 9999) {
    
}
$sindSel = (isset($_REQUEST['sindicato'])) ? $_REQUEST['sindicato'] : null;
if (isset($_REQUEST['gerar'])) {

    $sql = "SELECT B.id_clt, B.nome, D.nome nome_curso, D.id_curso, C.id_sindicato, C.sallimpo, C.a5019, C.mes, C.ano, A.valor_movimento, IF(C.a5019 = '0.00' && A.valor_movimento IS NULL, 'n','s') AS contribuinte
                    FROM rh_movimentos_clt AS A
                    LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
                    LEFT JOIN rh_folha_proc AS C ON (A.id_clt = C.id_clt AND C.mes = $mesSel AND C.ano = $anoSel)
                    LEFT JOIN curso AS D ON (B.id_curso = D.id_curso)
                    WHERE A.cod_movimento = 5019 AND C.id_sindicato = $sindSel $addsql AND A.`status` > 0 AND C.status > 0";

    $qr = mysql_query($sql);

    while ($rows = mysql_fetch_assoc($qr)) {
        $count++;
        $sindicatos[$rows['contribuinte']][$rows['id_sindicato']][$rows['id_clt']] = $rows;
        $totalizadores[$rows['contribuinte']][$rows['id_sindicato']]['salario'] += $rows['sallimpo'];
        $totalizadores[$rows['contribuinte']][$rows['id_sindicato']]['contribuicao'] += ($rows['a5019'] != "0.00" ? $rows['a5019'] : $rows['valor_movimento']);
    }
}
?>


<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Contribuição Sindical</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">


        <script>
            $(function () {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");

            });
        </script>

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório de Contribuição Sindical </h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <input type="hidden" name="hide_sindicato" id="hide_sindicato" value="<?php echo $sindSel ?>" />

                        <div class="form-group" >

                            <label for="select" class="col-sm-2 control-label hidden-print" >Região</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?><span class="loader"></span>
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print" >Compet&ecirc;ncia:</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($mes2, $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>

                            <div class="col-sm-3">
                                <?php echo montaSelect($ano, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print" >Sindicato:</label>
                            <div class="col-sm-9">
                                <?php echo montaSelect($sindicatoSelect, $sindSel, array('name' => "sindicato", 'id' => 'sindicato', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        </div>
                    </div>

                    <div class="panel-footer text-right hidden-print controls">
                        <button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <input type="hidden" id="data_xls" name="data_xls" value="">
                        <button type="submit" name="gerar" id="gerar" value="filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div> 


                <?php if (!empty($qr) && (isset($_POST['gerar']))) { ?>
                    <div id="exporta">
                        <table class="table_s table table-striped table-condensed table-bordered table-hover text-sm valign-middle" id="tabela">
                            <?php foreach ($sindicatos['s'] as $keySind => $sind) { ?>
                                <thead>
                                    <tr>
                                        <th class="bg-primary text-center" colspan="4">CONTRIBUINTES SINDICAIS</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">NOME</th>
                                        <th class="text-center">CARGO</th>
                                        <th class="text-center">SALÁRIO BASE</th>
                                        <th class="text-center">CONTRIBUIÇÃO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sind as $id_clt => $dados) { ?>
                                        <tr>
                                            <td><?= $dados['nome'] ?></td>
                                            <td><?= $dados['nome_curso'] ?></td>
                                            <td>R$ <?= number_format($dados['sallimpo'], 2, ',', '.') ?></td>
                                            <td>R$ <?= number_format($dados['valor_movimento'], 2, ',', '.') ?></td>
                                        </tr>
                                    <?php } ?>
                                    <tr style="font-weight:bold">
                                        <td class="text-right">TOTAL:</td>
                                        <td><?= $count ?> Funcionários</td>
                                        <td>R$ <?= number_format($totalizadores['s'][$keySind]['salario'], 2, ',', '.') ?></td>
                                        <td>R$ <?= number_format($totalizadores['s'][$keySind]['contribuicao'], 2, ',', '.') ?></td>
                                    </tr>
                                </tbody>
                            <?php } ?>
                        </table>
                    </div>
                    <table class="table_ns table table-striped table-condensed table-bordered table-hover text-sm valign-middle" id="tabela">
                        <?php foreach ($sindicatos['n'] as $keySind => $sind) { ?>
                            <thead>
                                <tr>
                                    <th class="bg-danger text-center" colspan="4">NÃO CONTRIBUINTES SINDICAIS</th>
                                </tr>
                                <tr>
                                    <th class="text-center">NOME</th>
                                    <th class="text-center">CARGO</th>
                                    <th class="text-center">SALÁRIO BASE</th>
                                    <th class="text-center">CONTRIBUIÇÃO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sind as $id_clt => $dados) { ?>
                                    <tr>
                                        <td><?= $dados['nome'] ?></td>
                                        <td><?= $dados['nome_curso'] ?></td>
                                        <td>R$ <?= number_format($dados['sallimpo'], 2, ',', '.') ?></td>
                                        <td>R$ <?= number_format($dados['valor_movimento'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php } ?>
                                <tr style="font-weight:bold">
                                    <td class="text-right">TOTAL:</td>
                                    <td><?= $count ?> Funcionários</td>
                                    <td>R$ <?= number_format($totalizadores['s'][$keySind]['salario'], 2, ',', '.') ?></td>
                                    <td>R$ <?= number_format($totalizadores['s'][$keySind]['contribuicao'], 2, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        <?php } ?>
                    </table>
                <?php } ?>
            </form>
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>

        <script>
            $(function () {
                $(".bt-image").on("click", function () {
                    var id = $(this).data("id");
                    var contratacao = $(this).data("contratacao");
                    var nome = $(this).parents("tr").find("td:first").html();
                    thickBoxIframe(nome, "relatorio_documentos_new.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
                });
            });
            $(function () {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
//                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaSindicatos"}, null, "sindicato");
                $("#exportarExcel").click(function () {
                    $("#exporta img:last-child").remove();

                    var html = $("#exporta").html();

                    $("#data_xls").val(html);
                    $("#form").submit();
                });
            });
        </script>
    </body>
</html>
