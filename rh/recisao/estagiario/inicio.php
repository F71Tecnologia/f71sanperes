<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Rescisão do Termo de Compromisso de Estágio</title>

        <link href="../../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">


    </head>
    <body>
        <?php include("../../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Rescisão do Termo de Compromisso de Estágio</h2></div>

            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print"><h4>Rescisão</h4></div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Nome</label>
                            <div class="col-sm-9">
                                <p class="form-control-static"><?= $row_estagiario['nome'] ?></p>
                                <input type="hidden" name="id_estagiario" value="<?= $_REQUEST['id_estagiario'] ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Data da Rescisão</label>
                            <div class="col-sm-3">
                                <input type="text" name="data_fim" class="form-control data">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Motivo da rescisão</label>
                            <?php foreach ($motivos as $fator => $mot) { ?>
                                <div class="col-sm-5">
                                    <p class="form-control-static">
                                        <?php echo ($fator == 1) ? 'ESTAGIÁRIO:' : 'EMPRESA:'; ?>
                                    </p>
                                    <?php foreach ($mot as $mmm) { ?>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="id_motivo" id="id_motivo<?= $mmm['id_motivo'] ?>" value="<?= $mmm['id_motivo'] ?>" data-id="obs_motivo_<?= $mmm['id_motivo'] ?>">
                                                <?= $mmm['descricao'] ?>
                                                <?php if ($mmm['obs'] == 1) { ?>
                                                    <input type="text" name="obs_motivo" class="form-control obs_motivo" id="obs_motivo_<?= $mmm['id_motivo'] ?>" disabled="">
                                                <?php } ?>
                                            </label>

                                        </div>
                                    <?php } ?>
                                </div>   
                            <?php } ?>
                        </div>
                    </div>

                    <div class="panel-footer text-right hidden-print">
                        <button type="submit" name="method" id="method" value="memoria_calculo" class="btn btn-primary"><i class="fa fa-calculator"></i> Visualizar</button>
                    </div>  
                </div>
            </form>


            <?php include('../../../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../../../js/jquery-1.10.2.min.js"></script>
        <script src="../../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../../resources/js/bootstrap.min.js"></script>
        <script src="../../../resources/js/tooltip.js"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../js/global.js"></script>
        <script>
            $(document).ready(function () {
                $('input').change(function () {
                    var id = $('input[name=id_motivo]:checked').data('id');
                    console.log(id);
                    $('.obs_motivo').prop('disabled', true);
                    $('#' + id).prop('disabled', false);
                });
            });
        </script>
    </body>
</html>

