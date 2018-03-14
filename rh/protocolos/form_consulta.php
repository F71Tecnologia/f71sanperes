<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Protocolos de Entregas</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include_once("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Protocolos de Entregas</small></h2></div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->

            <?php if (isset($_SESSION['status'])) { ?>
                <div class="alert alert-dismissable alert-<?= $_SESSION['status'] ?>">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <?= $_SESSION['mensagem'] ?>
                </div>
                <?php unset($_SESSION['status'], $_SESSION['mensagem']); ?>
            <?php } ?>


            <form class="form-horizontal" action="?m=consultar" method="post" name="form1" id="form1">
                <div class="panel panel-default">
                    <!--<div class="panel-heading"><h4>Filtro</h4></div>-->
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="tipo_protocolo" class="col-sm-2 control-label">Tipo de Protocolo</label>
                            <div class="col-sm-4">
                                <?= montaSelect($optTiposProtocolos, $tipoSelected, 'class="form-control" id="tipo_protocolo" name="tipo_protocolo"'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tipo_protocolo" class="col-sm-2 control-label">Competência</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <?= montaSelect(mesesArray(), $mes, 'class="form-control" id="mes_competencia" name="mes_competencia"'); ?>
                                    <div class="input-group-addon">/</div>
                                    <?= montaSelect(anosArray(date('Y') - 5, date('Y')), $ano, 'class="form-control" id="ano_competencia" name="ano_competencia"'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <a href="?m=novo" class="btn btn-success"><i class="fa fa-plus"></i> Novo</a>
                        <button type="submit" name="m" value="consultar" class="btn btn-primary"><i class="fa fa-search"></i> Consultar</button>
                    </div>
                </div>

                <?php if (isset($listaProtocolos) && !empty($listaProtocolos)) { ?>
                    <table class="table table-bordered table-condensed table-striped table-hover">
                        <thead>
                            <tr class="primary">
                                <th>Identificador</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                                <th>Competencia</th>
                                <th style="width:60px">&emsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listaProtocolos as $value) { ?>
                                <tr>
                                    <td><?= $value['identificador'] ?></td>
                                    <td><?= $optTiposProtocolos[$value['id_tipo_protocolo']] ?></td>
                                    <td><?= $value['descricao'] ?></td>
                                    <td><?= $value['mes_competencia'].'/'.$value['ano_competencia'] ?></td>
                                    <td class="text-right">
                                        <a href="?m=editar&id=<?= $value['id_protocolos_entregas'] ?>" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i></a>
                                        <button data-id="<?= $value['id_protocolos_entregas'] ?>" class="btn btn-danger btn-xs excluir" type="button"><i class="fa fa-trash-o"></i></button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>
                <?php } ?>
            </form>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskMoney.js"></script>
        <script src="../../js/jquery.maskedinput.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>

        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(document).ready(function () {
//                $('#tipo_protocolo,#mes_competencia,#ano_competencia').change(function () {
//                    if ($('#data_ini').val() != '' && $('#data_fim').val() != '') {
//                        $('#form1').submit();
//                    }
//                });

                $('.excluir').click(function () {
                    var $this = $(this);
                    bootConfirm('Tem certeza que deseja excluir?', 'Atenção', function (confirm) {
                        if (confirm) {
                            $.post('?m=excluir', {id: $this.data('id')}, function (data) {
                                bootAlert(data.msg, 'Atenção', null, data.status);
                                if (data.status == 'success') {
                                    $this.closest('tr').remove();
                                }
                            }, 'json');
                        }
                    }, 'danger');
                });
            });
        </script>
    </body>
</html>
