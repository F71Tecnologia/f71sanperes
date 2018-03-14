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

        <title>:: Intranet :: Hist�rico de Movimentos</title>
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
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Hist�rico de Movimentos</small></h2></div>
                </div>
            </div>
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" class="form-horizontal" >
                <div class="panel panel-default">
                    <div class="panel-body">
                        <input type="hidden" name="home" id="home" value="">
                        <input type="hidden" name="id_projeto" id="id_projeto" value="<?=$projetoR ?>" />
                        <input type="hidden" name="id_projeto" id="id_projeto" value="<?=$regiaoR ?>" />
                        <input type="hidden" name="id_clt" id="id_clt" value="<?=$id ?>">
                        <div class="form-group">
                            <label class="col-xs-offset-1 col-xs-1 control-label">Regi�o:</label>
                            <label class="col-xs-3 control-label text-left"><?=$regiaoArray[1]['regiao']?></label>
                            <label class="col-xs-1 control-label">Projeto:</label>
                            <div class="col-xs-5">
                                <?=montaSelect(GlobalClass::carregaProjetosByRegiao($regiaoR), $projetoR, "id='projeto' name='projeto' class='required[custom[select]] form-control'") ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" class="btn btn-primary" value="Filtrar" name="filtrar" />
                    </div>
                </div>
            </form>
            <?php
            if (isset($clt)) {
                if (count($clt) > 0) {
                    ?>
                    <p style="text-align: left; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relat�rio')" value="Exportar para Excel" class="btn btn-success pull-right exportarExcel"></p>
                    <table id="tbRelatorio" class="table table-striped table-hover table-condensed table-bordered text-sm">
                        <thead>
                            <tr class="bg-primary valign-middle">
                                <th>COD</th>
                                <th>MATR�CULA</th>
                                <th>NOME</th>
                                <th>CARGO</th>
                                <th>UNIDADE</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clt as $row_clt) { ?>
                                <tr class="valign-middle">
                                    <td style="text-align:center;"><?=$row_clt['id_clt'] ?></td>
                                    <td style="text-align:center;"><?=$row_clt['matricula'] ?></td>
                                    <td><?= $row_clt['nome'] ?> 
                                        <?php
                                        if ($row_clt['status'] == '40') {
                                            echo '<span class="text-info">(Em F�rias)</span>';
                                        } elseif ($row_clt['status'] == '200') {
                                            echo '<span class="text-danger">(Aguardando Demiss�o)</span>';
                                        }
                                        ?></td>
                                    <td><?= $row_clt['curso'] ?></td>
                                    <td style="text-align:center;"><?=$row_clt['locacao'] ?></td>
                                    <td style="text-align:center;">
                                        <a href="?tela=2&AMP;id=<?=encrypt($row_clt['id_clt']); ?>&AMP;projeto=<?=encrypt($projetoR); ?>&AMP;regiao=<?=encrypt($regiaoR); ?>" class="btn btn-xs btn-info link_evento"  title="Inserir evento para <?= $row_clt['nome'] ?>">
                                        <!--a href="?tela=2" data-clt="8106" data-regiao="36" class="btn btn-xs btn-info link_evento" title="Inserir evento para F TESTE DDSFDSFSF"-->
                                            <i class="fa fa-external-link-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div id='message-box' class='alert alert-warning'>
                        <p>Nenhum registro encontrado</p>
                    </div>
                <?php
                }
            } ?>
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