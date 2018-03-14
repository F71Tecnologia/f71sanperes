<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include("../conn.php");
include("../classes/funcionario.php");
include("../classes_permissoes/regioes.class.php");
include("../classes_permissoes/acoes.class.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();

$ACOES = new Acoes();


/* $mesesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
  $anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
  $meses = mesesArray(null, '', "<< Mês >>");
  $anoOpt = anosArray(null, null, array('' => "<< Ano >>")); */
$dt_ini = (isset($_REQUEST['data_ini'])) ? $_REQUEST['data_ini'] : date("d/m/Y", strtotime('-30 days'));
$dt_fim = (isset($_REQUEST['data_fim'])) ? $_REQUEST['data_fim'] : date("d/m/Y");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $dt_ini = $_REQUEST['data_ini'];
    $dt_fim = $_REQUEST['data_fim'];

    $dt_iniCon = converteData($dt_ini);
    $dt_fimCon = converteData($dt_fim);
    $dt_referencia = $ano . '-' . $mes . '-01';


    $sql = "SELECT A.id_clt, A.nome, A.matricula, A.cpf, A.pis, A.tel_cel, DATE_FORMAT(A.data_entrada,'%d/%m/%Y') AS dt_admissao, E.nome as projeto, C.nome as funcao, DATE_FORMAT(B.data_demi, '%d/%m/%Y') as dt_demissao, B.total_liquido, F.especifica
                FROM rh_clt AS A
                INNER JOIN rh_recisao AS B ON (A.id_clt = B.id_clt AND B.`status` = 1)
                LEFT JOIN curso AS C ON (A.id_curso = C.id_curso)
                LEFT JOIN regioes AS D ON (A.id_regiao = D.id_regiao)
                LEFT JOIN projeto AS E ON (A.id_projeto = E.id_projeto)
                LEFT JOIN rhstatus AS F ON (B.motivo = F.codigo)
                WHERE B.data_demi BETWEEN '{$dt_iniCon}' AND '{$dt_fimCon}' AND A.id_regiao = {$id_regiao} ";

    if ($_COOKIE['debug'] == 666) {
        echo $sql;
    }
//    $sql = "SELECT A.*, DATE_FORMAT(A.data_adm, '%d/%m/%Y') as dt_admissao, 
//                            DATE_FORMAT(A.data_demi, '%d/%m/%Y') as dt_demissao, B.nome as nome_projeto
//                            FROM rh_recisao  as A
//                            INNER JOIN projeto as B
//                            ON B.id_projeto = A.id_projeto
//                            WHERE A.id_regiao = '$id_regiao' ";
    if (!isset($_REQUEST['todos_projetos'])) {
        $sql .= " AND A.id_projeto = '$id_projeto' ";
    }

    $sql .= " ORDER BY A.id_projeto,A.nome";
    //$sql .= " AND MONTH(B.data_demi) = '$mes' AND YEAR(B.data_demi) = '$ano' AND B.status = 1; ";
    //echo $sql;
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório Rescisões</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Rescisões</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">

                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Filtro</div>
                    <div class="panel-body">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <div class="form-group">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label hidden-print">Região</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                            </div>
                            <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>

                        <div class="form-group datas">
                            <label for="data_ini" class="col-sm-offset-1 col-sm-1 control-label hidden-print">Período</label>
                            <div class="col-lg-9">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <input type="text" class="input form-control data" name="data_ini" id="data_ini" readonly="true" placeholder="Inicio" value="<?php echo $dt_ini ?>">
                                    <span class="input-group-addon ate">até</span>
                                    <input type="text" class="input form-control data" name="data_fim" id="data_fim" readonly="true" placeholder="Fim" value="<?php echo $dt_fim ?>">
                                    <span class="input-group-addon ate"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer text-right hidden-print">
                        <?php if (isset($_POST['gerar']) || isset($_REQUEST['todos_projetos']) || (mysql_num_rows($qr_relatorio) != 0)) { ?>
                            <button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório de Rescisões')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <?php } ?>

                        <?php
                        ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                        <?php } ?>

                        <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                    </div>
                </div>
            </form>


            <?php if (isset($_POST['gerar']) || isset($_REQUEST['todos_projetos']) || (mysql_num_rows($qr_relatorio) != 0)) { ?>
                <table class="table table-striped table-condensed table-bordered text-sm valign-middle" id="tbRelatorio">
                    <thead>
                        <tr>
                            <th>PROJETO</th>
                            <th>MATRÍCULA</th>
                            <th>NOME</th>
                            <th>CPF</th>
                            <th>PIS</th>
                            <th>TELEFONE</th>
                            <th>DATA DE ADMISSÃO</th>
                            <th>DATA DE DEMISSÃO</th>
                            <th>FUNÇÃO</th>
                            <th>LíQUIDO</th>
                            <th>MOTIVO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { ?>
                            <tr style="font-size:11px;">
                                <td><?php echo $row_rel['projeto'] ?></td>
                                <td><?php echo $row_rel['matricula'] ?></td>
                                <td><?php echo $row_rel['nome'] ?></td>
                                <td><?php echo $row_rel['cpf'] ?></td>
                                <td><?php echo $row_rel['pis'] ?></td>
                                <td><?php echo $row_rel['tel_cel'] ?></td>
                                <td align="center"><?php echo $row_rel['dt_admissao'] ?></td>
                                <td align="center"><?php echo $row_rel['dt_demissao'] ?></td>
                                <td align="center"><?php echo $row_rel['funcao'] ?></td>
                                <td align="center"><?php echo $row_rel['total_liquido'] ?></td>
                                <td><?php echo $row_rel['especifica'] ?></td>


                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="alert alert-dismissable alert-warning">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <span class="fa fa-exclamation-triangle"></span> Nenhum registro encontrado.
                </div>
            <?php }
            ?>  
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.ui.datepicker-pt-BR.js" type="text/javascript"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>

        <script>
                                $(function () {
                                    $('.data').datepicker({
                                        dateFormat: 'dd/mm/yy',
                                        changeMonth: true,
                                        changeYear: true,
                                        yearRange: '2005:c+1'
                                    });

                                    $("#form1").validationEngine();
                                    var id_destination = "projeto";

                                    $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function (data) {
                                        removeLoading();
                                        $("#" + id_destination).html(data);
                                        var selected = $("input[name=hide_" + id_destination + "]").val();
                                        if (selected !== undefined) {
                                            $("#" + id_destination).val(selected);
                                        }
                                        $('#projeto').trigger('change');
                                    }, "projeto");

                                });
        </script>

    </body>
</html>