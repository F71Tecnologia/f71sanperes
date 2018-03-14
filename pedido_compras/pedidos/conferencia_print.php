<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Print</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../favicon.ico">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/font-awesome.min.css" rel="stylesheet">
        <link href="../../resources/css/style-print.css" rel="stylesheet">
        <script src="../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../../resources/js/print.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-3">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <!--<button type="button" id="voltar" class="btn btn-default navbar-btn">Voltar</button>-->
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <div class="pagina">
            <h2 class="text-center"><strong>Conferência Pedido / NFe</strong></h2>
            <table class='table-striped table table-hover table-bordered table-condensed' id="tabela">
                <thead>
                    <tr class='text-center text-semibold'>
                        <th class='text-center text-semibold' colspan='2'>Pedido nº <?= $_REQUEST['id'] ?></th>
                        <th class='text-center text-semibold text-danger' colspan='3'>PEDIDO </th>
                        <th class='text-center text-semibold text-danger' colspan='3'>NFe </th>
                    </tr>
                    <tr class='text text-center text-sm'>
                        <th class='text text-center'>#</th>
                        <th class='text text-left'>Produto</th>
                        <th>Qtd.</th>
                        <th class='text text-right'>Valor Unit. (R$)</th>
                        <th class='text text-right'>Total Item (R$)</th>
                        <th>Qtd.</th>
                        <th class='text text-right'>Valor Unit. (R$)</th>
                        <th class='text text-right'>Total Item (R$)</th>
                    </tr>
                </thead>
                <tbody class='text-sm text-semibold'>
                    <?php
                    $i = 1;
                    $total_ped = 0;                    
                    $total_nfe = 0;                    
                    foreach ($array as $value) {
                        $vlr = number_format((float) ($value['NFE']['vUnCom']), 3, ",", ".") - number_format((float) ($value['pedido']['vUnCom']), 3, ",", ".");
                        $qntd = (float) $value['NFE']['qCom'] - (float) $value['pedido']['qCom'];
                        $corqtd = ($value['pedido']['qCom'] == $value['NFE']['qCom']) ? "text-success" : "text-danger";
                        $corvlr = ($value['pedido']['vUnCom'] == $value['NFE']['valor_produto']) ? "text-success" : "text-danger";
                        $cortot = ($value['pedido']['vProd'] == $value['NFE']['vProd']) ? "text-success" : "text-danger";
                        $total_ped +=$value['pedido']['vProd'];
                        $total_nfe +=$value['NFE']['vProd'];
                        ?>
                        <tr>    
                            <td class="text-center"><?= $i ?></td>
                            <td><input type="hidden" name="id_item_faltando[]" value="<?php echo $value['pedido']['id_item'] ?>">
                                <?php
                                if (empty($value['NFE'])) {
                                    echo $value['pedido']['xProd'];
                                } else {
                                    echo $value['NFE']['xProd'];
                                }
                                ?>
                            </td>
                            <td class='text-center <?= $corqtd ?>'><?php echo (float) $value['pedido']['qCom'] ?></td>
                            <td class="text-right <?= $corvlr ?>"><?php echo number_format((float) ($value['pedido']['vUnCom']), 3, ",", ".") ?></td>
                            <td class="text-right <?= $cortot ?>"><?php echo number_format((float) ($value['pedido']['vProd']), 2, ",", ".") ?></td>
                            <td class="text-center <?= $corqtd ?>"><?php echo (!empty($value['NFE']['qCom'])) ? (float) $value['NFE']['qCom'] : '-' ?></td>
                            <td class="text-right <?= $corvlr ?>"><?php echo (!empty($value['NFE']['valor_produto'])) ? number_format((float) ($value['NFE']['valor_produto']), 3, ",", ".") : '-' ?></td>
                            <td class="text-right <?= $cortot ?>"><?php echo (!empty($value['NFE']['vProd'])) ? number_format((float) ($value['NFE']['vProd']), 2, ",", ".") : '-' ?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                </tbody>
                <tfoot class='text-sm text-semibold'>
                    <tr>
                        <?php $cortot = ($total_ped == $total_nfe) ? "text-success" : "text-danger"; ?>
                        <td colspan="2"><strong>TOTAL:</strong></td>

                        <td>&emsp;</td>
                        <td>&emsp;</td>
                        <td class="text-right <?= $cortot ?>"><?= number_format($total_ped, 2, ",", ".") ?></td>
                        <td>&emsp;</td>
                        <td>&emsp;</td>
                        <td class="text-right <?= $cortot ?>"><?= number_format($total_nfe, 2, ",", ".") ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </body>
</html>