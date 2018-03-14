<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../conn.php");
include("../wfunction.php");

$sql = "SELECT * FROM terceiro_ponto WHERE data <= '2014-06-21'";
$result = mysql_query($sql);

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Normalização</title>

        <link rel="shortcut icon" href="favicon.ico" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <table class='table table-striped table-hover'>
                        <thead>
                            <th>Data Completa</th>
                            <th>Terceirizado</th>
                            <th>Autonomo</th>
                            <th>Funcionario</th>
                            <th>PIS</th>
                            <th>Imagem</th>
                            <th>#</th>
                        </thead>
                        <tbody>
                            <?php 
                            while ($row = mysql_fetch_assoc($result)){ 
                            ?>
                            <tr>
                                <td><?php echo $row['data_completa'] ?></td>
                                <td><?php echo $row['id_terceirizado'] ?></td>
                                <td><?php echo $row['id_autonomo'] ?></td>
                                <td><?php echo $row['id_funcionario'] ?></td>
                                <td><?php echo $row['pis'] ?></td>
                                <td><?php echo $row['imagem'] ?></td>
                                <td><a href="javascript:;" class="bt" data-key="<?php echo $row['imagem'] ?>">Alterar</a></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <footer>
                <div class="row">
                    <div class="page-header"></div>
                    <div class="pull-right"><a href="#top">Voltar ao topo</a></div>
                    <div class="col-lg-12">
                        <p>Pay All Fast 3.0</p>
                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                    </div>
                </div>
            </footer>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script>
            $(function(){
                $(".bt").click(function(){
                    var botao = $(this);
                    var especifico = botao.data('especifico');
                    var rescisao = botao.data('rescisao');
                    $.post("",{method: "atualiza", id_especifico: especifico, id_rescisao: rescisao}, function(data){
                        if(data.status==1){
                            botao.parents('tr').removeAttr("class").addClass("success");
                            botao.parent().prev().prev().html(rescisao);
                        }else{
                            alert("erro ao vincular rescisão a saída");
                        }
                    },"json");
                });
            });
        </script>
    </body>
</html>