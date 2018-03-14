<?php
if (empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
}

include("../conn.php");
include("../classes/regiao.php");
include("../classes/projeto.php");
include("../classes/funcionario.php");
include("../classes_permissoes/regioes.class.php");
include("../classes_permissoes/acoes.class.php");
include("../wfunction.php");
error_reporting(1);

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$optAnos = array();
$optMeses = mesesArray();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$optAnos = anosArray(null, null, array('' => "<< Ano >>"));

if (isset($_REQUEST['gerar'])) {
$cont = 0;
$arrayStatus = array(10, 20, 30, 40, 50, 51, 52);
$status = implode(",", $arrayStatus);

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];
$mes = sprintf("%02d", $_REQUEST['mes']);
$ano = $_REQUEST['ano'];

$projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
$sql = "SELECT A.id_curso, B.nome AS nome_curso, COUNT(A.id_curso) AS total_por_curso 
            FROM rh_clt AS A
            LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
            LEFT JOIN rh_folha_proc AS C ON(A.id_clt = C.id_clt)
            WHERE A.id_projeto = '{$id_projeto}' AND A.status = 10 AND C.mes = '{$mes}' AND C.ano = '{$ano}' GROUP BY A.id_curso";

echo "<!-- {$sql} -->";

$sql_status = "SELECT B.id_status, COUNT(A.status) AS total_status, B.especifica AS nome_status
            FROM rh_clt AS A
            LEFT JOIN rhstatus AS B ON(A.`status` = B.codigo)
            LEFT JOIN rh_folha_proc AS C ON(A.id_clt = C.id_clt)
            WHERE A.id_projeto = '{$id_projeto}' AND C.mes = '{$mes}' AND C.ano = '{$ano}' 
            GROUP BY A.status";

$qr_relatorio = mysql_query($sql) or die(mysql_error());
$qr_relatorio_status = mysql_query($sql_status) or die(mysql_error());

}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : null;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Participantes Ativos</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span>Relatório de Resumo por Unidade</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">
                <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />

                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print">Região</label>
                            <div class="col-sm-4">
                                </label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print">Mês</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optMeses, $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'form-control')); ?> <span class=""loader></span>
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print">Ano</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optAnos, $anoSel, array('name' => 'ano', 'id' => 'ano', 'class' => 'form-control')); ?> <span class=""loader></span>
                            </div>
                        </div>

                    </div>
                    <div class="panel-footer text-right hidden-print">
                        <?php if (!empty($qr_relatorio) && isset($_POST['gerar'])) { ?>
                        <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Por Unidade')" value="Exportar para Excel" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar Para Excel</button>
                        <?php } ?>
                        <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-file-excel-o"></span> Gerar</button>
                    </div>
                </div>
            </form> 

            <?php if (!empty($qr_relatorio) && isset($_POST['gerar'])) { ?>    
            <h3><?php echo "Totalizadores para " . $projeto['nome'] ?></h3>
            <table id="tbRelatorio" class="table table-striped table-condensed table-bordered table-hover text-sm valign-middle"> 
                <thead>
                    <tr>
                        <th colspan="3" class="text-center">TOTAIS POR CARGO EM ATIVIDADE NORMAL</th>
                    </tr>
                    <tr>
                        <th>ID</th>
                        <th>CARGO</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++% 2 == 0)?"even":"odd" ?>
                    <tr class="<?php echo $class ?>">
                        <td><?php echo $row_rel['id_curso'] ?></td>
                        <td> <?php echo $row_rel['nome_curso']; ?></td>
                        <td> <?php echo $row_rel['total_por_curso']; ?></td>
                        <?php $total_cargo += $row_rel['total_por_curso']; ?>
                    </tr>                                
                    <?php } ?>
                </tbody>
                <tfoot>
                <td colspan="2">Total</td>
                <td><?php echo $total_cargo; ?></td>
                </tfoot>
            </table>
        </td>
        <td>
            <table id="tbRelatorio" class="table table-striped table-condensed table-bordered table-hover text-sm valign-middle"> 
                <thead>
                    <tr>
                        <th colspan="3" class="text-center">TOTAIS POR STATUS</th>
                    </tr>
                    <tr>
                        <th>ID</th>
                        <th>NOME</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row_rel_status = mysql_fetch_assoc($qr_relatorio_status)) { $class = ($cont++% 2 == 0)?"even":"odd" ?>
                    <tr class="<?php echo $class ?>">
                        <td> <?php echo $row_rel_status['id_status']; ?></td>
                        <td> <?php echo $row_rel_status['nome_status']; ?></td>
                        <td><?php echo $row_rel_status['total_status'] ?></td>
                        <?php $total_status+= $row_rel_status['total_status']; ?>
                    </tr>                                
                    <?php } ?>
                </tbody>
                <tfoot>
                <td colspan="2">Total</td>
                <td><?php echo $total_status; ?></td>
                </tfoot>
            </table>
            <?php } ?>
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
                            $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
                        });
    </script>

</body>
</html>
