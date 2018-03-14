<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
} ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Histórico de Movimentos</title>
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
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Histórico de Movimentos</small></h2></div>
                </div>
            </div>
            <form action="?tela=3" method="post" name="form1" id="form1" enctype="multipart/form-data"  class="form-horizontal">
                <div class="panel panel-default">
                    <div class="panel-heading">Dados do Funcionário</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-xs-6"><label class="control-label">Região: </label> <?=$regiaoArray[1]['regiao']?></div>
                            <div class="col-xs-6"><label class="control-label">Projeto: </label> <?=$projetoArray[1]['nome']?></div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-6"><label class="control-label">Funcionário: </label> <?=$clt[1]['nome']?></div>
                            <div class="col-xs-6"><label class="control-label">Cargo: </label> <?=$clt[1]['curso']?></div>
                        </div>
                    </div>
                    <div class="panel-heading border-t">Filtro</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="control-label col-xs-3">Início do Período:</label>
                            <div class="col-xs-8">
                                <div class="input-group">
                                    <?=montaSelect(mesesArray(), (isset($mesIni)) ? $mesIni : date('m') - 1, 'name="mes_ini" id="mes_ini" class="form-control validate[required,funcCall[checkMeses]]"'); ?>
                                    <div class="input-group-addon">/</div>
                                    <input type="number" value="<?=(isset($ano)) ? $ano : date('Y')?>" name="ano" id="ano" class="form-control validate[required]">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="id_clt" id="id_clt" value="<?=$id_clt?>">
                        <input type="hidden" name="id_projeto" id="id_projeto" value="<?=$id_projeto?>">
                        <input type="hidden" name="id_regiao" id="id_regiao" value="<?=$id_regiao?>">
                        <div class="col-xs-6 text-left">
                            <a href="?tela=1&AMP;regiao=<?=$id_regiao?>&AMP;projeto=<?=$id_projeto?>" class="btn btn-default"><i class="fa fa-reply"></i> Voltar</a>
                        </div>
                        <div class="col-xs-6 text-right">
                            <input type="submit" class="btn btn-primary" value="Filtrar" name="filtrar" />
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </form>
            <?php
            if (isset($movimentos)) {
                if (count($movimentos) > 0) {
                    for ($ano = $ano; $ano <= $anoFim; $ano++) {
                        for ($mes = 1; $mes <= 13; $mes++) {
                            $count = 0;
                            if (!empty($movimentos[$ano][sprintf("%02d", $mes)])) { ?>
                                <table class="movimentos table table-bordered table-condensed table-hover">
                                    <thead>
                                        <tr class="bg-primary valign-middle">
                                            <th colspan="4">
                                                Mês dos Movimentos: <?=($mes != 13) ? mesCurto($mes) . '/' . $ano : '13º Salário' . '/' . $ano; ?>
                                            </th>
                                        </tr>
                                        <tr class="bg-info valign-middle">
                                            <th style="width:20%;">Cod</th>
                                            <th style="width:20%;">Tipo</th>
                                            <th style="width:40%;">Nome</th>
                                            <th style="width:20%;">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total = 0;
                                        foreach ($movimentos[$ano][sprintf("%02d", $mes)] as $key => $rowMov) {
                                            $total = ($rowMov['tipo'] == 'CREDITO') ? $total + $rowMov['valor'] : $total - $rowMov['valor']; ?>

                                            <tr class="valign-middle <?=($rowMov['tipo'] == 'CREDITO') ? 'success' : 'danger';
                                            ?>">
                                                <td style="text-align: center;"><?=$key ?></td>
                                                <td style="text-align: center;"><?=$rowMov['tipo'] ?></td>
                                                <td><?=$rowMov['nome'] ?></td>
                                                <td style="text-align: center;">R$ <?=number_format($rowMov['valor'], 2, ',', '.') ?></td>
                                            </tr>
                                            <?php
                                        } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="valign-middle">
                                            <td colspan="3" style="text-align:right; font-size: 1.1em;"><strong>Total:</strong></td>
                                            <td class="<?=($total >= 0) ? 'success' : 'danger'; ?>" style="text-align: center;">R$ 
                                                <?=number_format($total, 2, ',', '.'); unset($total); ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <br>
                                <?php
                            }
                        }
                    }
                } else { ?>
                    <br/>
                    <div id='message-box' class='alert alert-warning'>
                        <p>Nenhum registro encontrado</p>
                    </div>
                    <?php
                }
            }?>
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
</html>