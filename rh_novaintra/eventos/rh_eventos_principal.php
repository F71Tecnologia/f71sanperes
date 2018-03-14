<?php
/*
 * CONTROLLER: eventos/intex.php 
 * TELA:       rh_eventos_principal.php
 */
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Eventos ::</title>

        <link rel="shortcut icon" href="../../favicon.ico" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css">
        <style>
            .btn.btn-rounded {
                border-radius: 9999px;
            }
        </style>
    </head>
    <body>

        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">

                <div class="col-xs-12">

                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Eventos</small></h2></div>

                    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" name="form1" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <label for="projeto" class="col-xs-2 control-label">Projeto:</label>
                                    <div id="cel_evento" class="col-xs-3">
                                        <?= montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?>
                                    </div>
                                    <label for="clt_nome" class="col-xs-2 control-label">Nome do Funcionário:</label>
                                    <div id="cel_evento" class="col-xs-3">
                                        <input type="text" class="form-control" name="clt_nome" id="clt_nome" value="<?= (isset($_REQUEST['clt_nome']) || !empty($_REQUEST['clt_nome'])) ? $_REQUEST['clt_nome'] : "" ?>">
                                    </div>
                                    <div id="cel_evento" class="col-xs-2">
                                        <input type="hidden" name="home" id="home" value="">
                                        <input type="submit" value="Filtrar" id="filtrar" class="btn btn-primary">
                                    </div>
                                </div>
                            </div><!-- /.panel-body -->
                        </div><!-- /.panel -->
                        <input type="hidden" name="rhstatus" id="rhstatus" value="<?= $rhstatus ?>">
                        <input type="hidden" name="paginacao" id="paginacao" value="<?= $pagina ?>">
                        <input type="hidden" name="inicial" id="inicial" value="<?= $_REQUEST['inicial'] ?>">

                        <!-- para sumter os links -->
                        <input type="hidden" name="clt" id="clt">
                        <input type="hidden" name="regiao" id="regiao">
                        <input type="hidden" name="method" id="method">
                        <?php if (isset($participantes) && !empty($participantes)) { 

                            if (empty($_REQUEST['clt_nome'])) {
                                echo $eventoView->abasEventos($rhstatus);
                                echo $eventoView->paginacao($pagina, $rhstatus);
                            } else {
                                ?>
                                <p><input type="button" class="btn btn-default" id="tudo" value="Pesquisar por Evento"></p><br>
                            <?php } ?>

                            <?php foreach ($participantes as $id_projeto => $participante) { ?>
                                <h4><?= $id_projeto . " - " . $participante[0]['nome_projeto'] ?></h4>
                                <table id="tbRelatorio" class="table table-bordered table-hover table-condensed"> 
                                    <thead>
                                        <tr class="bg-primary valign-middle">
                                            <th style="width: 10%">COD</th>
                                            <th style="width: 30%">NOME</th>
                                            <th style="width: 25%">CARGO</th>
                                            <th style="width: 10%">STATUS</th>
                                            <th style="width: 15%">DURAÇÃO</th>
                                            <th style="width: 10%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($participante as $value) { ?>
                                            <tr class="valign-middle">
                                                <td><?= $value['id_clt'] ?></td>
                                                <td><?= abreviacao($value['nome'], 4, 1) ?></td>
                                                <td><?= $value['curso'] ?></td>
                                                <td><?= $value['status'] . " - " . $value['nome_status'] ?></td>
                                                <td>
                                                    <?php
                                                    if ($value['status'] != 10) {
                                                        echo ($value['pericia'] == 1) ? $value['data'] . ' - ' . $value['data_retorno'] : $value['data'] . ' - ' . $value['data_retorno_final'];
                                                    } else {
                                                        echo 'N/A';
                                                    } ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="#" data-clt="<?= $value['id_clt'] ?>" data-regiao="<?= $value['id_regiao'] ?>" class="btn btn-xs btn-rounded btn-info link_evento" title="Inserir evento para <?= $value['nome'] ?>">
                                                        <!--<img src="../../imagens/icones/icon-filego.gif" title="Inserir evento para <?= $value['nome'] ?>" alt="">-->
                                                        <i class="fa fa-external-link-square"></i>
                                                    </a>
                                                </td>
                                            </tr>                                
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <br>
                                <?php
                            }
                            if (empty($_REQUEST['clt_nome'])) {
                                echo $eventoView->paginacao($pagina, $rhstatus);
                            }
                        } else {
                            echo $eventoView->paginacao($pagina, $rhstatus);?>
                            <div class="bs-callout bs-callout-warning">
                                <h4 class="text-danger">ATENÇÃO!</h4>
                                <p>Não há resultados para essa pesquisa.</p>
                            </div>
                            <!--<div class="back-red"><img src="../../imagens/icones/icon-error.gif" title="ATENÇÃO" alt="ATENÇÃO"></div>-->
                            <?php
                        }
                        ?>

                    </form>
                </div><!-- /.col-xs-12 -->

            </div><!-- /.row -->
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>

        <script src="../../resources/js/rh/eventos/eventos_principal.js"></script>
    </body>
</html>