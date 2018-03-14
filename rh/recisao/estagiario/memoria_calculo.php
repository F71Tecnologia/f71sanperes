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
                    <div class="panel-body">
                        <input type="hidden" name="id_estagiario" value="<?= $_REQUEST['id_estagiario'] ?>">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Nome</label>
                            <div class="col-sm-10"><input type="text" value="<?= $row_estagiario['nome'] ?>" class="form-control" name="nome" readonly=""></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Início do Estágio</label>
                            <div class="col-sm-3"><input type="text" value="<?= converteData($row_estagiario['inicio_estagio'], 'd/m/Y') ?>" class="form-control" name="data_ini" readonly=""></div>
                            <label class="col-sm-2 control-label">Encerramento do Estágio</label>
                            <div class="col-sm-3"><input type="text" value="<?= $data_fim ?>" class="form-control" name="data_fim" readonly=""></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Motivo</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" disabled value="<?= $arr_motivo['fator'] == 1 ? 'ESTAGIÁRIO':'EMPRESA' ?> - <?= $arr_motivo['descricao'] ?>">
                            </div>
                            <div class="col-sm-5">
                                <input type="text" name="obs_motivo" class="form-control" readonly value="<?= $_REQUEST['obs_motivo'] ?>">
                                <input type="hidden" name="id_motivo" class="form-control" value="<?= $_REQUEST['id_motivo'] ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Saldo de Salário (<?= $dias ?> dia<?= $dias > 1 ? 's' : '' ?>)</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-addon">R$</div>
                                    <input type="text" value="<?= number_format($saldo_salario, 2, ',', '.') ?>" class="form-control real calcular" name="valor_bolsa">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Saldo do Recesso (<?= $meses ?>/12)</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-addon">R$</div>
                                    <input type="text" value="<?= number_format($saldo_recesso, 2, ',', '.') ?>" class="form-control real calcular" name="valor_recesso">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Total Liquido</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-addon">R$</div>
                                    <input type="text" value="<?= number_format($saldo_recesso + $saldo_salario, 2, ',', '.') ?>" class="form-control real" name="total_liquido" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" name="method" class="btn btn-primary" value="processar"><i class="fa fa-check"></i> Confirmar</button>
                    </div>
                </div>

            </form>


            <?php include('../../../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../../../js/jquery-1.10.2.min.js"></script>
        <script src="../../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../../resources/js/bootstrap.min.js"></script>
        <script src="../../../resources/js/tooltip.js"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../js/global.js"></script>
        <script>
            $(document).ready(function () {

                $(".real").maskMoney({thousands: '.', decimal: ','});

                $('.calcular').change(function () {
                    console.log('aqui');
                    var valor_recesso = $('input[name=valor_recesso]').maskMoney('unmasked')[0];
                    var valor_bolsa = $('input[name=valor_bolsa]').maskMoney('unmasked')[0];
                    console.log(valor_recesso);
                    console.log(valor_bolsa);

                    var total_liquido = valor_recesso + valor_bolsa;
                    console.log(total_liquido);
                    $('input[name=total_liquido]').maskMoney('mask', total_liquido);

                });
            });
        </script>
    </body>
</html>
