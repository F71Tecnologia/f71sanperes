<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}
include('../../conn.php');
include('../../wfunction.php');
include('../../classes/ViagemClass.php');

$objViagem = new ViagemClass();

//$percent = 8.33333;

$usuario = carregaUsuario();
$id = $_REQUEST['id'];
$row = $objViagem->getViagemById($id);

$arraySaidas = $objViagem->getSaidasByIdViagem($row['id_viagem']); ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="ISO-8859-9">
        <title>:: Intranet ::</title>
        <link href="../../favicon.png" rel="shortcut icon">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/style-print.css" rel="stylesheet" media="all">
        <style>
            /*@media all {*/
                * { padding: 2px; font-size: 10px; }
                div.pagina
                {
                  page-break-after: always;
                  page-break-inside: avoid;
                    /*padding: 0.7cm;*/
                }

/*                @media print{
                    .print { border: none; -webkit-appearance: none;}
                }*/

                /*td { width: 8.3333% !important; }*/

                .no-border-vr {
                    border-top-width: 0!important;
                    border-bottom-width: 0!important;
                }
            /*}*/
        </style>
    </head>
    <body class="text-sm">
        <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <button type="button" id="voltar" class="btn btn-default navbar-btn" onclick="window.close()"><i class="fa fa-reply"></i> Voltar</button>
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <div class="pagina">
            <div class="col-xs-12 no-padding">
                <table class="table table-bordered table-condensed no-margin valign-middle">
                    <tr>
                        <td colspan="2" class="text-center"><img src="/intranet/imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" style="width: 100px; height: 100px"></td>
                    </tr>
                    <tr>
                        <td class="text-bold">Prestação de conta de viagem: <?php echo $row['id_viagem'] ?></td>
                        <td>Data de Emissão: <?php echo $row['data'] ?></td>
                    </tr>
                </table>
                <table class="table table-bordered table-condensed no-margin valign-middle">
                    <tr>
                        <td class="no-border border-l">Prestação de Serv.:</td>
                        <td class="no-border"><?php echo $row['nome'] ?></td>
                        <td class="no-border">CPF:</td>
                        <td class="no-border border-r"><?php echo $row['cpf'] ?></td>
                    </tr>
                    <tr>
                        <td class="no-border border-l">Cargo / Função:</td>
                        <td class="no-border"><?php echo $row['funcao'] ?></td>
                        <td class="no-border">Locação:</td>
                        <td class="no-border border-r"><?php echo $row['locacao'] ?></td>
                    </tr>
                    <tr>
                        <td class="no-border border-l">Contato:</td>
                        <td class="no-border"><?php echo $row['tel_cel'] ?></td>
                        <td class="no-border">Dados Bancários:</td>
                        <td class="no-border border-r"><?php echo "{$row['banco']}: {$row['agencia']} / {$row['conta']}" ?></td>
                    </tr>
                    <tr>
                        <td class="no-border border-l" style="border-top: 1px solid #ddd !important;">Data Inicio:</td>
                        <td class="no-border" style="border-top: 1px solid #ddd !important;"><?php echo $row['data_ini'] ?></td>
                        <td class="no-border" style="border-top: 1px solid #ddd !important;">Data Término:</td>
                        <td class="no-border border-r" style="border-top: 1px solid #ddd !important;"><?php echo $row['data_fim'] ?></td>
                    </tr>
                    <tr>
                        <td class="no-border border-l">Objetivo da Viagem:</td>
                        <td colspan="3" class="no-border"><?php echo $row['descricao'] ?></td>
                    </tr>
                    <tr>
                        <td class="no-border border-l">Trajeto:</td>
                        <td colspan="3" class="no-border"><?php echo $row['trajeto'] ?></td>
                    </tr>
                    <tr>
                        <td class="no-border border-l">Cidade de Origem</td>
                        <td class="no-border"><?php echo $row['origem'] ?></td>
                        <td class="no-border">Cidade de Destino:</td>
                        <td class="no-border border-r"><?php echo $row['destino'] ?></td>
                    </tr>
                    <tr>
                        <td class="no-border border-l">Meio de Transporte:</td>
                        <td class="no-border"><?php echo ($row['id_tipo_meio_transporte'] == 1) ? 'ONIBUS' : 'CARRO' ?></td>
                        <td class="no-border">Veículo:</td>
                        <td class="no-border border-r"><?php echo ($row['id_tipo_meio_transporte'] == 1) ? $row['linha'] : $row['modelo'] . ' - ' . $row['placa'] ?></td>
                    </tr> 
                </table>
                <table class="table table-bordered table-condensed no-margin valign-middle">
                    <tr>
                        <td colspan="6" class="text-center text-bold no-padding-vr">Despesas de Viagem</td>
                    </tr>
                    <tr>
                        <td style="width: 1cm;" class="text-center no-padding-vr">Referência</td>
                        <td class="text-center no-padding-vr">Descrição da Despensa</td>
                        <td class="text-center no-padding-vr">Unid.</td>
                        <td style="width: 1cm;" class="text-center no-padding-vr">Quantidade</td>
                        <td style="width: 2.2cm;" class="text-center no-padding-vr">Valor Unitário</td>
                        <td class="text-center no-padding-vr">Valor Total</td>
                    </tr>
                    <?php foreach ($objViagem->getItensByIdViagem($row['id_viagem']) as $key => $value) { ?>
                    <tr>
                        <td class="text-center no-padding-vr"><?php echo $key ?></td>
                        <td class="text-left no-padding-vr"><?php echo $value['nome'] ?></td>
                        <td class="text-center no-padding-vr"><?php echo $value['unidade'] ?></td>
                        <td class="text-center no-padding-vr"><?php echo $value['qtd'] ?></td>
                        <td class="text-right no-padding-vr"><?php echo number_format($value['valor_unitario'],2,',','.') ?></td>
                        <td class="text-right no-padding-vr"><?php echo number_format($value['valor'],2,',','.') ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="4" class="text-center no-padding-vr"></td>
                        <td class="text-right text-bold no-padding-vr">TOTAL:</td>
                        <td class="text-right text-bold no-padding-vr"><?php echo number_format($row['valor'],2,',','.') ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-center text-bold no-padding-vr">Despesas Realizadas</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="width: 1cm;" class="text-center no-padding-vr">Id</td>
                        <td class="text-center no-padding-vr">Nome</td>
                        <td class="text-center no-padding-vr">Nota Fiscal</td>
                        <td class="text-center no-padding-vr">Valor</td>
                    </tr>
                    <?php foreach ($arraySaidas as $key => $value) { $valorTotalItens += $value['valor'] ?>
                    <tr>
                        <td class="text-center no-padding-vr"><?php echo $value['id_saida'] ?></td>
                        <td class="text-left no-padding-vr" colspan="3"><?php echo $value['nome'] ?></td>
                        <td class="text-center no-padding-vr"><?php echo $value['n_documento'] ?></td>
                        <td class="text-right no-padding-vr"><?php echo number_format($value['valor'], 2, ',', '.') ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="4" class="text-center no-padding-vr"></td>
                        <td class="text-right text-bold no-padding-vr">TOTAL:</td>
                        <td class="text-right text-bold no-padding-vr"><?php echo number_format($row['totalAcerto'],2,',','.') ?></td>
                    </tr>
                </table>
                <table class="table table-bordered table-condensed no-margin valign-middle">
                    <tr>
                        <td colspan="4" class="text-center text-bold">Resumo para acerto</td>
                    </tr>
                    <tr>
                        <th class="text-left">Previsão de despesa solicitada:</th>
                        <th class="text-right"><?php echo number_format($row['valor'],2,',','.') ?></th>
                        <th class="text-left">A devolver:</th>
                        <th class="text-right"><?php echo number_format($row['valor_devolver'],2,',','.') ?></th>
                    </tr>
                    <tr>
                        <th class="text-left">Despesa realizada:</th>
                        <th class="text-right"><?php echo number_format($row['totalAcerto'],2,',','.') ?></th>
                        <th class="text-left">A reembolsar:</th>
                        <th class="text-right"><?php echo number_format($row['valor_pagar'],2,',','.') ?></th>
                    </tr>
                </table>
                <table class="table table-bordered table-condensed no-margin valign-middle">
                    <tr>
                        <td colspan="4" class="text-center text-bold">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="text-center"></th>
                        <th class="text-center">Solicitação</th>
                        <th class="text-center">Autorização</th>
                        <th class="text-center">Liberação</th>
                    </tr>
                    <tr>
                        <td class="text-center">Data:</td>
                        <td class="text-center">___/___/______</td>
                        <td class="text-center">___/___/______</td>
                        <td class="text-center">___/___/______</td>
                    </tr>
                    <tr>
                        <td class="text-center">Visto:</td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-center text-bold">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="text-center"></th>
                        <th class="text-center">Pagamento</th>
                        <th class="text-center">Recebimento</th>
                        <th class="text-center">Observação</th>
                    </tr>
                    <tr>
                        <td class="text-center">Data:</td>
                        <td class="text-center">___/___/______</td>
                        <td class="text-center">___/___/______</td>
                        <td class="text-center"></td>
                    </tr>
                    <tr>
                        <td class="text-center">Visto:</td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- javascript aqui -->
        <script src="../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../../resources/js/print.js" type="text/javascript"></script>
    </body>
</html>
