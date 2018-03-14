<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Movimentos</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS</h2></div>
                    <h3>Relatório de Movimentos <small>Lista de Participantes</small></h3>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" class="form-horizontal" >
                        <input type="hidden" name="home" id="home" value="">
                        <input type="hidden" name="id_projeto" id="id_projeto" value="<?php echo $projetoR ?>" />
                        <input type="hidden" name="id_projeto" id="id_projeto" value="<?php echo $regiaoR ?>" />
                        <input type="hidden" name="id_clt" id="id_clt" value="<?php echo $id ?>">
                        <div class="form-group">
                            <label class="col-lg-1 control-label">Região: </label>
                            <label class="col-lg-1 control-label">
                                <?php echo $regiaoArray[1]['regiao']; ?>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-1 control-label">Projeto: </label>
                            <div class="col-lg-3">
                                <?php echo montaSelect(GlobalClass::carregaProjetosByRegiao($regiaoR), $projetoR, "id='projeto' name='projeto' class='required[custom[select]] form-control'") ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-1 control-label">
                                <input type="submit" class="btn btn-primary" value="Filtrar" name="filtrar" />
                            </label>
                        </div>
                        <hr>
                        <?php
                        if (isset($clt)) {
                            if (count($clt) > 0) {
                                ?>
                                <p style="text-align: left; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="btn btn-success pull-right exportarExcel"></p>
                                <table id="tbRelatorio" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>COD</th>
                                            <th>MATRÍCULA</th>
                                            <th>NOME</th>
                                            <th>CARGO</th>
                                            <th>UNIDADE</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($clt as $row_clt) { ?>
                                            <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                                <td style="text-align:center;"><?php echo $row_clt['id_clt'] ?></td>
                                                <td style="text-align:center;"><?php echo $row_clt['matricula'] ?></td>
                                                <td><?= $row_clt['nome'] ?> 
                                                    <?php
                                                    if ($row_clt['status'] == '40') {
                                                        echo '<span style="color:#069; font-weight:bold;">(Em Férias)</span>';
                                                    } elseif ($row_clt['status'] == '200') {
                                                        echo '<span style="color:red; font-weight:bold;">(Aguardando Demissão)</span>';
                                                    }
                                                    ?></td>
                                                <td><?= $row_clt['curso'] ?></td>
                                                <td style="text-align:center;"><?php echo $row_clt['locacao'] ?></td>
                                                <td style="text-align:center;">
                                                    <!--a href="?tela=2&AMP;id=<?php echo encrypt($row_clt['id_clt']); ?>&AMP;projeto=<?php echo encrypt($projetoR); ?>&AMP;regiao=<?php echo encrypt($regiaoR); ?>"--><?= $row_clt['nome'] ?><!--/a-->
                                                    <a href="#" data-clt="8106" data-regiao="36" class="btn btn-xs btn-info link_evento" title="Inserir evento para F TESTE DDSFDSFSF">
                                                        <i class="fa fa-external-link-square"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <br/>
                                <div id='message-box' class='message-yellow'>
                                    <p>Nenhum registro encontrado</p>
                                </div>
                            <?php
                            }
                        }
                        ?>

                    </form>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
    </body>
</htm>