<?php
    if (empty($_COOKIE['logado'])) {
        print "Efetue o Login<br><a href='../login.php'>Logar</a>";
        exit;
    }

    include "../conn.php";
    include "../classes/funcionario.php"; 
    include '../classes_permissoes/regioes.class.php';
    include "../wfunction.php";
    include "../classes_permissoes/acoes.class.php";
        
    $slq= " SELECT a.nome AS Nome , b.id_clt , c.unidade AS Unidade
            FROM rh_clt a
            LEFT JOIN rh_clt_unidades_assoc b
            ON a.id_clt = b.id_clt
            LEFT JOIN unidade c
            ON b.id_unidade = c.id_unidade
            WHERE a.status < 60 OR a.status = 200 OR a.status = 70";
    $query = mysql_query($slq);   
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Duplo Vinculo de Unidade </title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Duplo Vinculo de Unidade</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel-footer text-right"><button type="button" form="formPdf" name="pdf" data-title="Duplo Vinculo de Unidade" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button></div>
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <table class="table table-striped table-condensed table-bordered table-hover text-sm valign-middle" id="tbRelatorio">
                            <thead>
                            <tr>
                                <th class="text-center">Id</th>
                                <th class="text-center">Nome</th>
                                <th class="text-center">Unidade</th>                                
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            while($result = mysql_fetch_assoc($query)){
                            ?>                       
                                <tr align="center">
                                    <td><?=$result['id_clt'] ?></td>
                                    <td><?=$result['Nome'] ?></td>
                                    <td><?=$result['Unidade'] ?></td>                                                                       
                                </tr>                                
                            <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
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
    </body>
</html>

