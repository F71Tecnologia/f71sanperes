<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}
include("../conn.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$projeto = $_REQUEST['pro'];
$regiao = (!empty($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];

//$sql = "select t.*, c.campo2 from terceirizado t inner join curso c on t.id_curso = c.id_curso";
$sql = "select t.*, c.campo2, p.c_fantasia from terceirizado t inner join curso c on (t.id_curso = c.id_curso) left join prestadorservico as p on (t.id_prestador=p.id_prestador) ORDER BY nome";
//echo '<!-- '.$sql.' -->';
$result = mysql_query($sql);
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Funcionários com Insalubridade</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
    </head>

    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório <small> - Relatório de Terceirizados</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Terceirizados</div>

                </div>

                <div class="panel-footer text-right hidden-print">
                    <button type="button" value="exportar para excel" onclick="tableToExcel('tbRelatorio', 'Relatórios de Terceirizados')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                </div>   

                <table class="table table-striped table-hover table-condensed table-bordered text-sm valign-middle" id="tbRelatorio">
                    <thead>
                        <tr>
                            <th class="text-center">COD</th>
                            <th class="text-center">NOME</th>
                            <th class="text-center">CARGO</th>
                            <th class="text-center">CPF</th>
                            <th class="text-center">PRESTADOR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysql_fetch_array($result)) { ?>
                            <tr class="linha_<?php echo ($alternateColor++ % 2 == 0) ? "um" : "dois"; ?>">
                                <td><?= $row['id_terceirizado'] ?></td>
                                <td><a class="participante" href="alterterceiro.php?reg=<?= $regiao ?>&pro=<?= $projeto ?>&id=<?= $row['id_terceirizado'] ?>"><?= $row['nome'] ?></a></td>
                                <td><?= $row['campo2'] ?></td>
                                <td align="center"><?= $row['cpf'] ?></td>
                                <td align="center"><?= $row['c_fantasia'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
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
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
        </script>

    </body>
</html>
