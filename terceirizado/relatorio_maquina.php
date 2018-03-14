<?php
include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();


if (empty($_COOKIE['logado']))
{
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

$sql = "select maquina, date_format(data, '%d/%m/%Y %H:%i:%s') as data, datediff(curdate(), data) as diff
            from 
            (
            select p.maquina, MAX(p.data_completa) as data from terceiro_ponto p inner join acesso_maquina m on p.maquina = m.maquina where m.ativo = 'S'
            group by p.maquina) a;";

$result = mysql_query($sql);

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
//$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Controle de Acesso", "id_form" => "form1", "ativo" => "Relatório Controle de Máquina");
$breadcrumb_config = array("nivel" => "../", "key_btn" => "40", "area" => "Controle de Acesso", "id_form" => "form", "ativo" => "Relatório Controle de Máquina");
//$breadcrumb_pages = array("Escalas" => "escalas2.php");
?>
<html>
    <head>
        <title>:: Intranet :: Controle de Máquina</title>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />

        <link href="../jquery/css/smoothness/jquery-ui-1.10.0.custom.min.css" rel="stylesheet" type="text/css" />
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/add-ons.min.css" rel="stylesheet">

        <style media="print">
            .noprint{
                display: none;
            }
        </style>
    </head>
    <body>   
        <div class="imp_cl">
            <?php include("../template/navbar_default.php"); ?>
        </div>        
        <div class="container">
            <div id="content" class="container-fluid">
                <form  name="form" action="" method="post" id="form" class="form-horizontal" style="padding: 0 70px;">

                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header">Controle de Máquina
                                <small>Relatório</small>
                            </h1>
                        </div>
                    </div>

                    <div class="row noprint">


                    </div>
                    <div class="row">
                        <table id="tbRelatorio" class="table table-hover">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>NOME</th>
                                    <th style="text-align: center;">ÚLTIMA ATUALIZAÇÃO</th>
                                    <th style="text-align: center;">DIAS SEM ATUALIZAR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tot = 0;
                                while ($row = mysql_fetch_array($result)):
                                    ?>

                                    <!--tr class="linha_<?php echo ($alternateColor++ % 2 == 0) ? "um" : "dois"; ?>" style="font-size:12px;"-->
                                    <tr>
                                        <td style="text-align: center;">
                                            <?php if ($row['diff'] == 0): ?>
                                                <img src="./img/bullet_ball_glass_green.png" />
                                            <?php elseif ($row['diff'] == 1): ?>
                                                <img src="./img/bullet_ball_glass_yellow.png"/>
                                            <?php else: ?>
                                                <img src="./img/bullet_ball_glass_red.png"/>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $row['maquina'] ?></td>
                                        <td style="text-align: center;"><?= $row['data'] ?></td>
                                        <td style="text-align: center;"><?= $row['diff'] ?></td>
                                    </tr>

                                    <?php $tot = $tot + $row['valor'];
                                endwhile;
                                ?>
                            </tbody>
                            <!--tr class="linha_<?php echo ($alternateColor++ % 2 == 0) ? "um" : "dois"; ?>" style="font-size:12px;">
                                <td></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                                <td style="text-align: center;"></td>
                            </tr-->		

                        </table>
                    </div>
                </form>
            </div>
        </div>
        
        <script src="jquery/js/jquery.js"></script>
        <script src="jquery/js/jquery-ui.js"></script>
        <script src="jquery/jquery.mask.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        
    </body>
</html>