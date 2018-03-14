<?php
include("../conn.php");

$sql = "SELECT B.nome, B.pis, DATE_FORMAT(B.data_entrada, '%d/%m/%Y') entrada, CONCAT(DATE_FORMAT(A.data, '%d/%m/%Y'), ' à ', DATE_FORMAT(A.data_retorno, '%d/%m/%Y')) periodo, A.nome_status
        FROM rh_eventos A
        INNER JOIN rh_clt B ON A.id_clt = B.id_clt
        WHERE cod_status IN (80, 54, 50, 20, 51) AND B.id_regiao = 44 AND A.status = 1
        ORDER BY B.nome ASC;";
$query = mysql_query($sql);
while ($row = mysql_fetch_assoc($query)) {
    $arr[] = $row;
}

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <title>:: Intranet :: Participantes com Afastamento Previdênciario</title>
    </head>
    <div class="container">
        <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório <small> - Participantes com Afastamento Previdênciario</small></h2></div>
        <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
            <div class="panel panel-default">
                <table class="table table-striped table-hover text-sm valign-middle" id="tbRelatorio">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>PIS</th>
                            <th>ADMISSÃO</th>
                            <th>PERÍODO</th>
                            <th>MOTIVO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($arr as $value) { ?>
                        <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">       
                            <td><?php echo $value['nome']; ?></td>
                            <td><?php echo $value['pis']; ?></td>
                            <td><?php echo $value['entrada']; ?></td>
                            <td><?php echo $value['periodo']; ?></td>
                            <td><?php echo $value['nome_status']; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </form>
        <div class="clear"></div>
    </div>
    <link href="../favicon.png" rel="shortcut icon" />
    <script src="../js/jquery-1.10.2.min.js"></script>
    <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="../resources/js/bootstrap.min.js"></script>
    <script src="../resources/js/tooltip.js"></script>
    <script src="../resources/js/main.js"></script>
    <script src="../js/global.js"></script>
    <link href="../net1.css" rel="stylesheet" type="text/css" />
</body>
</html>
